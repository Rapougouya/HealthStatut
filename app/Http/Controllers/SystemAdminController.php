<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SystemAdminController extends Controller
{
    /**
     * Affiche la page de configuration du serveur
     */
    public function serverConfig()
    {
        return view('admin.system.server-config');
    }

    /**
     * Affiche la page des logs système
     */
    public function systemLogs(Request $request)
    {
        $logType = $request->get('type', 'laravel');
        $logs = $this->getSystemLogs($logType);
        
        return view('admin.system.logs', compact('logs', 'logType'));
    }

    /**
     * Affiche la page de maintenance système
     */
    public function systemMaintenance()
    {
        $systemStatus = $this->getSystemStatus();
        
        return view('admin.system.maintenance', compact('systemStatus'));
    }

    /**
     * Affiche la page d'export des données
     */
    public function exportData()
    {
        $tables = $this->getDatabaseTables();
        
        return view('admin.system.export-data', compact('tables'));
    }

    /**
     * Sauvegarde la configuration du serveur
     */
    public function saveServerConfig(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer',
            'db_name' => 'required|string',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|email',
            'memory_limit' => 'required|integer',
            'max_execution_time' => 'required|integer',
        ]);

        return redirect()->route('admin.system.server-config')->with('success', 'Configuration sauvegardée avec succès.');
    }

    /**
     * Effectue la maintenance système
     */
    public function performMaintenance(Request $request)
    {
        $action = $request->get('action');
        
        try {
            switch ($action) {
                case 'clear_cache':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');
                    break;
                
                case 'optimize':
                    Artisan::call('optimize');
                    break;
                
                case 'backup_db':
                    $this->backupDatabase();
                    break;
                
                case 'clean_logs':
                    $this->cleanOldLogs();
                    break;
            }
            
            return response()->json(['success' => true, 'message' => 'Maintenance effectuée avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la maintenance: ' . $e->getMessage()]);
        }
    }

    /**
     * Exporte les données
     */
    public function performExport(Request $request)
    {
        $request->validate([
            'tables' => 'required|array',
            'format' => 'required|in:csv,json,sql',
        ]);

        try {
            $exportPath = $this->exportTables($request->tables, $request->format);
            
            return response()->download($exportPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('admin.system.export-data')->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    // Private helper methods
    private function getSystemLogs($type)
    {
        $logs = [];
        
        try {
            switch ($type) {
                case 'laravel':
                    $logPath = storage_path('logs/laravel.log');
                    break;
                case 'apache':
                    $logPath = '/var/log/apache2/access.log';
                    break;
                case 'nginx':
                    $logPath = '/var/log/nginx/access.log';
                    break;
                case 'mysql':
                    $logPath = '/var/log/mysql/error.log';
                    break;
                default:
                    $logPath = storage_path('logs/laravel.log');
            }
            
            if (File::exists($logPath)) {
                $content = File::get($logPath);
                $lines = array_slice(array_reverse(explode("\n", $content)), 0, 100);
                
                foreach ($lines as $line) {
                    if (!empty(trim($line))) {
                        $logs[] = [
                            'timestamp' => now(),
                            'level' => 'info',
                            'message' => $line
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $logs[] = [
                'timestamp' => now(),
                'level' => 'error',
                'message' => 'Impossible de lire les logs: ' . $e->getMessage()
            ];
        }
        
        return $logs;
    }

    private function getSystemStatus()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'disk_usage' => disk_total_space('.') - disk_free_space('.'),
            'disk_total' => disk_total_space('.'),
            'uptime' => $this->getSystemUptime(),
            'database_status' => $this->getDatabaseStatus(),
        ];
    }

    private function getDatabaseTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableNames[] = array_values($tableArray)[0];
            }
            
            return $tableNames;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function backupDatabase()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }
        
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );
        
        exec($command);
        
        return $path;
    }

    private function cleanOldLogs()
    {
        $logPath = storage_path('logs');
        $files = File::glob($logPath . '/*.log');
        
        foreach ($files as $file) {
            if (File::lastModified($file) < strtotime('-30 days')) {
                File::delete($file);
            }
        }
    }

    private function exportTables($tables, $format)
    {
        $filename = 'export_' . date('Y-m-d_H-i-s') . '.' . $format;
        $path = storage_path('app/exports/' . $filename);
        
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }
        
        switch ($format) {
            case 'csv':
                $this->exportToCsv($tables, $path);
                break;
            case 'json':
                $this->exportToJson($tables, $path);
                break;
            case 'sql':
                $this->exportToSql($tables, $path);
                break;
        }
        
        return $path;
    }

    private function exportToCsv($tables, $path)
    {
        $handle = fopen($path, 'w');
        
        foreach ($tables as $table) {
            $data = DB::table($table)->get()->toArray();
            if (!empty($data)) {
                fputcsv($handle, array_keys((array) $data[0]));
                foreach ($data as $row) {
                    fputcsv($handle, (array) $row);
                }
            }
        }
        
        fclose($handle);
    }

    private function exportToJson($tables, $path)
    {
        $data = [];
        
        foreach ($tables as $table) {
            $data[$table] = DB::table($table)->get();
        }
        
        File::put($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function exportToSql($tables, $path)
    {
        $sql = '';
        
        foreach ($tables as $table) {
            $sql .= "-- Table: $table\n";
            $data = DB::table($table)->get();
            
            foreach ($data as $row) {
                $columns = implode(',', array_keys((array) $row));
                $values = implode(',', array_map(function($v) {
                    return "'" . addslashes($v) . "'";
                }, array_values((array) $row)));
                
                $sql .= "INSERT INTO $table ($columns) VALUES ($values);\n";
            }
            $sql .= "\n";
        }
        
        File::put($path, $sql);
    }

    private function getSystemUptime()
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $uptime = file_get_contents('/proc/uptime');
                $seconds = (int) explode(' ', $uptime)[0];
                return gmdate("H:i:s", $seconds);
            }
            return 'Non disponible';
        } catch (\Exception $e) {
            return 'Non disponible';
        }
    }

    private function getDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return 'Connecté';
        } catch (\Exception $e) {
            return 'Déconnecté';
        }
    }
}