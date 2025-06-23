<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alle existierenden Trips finden und Arbeitsplätze erstellen
        $trips = DB::table('trips')->get();
        
        foreach ($trips as $trip) {
            // Prüfen ob Arbeitsplatz bereits existiert
            $workplace = DB::table('workplaces')
                ->where('name', $trip->workplace_name)
                ->where('address', $trip->workplace_address)
                ->first();
            
            if (!$workplace) {
                // Arbeitsplatz erstellen
                $workplaceId = DB::table('workplaces')->insertGetId([
                    'name' => $trip->workplace_name,
                    'address' => $trip->workplace_address,
                    'default_distance_km' => $trip->distance_km,
                    'default_cost_per_km' => $trip->cost_per_km,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $workplaceId = $workplace->id;
            }
            
            // Trip aktualisieren mit workplace_id
            DB::table('trips')
                ->where('id', $trip->id)
                ->update(['workplace_id' => $workplaceId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Trip workplace_id zurücksetzen
        DB::table('trips')->update(['workplace_id' => null]);
    }
};
