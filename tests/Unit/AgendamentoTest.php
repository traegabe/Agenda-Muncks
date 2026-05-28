<?php

namespace Tests\Unit;

use App\Models\Agenda\Agendamento;
use App\Models\Agenda\Veiculo;
use App\Models\User;
use Tests\TestCase;

class AgendamentoTest extends TestCase
{
    public function test_veiculo_relationship(): void
    {
        $agendamento = new Agendamento;
        $relation = $agendamento->veiculo();

        $this->assertInstanceOf(Veiculo::class, $relation->getRelated());
        $this->assertEquals('veiculo_id', $relation->getForeignKeyName());
    }

    public function test_criador_relationship(): void
    {
        $agendamento = new Agendamento;
        $relation = $agendamento->criador();

        $this->assertInstanceOf(User::class, $relation->getRelated());
        $this->assertEquals('created_by', $relation->getForeignKeyName());
    }

    public function test_editor_relationship(): void
    {
        $agendamento = new Agendamento;
        $relation = $agendamento->editor();

        $this->assertInstanceOf(User::class, $relation->getRelated());
        $this->assertEquals('updated_by', $relation->getForeignKeyName());
    }

    public function test_fillable_contains_all_fields(): void
    {
        $agendamento = new Agendamento;
        $fillable = $agendamento->getFillable();

        $this->assertContains('cliente', $fillable);
        $this->assertContains('contato', $fillable);
        $this->assertContains('data_inicio', $fillable);
        $this->assertContains('data_fim', $fillable);
        $this->assertContains('veiculo_id', $fillable);
        $this->assertContains('valor_hora', $fillable);
        $this->assertContains('valor_total', $fillable);
        $this->assertContains('motorista', $fillable);
        $this->assertContains('tipo_servico', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('pago', $fillable);
    }

    public function test_connection_is_mysql_agenda(): void
    {
        $agendamento = new Agendamento;

        $this->assertEquals('mysql_agenda', $agendamento->getConnectionName());
    }

    public function test_scope_agendados_filters_by_status(): void
    {
        $query = Agendamento::agendados();
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('where "status" = ?', $sql);
        $this->assertStringContainsString('order by "data_inicio" asc', $sql);
        $this->assertContains('agendado', $bindings);
    }

    public function test_scope_concluidos_filters_by_status(): void
    {
        $query = Agendamento::concluidos();
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('where "status" = ?', $sql);
        $this->assertStringContainsString('order by "data_inicio" desc', $sql);
        $this->assertContains('concluido', $bindings);
    }

    public function test_casts_are_defined(): void
    {
        $agendamento = new Agendamento;
        $casts = $agendamento->getCasts();

        $this->assertArrayHasKey('data_inicio', $casts);
        $this->assertArrayHasKey('data_fim', $casts);
        $this->assertArrayHasKey('nf_c', $casts);
        $this->assertArrayHasKey('pago', $casts);
        $this->assertEquals('datetime', $casts['data_inicio']);
        $this->assertEquals('boolean', $casts['nf_c']);
        $this->assertEquals('boolean', $casts['pago']);
    }
}
