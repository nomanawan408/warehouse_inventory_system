# Repository Audit — Inventory System (Laravel 11 + Blade + MySQL)

- **Date:** October 2026
- **Scope:** full repo — backend architecture, database schema, frontend/UX, security, config, ops, testing
- **Method:** read-only static analysis + live read-only probes (row counts, endpoint memory profiles). No migrations run, no data modified for the audit itself.
- **Verdict: functional, revenue-driving POS with real product-market-fit habits (backup system, tests, services) — but carrying structural debt in authorization, financial atomicity, and database safety. Grade: C+ → B+ achievable in ~2 weeks of focused fixes.**

Scale context at audit time: 19.8 MB MariaDB, 29 tables, **11,042 sales / 41,037 sale items / 18,052 customer transactions / 8,608 payments / 236 customers / 291 products**, ~85 Blade views, 24 migrations, 6 Composer runtime deps (`php`, `barryvdh/laravel-dompdf`, `doctrine/dbal`, `laravel/framework`, `laravel/tinker`, `spatie/laravel-permission`).

The `/sales` memory outage (128 MB exhausted rendering the list) is a symptom of Finding H-1's pattern, not an isolated bug. It was fixed via server-side DataTables (`SalesController@data`, `sales.data` route) + streaming exports — that pattern is now the template for all other lists.

---

## 1. Architecture & system design

**What it is:** classic Laravel MVC monolith with a Blade + jQuery hybrid frontend — server-rendered pages for lists/forms, JSON endpoints for the POS cart and server-side DataTables, session auth via Breeze. No API layer, no queued jobs in use, two real service classes (`BackupService` — good layering; `ProfitCalculationService` — partially adopted).

**Strengths:** thin backup controller + rich service; Eloquent parameter binding everywhere (no SQL injection found); `$fillable` discipline on all models; SQLite in-memory test isolation (`phpunit.xml`); server-side sales feed as the proven template; print/thermal views are the best-crafted frontend area; filesystem backup design fits the 19.8 MB size well.

**Structural weaknesses:**
- **Fat controllers own everything** — `SalesController` (~665 lines), `ReportController` (585), `ProfitReportController` (341), `AccountController` (~320) mix HTTP, SQL, balance math, and stock mutation. No FormRequests, Policies, Observers, or API Resources. Every feature edit lands in a 600-line file.
- **Single `auth` group, zero authorization.** Spatie roles (`superadmin/admin/user`) are installed, seeded, and aliased as middleware — but applied to *zero* routes. Any logged-in user can delete companies, restore the database, and read every account. The backup UI gate exists (`BackupController:15-17`) but defaults to off (`config/backup.php:106-109`).
- **Two design systems coexist:** Tailwind/Alpine (Breeze auth pages + a dead Vite build nobody references) vs Bootstrap 5/jQuery (the actual app). `welcome.blade.php` doubles as the POS screen while `dashboard/sales/create.blade.php` is a 0-byte file.
- **Forked code already diverged:** `public/js/dashboard.js` vs the ~664-line inline copy in `welcome.blade.php` (discount logic lives in only one); 4 parallel profit-dashboard Blade forks (`_old`/`_new`/`_clean`); `print` vs `invoice` vs `export_pdf` triplicated markup.

---

## 2. Critical findings (data-loss / money-corruption class)

### C-1. No authorization — any authenticated user is admin
- **Evidence:** `routes/web.php:18` single `auth` group; zero `role`/`permission`/`can` middleware in `routes/`; `bootstrap/app.php:15-19` aliases unused.
- **Impact:** any credential (or the seeded superadmin if unrotated) can CRUD everything + restore the DB (destructive).
- **Fix:** define matrix (`superadmin > admin > user/cashier`); `->middleware('role:superadmin|admin')` on `/backups/*`, `/companies/*`, `/reports/profit/*`, all destroys; add `CompanyPolicy`/`SalePolicy` + `$this->authorize()`; set `BACKUP_ALLOWED_ROLES=superadmin,admin`; add 403 tests.

