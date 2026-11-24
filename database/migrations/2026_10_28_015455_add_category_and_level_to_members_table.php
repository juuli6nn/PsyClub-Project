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
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('membership_category_id')->nullable()->constrained('membership_categories')->nullOnDelete();
            $table->foreignId('membership_level_id')->nullable()->constrained('membership_levels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['membership_category_id']);
            $table->dropForeign(['membership_level_id']);
            $table->dropColumn(['membership_category_id', 'membership_level_id']);
        });
    }
};
