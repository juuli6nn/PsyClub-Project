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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('street_address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('street_address');
            $table->string('province')->nullable()->after('city');
            $table->string('country_code', 5)->default('PH')->after('province');
            $table->string('postal_code', 20)->nullable()->after('country_code');
            $table->decimal('latitude', 10, 7)->nullable()->after('postal_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->timestamp('profile_completed_at')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'first_name',
                'last_name',
                'phone',
                'street_address',
                'city',
                'province',
                'country_code',
                'postal_code',
                'latitude',
                'longitude',
                'profile_completed_at',
            ]);
        });
    }
};
