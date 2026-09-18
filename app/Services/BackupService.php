<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * Filesystem-based backup manager.
 *
 * Creates versioned ZIP backups containing:
 *  - database/db.sql   (for `database` and `full` types)
 *  - files/...         (for `files` and `full` types)
 *  - meta.json         (always)
 *
 * No database table is required: the backup directory itself is the index.
 */
class BackupService
{
    public const TYPE_DATABASE = 'database';
    public const TYPE_FILES = 'files';
    public const TYPE_FULL = 'full';

    public static function types(): array
    {
        return [self::TYPE_DATABASE, self::TYPE_FILES, self::TYPE_FULL];
    }

    protected function disk()
    {
        return Storage::disk(config('backup.disk', 'backups'));
    }

    protected function basePath(): string
    {
        return (string) config('backup.path', '');
    }

    protected function fullPath(string $filename): string
    {
        return $this->basePath() === ''
            ? $filename
            : trim($this->basePath(), '/').'/'.$filename;
    }

    /**
     * Guard against path traversal / arbitrary file access.
     */
    public function sanitizeFilename(string $filename): string
    {
        $base = basename($filename);

        if ($base !== $filename && basename($filename) !== $filename) {
            // basename strips directories; if input had traversal it won't match after strip
        }

        $prefix = preg_quote((string) config('backup.filename_prefix', 'backup'), '/');

        if (! preg_match('/^'.$prefix.'-(database|files|full)-[\d\-]+(-uploaded(-\d+)?)?\.zip$/', $base)) {
            throw new RuntimeException('Invalid backup filename.');
        }

        if (! $this->disk()->exists($this->fullPath($base))) {
            throw new RuntimeException('Backup file not found.');
        }

        return $base;
    }

    public function backupAbsolutePath(string $filename): string
    {
        $safe = $this->sanitizeFilename($filename);

        // Resolve absolute path for the configured local disk.
        $diskRoot = (string) config('filesystems.disks.'.config('backup.disk', 'backups').'.root', storage_path('app/backups'));

        return rtrim($diskRoot, '/').'/'.ltrim($this->fullPath($safe), '/');
    }

    /**
     * Create a new backup ZIP and return its filename.
     */
    public function create(string $type = self::TYPE_DATABASE): string
    {
        if (! in_array($type, self::types(), true)) {
            throw new RuntimeException('Invalid backup type. Use: '.implode(', ', self::types()));
        }

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP zip extension is required for backups.');
        }

        $this->ensureBackupDirectoryExists();

        $date = Carbon::now()->format((string) config('backup.date_format', 'Y-m-d-H-i-s'));
        $prefix = (string) config('backup.filename_prefix', 'backup');
        $filename = "{$prefix}-{$type}-{$date}.zip";
        $relativePath = $this->fullPath($filename);
        $absolutePath = $this->disk()->path($relativePath);

        // In the extremely unlikely case of a collision within the same second, suffix it.
        $counter = 1;
        while (File::exists($absolutePath)) {
            $filename = "{$prefix}-{$type}-{$date}-{$counter}.zip";
            $relativePath = $this->fullPath($filename);
            $absolutePath = $this->disk()->path($relativePath);
            $counter++;
        }

        $tmpDir = sys_get_temp_dir().'/laravel-backup-'.uniqid('', true);
        File::ensureDirectoryExists($tmpDir);
        $sqlFile = $tmpDir.'/db.sql';

        try {
            $tables = 0;
            $rows = 0;

            if ($type === self::TYPE_DATABASE || $type === self::TYPE_FULL) {
                ['tables' => $tables, 'rows' => $rows] = $this->dumpDatabaseToFile($sqlFile);
            }

            $zip = new ZipArchive();
            if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Could not create backup archive.');
            }

            if (File::exists($sqlFile)) {
                $zip->addFile($sqlFile, 'database/db.sql');
            }

            if ($type === self::TYPE_FILES || $type === self::TYPE_FULL) {
                $this->addApplicationFiles($zip);
            }

