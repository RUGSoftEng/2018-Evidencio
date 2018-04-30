<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\EvidencioAPI;
use GuzzleHttp\Exception\ClientException;

class EvidencioAPITest extends TestCase
{

    public function testOverview()
    {
        $overview = EvidencioAPI::overview();

        $this->assertArrayHasKey("allModels",$overview);
        $this->assertArrayHasKey("myModels",$overview);
    }

    public function testSearch()
    {
        $results = EvidencioAPI::search("cancer");

        $this->assertArrayHasKey("id",$results[0]);
        $this->assertArrayHasKey("title",$results[0]);
        $this->assertArrayHasKey("variableSet",$results[0]);
    }

    /* Omitting models() method test because it returns a huge amount of data */

    public function testGetModel()
    {
        $model = EvidencioAPI::getModel(576);

        $this->assertEquals($model["id"],576);
        $this->assertArrayHasKey("title",$model);
        $this->assertArrayHasKey("variables",$model);
    }

    public function testRun()
    {
        $values = [
                "8304" => "24",
                "1460" => "10",
                "3175" => "Present",
                "5213" => "Grade I",
                "9392" => "3",
                "2438" => "Negative"
        ];

        $result = EvidencioAPI::run("170",$values);

        $this->assertEquals($result["id"],170);
        $this->assertArrayHasKey("title",$result);
        $this->assertArrayHasKey("result",$result);

    }

    public function testWrongParams()
    {
        $this->ExpectException(ClientException::class);
        EvidencioAPI::run(170,["foo" => "bar"]);
    }

    public function testNonExistingId()
    {
        $this->ExpectException(ClientException::class);
        EvidencioAPI::getModel(-1);
    }
}
