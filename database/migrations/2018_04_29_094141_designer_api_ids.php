<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DesignerApiIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Add mapping of a field to an Evidencio variable id for the designer
         * page.
         */
        Schema::table('fields', function (Blueprint $table) {
            $table->unsignedBigInteger("evidencioVariableId");
        });

        /*
         * Table containing Evidencio models that are loaded in a workflow's
         * designer page.
         */
        Schema::create('loaded_evidencio_models', function (Blueprint $table) {
            $table->unsignedInteger("workflowId");
            $table->unsignedBigInteger("modelId");

            $table->primary(["workflowId","modelId"]);

            $table->foreign("workflowId")->references("id")->on("workflows");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn("evidencioVariableId");
        });

        Schema::dropIfExists('loaded_evidencio_models');
    }
}
