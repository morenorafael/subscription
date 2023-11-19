<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plan_subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id')->unsigned();
            $table->string('code');
            $table->smallInteger('used')->unsigned();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'code']);
            $table->foreign('subscription_id')->references('id')->on('plan_subscriptions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('plan_subscription_usages');
    }
};
