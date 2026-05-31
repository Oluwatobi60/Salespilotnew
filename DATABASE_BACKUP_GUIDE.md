# Database Backup System

## Overview
The application includes a custom database backup system that creates SQL backups of your MySQL database. The system intelligently uses two methods:

1. **mysqldump** (if available) - Faster and more reliable for large databases
2. **Laravel Database Export** (automatic fallback) - Pure PHP implementation that works everywhere without external dependencies

Backups are stored locally in `storage/app/backups/` and can be triggered manually from the Superadmin settings panel or via command line. The system automatically detects which method to use based on your environment.

## Features
- ✅ Automatic method detection (mysqldump or Laravel export)
- ✅ Works on Windows without external tools
- ✅ One-click manual database backup via web interface
- ✅ Automatic cleanup (keeps last 10 backups)
- ✅ Timestamped backup files
- ✅ File size reporting
- ✅ Stored in `storage/app/backups/`
- ✅ Optional scheduled backups

## Manual Backup

### Via Web Interface
1. Login as **Superadmin**
2. Navigate to **Settings**
3. Click the **"Backup Database"** button
4. Wait for confirmation message
5. Backup file will be saved to `storage/app/backups/`

### Via Command Line
```bash
php artisan backup:database
```

With custom filename:
```bash
php artisan backup:database --filename=my-backup.sql
```

## Backup Location
All backups are stored in:
```
storage/app/backups/backup-YYYY-MM-DD-HHMMSS.sql
```

Example:
```
storage/app/backups/backup-2026-05-20-143022.sql
```

## Automatic Cleanup
The system automatically keeps only the **10 most recent backups** and deletes older ones to save disk space.

## Requirements
- MySQL/MariaDB database
- Write permissions to `storage/app/backups/` directory
- Optional: `mysqldump` utility (system automatically falls back to Laravel export if not available)

## Restoring a Backup

### Method 1: Command Line
```bash
mysql -u username -p database_name < storage/app/backups/backup-2026-05-20-143022.sql
```

### Method 2: phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Go to "Import" tab
4. Choose the backup file
5. Click "Go"

### Method 3: Laravel Tinker
```bash
php artisan tinker
```

```php
DB::unprepared(file_get_contents('storage/app/backups/backup-2026-05-20-143022.sql'));
```

## Scheduling Automatic Backups

To schedule automatic backups, add this to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Daily backup at 2 AM
    $schedule->command('backup:database')->daily()->at('02:00');
    
    // Or weekly backup every Sunday
    // $schedule->command('backup:database')->weekly()->sundays()->at('03:00');
}
```

Then ensure your cron is configured:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Backup File Format
Backups are created as standard SQL dump files compatible with:
- MySQL
- MariaDB
- phpMyAdmin
- MySQL Workbench
- Other MySQL-compatible tools

## Troubleshooting

### "mysqldump: command not found" or "mysqldump is not recognized"
**No Action Required**: The system automatically detects this and falls back to Laravel's database export method. You'll see this message:
```
mysqldump not available, using Laravel database export...
```

If you prefer to use mysqldump for better performance:

**Ubuntu/Debian**:
```bash
sudo apt-get install mysql-client
```

**Windows**:
- mysqldump should be in MySQL installation directory
- Add to PATH: `C:\Program Files\MySQL\MySQL Server 8.0\bin\`

### Backup Methods Explained
1. **mysqldump Method**: 
   - Faster, more efficient
   - Uses native MySQL utility
   - Better for large databases
   - Requires mysqldump to be installed

2. **Laravel Export Method** (Fallback):
   - Pure PHP implementation
   - Works everywhere without extra tools
   - Slightly slower for very large databases
   - Automatically used when mysqldump is unavailable

### Permission Denied
**Solution**: Ensure the backup directory is writable:
```bash
chmod -R 775 storage/app/backups
chown -R www-data:www-data storage/app/backups
```

### Backup File is Empty
**Possible causes**:
- Incorrect database credentials
- Database connection issues
- Insufficient disk space

**Check logs**: `storage/logs/laravel.log`

### Large Database
For very large databases (>1GB), consider:
1. Using compression:
   ```bash
   php artisan backup:database | gzip > backup.sql.gz
   ```
2. Increasing PHP memory limit in `php.ini`
3. Using incremental backups

## Security Notes
⚠️ **Important Security Considerations**:

1. **Protect Backup Files**: Backup files contain your entire database including sensitive data
2. **Never Commit Backups**: Add `storage/app/backups/` to `.gitignore`
3. **Secure Storage**: Store backups in a secure location
4. **Encrypt Backups**: Consider encrypting backups for production:
   ```bash
   openssl enc -aes-256-cbc -salt -in backup.sql -out backup.sql.enc
   ```
5. **Off-site Backups**: For production, store backups off-site (AWS S3, DigitalOcean Spaces, etc.)

## Monitoring Backup Status
Check backup status and history:
```bash
ls -lh storage/app/backups/
```

View backup sizes:
```bash
du -sh storage/app/backups/*
```

## Advanced: Download Backup via Web Interface
If you need to download backups via web interface, add this route and method:

**Route** (`routes/web.php`):
```php
Route::get('/download-backup/{filename}', [SettingsController::class, 'downloadBackup'])
    ->name('superadmin.settings.download-backup');
```

**Controller** (`SettingsController.php`):
```php
public function downloadBackup($filename)
{
    $path = storage_path('app/backups/' . $filename);
    
    if (!File::exists($path)) {
        abort(404, 'Backup file not found');
    }
    
    return response()->download($path);
}
```

## Backup Command Options
```bash
# Basic backup
php artisan backup:database

# Custom filename
php artisan backup:database --filename=pre-migration-backup.sql

# View help
php artisan backup:database --help
```

## Related Files
- Command: `app/Console/Commands/DatabaseBackup.php`
- Controller: `app/Http/Controllers/Superadmin/SettingsController.php`
- Route: `routes/web.php` (superadmin.settings.run-backup)
- View: `resources/views/superadmin/settings/index.blade.php`

## Support
For issues with database backups, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Web server logs
3. Database connection settings in `.env`
4. Disk space availability
