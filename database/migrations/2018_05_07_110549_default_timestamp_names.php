<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultTimestampNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Rename timestamps to keep with the conventional laravel names.
         */
        Schema::table('steps',function (Blueprint $table) {
            $table->renameColumn('created', 'created_at');
            $table->renameColumn('updated', 'updated_at');
        });

        Schema::table('workflows',function (Blueprint $table) {
            $table->renameColumn('created', 'created_at');
            $table->renameColumn('updated', 'updated_at');
        });

        Schema::table('verification_comments',function (Blueprint $table) {
            $table->renameColumn('created','created_at');
        });

        Schema::table('comment_replies',function (Blueprint $table) {
            $table->renameColumn('created','created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps',function (Blueprint $table) {
            $table->renameColumn('created_at', 'created');
            $table->renameColumn('updated_at', 'updated');
        });

        Schema::table('workflows',function (Blueprint $table) {
            $table->renameColumn('created_at', 'created');
            $table->renameColumn('updated_at', 'updated');
        });

        Schema::table('verification_comments',function (Blueprint $table) {
            $table->renameColumn('created_at','created');
        });

        Schema::table('comment_replies',function (Blueprint $table) {
            $table->renameColumn('created_at','created');
        });
    }
}
