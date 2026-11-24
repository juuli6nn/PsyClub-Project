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
        Schema::table('membership_categories', function (Blueprint $table) {
            $table->string('level')->nullable()->after('name');
            $table->decimal('fee', 10, 2)->default(0)->after('description');
            $table->boolean('is_active')->default(true)->after('fee');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone_number');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->timestamp('verification_sent_at')->nullable()->after('is_active');
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_category_id')->constrained('membership_categories')->restrictOnDelete();
            $table->string('reference_no')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('verification_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_applications');

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude', 'verification_sent_at']);
        });

        Schema::table('membership_categories', function (Blueprint $table) {
            $table->dropColumn(['level', 'fee', 'is_active']);
        });
    }
};
