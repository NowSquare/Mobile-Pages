<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
          
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->char('uuid', 36)->nullable();
            $table->bigInteger('account_id')->unsigned()->nullable()->index()->default(1);
            $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('role')->unsigned()->default(3);
            $table->boolean('active')->default(true);
            $table->integer('plan_id')->unsigned()->nullable();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->string('remote_customer_id', 200)->nullable();
            $table->string('previous_remote_customer_id', 200)->nullable();
            $table->string('app_name', 64)->nullable();
            $table->string('app_logo', 250)->nullable();
            $table->string('app_vendor_id', 128)->nullable();
            $table->string('app_contact', 64)->nullable();
            $table->string('app_color', 7)->nullable();
            $table->string('app_host', 128)->nullable();
            $table->string('app_mail_name_from', 64)->nullable();
            $table->string('app_mail_address_from', 64)->nullable();
            $table->string('name', 64)->nullable();
            $table->string('email', 128)->nullable();
            $table->string('verification_code', 64)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('token')->nullable();
            $table->string('login_code', 64)->nullable();
            $table->timestamp('login_code_valid_until')->nullable();
            $table->string('language', 5)->nullable();
            $table->string('locale', 24)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->string('timezone', 32)->nullable();
            $table->tinyInteger('first_day_of_week')->nullable();
            $table->ipAddress('signup_ip_address')->nullable();
            $table->integer('logins')->default(0)->unsigned();
            $table->dateTime('last_login')->nullable();
            $table->ipAddress('last_login_ip_address')->nullable();
            $table->dateTime('expires')->nullable();
            $table->tinyInteger('emails_sent')->unsigned()->default(0);
            $table->mediumText('notes')->nullable();
            $table->json('settings')->nullable();
            $table->json('tags')->nullable();
            $table->json('attributes')->nullable();
            $table->json('meta')->nullable();

            $table->rememberToken();

            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
