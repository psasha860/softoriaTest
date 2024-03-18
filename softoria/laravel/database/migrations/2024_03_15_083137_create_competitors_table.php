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
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->string('excluded_target');
            $table->string('target_domain');
            $table->string('referring_domain');
            $table->integer('rank');
            $table->bigInteger('backlinks');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};
