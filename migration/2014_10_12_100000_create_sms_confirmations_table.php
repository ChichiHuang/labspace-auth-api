<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsConfirmationsTable extends Migration
{
    /**
     * 簡訊驗證碼
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone',20);
            $table->string('token');
            $table->char('status')->default(1);
            $table->text('msg')->default('');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_confirmations');
    }
}
