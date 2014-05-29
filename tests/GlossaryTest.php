<?php

require_once './init.php';

class PostGlossaryTest extends PHPUnit_Framework_TestCase
{
    private $key;
    private $secret;

    public function setUp()
    {
        $this->key = getenv('GENGO_PUBKEY');
        $this->secret = getenv('GENGO_PRIVKEY');
    }

    public function test_post_glossary()
    {
        $glossary_client = Gengo_Api::factory('glossary', $this->key, $this->secret);
        $glossary_client->postGlossary('tests/files/example_glossary.csv');

        $response_code = $glossary_client->getResponseCode();
        $body = $glossary_client->getResponseBody();
        $response_body = json_decode($body, true);

        $this->assertEquals(201, $response_code);
        $this->assertEquals($response_body['title'], 'example_glossary.csv');
        $this->assertGreaterThan(0, $response_body['id']);

        // test get of newly posted glossary
        $glossary_id = $response_body['id'];
        $this->_test_get_glossary_details($glossary_id);

        // put the glossary
        $this->_test_put_glossary($glossary_id);

        // delete the glossary
        $this->_test_delete_glossary($glossary_id);
    }


    private function _test_get_glossary_details($glossary_id)
    {
        $glossary_client = Gengo_Api::factory('glossary', $this->key, $this->secret);
        $glossary_client->getGlossaryDetails($glossary_id);

        $response_code = $glossary_client->getResponseCode();
        $body = $glossary_client->getResponseBody();
        $response_body = json_decode($body, true);

        $this->assertEquals(200, $response_code);
        $this->assertEquals($response_body['id'], $glossary_id);
    }


    private function _test_put_glossary($glossary_id)
    {
        $glossary_client = Gengo_Api::factory('glossary', $this->key, $this->secret);
        $glossary_client->putGlossary($glossary_id, 'tests/files/example_glossary.csv');

        $response_code = $glossary_client->getResponseCode();
        $body = $glossary_client->getResponseBody();
        $response_body = json_decode($body, true);

        $this->assertEquals(200, $response_code);
        $this->assertEquals($response_body['title'], 'example_glossary.csv');
        $this->assertEquals($glossary_id, $response_body['id']);
    }


    private function _test_delete_glossary($glossary_id)
    {
        $glossary_client = Gengo_Api::factory('glossary', $this->key, $this->secret);
        $glossary_client->deleteGlossary($glossary_id);

        $response_code = $glossary_client->getResponseCode();
        $body = $glossary_client->getResponseBody();
        $this->assertEquals(200, $response_code);
    }

}