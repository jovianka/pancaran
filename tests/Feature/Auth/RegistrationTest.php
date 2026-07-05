<?php

namespace Tests\Feature\Auth;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $faculty = Faculty::factory()->create();
        $major = Major::factory()->create(['faculty_id' => $faculty->id]);

        $response = $this->post('/register', [
            'nim' => '2308561123',
            'name' => 'Test User',
            'email' => 'test@student.unud.ac.id',
            'password' => 'password',
            'password_confirmation' => 'password',
            'faculty_id' => $faculty->id,
            'major_id' => $major->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
