<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'workplace_id')) {
                $table->foreignId('workplace_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (Schema::hasColumn('trips', 'workplace_name')) {
                $table->dropColumn(['workplace_name', 'workplace_address']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['workplace_id']);
            $table->dropColumn('workplace_id');
            $table->string('workplace_name')->after('id');
            $table->string('workplace_address')->after('workplace_name');
        });
    }
};
