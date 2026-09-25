<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Single source of truth for "has this app been installed?".
 *
 * Used by both CheckInstalled (middleware) and SetupController (defense in
 * depth at the controller level) so the rule only ever lives in one place.
 *
 * Important: this intentionally uses the 'file' cache store directly rather
 * than the app's configured default cache store (which is database-backed
 * here) — the cache/sessions/etc. tables don't exist yet before installation,
 * so relying on the default store would throw on every request pre-install.
 */
class InstallationStatus
{
    private const CACHE_KEY = 'app:installed';

    /**
     * Whether the app has completed first-time setup.
     *
     * Once true, the result is cached on disk forever so later requests
     * never have to hit the database again — and so the result survives
     * even if the `storage/installed` marker file is later removed.
     * A "not installed" result is never cached, since that state must
     * always reflect the live database.
     *
     * The database is the source of truth whenever it's reachable — the
     * `storage/installed` marker file is only a fallback for when the
     * database can't be reached at all. A stale marker left over from a
     * previous database (e.g. one that was later dropped/recreated, or a
     * deploy pointed at a different host) must never be allowed to claim
     * "installed" while the database itself says otherwise — that would
     * route every request away from /setup and into a session handler
     * that fatals trying to query a database that no longer has the data.
     */
    public static function isInstalled(): bool
    {
        if (Cache::store('file')->get(self::CACHE_KEY)) {
            return true;
        }

        try {
            if (
                Schema::hasTable('users') &&
                Schema::hasTable('organizations') &&
                DB::table('users')->exists() &&
                DB::table('organizations')->exists()
            ) {
                self::remember();
                return true;
            }

            // Database is reachable and confirms there's no install yet —
            // trust that over any stale marker file.
            return false;
        } catch (\Throwable) {
            // Database genuinely unreachable (not just empty) — fall back to
            // the marker file as a best-effort signal.
            return file_exists(storage_path('installed'));
        }
    }

    /**
     * Record that installation has just completed.
     */
    public static function markInstalled(): void
    {
        file_put_contents(storage_path('installed'), now()->toDateTimeString());
        self::remember();
    }

    /**
     * Drop the cached "installed" flag. Intended for tests/local debugging only.
     */
    public static function forget(): void
    {
        Cache::store('file')->forget(self::CACHE_KEY);
    }

    private static function remember(): void
    {
        Cache::store('file')->forever(self::CACHE_KEY, true);
    }
}
