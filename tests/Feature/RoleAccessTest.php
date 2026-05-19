<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_employees_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get(route('employees.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_employees_index()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get(route('employees.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_employees_create()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get(route('employees.create'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_attendances_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get(route('attendances.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_attendances_index()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get(route('attendances.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_attendances_create()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get(route('attendances.create'));
        $response->assertStatus(403);
    }
}
