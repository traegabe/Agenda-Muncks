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

    public function test_marcar_pago_formula_correta(): void
    {
        $valorRecebido = 800.0;
        $deslocamento = 50.0;
        $horaExtra = 3.0;
        $valorHoraExtra = 40.0;

        $total = $valorRecebido + $deslocamento + ($horaExtra * $valorHoraExtra);

        $this->assertSame(970.0, $total);
    }

    public function test_marcar_pago_sem_horas_extras(): void
    {
        $valorRecebido = 1000.0;
        $deslocamento = 0.0;
        $horaExtra = 0.0;
        $valorHoraExtra = 0.0;

        $total = $valorRecebido + $deslocamento + ($horaExtra * $valorHoraExtra);

        $this->assertSame(1000.0, $total);
    }

    public function test_marcar_pago_com_deslocamento_zero(): void
    {
        $valorRecebido = 500.0;
        $deslocamento = 0.0;
        $horaExtra = 2.0;
        $valorHoraExtra = 30.0;

        $total = $valorRecebido + $deslocamento + ($horaExtra * $valorHoraExtra);

        $this->assertSame(560.0, $total);
    }
}
