<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NegatedChartVars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * We create a flag for the variable to know if to use its 'negation'
         * (100-value) which can be useful when displaying a chart with one variable
         * and its 'negated' value as a comparison.
         */
         Schema::table('result_step_chart_items', function (Blueprint $table) {
             $table->boolean('item_is_negated')->default(false);
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_step_chart_items', function (Blueprint $table) {
            $table->dropColumn('item_is_negated');
        });
    }
}
