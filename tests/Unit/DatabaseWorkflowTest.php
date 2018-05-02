<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $reviewer;
    private $workflow;
    private $loadedEvidencioModel;
    private $step1;
    private $step2;
    private $field;
    private $option;
    private $result;

    public function setUp()
    {
        parent::setUp();

        $this->user = \App\User::create([
            'name' => 'testUser',
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => 'johnsmith@evidencio.com',
            'password' => 'abcdef',
            'languageCode' => 'en'
        ]);

        $this->reviewer = \App\User::create([
            'name' => 'reviewerUser',
            'firstName' => 'Jan',
            'lastName' => 'Jansen',
            'email' => 'janjansen@evidencio.com',
            'password' => '123456',
            'languageCode' => 'nl'
        ]);

        $this->workflow = new \App\Workflow([
            'title' => 'Breast cancer survival',
            'languageCode' => 'en'
        ]);

        $this->workflow->author()->associate($this->user);

        $this->workflow->verifiedByReviewer()->associate($this->reviewer);

        $this->workflow->save();

        $this->loadedEvidencioModel = $this->workflow->loadedEvidencioModels()->create(['modelId' => '576']);

        $this->step1 = \App\Step::create(['title' => 'Step 1']);
        $this->step2 = \App\Step::create(['title' => 'Step 2']);

        $this->step1->workflow()->associate($this->workflow);
        $this->step1->save();
        $this->workflow->steps()->save($this->step2);

        $this->step1->nextSteps()->attach($this->step2,['condition' => '']);

        $this->field = \App\Field::create(['friendlyTitle' => "Are you sick?", 'evidencioVariableId' => '1234']);

        $this->step1->fields()->attach($this->field);

        $this->option = $this->field->options()->create(['value' => 'Yes']);

        $this->step1->modelRunFields()->attach($this->field,['evidencioModelId' => '576', 'evidencioFieldId' => '1234']);

        $this->result = $this->step1->modelRunResults()->create(['resultName' => 'breastSurvival', 'resultNumber' => '0', 'evidencioModelId' => '576']);

    }

    public function testWorkflowRelations()
    {
        $this->assertEquals($this->user['id'], $this->workflow->author['id']);
        $this->assertEquals($this->workflow['id'], $this->user->createdWorkflows->first()['id']);

        $this->assertEquals($this->reviewer['id'], $this->workflow->verifiedByReviewer['id']);
        $this->assertEquals($this->workflow['id'], $this->reviewer->verifiedWorkflows->first()['id']);

        $this->assertEquals($this->loadedEvidencioModel['id'], $this->workflow->loadedEvidencioModels->first()['id']);
        $this->assertEquals($this->workflow['id'], $this->loadedEvidencioModel->workflow['id']);

        $this->assertEquals($this->step1['id'], $this->workflow->steps->sortBy('id')->first()['id']);
        $this->assertEquals($this->workflow['id'], $this->step1->workflow['id']);

        $this->assertEquals($this->step2['id'], $this->workflow->steps->sortBy('id')->last()['id']);
        $this->assertEquals($this->workflow['id'], $this->step2->workflow['id']);
    }

    public function testStepRelations()
    {
        $this->assertEquals($this->step1['id'], $this->step2->previousSteps->first()['id']);
        $this->assertEquals($this->step2['id'], $this->step1->nextSteps->first()['id']);

        $this->assertEquals($this->field['id'], $this->step1->fields->first()['id']);
        $this->assertEquals($this->step1['id'], $this->field->inputSteps->first()['id']);

        $this->assertContains('576', $this->step1->modelRuns());

        $this->assertEquals($this->result['id'], $this->step1->modelRunResults->first()['id']);
        $this->assertEquals($this->result['id'], $this->step1->modelRunResultsById('576')->first()['id']);
        $this->assertEquals($this->step1['id'], $this->result->step['id']);

        $this->assertEquals($this->field['id'], $this->step1->modelRunFields->first()['id']);
        $this->assertEquals('1234', $this->step1->modelRunFields->first()->pivot['evidencioFieldId']);
        $this->assertEquals($this->field['id'], $this->step1->modelRunFieldsById('576')->first()['id']);
        $this->assertEquals($this->step1['id'], $this->field->usedInRunsInSteps->first()['id']);
    }

    public function testFieldRelations()
    {
        $this->assertEquals($this->option['id'],$this->field->options->first()['id']);
        $this->assertEquals($this->field['id'],$this->option->categoricalField['id']);
    }

}
