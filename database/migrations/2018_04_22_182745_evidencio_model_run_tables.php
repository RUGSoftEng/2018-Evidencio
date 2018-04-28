<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EvidencioModelRunTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropColumn("evidencioModelId");
            $table->dropColumn("resultStepExpectedResultType");
            $table->dropColumn("resultStepRepresentationType");
        });

        Schema::create('model_run_field_mappings', function (Blueprint $table) {
            $table->unsignedBigInteger("evidencioModelId");
            $table->unsignedInteger("fieldId");
            $table->unsignedInteger("stepId");
            $table->unsignedBigInteger("evidencioFieldId");

            $table->primary(["fieldId", "stepId", "evidencioModelId"]);

            $table->foreign("fieldId")->references("id")->on("fields");
            $table->foreign("stepId")->references("id")->on("steps");
        });

        Schema::create('results', function(Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger("evidencioModelId");
            $table->string("resultName",20);
            $table->unsignedSmallInteger("resultNumber");
            $table->unsignedInteger("stepId");
            $table->string("expectedType",20)->nullable();
            $table->string("representationLabel",40)->nullable();
            $table->string("representationType",20)->nullable();

            $table->foreign("stepId")->references("id")->on("steps");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->unsignedInteger("evidencioModelId");
            $table->unsignedSmallInteger("resultStepExpectedResultType")->nullable();
            $table->unsignedSmallInteger("resultStepRepresentationType")->nullable();
        });

        Schema::dropIfExists('model_run_field_mappings');
        Schema::dropIfExists('results');
    }
}
