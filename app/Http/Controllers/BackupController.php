<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BackupController extends Controller
{
    public function __construct(protected BackupService $backups)
    {
        $roles = (array) config('backup.allowed_roles', []);
        if (! empty($roles)) {
            $this->middleware('role:'.implode('|', $roles));
        }
    }

    /**
     * Backup dashboard.
     */
    public function index(): View
    {
        $backups = $this->backups->list();
        $stats = $this->backups->stats();

        return view('dashboard.backups.index', compact('backups', 'stats'));
    }

    /**
     * Create a new backup.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        try {
            $filename = $this->backups->create($validated['type']);

            // Enforce retention automatically so the disk never grows forever.
            try {
                $this->backups->cleanup();
            } catch (\Throwable) {
                // Retention failure must not hide a successful backup.
            }

            return redirect()->route('backups.index')
                ->with('success', "Backup created successfully: {$filename}");
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('backups.index')
                ->with('error', 'Backup failed: '.$e->getMessage());
        }
    }

    /**
     * Download a backup ZIP.
     */
    public function download(string $filename): SymfonyResponse|RedirectResponse
    {
        try {
            $absolute = $this->backups->backupAbsolutePath($filename);

            return response()->download($absolute, basename($absolute));
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a backup ZIP.
     */
    public function destroy(string $filename): RedirectResponse
    {
        try {
            $this->backups->delete($filename);

            return redirect()->route('backups.index')
                ->with('success', 'Backup deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Restore the database from a backup.
     * A safety backup of the current DB is taken first.
     */
    public function restore(Request $request, string $filename): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|accepted',
            'scope' => 'nullable|in:database,files,full',
        ]);

        $scope = $request->input('scope', 'database');

        try {
            $messages = [];

            if (in_array($scope, ['database', 'full'], true)) {
                $safety = $this->backups->restoreDatabase($filename);
                $messages[] = "Database restored. Safety backup of previous data: {$safety}.";
            }

            if (in_array($scope, ['files', 'full'], true)) {
                $count = $this->backups->restoreFiles($filename);
                $messages[] = "Files restored ({$count} files).";
            }

            return redirect()->route('backups.index')
                ->with('success', implode(' ', $messages));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('backups.index')
                ->with('error', 'Restore failed: '.$e->getMessage());
        }
    }

    /**
     * Manually trigger retention cleanup.
     */
    public function clean(): RedirectResponse
    {
        try {
            $deleted = $this->backups->cleanup();

            return redirect()->route('backups.index')
                ->with('success', count($deleted) === 0
                    ? 'Nothing to clean. All backups are within retention limits.'
                    : 'Cleanup done. Deleted '.count($deleted).': '.implode(', ', $deleted));
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Cleanup failed: '.$e->getMessage());
        }
    }
}
