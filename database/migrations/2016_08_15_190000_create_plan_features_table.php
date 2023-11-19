<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('value');
            $table->smallInteger('sort_order')->nullable();
            $table->unsignedBigInteger('plan_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('plan_features');
    }
};
