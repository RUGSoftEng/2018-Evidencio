<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Add some more attributes to the user section, including basic
         * information and permissions.
         */
        Schema::table('users',function (Blueprint $table) {
            $table->string("firstName",30);
            $table->string("lastName",30);
            $table->string("languageCode",2);
            $table->string("photoURL",200)->nullable();
            $table->string("academicDegree",10)->nullable();
            $table->string("bio",5000)->nullable();
            $table->boolean("isAdministrator")->default(false);
            $table->boolean("isDeactivated")->default(false);
            $table->boolean("isReviewer")->default(false);
            $table->boolean("isVerified")->default(false);
            $table->string("organisation",50)->nullable();

            $table->unsignedInteger("verifiedByAdminId")->nullable();
            $table->timestamp("verificationDate")->nullable();

            $table->unique("photoURL");
            $table->foreign("verifiedByAdminId")->references("id")->on("users");
        });

        /*
         * Table containing locations to documents which are provided during
         * registration process to verify a user as a medical professional.
         */
        Schema::create('registration_documents', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger("registreeId");
            $table->string("name",30);
            $table->string("URL",200);

            $table->unique("URL");
            $table->foreign("registreeId")->references("id")->on("users");
        });

        /*
         * Table containing comments which can be left by a reviewer when they
         * have any remarks regarding a workflow.
         */
        Schema::create('verification_comments', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp("created")->useCurrent();
            $table->string("text",5000);
            $table->unsignedInteger("reviewerId");
            $table->unsignedInteger("workflowId");

            $table->foreign("reviewerId")->references("id")->on("users");
            $table->foreign("workflowId")->references("id")->on("workflows");
        });

        /*
         * Table containing replies to a verification comment which can create
         * a discussion between a reviewer and workflow creator.
         */
        Schema::create('comment_replies', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp("created")->useCurrent();
            $table->string("text",5000);
            $table->unsignedInteger("authorId");
            $table->unsignedInteger("verificationCommentId");

            $table->foreign("authorId")->references("id")->on("users");
            $table->foreign("verificationCommentId")->references("id")->on("verification_comments");
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(["verifiedByAdminId"]);

            $table->dropColumn("firstName");
            $table->dropColumn("lastName");
            $table->dropColumn("languageCode");
            $table->dropColumn("photoURL");
            $table->dropColumn("academicDegree");
            $table->dropColumn("bio");
            $table->dropColumn("isAdministrator");
            $table->dropColumn("isDeactivated");
            $table->dropColumn("isReviewer");
            $table->dropColumn("isVerified");
            $table->dropColumn("organisation");
            $table->dropColumn("verifiedByAdminId");
            $table->dropColumn("verificationDate");
        });

        Schema::dropIfExists('comment_replies');
        Schema::dropIfExists('verification_comments');

        Schema::dropIfExists('registration_documents');
    }
}
