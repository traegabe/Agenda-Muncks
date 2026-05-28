<?php

namespace Tests\Unit;

use App\Models\Agenda\Agendamento;
use App\Models\Agenda\Veiculo;
use Tests\TestCase;

class VeiculoTest extends TestCase
{
    public function test_fillable_contains_all_fields(): void
    {
        $veiculo = new Veiculo;
        $fillable = $veiculo->getFillable();

        $this->assertContains('placa', $fillable);
        $this->assertContains('modelo', $fillable);
        $this->assertContains('marca', $fillable);
    }

    public function test_agendamentos_relationship(): void
    {
        $veiculo = new Veiculo;
        $relation = $veiculo->agendamentos();

        $this->assertInstanceOf(Agendamento::class, $relation->getRelated());
    }

    public function test_connection_is_mysql_agenda(): void
    {
        $veiculo = new Veiculo;

        $this->assertEquals('mysql_agenda', $veiculo->getConnectionName());
    }
}
