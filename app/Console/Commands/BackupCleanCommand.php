<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupCleanCommand extends Command
{
    protected $signature = 'backup:clean';

    protected $description = 'Delete old backups exceeding retention limits (count / age / total size)';

    public function handle(BackupService $backups): int
    {
        try {
            $deleted = $backups->cleanup();
        } catch (\Throwable $e) {
            $this->error('Cleanup failed: '.$e->getMessage());
            report($e);

            return self::FAILURE;
        }

        if (count($deleted) === 0) {
            $this->info('Nothing to clean. All backups are within retention limits.');
        } else {
            $this->info('Deleted '.count($deleted).' old backup(s):');
            foreach ($deleted as $file) {
                $this->line(" - {$file}");
            }
        }

        return self::SUCCESS;
    }
}
