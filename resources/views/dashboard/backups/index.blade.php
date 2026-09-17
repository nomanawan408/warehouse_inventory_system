@extends('layouts.app')

@section('title', 'System Backups')

@section('styles')
<style>
    .backup-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    }
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 18px rgba(0,0,0,.1); }
    .stat-value { font-size: 1.6rem; font-weight: 700; }
    .stat-label { color: #6c757d; font-size: .8rem; text-transform: uppercase; margin-bottom: 0; }
    .type-badge-database { background: rgba(78,115,223,.12); color: #2e59d9; }
    .type-badge-files { background: rgba(28,200,138,.12); color: #13855c; }
    .type-badge-full { background: rgba(246,194,62,.15); color: #9a6b00; }
    .table thead th { font-size: .8rem; text-transform: uppercase; color: #6c757d; border-bottom-width: 1px; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: .85rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow-sm mb-4 backup-header rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white p-2 rounded-circle me-3 d-flex justify-content-center align-items-center" style="width:48px;height:48px">
                        <i class="fas fa-database text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-0 text-white fw-bold">System Backups</h1>
                        <p class="text-white-50 mb-0">Database &amp; file backups, restore, download and retention</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('backups.store') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                        @csrf
                        <select name="type" class="form-select form-select-sm" style="width:auto" required>
                            <option value="database" selected>Database only</option>
                            <option value="files">Files only</option>
                            <option value="full">Full (DB + files)</option>
                        </select>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i> Create Backup
                        </button>
                    </form>
                    <form action="{{ route('backups.clean') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm" title="Delete backups exceeding retention limits">
                            <i class="fas fa-broom me-1"></i> Clean Old
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-archive text-primary"></i><span class="stat-label">Total backups</span></div>
                <div class="stat-value">{{ $stats['count'] }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-hdd text-success"></i><span class="stat-label">Backup storage used</span></div>
                <div class="stat-value">{{ $stats['total_size_human'] }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-table text-info"></i><span class="stat-label">Live database size</span></div>
                <div class="stat-value">{{ $stats['database_size_human'] }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-clock text-warning"></i><span class="stat-label">Latest backup</span></div>
                <div class="fw-semibold">{{ $stats['latest']['modified_formatted'] ?? '—' }}</div>
                <div class="text-muted small mono">{{ $stats['latest']['name'] ?? 'No backups yet' }}</div>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Available Backups</h5>
            <small class="text-muted">Keeps last {{ config('backup.keep_count') }} backups / {{ config('backup.keep_days') }} days &middot; Auto-backup daily at {{ config('backup.schedule_daily_at') }}</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">File</th>
                            <th>Type</th>
                            <th>Contents</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td class="ps-4 mono">{{ $backup['name'] }}</td>
                            <td>
                                <span class="badge rounded-pill type-badge-{{ $backup['type'] }} px-3 py-2 text-capitalize">
                                    {{ $backup['type'] }}
                                </span>
                            </td>
                            <td>
                                @if($backup['has_database'])
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-1" title="Contains database dump"><i class="fas fa-database me-1"></i>DB</span>
                                @endif
                                @if($backup['has_files'])
                                    <span class="badge bg-success bg-opacity-10 text-success" title="Contains application files"><i class="fas fa-folder me-1"></i>Files</span>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $backup['size_human'] }}</td>
                            <td>
                                <div>{{ $backup['modified_formatted'] }}</div>
                                <small class="text-muted">{{ $backup['modified_human'] }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('backups.download', $backup['name']) }}" class="btn btn-outline-primary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if($backup['has_database'] || $backup['has_files'])
                                    <button type="button" class="btn btn-outline-warning" title="Restore" data-bs-toggle="modal" data-bs-target="#restoreModal-{{ md5($backup['name']) }}">
                                        <i class="fas fa-rotate-left"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ md5($backup['name']) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Restore modal -->
                                <div class="modal fade text-start" id="restoreModal-{{ md5($backup['name']) }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('backups.restore', $backup['name']) }}" method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header bg-warning bg-opacity-10">
                                                <h5 class="modal-title"><i class="fas fa-triangle-exclamation text-warning me-2"></i>Restore backup?</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mono small bg-light p-2 rounded">{{ $backup['name'] }}</p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">What to restore</label>
                                                    <select name="scope" class="form-select" required>
                                                        @if($backup['has_database'])
                                                        <option value="database" selected>Database only (recommended)</option>
                                                        @endif
                                                        @if($backup['has_files'])
                                                        <option value="files" @if(!$backup['has_database']) selected @endif>Files only (storage/app/public)</option>
                                                        @endif
                                                        @if($backup['has_database'] && $backup['has_files'])
                                                        <option value="full">Full (database + files)</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="alert alert-danger small mb-0">
                                                    Restoring the <strong>database</strong> overwrites current data.
                                                    A safety backup of the current database is created automatically first.
                                                </div>
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirm-{{ md5($backup['name']) }}" required>
                                                    <label class="form-check-label" for="confirm-{{ md5($backup['name']) }}">
                                                        I understand and want to restore this backup
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Restore</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Delete modal -->
                                <div class="modal fade text-start" id="deleteModal-{{ md5($backup['name']) }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete backup?</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mono small bg-light p-2 rounded">{{ $backup['name'] }}</p>
                                                <p class="text-muted mb-0">This cannot be undone. Download it first if you might need it.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-database fa-2x mb-3 d-block"></i>
                                No backups yet. Create your first backup using the button above,<br>
                                or run <code>php artisan backup:run</code> on the server.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="fas fa-circle-info me-2 text-info"></i>How backups work</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 small text-muted">
                <div class="col-md-4">
                    <strong class="text-dark">Database only</strong><br>
                    Dumps every table (schema + data) into <code>database/db.sql</code> inside the ZIP. Uses <code>mysqldump</code> when available, otherwise a built-in dumper. Safe for MySQL, MariaDB and SQLite.
                </div>
                <div class="col-md-4">
                    <strong class="text-dark">Files / Full</strong><br>
                    Additionally zips <code>storage/app/public</code> (uploads) under <code>files/</code>. Restore merges files back — existing files are overwritten, nothing is deleted.
                </div>
                <div class="col-md-4">
                    <strong class="text-dark">Automation</strong><br>
                    Runs daily at <code>{{ config('backup.schedule_daily_at') }}</code> once the server cron calls <code>php artisan schedule:run</code> every minute. Retention keeps the newest {{ config('backup.keep_count') }} backups for {{ config('backup.keep_days') }} days. Backups live in <code>storage/app/backups</code> (never public).
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
