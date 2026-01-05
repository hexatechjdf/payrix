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
        Schema::create('location_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('location_id', 190)->nullable();
            $table->string('generic_flags', 190)->nullable();
            $table->json('generic_flags_options')->nullable();
            $table->string('active_subscriptions', 190)->nullable();
            $table->json('active_subscriptions_options')->nullable();
            $table->json('content', 190)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_custom_fields');
    }
};
