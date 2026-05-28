<?php

namespace Tests\Unit;

use App\Models\Agenda\Agendamento;
use Carbon\Carbon;
use Tests\TestCase;

class AgendamentoCalculoTest extends TestCase
{
    public function test_calcula_base_zero_quando_sem_valores(): void
    {
        $a = new Agendamento;

        $this->assertSame(0.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_valor_hora_e_data_fim(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 100);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 18:00:00'));

        $this->assertSame(1000.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_deslocamento(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 100);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 18:00:00'));
        $a->setAttribute('deslocamento', 50);

        $this->assertSame(1050.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_horas_extras(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 100);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 18:00:00'));
        $a->setAttribute('hora_extra_funcionario', 3);
        $a->setAttribute('valor_hora_extra', 50);

        $this->assertSame(1150.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_todos_componentes(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 100);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 18:00:00'));
        $a->setAttribute('deslocamento', 50);
        $a->setAttribute('hora_extra_funcionario', 2);
        $a->setAttribute('valor_hora_extra', 40);

        $this->assertSame(1130.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_sem_data_fim_retorna_sem_horista(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 100);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('deslocamento', 30);

        $this->assertSame(30.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_horas_extras_sem_base_horista(): void
    {
        $a = new Agendamento;
        $a->setAttribute('deslocamento', 20);
        $a->setAttribute('hora_extra_funcionario', 4);
        $a->setAttribute('valor_hora_extra', 60);

        $this->assertSame(260.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_deslocamento_nulo_nao_quebra(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 50);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 12:00:00'));

        $this->assertSame(200.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_hora_extra_nulo_nao_quebra(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 50);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 12:00:00'));
        $a->setAttribute('deslocamento', 30);

        $this->assertSame(230.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_com_valor_hora_nulo(): void
    {
        $a = new Agendamento;
        $a->setAttribute('deslocamento', 100);
        $a->setAttribute('hora_extra_funcionario', 2);
        $a->setAttribute('valor_hora_extra', 50);

        $this->assertSame(200.0, $a->calcularValorTotalBase());
    }

    public function test_calcula_base_retorna_float(): void
    {
        $a = new Agendamento;
        $a->setAttribute('valor_hora', 75.5);
        $a->setAttribute('data_inicio', Carbon::parse('2026-01-01 08:00:00'));
        $a->setAttribute('data_fim', Carbon::parse('2026-01-01 17:00:00'));

        $resultado = $a->calcularValorTotalBase();

        $this->assertIsFloat($resultado);
        $this->assertSame(679.5, $resultado);
    }
}
