<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseUserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $reviewer;
    protected $workflow;
    protected $registrationDoc;
    protected $verificationComment;
    protected $commentReply;

    public function setUp()
    {
        parent::setUp();

        $this->user = \App\User::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'johnsmith@evidencio.com',
            'password' => 'abcdef',
            'language_code' => 'en'
        ]);

        $this->workflow = new \App\Workflow([
            'title' => 'Breast cancer survival',
            'language_code' => 'en'
        ]);

        $this->reviewer = \App\User::create([
            'first_name' => 'Jan',
            'last_name' => 'Jansen',
            'email' => 'janjansen@evidencio.com',
            'password' => '123456',
            'language_code' => 'nl'
        ]);

        $this->workflow->author()->associate($this->user);

        $this->workflow->save();

        $this->registrationDoc = $this->user->registrationDocuments()->create(['name' => 'Diploma', 'url' => 'www.diplomas.com/123456.html']);

        $this->verificationComment = new \App\VerificationComment(['text' => 'Lorem ipsum dolor sit amet.']);

        $this->verificationComment->writtenByReviewer()->associate($this->reviewer);
        $this->verificationComment->workflow()->associate($this->workflow);

        $this->verificationComment->save();

        $this->commentReply = new \App\CommentReply(['text' => 'Lorem ipsum.']);

        $this->commentReply->author()->associate($this->user);
        $this->commentReply->verificationComment()->associate($this->verificationComment);

        $this->commentReply->save();

    }

    public function testUserRelations()
    {
        $this->assertEquals($this->registrationDoc['id'], $this->user->registrationDocuments->first()['id']);
        $this->assertEquals($this->user['id'], $this->registrationDoc->registree['id']);

        $this->assertEquals($this->reviewer['id'], $this->verificationComment->writtenByReviewer['id']);
        $this->assertEquals($this->verificationComment['id'], $this->reviewer->verificationCommentsWritten->first()['id']);

        $this->assertEquals($this->user['id'], $this->commentReply->author['id']);
        $this->assertEquals($this->commentReply['id'], $this->user->commentRepliesWritten->first()['id']);
    }

    public function testVerificationCommentRelations()
    {
        $this->assertEquals($this->verificationComment['id'], $this->workflow->verificationComments->first()['id']);
        $this->assertEquals($this->workflow['id'], $this->verificationComment->workflow['id']);

        $this->assertEquals($this->commentReply['id'], $this->verificationComment->commentReplies->first()['id']);
        $this->assertEquals($this->verificationComment['id'], $this->commentReply->verificationComment['id']);

        $this->assertEquals($this->verificationComment['id'], $this->workflow->verificationComments->first()['id']);
        $this->assertEquals($this->workflow['id'], $this->verificationComment->workflow['id']);
    }
}
