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
        /*
         * User can save a workflow as a draft, which doesn't have to have unique
         * field to evidencio field mappings in a step (fields can repeat).
         * That's why we remove the current primary key constraint and create
         * a proper primary key instead.
         *
         * We temporarily remove the foreign key, because dropping the primary
         * produced errors otherwise.
         */
        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            $table->dropForeign('model_run_field_mappings_fieldId_foreign');
            $table->dropPrimary();
        });


        Schema::table('model_run_field_mappings', function (Blueprint $table) {

            $table->increments('id')->first();
            // We keep the old name of the constraint (before converting to snake
            // case) for the possibility to rollback and migrate again without errors.
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
            $table->dropColumn('id');
        });

        Schema::table('model_run_field_mappings', function (Blueprint $table) {
            $table->primary(["field_id", "step_id", "evidencio_model_id"],'model_run_field_mappings_primary');
        });
    }
}
