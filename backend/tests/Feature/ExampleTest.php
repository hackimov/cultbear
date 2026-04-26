<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_payment_redirect_pages_are_available(): void
    {
        $this->get('/payment/success')->assertOk()->assertSee('Оплата успешно принята', false);
        $this->get('/payment/failed')->assertOk()->assertSee('Оплата не завершена', false);
    }
}
