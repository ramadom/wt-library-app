<?php

namespace WT\Library\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    private const API_URL = 'http://localhost:7778/api/book';

    private $client;

    #[\Override]
    protected function setUp(): void {
      $this->client = static::createClient();
    }

    #[\Override]
    protected function tearDown(): void {

        parent::tearDown();
    }

    public function testCreateBook() {

        $book = [
            'title' => 'Symfony for dummies',
            'ISBN' => '123456789000',
            'author' => 'Unknown',
            'publicationDate' => '2001-01-01',
            'genre' => "Computers",
            'copies' => 10
        ];

        $this->client->request('POST', self::API_URL . '/', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($book));

        $json = $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Response: $json");

        $res = json_decode($json, true);
        $data = $res['data'];

        $this->assertArrayHasKey('id', $data, "Response: $json");

        return ['book' => $data];
    }

    /**
     * @depends testCreateBook
     */
    public function testGetBook($test) {

        $book = $test['book'];
        $bookId = $book['id'];

        $this->client->request('GET', self::API_URL . '/' . $bookId);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = $this->client->getResponse()->getContent();
        $res = json_decode($json, true);
        $data = $res['data'];

        //TODO check all fields
        $this->assertArrayHasKey('title', $data, "Response: $json");
        $this->assertEquals($data['title'], $book['title']);

        return ['book' => $data];
    }

    /**
     * @depends testGetBook
     */
    public function testGetNonExistingBook($test) {

        $bookId = 99999; // Bad ID

        $this->client->request('GET', self::API_URL . '/' . $bookId);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateSecondBook() {

        $book = [
            'title' => 'Programming basics',
            'author' => 'Unknown',
            'ISBN' => '000123456789',
            'publicationDate' => '2010-10-10',
            'genre' => "Computers",
            'copies' => 50
        ];

        $this->client->request('POST', self::API_URL . '/', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($book));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = $this->client->getResponse()->getContent();
        $res = json_decode($json, true);
        $data = $res['data'];

        $this->assertArrayHasKey('id', $data, "Response: $json");

        return ['book' => $data];
    }

    /**
     * @depends testCreateSecondBook
     */
    public function testGetTwoBooks($test) {

        $count = 2;

        $this->client->request('GET', self::API_URL . '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = $this->client->getResponse()->getContent();
        $res = json_decode($json, true);
        $data = $res['data'];

        $this->assertCount($count, $data);
    }

    /**
     * @depends testGetBook
     */
    public function testUpdateBook($test) {

        $book = $test['book'];
        $bookId = $book['id'];

        // Change publication date
        $book['publicationDate'] = "2002-02-02";

        $this->client->request('PUT', self::API_URL . '/' . $bookId, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($book));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = $this->client->getResponse()->getContent();
        $res = json_decode($json, true);
        $data = $res['data'];
        $this->assertArrayHasKey('publicationDate', $data, "Response: $json");
        $this->assertEquals($data['publicationDate'], $book['publicationDate']);

        return ['book' => $data];
    }

    /**
     * @depends testUpdateBook
     */
    public function testDeleteNonExistingBook($test) {

        $bookId = 99999; // Bad ID

        $this->client->request('DELETE', self::API_URL . '/' . $bookId);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @depends testUpdateBook
     */
    public function testDeleteBook($test) {

        $book = $test['book'];
        $bookId = $book['id'];

        $this->client->request('DELETE', self::API_URL . '/' . $bookId);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the book is deleted
        $this->client->request('GET', self::API_URL . '/' . $bookId);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

}