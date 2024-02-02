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

    public function testPaginateMustHas10Records()
    {
        // Looping  11 times so it will create 11 records on table `couriers` with name=Courier_1-11
        for ($i = 1; $i <= 11; $i++) {
            Courier::factory()->create([
                'name' => 'Courier_' . $i,
            ]);
        }
        Courier::orderBy('created_at', 'desc')->paginate(10);
        $response = $this->get('/couriers?page=1');
        $response->assertJsonFragment(['per_page' => 10]);
        $response->assertJsonCount(10, 'data');
        $response->assertSee('Courier_10');
        $response->assertStatus(200);
    }

    public function testEditValidationErrorRedirectBack()
    {
        // Create Factory
        $courier = Courier::factory()->create();

        //  Send Patch Request With Empty Value and false velue
        $response = $this->put('/couriers/' . $courier->id, [
            'name' => '',
            'driver_license' => '/%23*&2'
        ]);

        // Assertion that Redirect Back After Submit Form Validation Errors.
        $response->assertStatus(302);
        $response->assertInvalid(['name', 'driver_license']);
    }

    public function testEditCouriersContainsCorrectValues()
    {
        $courier = Courier::factory()->create();

        $response = $this->get('/couriers/' . $courier->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee($courier->name, false);
        $response->assertSee($courier->delivery_status, false);
    }

    public function testDeleteCourierSuccessByTheIdVelue()
    {
        $courier = Courier::factory()->create();

        $response = $this->delete('/couriers/' . $courier->id);

        $response->assertStatus(302);
        $response->assertRedirect('couriers');

        $this->assertDatabaseMissing('couriers', $courier->toArray());
    }
}
