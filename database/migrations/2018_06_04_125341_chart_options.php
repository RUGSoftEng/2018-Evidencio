<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChartOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Reuse the main label for chart options
         */
        Schema::table('steps',function(Blueprint $table){
            $table->string('result_step_main_label',5000)->change();
        });

        Schema::table('steps',function(Blueprint $table) {
            $table->renameColumn('result_step_main_label','result_step_chart_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps',function(Blueprint $table){
            $table->string('result_step_chart_options',255)->change();
        });

        Schema::table('steps',function(Blueprint $table) {
            $table->renameColumn('result_step_chart_options','result_step_main_label');
        });
    }
}
