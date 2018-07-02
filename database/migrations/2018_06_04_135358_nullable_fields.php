<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Step's fields should be nullable because step can be an input step
         */
         Schema::table('steps',function(Blueprint $table){
             $table->string('result_step_chart_type',5000)->nullable()->change();
             $table->string('result_step_chart_options',5000)->nullable()->change();
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
            $table->string('result_step_chart_type',5000)->change();
            $table->string('result_step_chart_options',5000)->change();
        });
    }
}
