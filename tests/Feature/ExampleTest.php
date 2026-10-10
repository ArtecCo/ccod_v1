<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_redirects_guests_to_authentication(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }
}
