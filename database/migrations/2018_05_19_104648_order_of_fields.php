<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderOfFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Introduce ordering of fields in a step
         */
        Schema::table('field_in_input_steps', function(Blueprint $table){
            $table->unsignedInteger("order");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_in_input_steps', function(Blueprint $table){
            $table->dropColumn("order");
        });
    }
}
