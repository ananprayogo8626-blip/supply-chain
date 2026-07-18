<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Menampilkan semua user dengan pagination
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by role
        if ($request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by status (active/inactive/trash)
        if ($request->status === 'active') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->status === 'inactive') {
            $query->whereNull('email_verified_at');
        } elseif ($request->status === 'trash') {
            $query->onlyTrashed();
        }
        
        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(12)->withQueryString();
        
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,analyst,viewer',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'email_verified_at' => now(),
        ];

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $photoPath = $photo->store('profile-photos', 'public');
            $data['profile_photo'] = $photoPath;
        }

        $user = User::create($data);

        ActivityLog::log('Create', "Created User: {$user->name} (#{$user->id})", $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail user
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form edit user
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,analyst,viewer',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $photo = $request->file('profile_photo');
            $photoPath = $photo->store('profile-photos', 'public');
            $data['profile_photo'] = $photoPath;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::log('Update', "Updated User: {$user->name} (#{$user->id})", $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat menghapus Super Admin.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        ActivityLog::log('Delete', "Soft-deleted User: {$user->name} (#{$user->id})", $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Restore user
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::log('Restore', "Restored User: {$user->name} (#{$user->id})", $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dipulihkan.');
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('Update', "Reset password for User: {$user->name} (#{$user->id})", $user);

        return back()->with('success', 'Password berhasil direset.');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat mengubah status Super Admin.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat mengubah status akun sendiri.');
        }

        if ($user->email_verified_at) {
            $user->update(['email_verified_at' => null]);
            $message = 'User berhasil dinonaktifkan.';
            ActivityLog::log('Update', "Deactivated User: {$user->name} (#{$user->id})", $user);
        } else {
            $user->update(['email_verified_at' => now()]);
            $message = 'User berhasil diaktifkan.';
            ActivityLog::log('Update', "Activated User: {$user->name} (#{$user->id})", $user);
        }

        return back()->with('success', $message);
    }

    /**
     * Export users to CSV
     */
    public function export()
    {
        $users = User::all();
        $headers = ['ID', 'Name', 'Email', 'Role', 'Phone', 'Status', 'Created At'];
        
        return \App\Services\ExportImportHelper::exportCsv('users', $headers, $users, function($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->phone ?? '—',
                $user->email_verified_at ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * Export users to PDF
     */
    public function exportPdf()
    {
        $users = User::all();
        $headers = ['ID', 'Name', 'Email', 'Role', 'Phone', 'Status', 'Joined'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->phone ?? '—',
                $user->email_verified_at ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d'),
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Users List', $headers, $rows);
    }

    /**
     * Import users from CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle);
            $imported = 0;
            $updated = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;

                $name = $row[1] ?? '';
                $email = $row[2] ?? '';
                $role = $row[3] ?? 'viewer';
                $phone = $row[4] ?? null;
                $password = 'password123';

                if (empty($email) || empty($name)) continue;

                $user = User::withTrashed()->updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'role' => in_array($role, ['super_admin', 'admin', 'analyst', 'viewer']) ? $role : 'viewer',
                        'phone' => $phone,
                        'password' => Hash::make($password),
                        'email_verified_at' => now(),
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $user->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} users, updated {$updated} users from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new users created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}
