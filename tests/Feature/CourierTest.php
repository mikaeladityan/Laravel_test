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

    public function testIfUserRequestSearch()
    {
        Courier::factory(10)->create();
        // Route with Get Method of Couriers with request search
        $response = $this->get('/couriers?search=');
        $response->assertStatus(200)->assertOk();
    }

    public function testIfUserRequestLevel()
    {
        Courier::factory(10)->create();
        // Route with Get Method of Couriers with request level
        $response = $this->get('/couriers?level=2');
        $response->assertStatus(200)->assertOk();
    }

    public function testCreateCourierWithInstanceModel(): void
    {
        // Created new couriers model instance
        $couriers = Courier::create([
            'name' => 'Mikael Aditya N',
            'driver_license' => '12365445665445',
            'phone' => '12365445665445',
            'address' => 'Surabaya',
        ]);

        $response = $this->get('/couriers');
        $response->assertStatus(200);
        // Check in the view the data has input before
        $response->assertSee($couriers['name']);
        $response->assertSee($couriers['driver_license']);
        $response->assertSee($couriers['phone']);
        $response->assertSee($couriers['address']);
        $response->assertSee($couriers['level']);
        $response->assertSee($couriers['active']);
        $response->assertStatus(200)->assertDontSee('Not Found');

        // Check if in Database has the same value as we sent to server
        $this->assertDatabaseHas('couriers', [
            'name' => 'Mikael Aditya N',
            'driver_license' => '12365445665445',
            'phone' => '12365445665445',
            'address' => 'Surabaya',
        ]);
    }

    public function testCreateCourierWithPostMethod(): void
    {
        // send the post with value of database
        $courier = [
            'name' => 'Mikael Aditya N',
            'driver_license' => '12332112332112',
            'phone' => '12332112332112',
            'address' => 'Surabaya',
        ];
        $response = $this->post('/couriers', $courier);
        // Check if in Database has the same value as we sent to server
        $this->assertDatabaseHas('couriers', $courier);
    }
}