### C-2. `SalesController::store` leaks open transactions + races on stock
- **Evidence:** `SalesController.php:286` `DB::beginTransaction()`, then `307-311` early `return 400` on missing/insufficient stock **without rollback**; `Product::find()` + `decrement()` with no `lockForUpdate`.
- **Fix:** `DB::transaction(fn…)` closure (or throw `ValidationException` instead of early return); `Product::whereKey(…)->lockForUpdate()->first()` before the quantity check.

### C-3. All other money movements are non-atomic
- **Evidence:** `AccountController:24-78` (`storePayment`, 3 writes), `:161-197` (`storePendingAmount`), `:218-318` read-modify-write balances; `CompanyController:148-177`, `:198-297` — none use transactions. Concurrent payments corrupt `total_paid`/`pending_balance`.
- **Fix:** wrap each in `DB::transaction()`, `lockForUpdate()` the account row, prefer atomic `increment/decrement` inside the tx.

### C-4. Destructive cascades + zero soft deletes
- **Evidence:** `products.company_id CASCADE`, `payments.* CASCADE`, `sale_items.sale_id CASCADE`; live adds `sale_items.product_id CASCADE`; `grep SoftDeletes` = 0 hits. Deleting a company cascades through products into sale history; deleting a sale deletes receipts but only nulls transactions.
- **Fix:** migration to `RESTRICT`/`SET NULL` on all money facts (`payments.sale_id`, `customer_transactions.sale_id`, `products.company_id`); `softDeletes()` on customers/companies/products/sales/purchases + trait + scoped reports.

### C-5. Silent wrong-money bugs (each verified in code)
- `CustomerTransaction::$fillable` omits `detail` (migration has the column; `AccountController:61,134-139,183` writes it → silently dropped). Company twin includes it.
- `ReportController:412-432` sums `discount_amount`/`net_amount` — columns that don't exist (`sales` uses `discount`/`net_total`) → customer PDF totals always 0. The sibling `customerReport:272-274` is correct.
- `SaleItem::getProfitMarginAttribute (:36-42)` shadows the stored `profit_margin` column on every read and fatals if the product was deleted; reports `SUM()` the stored value while PHP recomputes — two profits diverge.
- `Company::sales() (:16-19)` targets `sales` with no `company_id` (always empty); `Payment::account()` / `CustomerAccount::payments()` join on a nonexistent `customer_account_id`.
- **Fix:** allowlist `detail`; correct export sums + regression test vs `customerReport`; remove/rename the accessor (stored value is truth); repair relations (`hasManyThrough` or explicit keys); unify on one.

### C-6. Migration history cannot rebuild production
- **Evidence:** `down()` typos (`dropIfExists('cart')` vs `create('carts')`; `companies_accounts` vs `company_accounts`); `dropForeign(['product_id'])` for an FK that exists only live; live-only FKs (`customer_transactions.customer_id`, `sale_items.product_id`); frozen `->default(now())` timestamps (live defaults stuck at Feb/Mar 2025); `pedding_amount` typo live; one empty no-op migration.
- **Fix:** one idempotent guarded repair migration (`Schema::hasColumn`/`hasIndex` checks); prove with staging `migrate:fresh --seed`. Never edit old files.

---

## 3. High findings (scale & correctness)

### H-1. Unbounded loads on every list except sales
- **Evidence:** `ProductsController:18-19`, `CustomerController:12-13`, `CompanyController:34-35,101`, `AccountController:13-14`, `SalesController:55-56,264-265,416`, `web.php:145-161` `/products/all`, `searchProducts` with no limit + a debug `Log::info($products->toArray())` (`SalesController:639`, includes costs).
- **Fix:** replicate the proven server-side pattern (11,042 rows → 25 in 0.15 s @ 38 MB, measured at 128 M cap); paginate or `limit(20)` search endpoints; delete the debug log.

