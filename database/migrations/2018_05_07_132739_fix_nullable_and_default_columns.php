<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixNullableAndDefaultColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Boolean fields with default values should not be nullable
         */
        Schema::table('workflows', function (Blueprint $table) {
            $table->boolean("isDraft")->nullable(false)->default(true)->change();
            $table->boolean("isPublished")->nullable(false)->default(false)->change();
            $table->boolean("isVerified")->nullable(false)->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->boolean("isDraft")->nullable()->default(true)->change();
            $table->boolean("isPublished")->nullable()->default(false)->change();
            $table->boolean("isVerified")->nullable()->default(false)->change();
        });
    }
}
