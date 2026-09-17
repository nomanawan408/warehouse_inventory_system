<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Disk & Path
    |--------------------------------------------------------------------------
    |
    | The disk defined in `config/filesystems.php` where backups are stored,
    | and the directory inside that disk. Keeping backups on a dedicated
    | private disk keeps them out of the public web root.
    |
    */

    'disk' => env('BACKUP_DISK', 'backups'),
    'path' => env('BACKUP_PATH', ''), // subdirectory inside the disk, empty = disk root

    /*
    |--------------------------------------------------------------------------
    | Backup filename
    |--------------------------------------------------------------------------
    |
    | {type}  = database | files | full
    | {date}  = Y-m-d-H-i-s
    |
    */

    'filename_prefix' => env('BACKUP_FILENAME_PREFIX', 'backup'),
    'date_format' => 'Y-m-d-H-i-s',

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | How many backups to keep / how old they may be before the
    | `backup:clean` command deletes them. Both rules apply: a backup is
    | deleted when it exceeds EITHER limit.
    |
    */

    'keep_count' => (int) env('BACKUP_KEEP_COUNT', 14),
    'keep_days' => (int) env('BACKUP_KEEP_DAYS', 30),
    'max_total_size_mb' => (int) env('BACKUP_MAX_TOTAL_SIZE_MB', 1024), // 0 = unlimited

    /*
    |--------------------------------------------------------------------------
    | What to include in "files" / "full" backups
    |--------------------------------------------------------------------------
    |
    | Absolute paths included in the `files/` folder of the zip.
    | storage/app/public holds user uploads; add more dirs as needed.
    |
    */

    'include_files' => [
        storage_path('app/public'),
    ],

    // File/directory basenames to skip while zipping (e.g. caches, thumbnails).
    'exclude_basenames' => ['.DS_Store', 'Thumbs.db'],

    /*
    |--------------------------------------------------------------------------
    | mysqldump
    |--------------------------------------------------------------------------
    |
    | If a mysqldump binary is available it is used for MySQL/MariaDB because
    | it is faster and more faithful than a PHP dumper. Otherwise the built-in
    | pure-PHP dumper is used automatically. Set BACKUP_MYSQLDUMP_PATH to
    | empty to force the PHP dumper.
    |
    */

    'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH', 'mysqldump'),

    // Extra mysqldump flags (single string, appended as-is).
    'mysqldump_options' => env('BACKUP_MYSQLDUMP_OPTIONS', '--single-transaction --skip-lock-tables --quick'),

    /*
    |--------------------------------------------------------------------------
    | Schedule defaults (used when scheduling in routes/console.php)
    |--------------------------------------------------------------------------
    */

    'schedule_daily_at' => env('BACKUP_SCHEDULE_DAILY_AT', '02:00'),
    'schedule_type' => env('BACKUP_SCHEDULE_TYPE', 'database'), // database | files | full

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Optional role gate for the backup UI + download/restore actions.
    | Leave empty to allow any authenticated user (current app behaviour for
    | all other sections). Example: ['superadmin', 'admin'] requires the
    | spatie/laravel-permission `role` middleware.
    |
    */

    'allowed_roles' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('BACKUP_ALLOWED_ROLES', ''))
    ))),
];
