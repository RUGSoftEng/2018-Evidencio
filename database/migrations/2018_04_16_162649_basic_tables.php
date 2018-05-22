<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BasicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Table containing individual workflows including their basic
         * information and current status.
         */
        Schema::create('workflows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("authorId");
            $table->timestamp("created")->useCurrent();
            $table->timestamp("updated")->nullable();
            $table->string("languageCode",2);
            $table->string("title",30);
            $table->string("description",5000)->nullable();
            $table->boolean("isDraft")->nullable()->default(true);
            $table->boolean("isPublished")->nullable()->default(false);
            $table->boolean("isVerified")->nullable()->default(false);
            $table->timestamp("verificationDate")->nullable();
            $table->unsignedInteger("verifiedByReviewerId")->nullable();

            $table->foreign("authorId")->references("id")->on("users");
            $table->foreign("verifiedByReviewerId")->references("id")->on("users");
        });

        /*
         * Table containing steps (nodes in the workflow tree), which can be
         * either input steps (where one inputs data) or result steps (where
         * one is presented with their result). Thus, some attributes apply only
         * to one type of a step and are empty when the step is of other type.
         * Step can also be a "stored step", it is then not connected to
         * a workflow but serves as a possible starting point for creating new
         * steps.
         */
        Schema::create('steps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp("created")->useCurrent();
            $table->timestamp("updated")->nullable();
            $table->unsignedInteger("evidencioModelId");
            $table->string("title",30);
            $table->string("categoryName",30)->nullable();
            $table->string("description",5000)->nullable();
            $table->boolean("isStored")->nullable();
            $table->unsignedSmallInteger("resultStepExpectedResultType")->nullable();
            $table->unsignedSmallInteger("resultStepRepresentationType")->nullable();
            $table->unsignedSmallInteger("workflowStepLevel")->nullable();
            $table->unsignedInteger("workflowStepWorkflowId")->nullable();

            $table->foreign("workflowStepWorkflowId")->references("id")->on("workflows");
        });

        /*
         * Table containing fields that are provided in an input step. They can
         * be either categorical (then they contain a number of options and have
         * empty continuous attributes) or continuous (they provide additional
         * attributes).
         */
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string("friendlyTitle",40);
            $table->string("friendlyDescription")->nullable();
            $table->integer("continuousFieldMax")->nullable();
            $table->integer("continuousFieldMin")->nullable();
            $table->integer("continuousFieldStepBy")->nullable();
            $table->string("continuousFieldUnit",15)->nullable();
        });

        /*
         * Table representing a many-to-many relationship between a field and
         * a step describing fields that are inputted in every step.
         */
        Schema::create('field_in_input_steps', function (Blueprint $table) {
            $table->unsignedInteger("fieldId");
            $table->unsignedInteger("inputStepId");

            $table->primary(["fieldId","inputStepId"]);

            $table->foreign("fieldId")->references("id")->on("fields");
            $table->foreign("inputStepId")->references("id")->on("steps");

        });

        /*
         * Table containing possible options of a categorical field.
         */
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->string("value",15);
            $table->unsignedInteger("categoricalFieldId");

            $table->foreign("categoricalFieldId")->references("id")->on("fields");
        });

        /*
         * Table containing a many-to-many relationship between steps to describe
         * possible next steps from a given step with a set of rules (condition)
         * that must be/are recommended to be met to move to that step.
         */
        Schema::create('next_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("nextStepId");
            $table->unsignedInteger("previousStepId");
            $table->string("condition",10000);
            $table->string("description",5000)->nullable();
            $table->string("title",30)->nullable();

            $table->foreign("nextStepId")->references("id")->on("steps");
            $table->foreign("previousStepId")->references("id")->on("steps");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('next_steps');
        Schema::dropIfExists('options');
        Schema::dropIfExists('field_in_input_steps');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('steps');
        Schema::dropIfExists('workflows');
    }
}
