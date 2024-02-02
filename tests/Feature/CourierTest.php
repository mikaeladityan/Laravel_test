<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Courier;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourierTest extends TestCase
{
    use RefreshDatabase;

    public function testTheCouriersIsNull(): void
    {
        // Get Couriers view with get
        $response = $this->get('/couriers');

        // has status code page 404, cz Couriers is null
        $response->assertStatus(404);
        // and then see the text in this response
        $response->assertSeeText('Data Masih Kosong!');
    }

    public function testIfUserRequestSort()
    {
        // Create new Couriers
        Courier::factory(10)->create();
        // Get the route of Paginate
        $response = $this->get('/couriers?sort');
        // Check if the response success (status code 200)
        $response->assertOk();

        // Get data from json
        $data = $response->json();
        // Assert that data array not empty
        $this->assertNotEmpty($data['data']);
        // Assert that data length equal to 10
        $this->assertCount(10, $data['data']);
        // And check each item on data
        foreach ($data['data'] as $item) {
            $this->assertTrue(isset($item['id']) && isset($item['name']));
        }
    }
}
