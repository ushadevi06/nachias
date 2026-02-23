<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Backup;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view backup-restore')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $backups = Backup::with('creator')->orderBy('id', 'desc')->get();
            $data = [];
            $i = 1;
            foreach ($backups as $row) {
                $statusBadge = match($row->status) {
                    'Success' => '<span class="badge bg-success">Success</span>',
                    'Failed' => '<span class="badge bg-danger">Failed</span>',
                    'Running' => '<span class="badge bg-info"><i class="ri-loader-4-line animation-spin"></i> Running</span>',
                    default => '<span class="badge bg-warning">Pending</span>',
                };

                $action = '<div class="button-box">';
                if ($row->status == 'Success' && file_exists(public_path('uploads/backup/' . $row->filename))) {
                    $action .= '<a href="' . url('backup_restore/download/' . $row->id) . '" class="btn btn-view" title="Download"><i class="icon-base ri ri-download-line"></i></a>';
                    $action .= '<a href="javascript:void(0)" onclick="confirmRestore(' . $row->id . ')" class="btn btn-view" title="Restore"><i class="icon-base ri ri-refresh-line"></i></a>';
                }
                $action .= '<a href="javascript:void(0)" onclick="delete_data(\''.url('backup_restore/delete/'.$row->id).'\')" class="btn btn-delete" title="Delete"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'backup_no' => $row->backup_no,
                    'backup_type' => $row->backup_type,
                    'created_at' => $row->created_at->format('d-m-Y H:i A'),
                    'location' => $row->location,
                    'file_size' => $row->file_size ?? '-',
                    'created_by' => $row->creator->name ?? 'System',
                    'status' => $statusBadge,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }
        return view('backups/view');
    }

    public function generate()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view backup-restore')) {
            return unauthorizedRedirect();
        }
        try {
            if (Backup::where('status', 'Running')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'A backup process is already running.']);
            }

            $backupNo = 'BK-' . date('YmdHis');
            $filename = $backupNo . '.sql';
            $path = public_path('uploads/backup/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $backup = Backup::create([
                'backup_no' => $backupNo,
                'filename' => $filename,
                'backup_type' => 'Database Only',
                'status' => 'Running',
                'location' => 'Local',
                'created_by' => auth()->id(),
            ]);

            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');

            $dumpPath = env('MYSQLDUMP_PATH');
            $command = "\"{$dumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$path}{$filename}\" 2>&1";
            
            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                $size = filesize($path . $filename);
                $backup->update([
                    'status' => 'Success',
                    'file_size' => $this->formatSize($size),
                ]);
                return response()->json(['status' => 'success', 'message' => 'Backup generated successfully.']);
            } else {
                $errorMessage = implode("\n", $output);
                Log::error("Backup failed. Return var: $returnVar. Output: " . $errorMessage);
                
                $backup->update([
                    'status' => 'Failed',
                    'error_message' => 'mysqldump failed. Code: ' . $returnVar . '. Error: ' . $errorMessage,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Backup generation failed. Code: ' . $returnVar . ' Details: ' . $errorMessage]);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function restore(Request $request)
    {
        if (auth()->id() != 1) {
            return unauthorizedRedirect();
        }
        $request->validate([
            'id' => 'required|exists:backups,id',
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid password.']);
        }

        if (auth()->id() != 1) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.']);
        }

        $backup = Backup::find($request->id);
        $path = public_path('uploads/backup/' . $backup->filename);

        if (!file_exists($path)) {
            return response()->json(['status' => 'error', 'message' => 'Backup file not found.']);
        }

        try {
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $mysqlPath = env('MYSQL_PATH', 'C:\xampp\mysql\bin\mysql.exe');
            $command = "\"{$mysqlPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} < \"{$path}\"";
            exec($command, $output, $returnVar);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            if ($returnVar === 0) {
                return response()->json(['status' => 'success', 'message' => 'Database restored successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Restore failed with return code ' . $returnVar]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function download($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view backup-restore')) {
            return unauthorizedRedirect();
        }
        $backup = Backup::findOrFail($id);
        $path = public_path('uploads/backup/' . $backup->filename);
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        return redirect()->back()->with('error', 'File not found.');
    }

    public function delete($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view backup-restore')) {
            return unauthorizedRedirect();
        }
        $backup = Backup::findOrFail($id);
        $path = public_path('uploads/backup/' . $backup->filename);
        if (file_exists($path)) {
            unlink($path);
        }
        $backup->delete();
        return redirect()->back()->with('success', 'Backup deleted successfully.');
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
