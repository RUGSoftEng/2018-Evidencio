<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FieldMappingsPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            $table->dropPrimary('PRIMARY');
        });


        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            $table->increments('id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
