<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Charts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Add a connection between result step and a result to save data of
         * charts
         */
        Schema::table('steps', function(Blueprint $table){
            $table->unsignedSmallInteger('result_step_chart_type');
            $table->string('result_step_main_label',255);
        });

        Schema::create('result_step_chart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_result_step_id');
            $table->unsignedInteger('item_result_id');
            $table->string('item_label',255);
            $table->string('item_background_colour',7);
            $table->unsignedInteger('item_data');

            $table->foreign('item_result_step_id')->references('id')->on('steps');
            $table->foreign('item_result_id')->references('id')->on('results');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps', function(Blueprint $table){
            $table->dropColumn('result_step_chart_type');
            $table->dropColumn('result_step_main_label');
        });

        Schema::dropIfExists('result_step_chart_items');
    }
}
