<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $event = DB::table('events')->where('slug', 'what-to-expect-at-accelerated-cmc-strategies-for-expedited-regulatory-pathways-masterclass-eFy4')->first();
        if (!$event) {
            return;
        }

        DB::table('ticket_types')->where('id', 12)->where('event_id', $event->id)->where('name', 'Training Pass')
            ->where('price', 1548.00)->update(['name' => 'Training Fee', 'price_eur' => 1350.00]);

        $paragraph = '<p><strong style="color: rgb(89, 89, 89);">LifeScend</strong><span style="color: rgb(89, 89, 89);"> | [</span>NN5 7ER, 130 Pembroke Road.<span style="color: rgb(89, 89, 89);">] | [</span><strong style="color: rgb(89, 89, 89);">+44</strong>1604372801<span style="color: rgb(89, 89, 89);">] | [</span><strong style="color: rgb(89, 89, 89);">jackline@lifescend.events</strong><span style="color: rgb(89, 89, 89);">] | Price: [</span><strong style="color: rgb(89, 89, 89);">1350 Euros per delegate</strong><span style="color: rgb(89, 89, 89);">]</span></p>';
        if (str_contains($event->description ?? '', $paragraph)) {
            DB::table('events')->where('id', $event->id)->update([
                'description' => str_replace($paragraph, '', $event->description),
            ]);
        }
    }

    public function down(): void
    {
        // Do not overwrite pricing or description changes made after deployment.
    }
};