            $zip->addFromString('meta.json', json_encode($this->buildMeta($type, $tables, $rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $zip->close();

            if (! File::exists($absolutePath) || File::size($absolutePath) === 0) {
                throw new RuntimeException('Backup creation failed (empty archive).');
            }
        } finally {
            File::deleteDirectory($tmpDir);
        }

        return $filename;
    }

    /**
     * Store an uploaded backup ZIP and return its stored filename.
     *
     * The uploaded file must be a genuine ZIP containing at least a
     * database dump (database/db.sql) or application files (files/).
     * Uploads are renamed to the standard backup naming scheme based on
     * their detected contents, so they work with download/restore/cleanup.
     *
     * @param \Illuminate\Http\UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function storeUpload($file): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP zip extension is required for backups.');
        }

        if (! $file->isValid()) {
            throw new RuntimeException('Uploaded file is invalid or incomplete.');
        }

        $maxMb = max(0, (int) config('backup.upload_max_size_mb', 200));
        if ($maxMb > 0 && $file->getSize() > $maxMb * 1024 * 1024) {
            throw new RuntimeException("Uploaded file exceeds the {$maxMb} MB limit.");
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension !== 'zip' && strtolower(substr((string) $file->getClientOriginalName(), -4)) !== '.zip') {
            throw new RuntimeException('Only .zip backup files can be uploaded.');
        }

        $tmpPath = $file->getRealPath();
        if ($tmpPath === false || ! is_file($tmpPath)) {
            throw new RuntimeException('Could not read uploaded file.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            throw new RuntimeException('Uploaded file is not a valid ZIP archive.');
        }

        try {
            $hasDatabase = $zip->locateName('database/db.sql') !== false;
            $hasFiles = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (is_string($name) && str_starts_with($name, 'files/') && ! str_ends_with($name, '/') && $name !== 'files/.gitkeep') {
                    $hasFiles = true;
                    break;
                }
            }
        } finally {
            $zip->close();
        }

        if (! $hasDatabase && ! $hasFiles) {
            throw new RuntimeException('This ZIP is not a valid backup (no database dump or application files found).');
        }

        $type = $hasDatabase && $hasFiles ? self::TYPE_FULL : ($hasDatabase ? self::TYPE_DATABASE : self::TYPE_FILES);

        $this->ensureBackupDirectoryExists();

        $date = Carbon::now()->format((string) config('backup.date_format', 'Y-m-d-H-i-s'));
        $prefix = (string) config('backup.filename_prefix', 'backup');
        $filename = "{$prefix}-{$type}-{$date}-uploaded.zip";
        $relativePath = $this->fullPath($filename);

        $counter = 1;
        while ($this->disk()->exists($relativePath)) {
            $filename = "{$prefix}-{$type}-{$date}-uploaded-{$counter}.zip";
            $relativePath = $this->fullPath($filename);
            $counter++;
        }

        // Stream the upload into place (never trust the client filename).
        $stream = fopen($tmpPath, 'r');
        if ($stream === false) {
            throw new RuntimeException('Could not read uploaded file.');
        }
        try {
            if (! $this->disk()->put($relativePath, $stream)) {
                throw new RuntimeException('Could not store uploaded backup.');
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $filename;
    }

    /**
     * Newest-first list of backups with metadata.
     *
     * @return array<int, array{name:string,size:int,size_human:string,modified:int,modified_human:string,type:string,has_database:bool,has_files:bool}>
     */
    public function list(): array
    {
        $this->ensureBackupDirectoryExists();

        $files = $this->disk()->files($this->basePath());
        $prefix = (string) config('backup.filename_prefix', 'backup');

        $backups = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (! preg_match('/^'.preg_quote($prefix, '/').'-(database|files|full)-[\d\-]+(-uploaded(-\d+)?)?\.zip$/', $name, $m)) {
                continue;
            }

            $absolute = $this->disk()->path($file);
            $modified = File::exists($absolute) ? File::lastModified($absolute) : 0;
            $size = File::exists($absolute) ? File::size($absolute) : 0;

            $info = $this->inspectArchive($absolute);

            $backups[] = [
                'name' => $name,
                'size' => $size,
                'size_human' => self::humanSize($size),
                'modified' => $modified,
                'modified_human' => $modified ? Carbon::createFromTimestamp($modified)->diffForHumans() : '—',
                'modified_formatted' => $modified ? Carbon::createFromTimestamp($modified)->format('d M Y, h:i A') : '—',
                'type' => $m[1],
                'has_database' => $info['has_database'],
                'has_files' => $info['has_files'],
            ];
        }

        usort($backups, fn ($a, $b) => $b['modified'] <=> $a['modified']);

        return $backups;
    }

