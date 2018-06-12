<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LongerMaxFieldValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Some string columns actually need to have longer maximum possible
         * lengths
         */
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->string('name',255)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name',255)->change();
            $table->string('last_name',255)->change();
            $table->string('organisation',255)->nullable()->change();
            $table->string('academic_degree',30)->nullable()->change();
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->string('title',255)->change();
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->string('title',255)->change();
        });

        Schema::table('next_steps', function (Blueprint $table) {
            $table->string('title',255)->nullable()->change();
        });

        Schema::table('results', function (Blueprint $table) {
            $table->string('representation_label',255)->nullable()->change();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->string('friendly_title',255)->change();
            $table->string('continuous_field_unit',30)->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->string('name',30)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name',30)->change();
            $table->string('last_name',30)->change();
            $table->string('organisation',50)->nullable()->change();
            $table->string('academic_degree',10)->nullable()->change();
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->string('title',30)->change();
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->string('title',30)->change();
        });

        Schema::table('next_steps', function (Blueprint $table) {
            $table->string('title',30)->nullable()->change();
        });

        Schema::table('results', function (Blueprint $table) {
            $table->string('representation_label',40)->nullable()->change();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->string('continuous_field_unit',15)->nullable()->change();
            $table->string('friendly_title',40)->change();
        });
    }
}
