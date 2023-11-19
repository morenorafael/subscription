<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscribable_type')->index();
            $table->string('name');
            $table->boolean('canceled_immediately')->nullable();
            $table->unsignedBigInteger('subscribable_id');
            $table->unsignedBigInteger('plan_id');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('plan_subscriptions');
    }
};