    public function delete(string $filename): void
    {
        $safe = $this->sanitizeFilename($filename);

        if (! $this->disk()->delete($this->fullPath($safe))) {
            throw new RuntimeException('Could not delete backup file.');
        }
    }

    public function stats(): array
    {
        $backups = $this->list();
        $totalSize = array_sum(array_column($backups, 'size'));

        return [
            'count' => count($backups),
            'total_size' => $totalSize,
            'total_size_human' => self::humanSize($totalSize),
            'latest' => $backups[0] ?? null,
            'database_size_human' => $this->databaseSizeHuman(),
        ];
    }

    /**
     * Delete old backups per retention config. Returns deleted filenames.
     *
     * @return string[]
     */
    public function cleanup(): array
    {
        $backups = $this->list(); // newest first
        $deleted = [];

        $keepCount = max(1, (int) config('backup.keep_count', 14));
        $keepDays = max(0, (int) config('backup.keep_days', 30));
        $maxTotalMb = max(0, (int) config('backup.max_total_size_mb', 0));

        // 1) Keep only the N newest.
        foreach (array_slice($backups, $keepCount) as $old) {
            $this->disk()->delete($this->fullPath($old['name']));
            $deleted[] = $old['name'];
        }

        $remaining = array_values(array_filter(
            $this->list(),
            fn ($b) => ! in_array($b['name'], $deleted, true)
        ));

        // 2) Delete anything older than keep_days.
        if ($keepDays > 0) {
            $cutoff = Carbon::now()->subDays($keepDays)->timestamp;
            foreach ($remaining as $key => $backup) {
                if ($backup['modified'] < $cutoff) {
                    $this->disk()->delete($this->fullPath($backup['name']));
                    $deleted[] = $backup['name'];
                    unset($remaining[$key]);
                }
            }
            $remaining = array_values($remaining);
        }

        // 3) Enforce a total-size cap (delete oldest first).
        if ($maxTotalMb > 0) {
            $maxBytes = $maxTotalMb * 1024 * 1024;
            $total = array_sum(array_column($remaining, 'size'));
            // Oldest first:
            usort($remaining, fn ($a, $b) => $a['modified'] <=> $b['modified']);
            foreach ($remaining as $backup) {
                if ($total <= $maxBytes) {
                    break;
                }
                // Always keep at least the single newest backup.
                if (count($remaining) - count($deleted) <= 1 && count($this->list()) <= 1) {
                    break;
                }
                $this->disk()->delete($this->fullPath($backup['name']));
                $deleted[] = $backup['name'];
                $total -= $backup['size'];
            }
        }

        return array_values(array_unique($deleted));
    }