### H-2. Missing indexes on ledger hot paths
- Only `sales_updated_at_index` exists beyond FK auto-indexes. Add: `(customer_id, transaction_date)` + `(transaction_date)` on `customer_transactions`; `(customer_id, payment_date)` + `(payment_date)` on `payments`; `(customer_id, created_at)` + `(created_at)` on `sales`; `(name)` on `products`; unique guards on the 1:1 account tables. Minutes of work; prevents full scans at 18k→100k rows.

### H-3. Denormalized balances, no reconciliation, duplicate ledger
- `customer_accounts`/`company_accounts`/`sales` store derivable sums with no unique constraint, no locks, no checks; legacy `accounts` (58 rows, incl. `pedding_amount` typo) duplicates `customer_accounts` (236 rows); seeder backfills only the company side.
- **Fix:** unique constraints + nightly reconcile job (`SUM(transactions)` vs accounts); long-term make accounts a view or tx-only writes.

### H-4. Timestamp schizophrenia
- List/filters run on `updated_at`, reports/profit on `created_at`; editing a sale teleports it between views. → One canonical sale date (new `sale_date` or canonicalize `created_at`) + index.

### H-5. Supply-chain & asset hygiene
- Zero SRI on all CDN includes; two unpinned `latest` URLs (Chart.js v4 drift risk vs v2/v3 API in use); jQuery 2–3× + Bootstrap JS 2× per page (~300 KB duplicate JS, version skew 3.6.4 vs 3.6.0). → Single load in the layout, pin + SRI everything, move POS JS to one Vite entry.

### H-6. Stored-XSS path via product/customer names
- 29× `{!! json_encode(…names) !!}` in profit/print views; `${product.name}`, `${customer.name}`, `${errorMsg}` interpolated into `.html()`. Names are user-written and rendered for every cashier.
- **Fix:** `@json()` in Blade, `textContent`/`.text()`/`escapeHtml()` in JS, add CSP header. (Note: `sales/index` already escapes correctly — copy that.)

### H-7. Money precision + naming drift
- Precision upgrade covered only sales-side; `tax`, payments, purchases, products, company side still `decimal(10,2)`; `pedding_amount` vs `pending_amount` vs `pending_balance`; `total_paid` vs `amount_paid`; capitalized `Cash` enum vs lowercase `debit` vs free-text `transaction_type` twin.
- **Fix:** one migration unifying to `decimal(15,2)` (doctrine/dbal present), rename typo, normalize lowercase enums.

### H-8. Backup restore ungated + secrets in history
- Restore/delete/download default to any-authenticated-user; seeded superadmin password committed to git; `APP_DEBUG=true` in `.env.example`; resets mailed to `log`.
- **Fix:** `BACKUP_ALLOWED_ROLES`, password re-confirm before restore, rotate/remove seeded secret (env-provisioned bootstrap), prod env checklist, real mailer.

---

## 4. Medium / low findings

