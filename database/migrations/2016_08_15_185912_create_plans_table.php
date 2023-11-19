<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 7, 2)->default('0.00');
            $table->string('interval')->default('month');
            $table->smallInteger('interval_count')->default(1);
            $table->smallInteger('trial_period_days')->nullable();
            $table->smallInteger('sort_order')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('plans');
    }
};