    /**
     * Restore ONLY the database from a backup ZIP.
     * A safety backup of the current DB is created first and its filename returned.
     *
     * @return string filename of the pre-restore safety backup
     */
    public function restoreDatabase(string $filename): string
    {
        $safe = $this->sanitizeFilename($filename);
        $absolute = $this->backupAbsolutePath($safe);

        $tmpDir = sys_get_temp_dir().'/laravel-restore-'.uniqid('', true);
        File::ensureDirectoryExists($tmpDir);

        try {
            $zip = new ZipArchive();
            if ($zip->open($absolute) !== true) {
                throw new RuntimeException('Could not open backup archive.');
            }
            $sqlIndex = $zip->locateName('database/db.sql');
            if ($sqlIndex === false) {
                $zip->close();
                throw new RuntimeException('This backup does not contain a database dump.');
            }
            $sql = $zip->getFromIndex($sqlIndex);
            $zip->close();

            if ($sql === false || trim($sql) === '') {
                throw new RuntimeException('Database dump inside backup is empty.');
            }

            $sqlFile = $tmpDir.'/restore.sql';
            File::put($sqlFile, $sql);

            // Safety net: back up the current database before overwriting it.
            $safetyBackup = $this->create(self::TYPE_DATABASE);

            $this->executeSqlFile($sqlFile);

            return $safetyBackup;
        } finally {
            File::deleteDirectory($tmpDir);
        }
    }

