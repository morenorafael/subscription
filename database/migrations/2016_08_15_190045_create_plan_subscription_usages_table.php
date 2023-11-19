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
            $table->string('code');
            $table->unsignedSmallInteger('used');
            $table->timestamp('valid_until')->nullable();
            $table->unsignedBigInteger('subscription_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('plan_subscription_usages');
    }
};
