<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CategoryToColour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * We won't use categories for now so the colour attribute can be
         * directly connected to a step.
         */
        Schema::table('steps',function (Blueprint $table) {
            $table->dropColumn("categoryName");
            $table->string("colour",7); // Keep it in HTML format (#rrggbb)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps',function (Blueprint $table) {
            $table->dropColumn("colour");
            $table->string("categoryName",30)->nullable();
        });
    }
}
