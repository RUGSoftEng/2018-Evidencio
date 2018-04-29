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
        Schema::table('fields', function (Blueprint $table) {
            $table->unsignedBigInteger("evidencioVariableId");
        });

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
