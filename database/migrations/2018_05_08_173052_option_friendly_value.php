<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OptionFriendlyValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Values should be longer, introduce a title that is friendly for the patient
         */
        Schema::table('options', function (Blueprint $table) {
            $table->string('value',50)->change();

            $table->string('friendlyTitle',30);
        });

        /*
         * Rename value to title for consistency with Evidencio API
         */
        Schema::table('options', function (Blueprint $table) {
            $table->renameColumn('value', 'title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->string('title',15)->change();

            $table->dropColumn('friendlyTitle');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->renameColumn('title', 'value');
        });
    }
}
