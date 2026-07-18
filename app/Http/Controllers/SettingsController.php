<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman settings
     */
    public function index()
    {
        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $files = scandir($backupPath);
        $backups = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'name' => $file,
                    'size' => round(filesize($backupPath . '/' . $file) / 1024, 2) . ' KB',
                    'date' => date('Y-m-d H:i:s', filemtime($backupPath . '/' . $file)),
                ];
            }
        }

        return view('settings.index', compact('backups'));
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string',
        ]);

        // Update .env file
        $this->updateEnv([
            'APP_NAME' => $request->app_name,
            'APP_TIMEZONE' => $request->app_timezone,
            'APP_LOCALE' => $request->app_locale,
        ]);

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Update API settings
     */
    public function updateApi(Request $request)
    {
        $request->validate([
            'openweather_api_key' => 'nullable|string',
            'exchange_rate_api_key' => 'nullable|string',
            'gnews_api_key' => 'nullable|string',
        ]);

        // Update .env file
        $this->updateEnv([
            'OPENWEATHER_API_KEY' => $request->openweather_api_key,
            'EXCHANGE_RATE_API_KEY' => $request->exchange_rate_api_key,
            'GNEWS_API_KEY' => $request->gnews_api_key,
        ]);

        return back()->with('success', 'API settings updated successfully.');
    }

    /**
     * Update theme settings
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:dark,light',
            'primary_color' => 'required|string',
        ]);

        // Store theme preferences in session or database
        session([
            'theme' => $request->theme,
            'primary_color' => $request->primary_color,
        ]);

        return back()->with('success', 'Theme settings updated successfully.');
    }

    /**
     * Backup database
     */
    public function backup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $outputPath = $backupDir . '/' . $filename;

            // XAMPP absolute path check
            $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump';
            if (!file_exists($mysqldumpPath . '.exe')) {
                $mysqldumpPath = 'mysqldump';
            }

            $password = config('database.connections.mysql.password');
            $passOption = !empty($password) ? '-p' . escapeshellarg($password) : '';

            $command = sprintf(
                '"%s" -u%s %s %s > "%s"',
                $mysqldumpPath,
                config('database.connections.mysql.username'),
                $passOption,
                config('database.connections.mysql.database'),
                $outputPath
            );

            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                throw new \Exception("Backup failed with code {$resultCode}. Command: {$command}");
            }

            Log::info('Database backup created: ' . $filename);
            \App\Models\ActivityLog::log('Backup', 'Database backup created: ' . $filename);

            return back()->with('success', 'Database backup created successfully: ' . $filename);
        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
            return back()->with('error', 'Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore database from backup
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        try {
            $backupPath = storage_path('app/backups/' . $request->backup_file);
            
            if (!file_exists($backupPath)) {
                return back()->with('error', 'Backup file not found.');
            }

            // XAMPP absolute path check
            $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql';
            if (!file_exists($mysqlPath . '.exe')) {
                $mysqlPath = 'mysql';
            }

            $password = config('database.connections.mysql.password');
            $passOption = !empty($password) ? '-p' . escapeshellarg($password) : '';

            $command = sprintf(
                '"%s" -u%s %s %s < "%s"',
                $mysqlPath,
                config('database.connections.mysql.username'),
                $passOption,
                config('database.connections.mysql.database'),
                $backupPath
            );

            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                throw new \Exception("Restore failed with code {$resultCode}. Command: {$command}");
            }

            Log::info('Database restored from: ' . $request->backup_file);
            \App\Models\ActivityLog::log('Restore', 'Database restored from: ' . $request->backup_file);

            return back()->with('success', 'Database restored successfully from: ' . $request->backup_file);
        } catch (\Exception $e) {
            Log::error('Database restore failed: ' . $e->getMessage());
            return back()->with('error', 'Database restore failed: ' . $e->getMessage());
        }
    }

    /**
     * View system logs
     */
    public function logs(Request $request)
    {
        $logType = $request->log_type ?? 'laravel';
        $lines = $request->lines ?? 100;
        
        $logFile = match($logType) {
            'laravel' => storage_path('logs/laravel.log'),
            'error' => storage_path('logs/error.log'),
            default => storage_path('logs/laravel.log'),
        };

        if (!file_exists($logFile)) {
            return view('settings.logs', [
                'logs' => 'Log file not found.',
                'logType' => $logType,
            ]);
        }

        $logs = $this->tailFile($logFile, $lines);

        return view('settings.logs', [
            'logs' => $logs,
            'logType' => $logType,
        ]);
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return back()->with('success', 'Application cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Get list of backup files
     */
    public function getBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $files = scandir($backupPath);
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'name' => $file,
                    'size' => filesize($backupPath . '/' . $file),
                    'date' => date('Y-m-d H:i:s', filemtime($backupPath . '/' . $file)),
                ];
            }
        }

        return response()->json($backups);
    }

    /**
     * Helper function to update .env file
     */
    private function updateEnv(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            $envContent = preg_replace($pattern, $replacement, $envContent);
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Helper function to read last N lines of a file
     */
    private function tailFile($file, $lines)
    {
        $fileContent = file_get_contents($file);
        $fileArray = explode("\n", $fileContent);
        $fileArray = array_slice($fileArray, -$lines);
        
        return implode("\n", $fileArray);
    }
}
