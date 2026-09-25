<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDatabaseReachable
{
    public function handle(Request $request, Closure $next): Response
    {
        // Always verify reachability when sessions are database-backed — don't
        // skip this just because installation has completed. The database can
        // become unreachable later too (dropped/recreated, moved to a new host,
        // a stale `storage/installed` marker left over from a different
        // database, a temporary outage) and without this check the request
        // fatals deep inside the session handler instead of degrading
        // gracefully to file sessions.
        if (config('session.driver') === 'database') {
            try {
                $cfg = config('database.connections.mysql');
                $pdo = new \PDO(
                    "mysql:host={$cfg['host']};port={$cfg['port']}",
                    $cfg['username'],
                    $cfg['password'],
                    [\PDO::ATTR_TIMEOUT => 3]
                );
                $pdo->query("USE `{$cfg['database']}`");
            } catch (\Throwable) {
                config(['session.driver' => 'file']);
            }
        }

        return $next($request);
    }
}
