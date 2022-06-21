<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;


class MainTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_main_end_point()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // success...
    public function test_xml_to_json_endpoint()
    {   
        // endpoint call...
        $response = $this->get("/api/v1/xmlToJson?url=https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_1.xml"); 

        // assertStatus should http status be 200 (OK)
        $response->assertStatus(200);

        $response->assertJsonMissing([
            'error'
        ]);

    }

    // failed request (error)
    public function test_failed_request()
    {
        // endpoint call...
        $response = $this->get("/api/v1/xmlToJson?url=https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/fed_1.xml"); 

        // assertStatus should http status be 500 (Error)
        $response->assertStatus(500);

        // check body of response...
        $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('error')
        );

        // structure..
        $response->assertJsonStructure([]);
    }

    // bad request...
    public function test_bad_request()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])
        ->get("/api/v1/xmlToJson?url="); 

        // assertStatus should http status be 500 (Error)
        $response->assertStatus(422);

        $response->assertJsonFragment(['message' => 'The url field is required.']);        
    }
}
