<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRunCommand extends Command
{
    protected $signature = 'backup:run
                            {--type=database : Backup type: database, files or full}
                            {--no-cleanup : Skip retention cleanup after backup}';

    protected $description = 'Create a new application backup (database and/or files)';

    public function handle(BackupService $backups): int
    {
        $type = (string) $this->option('type');

        if (! in_array($type, BackupService::types(), true)) {
            $this->error('Invalid type. Use: '.implode(', ', BackupService::types()));

            return self::FAILURE;
        }

        $this->info("Creating {$type} backup...");

        try {
            $filename = $backups->create($type);
        } catch (\Throwable $e) {
            $this->error('Backup failed: '.$e->getMessage());
            report($e);

            return self::FAILURE;
        }

        $this->info("Backup created: {$filename}");

        if (! $this->option('no-cleanup')) {
            try {
                $deleted = $backups->cleanup();
                if (count($deleted) > 0) {
                    $this->info('Retention cleanup deleted '.count($deleted).' old backup(s).');
                }
            } catch (\Throwable $e) {
                $this->warn('Retention cleanup failed: '.$e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
