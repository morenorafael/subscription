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
            $table->integer('plan_id')->unsigned();
            $table->string('code');
            $table->string('value');
            $table->smallInteger('sort_order')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'code']);
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('plan_features');
    }
};
