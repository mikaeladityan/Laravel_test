<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
