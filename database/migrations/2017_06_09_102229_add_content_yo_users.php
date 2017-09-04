<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentYoUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('mid')->unsigned()->comment('会员id');
            $table->string('code', 20)->nullable()->comment('验证码');
            $table->string('content', 250)->nullable()->comment('短信内容');
            $table->tinyInteger('type')->unsigned()->nullable()->comment('短信类型，0 注册,1 找回密码,2 绑定手机');
            $table->tinyInteger('isused')->unsigned()->nullable()->comment('是否使用过　0 是，1 否');
            $table->integer('ctime')->unsigned()->comment('下发时间');
            $table->integer('times')->unsigned()->comment('手机短信下发次数');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
