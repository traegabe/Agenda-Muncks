<?php

namespace Tests\Feature;

use App\Models\Agenda\Agendamento;
use App\Models\Agenda\Veiculo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AgendamentoCrudTest extends TestCase
{
    private User $user;
    private Veiculo $veiculo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--force' => true]);

        if (!Schema::connection('mysql_agenda')->hasTable('users')) {
            Schema::connection('mysql_agenda')->create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        $this->user = User::factory()->create();
        $this->veiculo = Veiculo::create([
            'placa' => 'TEST-101',
            'modelo' => 'Teste',
            'marca' => 'Marca Teste',
        ]);
    }

    public function test_store_creates_agendamento(): void
    {
        $data = [
            'cliente' => 'Empresa Teste',
            'contato' => '11999999999',
            'data_inicio' => '2026-06-01 08:00:00',
            'data_fim' => '2026-06-01 18:00:00',
            'valor_hora' => '100.00',
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste de criação',
            'efetuou_pagamento' => 'NAO',
        ];

        $response = $this->actingAs($this->user)->post('/agenda/agendamentos', $data);

        $response->assertRedirect('/agenda/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('agendamentos', [
            'cliente' => 'Empresa Teste',
            'veiculo_id' => $this->veiculo->id,
            'status' => 'agendado',
        ], 'mysql_agenda');
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post('/agenda/agendamentos', []);

        $response->assertSessionHasErrors(['cliente', 'contato', 'data_inicio', 'motorista', 'veiculo_id', 'tipo_servico', 'efetuou_pagamento']);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post('/agenda/agendamentos', [
            'cliente' => 'Teste',
            'contato' => '11999999999',
            'data_inicio' => '2026-06-01 08:00:00',
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste',
            'efetuou_pagamento' => 'NAO',
        ]);

        $response->assertRedirect('/agenda/login');
    }

    public function test_show_displays_agendamento(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->get("/agenda/agendamentos/{$agendamento->id}");

        $response->assertStatus(200);
        $response->assertSee($agendamento->cliente);
    }

    public function test_show_requires_authentication(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->get("/agenda/agendamentos/{$agendamento->id}");

        $response->assertRedirect('/agenda/login');
    }

    public function test_update_modifies_agendamento(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->put("/agenda/agendamentos/{$agendamento->id}", [
            'cliente' => 'Empresa Editada',
            'contato' => '11988888888',
            'data_inicio' => '2026-06-15 09:00:00',
            'data_fim' => '2026-06-15 17:00:00',
            'valor_hora' => '120.00',
            'motorista' => 'João Paulo',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste de edição',
            'efetuou_pagamento' => 'SIM',
        ]);

        $response->assertRedirect('/agenda/dashboard');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertEquals('Empresa Editada', $agendamento->cliente);
        $this->assertEquals('João Paulo', $agendamento->motorista);
    }

    public function test_destroy_cancels_agendamento(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->delete("/agenda/agendamentos/{$agendamento->id}");

        $response->assertRedirect('/agenda/dashboard');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertEquals('cancelado', $agendamento->status);
    }

    public function test_excluir_deletes_agendamento(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->delete("/agenda/agendamentos/{$agendamento->id}/excluir");

        $response->assertRedirect('/agenda/dashboard?aba=concluidos');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('agendamentos', [
            'id' => $agendamento->id,
        ], 'mysql_agenda');
    }

    public function test_marcar_pago_updates_values(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->post("/agenda/agendamentos/{$agendamento->id}/pagar", [
            'valor_recebido' => 1500.00,
        ]);

        $response->assertRedirect('/agenda/dashboard?aba=concluidos');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertTrue($agendamento->pago);
        $this->assertEquals('concluido', $agendamento->status);
        $this->assertEquals('SIM', $agendamento->efetuou_pagamento);
    }

    public function test_fluxo_pagamento_com_pagamento_sim(): void
    {
        $agendamento = Agendamento::create([
            'cliente' => 'Fluxo Test',
            'contato' => '11977777777',
            'data_inicio' => Carbon::parse('2026-06-10 08:00:00'),
            'data_fim' => Carbon::parse('2026-06-10 18:00:00'),
            'valor_hora' => 100,
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste fluxo',
            'efetuou_pagamento' => 'SIM',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post("/agenda/agendamentos/{$agendamento->id}/fluxo-pagamento");

        $response->assertRedirect('/agenda/dashboard?aba=concluidos');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertEquals('SIM', $agendamento->servico_concluido);
        $this->assertEquals('concluido', $agendamento->status);
        $this->assertTrue($agendamento->pago);
        $this->assertEquals(1000.0, $agendamento->valor_total);
    }

    public function test_fluxo_pagamento_com_pagamento_nao(): void
    {
        $agendamento = Agendamento::create([
            'cliente' => 'Fluxo Test NAO',
            'contato' => '11966666666',
            'data_inicio' => Carbon::parse('2026-06-10 08:00:00'),
            'data_fim' => Carbon::parse('2026-06-10 18:00:00'),
            'valor_hora' => 100,
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste fluxo',
            'efetuou_pagamento' => 'NAO',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post("/agenda/agendamentos/{$agendamento->id}/fluxo-pagamento");

        $response->assertRedirect('/agenda/dashboard?aba=naopagos');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertEquals('SIM', $agendamento->servico_concluido);
        $this->assertEquals('nao_pago', $agendamento->status);
        $this->assertFalse($agendamento->pago);
    }

    public function test_update_status_to_concluido(): void
    {
        $agendamento = $this->criarAgendamento();

        $response = $this->actingAs($this->user)->post("/agenda/agendamentos/{$agendamento->id}/status", [
            'status' => 'concluido',
        ]);

        $response->assertRedirect('/agenda/dashboard');
        $response->assertSessionHas('success');

        $agendamento->refresh();
        $this->assertEquals('concluido', $agendamento->status);
    }

    public function test_calendario_requires_auth(): void
    {
        $response = $this->get('/agenda/calendario');

        $response->assertRedirect('/agenda/login');
    }

    public function test_calendario_com_auth_retorna_200(): void
    {
        $response = $this->actingAs($this->user)->get('/agenda/calendario');

        $response->assertStatus(200);
    }

    public function test_store_detecta_conflito(): void
    {
        $this->criarAgendamento();

        $data = [
            'cliente' => 'Conflitante',
            'contato' => '11955555555',
            'data_inicio' => '2026-06-01 10:00:00',
            'data_fim' => '2026-06-01 12:00:00',
            'valor_hora' => '100.00',
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste conflito',
            'efetuou_pagamento' => 'NAO',
        ];

        $response = $this->actingAs($this->user)->post('/agenda/agendamentos', $data);

        $response->assertSessionHasErrors('conflito');
    }

    public function test_relatorio_pdf_naopagos(): void
    {
        $this->criarAgendamento();

        $response = $this->actingAs($this->user)->get('/agenda/relatorio-pdf?aba=naopagos');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_relatorio_pdf_concluidos(): void
    {
        $this->criarAgendamento();

        $response = $this->actingAs($this->user)->get('/agenda/relatorio-pdf?aba=concluidos');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_dashboard_returns_view_with_data(): void
    {
        $this->criarAgendamento();

        $response = $this->actingAs($this->user)->get('/agenda/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Empresa Teste');
    }

    public function test_dashboard_com_filtros(): void
    {
        $this->criarAgendamento();

        $response = $this->actingAs($this->user)->get('/agenda/dashboard?aba=agendados&filtro_inicio=2026-06-01&filtro_fim=2026-06-30');

        $response->assertStatus(200);
        $response->assertSee('Empresa Teste');
    }

    private function criarAgendamento(): Agendamento
    {
        return Agendamento::create([
            'cliente' => 'Empresa Teste',
            'contato' => '11999999999',
            'data_inicio' => Carbon::parse('2026-06-01 08:00:00'),
            'data_fim' => Carbon::parse('2026-06-01 18:00:00'),
            'valor_hora' => 100.00,
            'motorista' => 'Jefferson Gomes',
            'veiculo_id' => $this->veiculo->id,
            'tipo_servico' => 'Teste automacao',
            'efetuou_pagamento' => 'NAO',
            'status' => 'agendado',
            'created_by' => $this->user->id,
        ]);
    }
}
