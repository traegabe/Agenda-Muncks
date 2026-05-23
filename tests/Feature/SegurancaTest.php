<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SegurancaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/0001_01_01_000000_create_users_table.php',
                '--realpath' => true,
                '--force' => true,
            ]);
        }
    }

    public function test_rota_dashboard_sem_auth_redireciona_para_login(): void
    {
        $response = $this->get('/agenda/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/agenda/login');
    }

    public function test_rota_calendario_sem_auth_redireciona_para_login(): void
    {
        $response = $this->get('/agenda/calendario');

        $response->assertStatus(302);
        $response->assertRedirect('/agenda/login');
    }

    public function test_rota_relatorio_pdf_sem_auth_redireciona_para_login(): void
    {
        $response = $this->get('/agenda/relatorio-pdf');

        $response->assertStatus(302);
        $response->assertRedirect('/agenda/login');
    }

    public function test_dashboard_com_auth_retorna_200(): void
    {
        $user = User::create([
            'name' => 'Teste',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123'),
        ]);

        $response = $this->actingAs($user)->get('/agenda/dashboard');

        $response->assertStatus(200);
    }

    public function test_calendario_com_auth_retorna_200(): void
    {
        $user = User::create([
            'name' => 'Teste',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123'),
        ]);

        $response = $this->actingAs($user)->get('/agenda/calendario');

        $response->assertStatus(200);
    }

    public function test_model_user_esconde_password_da_serializacao(): void
    {
        $user = new User;
        $user->password = 'secret';
        $user->remember_token = 'token123';

        $json = $user->toArray();

        $this->assertArrayNotHasKey('password', $json);
        $this->assertArrayNotHasKey('remember_token', $json);
    }

    public function test_model_user_senha_esta_hash_no_banco(): void
    {
        $user = User::create([
            'name' => 'Teste',
            'email' => 'teste@teste.com',
            'password' => 'Poli@2575',
        ]);

        $this->assertNotSame('Poli@2575', $user->password);
        $this->assertTrue(Hash::check('Poli@2575', $user->password));
    }
}
