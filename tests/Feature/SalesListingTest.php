<?php

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::create([
        'name' => 'Test Customer',
        'business_name' => 'Test Co',
        'phone_no' => '03000000000',
        'address' => 'Test Street',
        'cnic' => '12345-1234567-1',
        'discount' => 0,
    ]);

    // 30 sales: enough to prove only one page is ever returned.
    foreach (range(1, 30) as $i) {
        Sale::create([
            'customer_id' => $this->customer->id,
            'total_amount' => 100 * $i,
            'discount' => 0,
            'discount_percent' => 0,
            'tax' => 0,
            'net_total' => 100 * $i,
            'amount_paid' => 100 * $i,
            'pending_amount' => 0,
        ]);
    }
});

it('requires authentication for the sales list, feed and exports', function () {
    $this->get('/sales')->assertRedirect('/login');
    $this->get('/sales/data')->assertRedirect('/login');
    $this->get('/sales/export/csv')->assertRedirect('/login');
});

it('renders the list shell without embedding every sale', function () {
    $response = $this->actingAs($this->user)->get('/sales');

    $response->assertOk();
    // Server-side feed wired up (URL is JSON-escaped in the script)...
    $response->assertSee('sales\\/data', false);
    $response->assertSee('serverSide', false);
    // ...but no sale rows embedded in the HTML (only the thead <tr>).
    $response->assertDontSee('# 030');
    expect(substr_count($response->getContent(), '<tr>'))->toBe(1);
});

it('serves one page at a time from the JSON feed', function () {
    $response = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=25');

    $response->assertOk()->assertJson([
        'draw' => 1,
        'recordsTotal' => 30,
        'recordsFiltered' => 30,
    ]);
    expect($response->json('data'))->toHaveCount(25);

    $second = $this->actingAs($this->user)->getJson('/sales/data?draw=2&start=25&length=25');
    expect($second->json('data'))->toHaveCount(5);
});

it('searches by customer name and caps page size', function () {
    $response = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=25&search[value]=Test+Customer');
    expect($response->json('recordsFiltered'))->toBe(30);

    $none = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=25&search[value]=Nobody+Here');
    expect($none->json('recordsFiltered'))->toBe(0);

    // Absurd page sizes are clamped, never fatal.
    $huge = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=1000000');
    expect($huge->json('data'))->toHaveCount(30);
});

it('filters by daily range', function () {
    // Backdate one sale out of the daily window (query builder: Eloquent
    // would auto-touch updated_at back to now).
    Sale::where('id', Sale::first()->id)->update(['updated_at' => now()->subMonth()->format('Y-m-d H:i:s')]);

    $response = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=50&filter=daily');
    expect($response->json('recordsFiltered'))->toBe(29);

    $all = $this->actingAs($this->user)->getJson('/sales/data?draw=1&start=0&length=50&filter=all');
    expect($all->json('recordsFiltered'))->toBe(30);
});

it('streams the full filtered CSV export', function () {
    $response = $this->actingAs($this->user)->get('/sales/export/csv?filter=all');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/csv');
    $lines = array_filter(explode("\n", trim($response->streamedContent())));
    // Header + all 30 rows.
    expect($lines)->toHaveCount(31);
    expect($lines[0])->toContain('Invoice #');
});

it('downloads the PDF export and rejects unknown formats', function () {
    $this->actingAs($this->user)->get('/sales/export/pdf?filter=all')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    $this->actingAs($this->user)->get('/sales/export/xlsx')->assertNotFound();
});