- **M-1** Validation gaps: company/customer saves unvalidated phone/CNIC/address; `company_id` never validated (`ProductsController:30-43`); `paid_amount` nullable then `increment(total_paid, null)`; over-discount hidden by `max(0,…)`; `store` (array cart) vs `update` (JSON-string cart) disagree. → Shared `SaleRequest` FormRequest.
- **M-2** Inverted debit/credit semantics between customer and company modules; undocumented. → Normalize + document + balance-math tests.
- **M-3** Non-RESTful manual CRUD routes, duplicate `accounts`/`accounts/show`, business logic in route closures, dead controllers (`StockController`, 4-line `InvoiceController`, unrouted `PurchaseController` actions). → `Route::resource`, `whereNumber`, move closures to controllers.
- **M-4** Null-deref lookups (`Company::find`, `Customer::find` without checks) → `findOrFail` + route-model binding.
- **M-5** Timezone fragility: `Asia/Karachi` hardcoded + global `Carbon::setTimezone` mutation while `.env` says UTC; mixed `date`/`datetime`/`timestamp` columns. → UTC in DB, convert in presenters.
- **M-6** Tooltips declared everywhere, initialized almost nowhere; Bootstrap-4 dropdown JS fights Bootstrap 5; stale server-time clock badge.
- **M-7** No audit trail (`created_by`/activity log/deleted_at all absent); stored `profit_margin` dead while accessor recomputes.
- **M-8** POS leaks full customer PII (CNIC/address/phone) into page source via `@json($customers)`; preloads whole catalog then `slice(0,20)`. → Paged AJAX search, `select(id,name,phone)`.
- **M-9** Icon-only buttons lack `aria-label`; tables lack responsive wrappers/captions; deletes use native `confirm()`.
- **M-10** Scheduler exists but no system cron guarantee; `QUEUE_CONNECTION=database` with zero jobs; `SESSION_SECURE_COOKIE` unset; root `inventory_system.zip` not git-ignored; stale `database.sqlite` confuses restores.
- **L-1** Tests: 6 stale failures on routes that don't exist by design (register disabled, `dashboard` route missing, `/` redirects) — see §6.

---

## 5. System-design assessment

The monolith choice fits the scale (single shop, 20 MB DB, one server). No redesign needed; what it needs is **boundaries inside the monolith**: service layer for money/stock (extend the `BackupService` precedent), policies for authz, query discipline (paginate/stream/chunk as the default for anything touching `sales`/`sale_items`/`transactions`/`payments`), and DB-level guards (FK discipline, uniques, soft deletes) so application bugs can't become data loss. Deferred decisions to revisit at 10× scale: read replicas/reporting DB, queue workers for exports, API tokens for a mobile/POS client.

---

## 6. Test-suite baseline (October 2026)

`php artisan test` → **26 passed, 6 failed (82 assertions)**; `SalesListingTest` 7/7 pass. The 6 failures are stale/pre-existing and unrelated to sales or backups:
- `RegistrationTest` ×2 — `/register` 404 by design (`routes/auth.php:15-18` commented out).
- `ExampleTest` ×1 — `/` 302-redirects to `sales.create` (`web.php:20-22`), test expects 200.
- `AuthenticationTest` / `EmailVerificationTest` / `PasswordConfirmationTest` ×3 — expect a `dashboard` route that doesn't exist (app redirects to `sales.create`).
- `phpunit.xml` correctly forces SQLite `:memory:` — suite can never touch the live DB again (this exact misconfiguration once wiped production data during verification; see project history).

---

## 7. Roadmap

**Week 1 — stop the bleeding:** role gates on admin/destructive routes + `BACKUP_ALLOWED_ROLES`; `DB::transaction()` + `lockForUpdate` on all balance/stock writes (incl. the open-transaction early return); rotate seeded credential; `APP_ENV=production, APP_DEBUG=false`, unique `APP_KEY`, `SESSION_SECURE_COOKIE=true`, real mailer; install `schedule:run` cron; remove debug `Log::info(toArray())`.
**Week 2 — money truth:** fillable `detail`; correct export sums + regression test; un-shadow `profit_margin`; repair the three relations; canonical sale date; `decimal(15,2)` migration; ledger indexes + 1:1 uniques; server-side remaining lists; cap/remove `/products/all`; stop embedding customer PII.
**Next — durability:** guarded repair migration (FKs, `useCurrent`, typo rename, soft deletes, `RESTRICT` cascades) proven via staging `migrate:fresh`; nightly reconcile job; activity log; single Vite POS entry; SRI-pin CDN; delete dead forks/views; green suite.

Highest ROI order: **authz gates → transaction atomicity → cascade/soft-delete safety → server-side lists → ledger indexes.**
