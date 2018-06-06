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

            $table->dropForeign('model_run_field_mappings_fieldId_foreign');
            $table->dropPrimary();
        });


        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            $table->increments('id')->first();
            $table->foreign('field_id','model_run_field_mappings_fieldId_foreign')->references('id')->on('fields');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            //$table->dropPrimary();
            $table->dropColumn('id');
        });

        Schema::table('model_run_field_mappings', function (Blueprint $table) {
            $table->primary(["field_id", "step_id", "evidencio_model_id"],'model_run_field_mappings_primary');
        });
    }
}
