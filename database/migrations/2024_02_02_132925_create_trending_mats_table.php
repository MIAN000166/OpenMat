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
        Schema::create('trending_mats', function (Blueprint $table) {
            $table->id();
//            abc
            $table->string('name');
            $table->string('region');
            $table->string('state');
            $table->string('location');
            $table->string('phone_number');
            $table->string('website')->nullable();
            $table->string('physical_address');
            $table->string('open_mat_time');
            $table->string('open_mat_day');
            $table->string('link_to_waiver')->nullable();
            $table->string('other_info')->nullable();
            $table->string('event_date')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('mat_id');
            $table->unsignedBigInteger('user_id');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trending_mats');
    }
};
