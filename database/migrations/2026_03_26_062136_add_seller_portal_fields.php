<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->unique('code');
            $table->string('onboarding_status')->default('new')->after('type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('is_admin')->constrained()->nullOnDelete();
            $table->string('role')->default('user')->after('seller_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'role']);
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('onboarding_status');
        });
    }
};
