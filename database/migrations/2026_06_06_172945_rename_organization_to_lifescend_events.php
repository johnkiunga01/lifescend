<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('organizations')
            ->where('name', 'Lifescend Academy')
            ->update(['name' => 'Lifescend.events']);
    }

    public function down(): void
    {
        DB::table('organizations')
            ->where('name', 'Lifescend.events')
            ->update(['name' => 'Lifescend Academy']);
    }
};
