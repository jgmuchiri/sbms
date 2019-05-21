<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RolesTest extends TestCase
{
    
    /**
     * Test table has data
     */
    public function test_database(){
        $this->assertDatabaseHas('roles', [
            'name' => 'admin'
        ]);
    }
    
    /**
     * Test main route
     *
     * @return void
     */
    public function test_routes()
    {
        $response = $this->get('/admin/roles');

        $response->actingAs('admin')->assertStatus(200);
    }
}