    /**
     * Restore uploaded files (storage/app/public) from a files/full backup.
     * Existing files are NOT deleted; archive files overwrite same paths.
     *
     * @return int number of files restored
     */
    public function restoreFiles(string $filename): int
    {
        $safe = $this->sanitizeFilename($filename);
        $absolute = $this->backupAbsolutePath($safe);
        $restored = 0;

        $zip = new ZipArchive();
        if ($zip->open($absolute) !== true) {
            throw new RuntimeException('Could not open backup archive.');
        }

        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ($entry === false || ! str_starts_with($entry, 'files/')) {
                    continue;
                }
                $relative = substr($entry, strlen('files/'));
                if ($relative === '' || $relative === false) {
                    continue;
                }
                // Skip directory entries; they are recreated implicitly.
                if (str_ends_with($entry, '/')) {
                    continue;
                }
                // Prevent zip-slip.
                if (str_contains($relative, '..')) {
                    continue;
                }

                $contents = $zip->getFromIndex($i);
                if ($contents === false) {
                    continue;
                }

                $target = storage_path('app/public/'.$relative);
                File::ensureDirectoryExists(dirname($target));
                File::put($target, $contents);
                $restored++;
            }
        } finally {
            $zip->close();
        }

        if ($restored === 0) {
            throw new RuntimeException('This backup does not contain application files.');
        }

        return $restored;
    }

    // -----------------------------------------------------------------
    // Database dump
    // -----------------------------------------------------------------

    /**
     * @return array{tables:int, rows:int}
     */
    protected function dumpDatabaseToFile(string $sqlFile): array
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver", 'mysql');

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            try {
                return $this->dumpMysqlWithMysqldump($sqlFile);
            } catch (\Throwable) {
                // Fall through to the pure-PHP dumper.
            }

            return $this->dumpMysqlWithPhp($sqlFile);
        }

        if ($driver === 'sqlite') {
            return $this->dumpSqliteWithPhp($sqlFile);
        }

        throw new RuntimeException("Database driver [{$driver}] is not supported by the backup system.");
    }

    /**
     * @return array{tables:int, rows:int}
     */
    protected function dumpMysqlWithMysqldump(string $sqlFile): array
    {
        $binary = trim((string) config('backup.mysqldump_path', 'mysqldump'));
        if ($binary === '') {
            throw new RuntimeException('mysqldump disabled by configuration.');
        }

        // Resolve binary: allow bare "mysqldump" from PATH.
        $resolved = $binary;
        if (! str_contains($binary, '/')) {
            $which = trim((string) shell_exec('command -v '.escapeshellarg($binary).' 2>/dev/null'));
            if ($which === '') {
                throw new RuntimeException('mysqldump binary not found.');
            }
            $resolved = $which;
        } elseif (! is_executable($resolved)) {
            throw new RuntimeException('mysqldump binary not executable.');
        }

        $connection = config('database.default');
        $cfg = config("database.connections.{$connection}");

        $host = $cfg['host'] ?? '127.0.0.1';
        $port = $cfg['port'] ?? '3306';
        $database = $cfg['database'] ?? '';
        $username = $cfg['username'] ?? '';
        $password = $cfg['password'] ?? '';
        $options = (string) config('backup.mysqldump_options', '--single-transaction --skip-lock-tables --quick');

        if ($database === '') {
            throw new RuntimeException('Database name is not configured.');
        }

        // Password via env var so it never appears in the process list.
        $env = array_merge($_ENV ?? [], [
            'MYSQL_PWD' => (string) $password,
        ]);

        $command = sprintf(
            '%s %s --host=%s --port=%s --user=%s %s > %s 2> %s',
            escapeshellcmd($resolved),
            $options,
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($sqlFile),
            escapeshellarg($sqlFile.'.err')
        );

        $descriptorSpec = [0 => ['file', '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($command, $descriptorSpec, $pipes, null, $env);
        if (! is_resource($process)) {
            throw new RuntimeException('Could not start mysqldump process.');
        }
        foreach ($pipes as $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
            }
        }
        $exitCode = proc_close($process);
        @unlink($sqlFile.'.err');

        if ($exitCode !== 0 || ! File::exists($sqlFile) || File::size($sqlFile) === 0) {
            @unlink($sqlFile);
            throw new RuntimeException('mysqldump failed with exit code '.$exitCode.'.');
        }

        return ['tables' => $this->countMysqlTables(), 'rows' => -1];
    }

    /**
     * @return array{tables:int, rows:int}
     */
    protected function dumpMysqlWithPhp(string $sqlFile): array
    {
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $database = DB::connection()->getDatabaseName();
        $tables = $pdo->query('SHOW FULL TABLES WHERE TABLE_TYPE = \'BASE TABLE\'')->fetchAll(\PDO::FETCH_COLUMN);

        $handle = fopen($sqlFile, 'w');
        if ($handle === false) {
            throw new RuntimeException('Could not write database dump file.');
        }

        $tableCount = 0;
        $rowCount = 0;

        try {
            fwrite($handle, $this->dumpHeader('mysql', $database));

            foreach ($tables as $table) {
                $tableCount++;
                $quoted = '`'.str_replace('`', '``', $table).'`';

                $create = $pdo->query("SHOW CREATE TABLE {$quoted}")->fetch(\PDO::FETCH_ASSOC);
                $createSql = $create['Create Table'] ?? null;
                if ($createSql) {
                    fwrite($handle, "\nDROP TABLE IF EXISTS {$quoted};\n{$createSql};\n");
                }

                $count = (int) $pdo->query("SELECT COUNT(*) FROM {$quoted}")->fetchColumn();
                if ($count === 0) {
                    continue;
                }

                $columns = $pdo->query("SHOW COLUMNS FROM {$quoted}")->fetchAll(\PDO::FETCH_COLUMN);
                $columnList = implode(',', array_map(fn ($c) => '`'.str_replace('`', '``', $c).'`', $columns));

                $offset = 0;
                $chunk = 500;
                while (true) {
                    $stmt = $pdo->query("SELECT * FROM {$quoted} LIMIT {$chunk} OFFSET {$offset}");
                    $rows = $stmt->fetchAll(\PDO::FETCH_NUM);
                    if (empty($rows)) {
                        break;
                    }
                    $values = [];
                    foreach ($rows as $row) {
                        $rowCount++;
                        $escaped = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), $row);
                        $values[] = '('.implode(',', $escaped).')';
                    }
                    fwrite($handle, "INSERT INTO {$quoted} ({$columnList}) VALUES\n".implode(",\n", $values).";\n");
                    $offset += $chunk;
                    if (count($rows) < $chunk) {
                        break;
                    }
                }
            }

            fwrite($handle, "\n-- Dump completed on ".Carbon::now()->toDateTimeString()."\n");
        } finally {
            fclose($handle);
        }

        return ['tables' => $tableCount, 'rows' => $rowCount];
    }

    /**
     * @return array{tables:int, rows:int}
     */
    protected function dumpSqliteWithPhp(string $sqlFile): array
    {
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $database = DB::connection()->getDatabaseName();
        $tables = $pdo->query(
            "SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $handle = fopen($sqlFile, 'w');
        if ($handle === false) {
            throw new RuntimeException('Could not write database dump file.');
        }

        $tableCount = 0;
        $rowCount = 0;

        try {
            fwrite($handle, $this->dumpHeader('sqlite', (string) $database));
            fwrite($handle, "PRAGMA foreign_keys=OFF;\n");

            foreach ($tables as $table) {
                $name = $table['name'];
                $createSql = $table['sql'] ?? null;
                if (! $createSql) {
                    continue;
                }
                $tableCount++;
                $quoted = '"'.str_replace('"', '""', $name).'"';

                fwrite($handle, "\nDROP TABLE IF EXISTS {$quoted};\n{$createSql};\n");

                $count = (int) $pdo->query("SELECT COUNT(*) FROM {$quoted}")->fetchColumn();
                if ($count === 0) {
                    continue;
                }

                $offset = 0;
                $chunk = 500;
                while (true) {
                    $stmt = $pdo->query("SELECT * FROM {$quoted} LIMIT {$chunk} OFFSET {$offset}");
                    $rows = $stmt->fetchAll(\PDO::FETCH_NUM);
                    if (empty($rows)) {
                        break;
                    }
                    foreach ($rows as $row) {
                        $rowCount++;
                        $escaped = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), $row);
                        fwrite($handle, "INSERT INTO {$quoted} VALUES (".implode(',', $escaped).");\n");
                    }
                    $offset += $chunk;
                    if (count($rows) < $chunk) {
                        break;
                    }
                }
            }
        } finally {
            fclose($handle);
        }

        return ['tables' => $tableCount, 'rows' => $rowCount];
    }

    protected function dumpHeader(string $driver, string $database): string
    {
        return '-- Laravel inventory backup'."\n"
            .'-- App: '.config('app.name')."\n"
            .'-- Driver: '.$driver."\n"
            .'-- Database: '.$database."\n"
            .'-- Created: '.Carbon::now()->toDateTimeString()."\n"
            .'-- --------------------------------------------------------'."\n\n"
            .($driver === 'mysql' ? "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n" : '');
    }

    protected function executeSqlFile(string $sqlFile): void
    {
        $sql = File::get($sqlFile);
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver", 'mysql');

        $statements = $this->splitSqlStatements($sql);
        // MySQL/MariaDB implicitly commit on DDL (DROP/CREATE/ALTER) and on
        // LOCK/UNLOCK TABLES, so a wrapping transaction is useless there and
        // even breaks the restore ("no active transaction"). Only SQLite
        // (fully transactional DDL) gets a wrapping transaction.
        $useTransaction = $driver === 'sqlite';

        $pdo = DB::connection()->getPdo();

        if ($useTransaction) {
            DB::connection()->beginTransaction();
        }

        try {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            } elseif ($driver === 'sqlite') {
                $pdo->exec('PRAGMA foreign_keys=OFF');
            }

            foreach ($statements as $statement) {
                if (! $this->statementHasExecutableSql($statement)) {
                    continue;
                }
                $pdo->exec($statement);
            }

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            } elseif ($driver === 'sqlite') {
                $pdo->exec('PRAGMA foreign_keys=ON');
            }

            if ($useTransaction) {
                DB::connection()->commit();
            }
        } catch (\Throwable $e) {
            if ($useTransaction) {
                try {
                    DB::connection()->rollBack();
                } catch (\Throwable) {
                    // Ignore rollback errors; report the original failure.
                }
            } else {
                try {
                    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
                } catch (\Throwable) {
                    // Best effort.
                }
            }
            throw new RuntimeException('Database restore failed: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Check whether a (possibly comment-prefixed) statement contains
     * executable SQL. mysqldump interleaves dash-dash and conditional
     * comments with real statements in the same chunk, so a naive
     * "starts with dashes" check would wrongly skip DROPs and CREATEs.
     */
    protected function statementHasExecutableSql(string $statement): bool
    {
        $remaining = trim($statement);
        if ($remaining === '') {
            return false;
        }

        // Iteratively strip leading line comments and block comments.
        while (true) {
            $before = $remaining;

            // Leading -- comment to end of line.
            $remaining = (string) preg_replace('/^(--[^\n]*\n\s*)+/', '', $remaining);
            // Leading # comment to end of line.
            $remaining = (string) preg_replace('/^(#[^\n]*\n\s*)+/', '', $remaining);
            // Leading /* ... */ blocks (including /*! MySQL conditional comments
            // that contain only SET statements handled separately below).
            while (preg_match('/^\/\*.*?\*\/\s*;?\s*/s', $remaining, $m)) {
                $block = $m[0];
                // MySQL conditional comments like /*!40101 SET ... */ ARE
                // executable — keep the statement if the block holds SET etc.
                $inner = preg_replace('/^\/\*!\d*\s*|\s*\*\/$/', '', trim($block));
                if ($inner !== '' && rtrim($inner) !== ';' && preg_match('/\b(SET|CREATE|ALTER|INSERT|UPDATE|DELETE|DROP|LOCK|UNLOCK)\b/i', $inner)) {
                    return true;
                }
                $remaining = ltrim(substr($remaining, strlen($block)));
            }

            $remaining = ltrim($remaining);
            if ($remaining === '' || $remaining === $before) {
                break;
            }
        }

        return trim($remaining, " \t\n\r;") !== '';
    }

    /**
     * Split a SQL dump into individual statements, respecting quoted strings.
     *
     * @return string[]
     */
    protected function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inSingle = false;
        $inDouble = false;
        $inBacktick = false;
        $inLineComment = false;
        $inBlockComment = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $len ? $sql[$i + 1] : '';

            // Line comments: --<space> and # (mysql style)
            if (! $inSingle && ! $inDouble && ! $inBacktick && ! $inBlockComment) {
                if (! $inLineComment && $char === '-' && $next === '-' && isset($sql[$i + 2]) && ctype_space($sql[$i + 2])) {
                    $inLineComment = true;
                } elseif (! $inLineComment && $char === '#') {
                    $inLineComment = true;
                } elseif ($char === "\n") {
                    $inLineComment = false;
                }
            }

            // Block comments
            if (! $inSingle && ! $inDouble && ! $inBacktick && ! $inLineComment) {
                if (! $inBlockComment && $char === '/' && $next === '*') {
                    $inBlockComment = true;
                } elseif ($inBlockComment && $char === '*' && $next === '/') {
                    $inBlockComment = false;
                    $current .= '*/';
                    $i++;
                    continue;
                }
            }

            if ($inLineComment || $inBlockComment) {
                $current .= $char;
                continue;
            }

            if ($char === "'" && ! $inDouble && ! $inBacktick) {
                // Handle escaped '' inside string.
                if ($inSingle && $next === "'") {
                    $current .= "''";
                    $i++;
                    continue;
                }
                // Backslash escapes
                $backslashes = 0;
                for ($j = $i - 1; $j >= 0 && $sql[$j] === '\\'; $j--) {
                    $backslashes++;
                }
                if ($backslashes % 2 === 0) {
                    $inSingle = ! $inSingle;
                }
            } elseif ($char === '"' && ! $inSingle && ! $inBacktick) {
                $backslashes = 0;
                for ($j = $i - 1; $j >= 0 && $sql[$j] === '\\'; $j--) {
                    $backslashes++;
                }
                if ($backslashes % 2 === 0) {
                    $inDouble = ! $inDouble;
                }
            } elseif ($char === '`' && ! $inSingle && ! $inDouble) {
                $inBacktick = ! $inBacktick;
            }

            if ($char === ';' && ! $inSingle && ! $inDouble && ! $inBacktick) {
                $statements[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (trim($current) !== '') {
            $statements[] = $current;
        }

        return $statements;
    }

    // -----------------------------------------------------------------
    // Files
    // -----------------------------------------------------------------

    protected function addApplicationFiles(ZipArchive $zip): void
    {
        $excluded = (array) config('backup.exclude_basenames', []);
        $added = false;

        foreach ((array) config('backup.include_files', []) as $path) {
            if (! File::exists($path)) {
                continue;
            }
            $added = true;
            $base = basename((string) $path);

            if (File::isFile($path)) {
                $zip->addFile($path, 'files/'.$base);
                continue;
            }

            $this->addDirectoryToZip($zip, (string) $path, 'files/'.$base, $excluded);
        }

        if (! $added) {
            $zip->addFromString('files/.gitkeep', '');
        }
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPrefix, array $excluded): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (in_array($file->getBasename(), $excluded, true)) {
                continue;
            }
            $realPath = $file->getRealPath();
            if ($realPath === false) {
                continue;
            }
            $relative = ltrim(str_replace($dir, '', $realPath), '/\\');
            $relative = str_replace('\\', '/', $relative);
            if ($relative === '') {
                continue;
            }
            if ($file->isDir()) {
                $zip->addEmptyDir($zipPrefix.'/'.$relative);
            } else {
                $zip->addFile($realPath, $zipPrefix.'/'.$relative);
            }
        }
    }

    protected function inspectArchive(string $absolute): array
    {
        $result = ['has_database' => false, 'has_files' => false];

        $zip = new ZipArchive();
        if ($zip->open($absolute) !== true) {
            return $result;
        }

        $result['has_database'] = $zip->locateName('database/db.sql') !== false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (is_string($name) && str_starts_with($name, 'files/') && ! str_ends_with($name, '/') && $name !== 'files/.gitkeep') {
                $result['has_files'] = true;
                break;
            }
        }

        $zip->close();

        return $result;
    }

    protected function buildMeta(string $type, int $tables, int $rows): array
    {
        $connection = config('database.default');

        return [
            'app' => config('app.name'),
            'url' => config('app.url'),
            'type' => $type,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_at_timezone' => config('app.timezone'),
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'db_connection' => $connection,
            'db_driver' => config("database.connections.{$connection}.driver"),
            'db_database' => config("database.connections.{$connection}.database"),
            'tables' => $tables,
            'rows' => $rows,
        ];
    }

    protected function countMysqlTables(): int
    {
        try {
            return count(DB::connection()->select('SHOW FULL TABLES WHERE TABLE_TYPE = \'BASE TABLE\''));
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function ensureBackupDirectoryExists(): void
    {
        $path = $this->disk()->path($this->basePath() === '' ? '.' : $this->basePath());
        File::ensureDirectoryExists($path);

        // Prevent web access if someone points the disk inside public/.
        $sentinel = rtrim($path, '/').'/.htaccess';
        if (! File::exists($sentinel)) {
            @File::put($sentinel, "Deny from all\n");
        }
    }

    protected function databaseSizeHuman(): string
    {
        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");

            if ($driver === 'sqlite') {
                $file = config("database.connections.{$connection}.database");
                if (is_string($file) && File::exists($file)) {
                    return self::humanSize(File::size($file));
                }

                return '—';
            }

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $database = DB::connection()->getDatabaseName();
                $row = DB::selectOne(
                    'SELECT ROUND(SUM(data_length + index_length)) AS bytes FROM information_schema.tables WHERE table_schema = ?',
                    [$database]
                );

                if ($row && isset($row->bytes) && $row->bytes) {
                    return self::humanSize((int) $row->bytes);
                }
            }
        } catch (\Throwable) {
            // Size display must never break the page.
        }

        return '—';
    }

    public static function humanSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
