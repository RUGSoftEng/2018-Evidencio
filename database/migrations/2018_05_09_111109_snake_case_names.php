<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SnakeCaseNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Rename column names to snake case because there were some issues
         * when using capital letters in names
         */
        Schema::table('comment_replies', function(Blueprint $table){
            $table->renameColumn('authorId','author_id');
            $table->renameColumn('verificationCommentId','verification_comment_id');
        });

        Schema::table('field_in_input_steps', function(Blueprint $table){
            $table->renameColumn('fieldId','field_id');
            $table->renameColumn('inputStepId','input_step_id');
        });

        Schema::table('fields', function(Blueprint $table){
            $table->renameColumn('friendlyTitle','friendly_title');
            $table->renameColumn('friendlyDescription','friendly_description');
            $table->renameColumn('continuousFieldMax','continuous_field_max');
            $table->renameColumn('continuousFieldMin','continuous_field_min');
            $table->renameColumn('continuousFieldStepBy','continuous_field_step_by');
            $table->renameColumn('continuousFieldUnit','continuous_field_unit');
            $table->renameColumn('evidencioVariableId','evidencio_variable_id');
        });

        Schema::table('loaded_evidencio_models', function(Blueprint $table){
            $table->renameColumn('workflowId','workflow_id');
            $table->renameColumn('modelId','model_id');
        });

        Schema::table('model_run_field_mappings', function(Blueprint $table){
            $table->renameColumn('evidencioModelId','evidencio_model_id');
            $table->renameColumn('fieldId','field_id');
            $table->renameColumn('stepId','step_id');
            $table->renameColumn('evidencioFieldId','evidencio_field_id');
        });

        Schema::table('next_steps', function(Blueprint $table){
            $table->renameColumn('nextStepId','next_step_id');
            $table->renameColumn('previousStepId','previous_step_id');
        });

        Schema::table('options', function(Blueprint $table){
            $table->renameColumn('categoricalFieldId','categorical_field_id');
            $table->renameColumn('friendlyTitle','friendly_title');
        });

        Schema::table('registration_documents', function(Blueprint $table){
            $table->renameColumn('registreeId','registree_id');
            $table->renameColumn('URL','url');
        });

        Schema::table('results', function(Blueprint $table){
            $table->renameColumn('evidencioModelId','evidencio_model_id');
            $table->renameColumn('resultName','result_name');
            $table->renameColumn('resultNumber','result_number');
            $table->renameColumn('stepId','step_id');
            $table->renameColumn('expectedType','expected_type');
            $table->renameColumn('representationLabel','representation_label');
            $table->renameColumn('representationType','representation_type');
        });

        Schema::table('steps', function(Blueprint $table){
            $table->renameColumn('workflowStepLevel','workflow_step_level');
            $table->renameColumn('workflowStepWorkflowId','workflow_step_workflow_id');
            $table->renameColumn('isStored','is_stored');
        });

        Schema::table('users', function(Blueprint $table){
            $table->renameColumn('firstName','first_name');
            $table->renameColumn('lastName','last_name');
            $table->renameColumn('languageCode','language_code');
            $table->renameColumn('photoURL','photo_url');
            $table->renameColumn('academicDegree','academic_degree');
            $table->renameColumn('isAdministrator','is_administrator');
            $table->renameColumn('isDeactivated','is_deactivated');
            $table->renameColumn('isReviewer','is_reviewer');
            $table->renameColumn('isVerified','is_verified');
            $table->renameColumn('verifiedByAdminId','verified_by_admin_id');
            $table->renameColumn('verificationDate','verification_date');
        });

        Schema::table('verification_comments', function(Blueprint $table){
            $table->renameColumn('reviewerId','reviewer_id');
            $table->renameColumn('workflowId','workflow_id');
        });

        Schema::table('workflows', function(Blueprint $table){
            $table->renameColumn('authorId','author_id');
            $table->renameColumn('languageCode','language_code');
            $table->renameColumn('isDraft','is_draft');
            $table->renameColumn('isPublished','is_published');
            $table->renameColumn('isVerified','is_verified');
            $table->renameColumn('verifiedByReviewerId','verified_by_reviewer_id');
            $table->renameColumn('verificationDate','verification_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_replies', function(Blueprint $table){
            $table->renameColumn('author_id', 'authorId');
            $table->renameColumn('verification_comment_id', 'verificationCommentId');
        });

        Schema::table('field_in_input_steps', function(Blueprint $table){
            $table->renameColumn('field_id', 'fieldId');
            $table->renameColumn('input_step_id', 'inputStepId');
        });

            Schema::table('fields', function(Blueprint $table){
                $table->renameColumn('friendly_title','friendlyTitle');
                $table->renameColumn('friendly_description','friendlyDescription');
                $table->renameColumn('continuous_field_max','continuousFieldMax');
                $table->renameColumn('continuous_field_min','continuousFieldMin');
                $table->renameColumn('continuous_field_step_by','continuousFieldStepBy');
                $table->renameColumn('continuous_field_unit','continuousFieldUnit');
                $table->renameColumn('evidencio_variable_id','evidencioVariableId');
            });

            Schema::table('loaded_evidencio_models', function(Blueprint $table){
                $table->renameColumn('workflow_id','workflowId');
                $table->renameColumn('model_id','modelId');
            });

            Schema::table('model_run_field_mappings', function(Blueprint $table){
                $table->renameColumn('evidencio_model_id','evidencioModelId');
                $table->renameColumn('field_id','fieldId');
                $table->renameColumn('step_id','stepId');
                $table->renameColumn('evidencio_field_id','evidencioFieldId');
            });

            Schema::table('next_steps', function(Blueprint $table){
                $table->renameColumn('next_step_id','nextStepId');
                $table->renameColumn('previous_step_id','previousStepId');
            });

            Schema::table('options', function(Blueprint $table){
                $table->renameColumn('categorical_field_id','categoricalFieldId');
                $table->renameColumn('friendly_title','friendlyTitle');
            });

            Schema::table('registration_documents', function(Blueprint $table){
                $table->renameColumn('registree_id','registreeId');
                $table->renameColumn('url','URL');
            });

            Schema::table('results', function(Blueprint $table){
                $table->renameColumn('evidencio_model_id','evidencioModelId');
                $table->renameColumn('result_name','resultName');
                $table->renameColumn('result_number','resultNumber');
                $table->renameColumn('step_id','stepId');
                $table->renameColumn('expected_type','expectedType');
                $table->renameColumn('representation_label','representationLabel');
                $table->renameColumn('representation_type','representationType');
            });

            Schema::table('steps', function(Blueprint $table){
                $table->renameColumn('workflow_step_level','workflowStepLevel');
                $table->renameColumn('workflow_step_workflow_id','workflowStepWorkflowId');
                $table->renameColumn('is_stored','isStored');
            });

            Schema::table('users', function(Blueprint $table){
                $table->renameColumn('first_name','firstName');
                $table->renameColumn('last_name','lastName');
                $table->renameColumn('language_code','languageCode');
                $table->renameColumn('photo_url','photoURL');
                $table->renameColumn('academic_degree','academicDegree');
                $table->renameColumn('is_administrator','isAdministrator');
                $table->renameColumn('is_deactivated','isDeactivated');
                $table->renameColumn('is_reviewer','isReviewer');
                $table->renameColumn('is_verified','isVerified');
                $table->renameColumn('verified_by_admin_id','verifiedByAdminId');
                $table->renameColumn('verification_date','verificationDate');
            });

            Schema::table('verification_comments', function(Blueprint $table){
                $table->renameColumn('reviewer_id','reviewerId');
                $table->renameColumn('workflow_id','workflowId');
            });

            Schema::table('workflows', function(Blueprint $table){
                $table->renameColumn('author_id','authorId');
                $table->renameColumn('language_code','languageCode');
                $table->renameColumn('is_draft','isDraft');
                $table->renameColumn('is_published','isPublished');
                $table->renameColumn('is_verified','isVerified');
                $table->renameColumn('verified_by_reviewer_id','verifiedByReviewerId');
                $table->renameColumn('verification_date','verificationDate');
            });
    }
}
