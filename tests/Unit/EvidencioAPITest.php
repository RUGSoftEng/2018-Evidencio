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

        $this->assertArrayHasKey("allModels",$overview, "API response for method 'overview' missing 'allModels' attribute");
        $this->assertArrayHasKey("myModels",$overview, "API response for method 'overview' missing 'myModels' attribute");
    }

    public function testSearch()
    {
        $results = EvidencioAPI::search("cancer");

        $this->assertArrayHasKey("id",$results[0], "API response for method 'search' missing 'id' attribute");
        $this->assertArrayHasKey("title",$results[0], "API response for method 'search' missing 'title' attribute");
        $this->assertArrayHasKey("variableSet",$results[0], "API response for method 'search' missing 'variableSet' attribute");
    }

    /* Omitting models() method test because it returns a huge amount of data */

    public function testGetModel()
    {
        $model = EvidencioAPI::getModel(576);

        $this->assertEquals($model["id"],576, "API response for method 'getModel' returned different model than requested");
        $this->assertArrayHasKey("title",$model, "API response for method 'getModel' missing 'title' attribute");
        $this->assertArrayHasKey("variables",$model, "API response for method 'getModel' missing 'title' attribute");
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

        $result = EvidencioAPI::run(170,$values);

        $this->assertEquals($result["id"],170, "API response for method 'run' returned result of different model than requested");
        $this->assertArrayHasKey("title",$result, "API response for method 'run' missing 'title' attribute");
        $this->assertArrayHasKey("result",$result, "API response for method 'run' missing 'result' attribute");

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
