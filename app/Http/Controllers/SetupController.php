<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\Organization;
use App\Models\User;
use App\Services\InstallationStatus;

class SetupController extends Controller
{
    public function index()
    {
        // Defense in depth: CheckInstalled middleware already blocks this route once
        // installed, but the controller refuses too in case that ever changes.
        if (InstallationStatus::isInstalled()) {
            return redirect()->route('dashboard');
        }

        return view('setup.index');
    }

    public function success()
    {
        return view('setup.success');
    }

    public function store(Request $request)
    {
        $request->validate([
            'org_name' => 'required|string|max:255',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        // Guard: never wipe a completed install. Only allow (re-)running setup —
        // including the destructive migrate:fresh below — if installation hasn't
        // actually completed yet (e.g. a previous attempt failed midway).
        if (InstallationStatus::isInstalled()) {
            Log::debug('[Setup] Already installed — refusing to re-run setup');
            return redirect()->route('dashboard');
        }

        try {
            // Clean slate — remove any stale installed marker from a previous failed attempt.
            @unlink(storage_path('installed'));
            Log::debug('[Setup] Removed stale installed marker');

            $dbName = config('database.connections.mysql.database');
            Log::debug('[Setup] Target database: ' . $dbName);

            // Connect without a database so we can create one on a blank server.
            config()->set('database.connections.mysql.database', null);
            DB::purge('mysql');
            DB::reconnect('mysql');
            Log::debug('[Setup] Connected to MySQL server (no database)');

            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            Log::debug('[Setup] Database created/exists');

            // Now reconnect to the newly created database.
            config()->set('database.connections.mysql.database', $dbName);
            DB::purge('mysql');
            DB::reconnect('mysql');
            Log::debug('[Setup] Reconnected to database: ' . $dbName);

            // Drop all tables and rebuild clean (removes any demo/seeder data).
            Artisan::call('migrate:fresh', ['--force' => true]);
            $migrateOutput = Artisan::output();
            Log::debug('[Setup] Migrations complete', ['output' => $migrateOutput]);

            // Create the admin user first — email_verified_at left null; OTP verification follows.
            $user = User::create([
                'name'     => $request->name,
                'username' => Str::slug($request->name) . rand(100, 999),
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => 'active',
            ]);
            Log::debug('[Setup] Admin user created', ['id' => $user->id, 'email' => $user->email]);

            // Create the organization — created_by is a required FK to users, so the
            // user must exist first.
            $org = Organization::create([
                'name'       => $request->org_name,
                'slug'       => Str::slug($request->org_name),
                'email'      => $request->email,
                'settings'   => [],
                'created_by' => $user->id,
            ]);
            Log::debug('[Setup] Organization created', ['id' => $org->id, 'name' => $org->name]);

            // Attach user as org admin
            $org->users()->attach($user->id, [
                'role'   => 'admin',
                'status' => 'active',
            ]);
            Log::debug('[Setup] User attached to organization as admin');

            // Mark as installed — writes the marker file and caches the flag so
            // future requests never have to re-check the database, and the route
            // stays locked even if the marker file is later deleted.
            InstallationStatus::markInstalled();
            Log::debug('[Setup] Installed marker written', ['path' => storage_path('installed')]);

            // Verify the database-based check also works
            $dbCheck = \Illuminate\Support\Facades\DB::table('organizations')->count();
            Log::debug('[Setup] Organization count from DB: ' . $dbCheck);

            return redirect()
                ->route('login')
                ->with('success', 'Installation complete! Your admin account has been created. Please sign in.');

        } catch (\Throwable $e) {
            Log::error('[Setup] Exception caught', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['setup' => 'Setup failed: ' . $e->getMessage()]);
        }
    }
}
