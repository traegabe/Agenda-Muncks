<?php

namespace App\Models\Agenda;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $connection = 'mysql_agenda';
    protected $fillable = [
        'cliente', 'contato', 'data_inicio', 'data_fim',
        'valor_hora', 'valor_total', 'nf_c', 'motorista',
        'veiculo_id', 'tipo_servico', 'deslocamento', 'hora_extra_funcionario', 'valor_hora_extra',
        'efetuou_pagamento',
        'status', 'servico_concluido', 'motivo_pendencia', 'pago', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
            'nf_c' => 'boolean',
            'pago' => 'boolean',
            'valor_hora' => 'decimal:2',
            'valor_total' => 'decimal:2',
            'deslocamento' => 'decimal:2',
            'hora_extra_funcionario' => 'decimal:2',
            'valor_hora_extra' => 'decimal:2',
        ];
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function calcularValorTotalBase(): float
    {
        if ($this->valor_hora && $this->data_fim) {
            $horas = $this->data_inicio->diffInHours($this->data_fim);
            $horas = $horas > 0 ? $horas : 1;
            $base = $this->valor_hora * $horas;
        } else {
            $base = 0;
        }

        return $base
            + (float)($this->deslocamento ?? 0)
            + ((float)($this->hora_extra_funcionario ?? 0) * (float)($this->valor_hora_extra ?? 0));
    }

    public function scopeAgendados($query)
    {
        return $query->where('status', 'agendado')->orderBy('data_inicio');
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', 'concluido')->orderByDesc('data_inicio');
    }

    public function scopeNaoPagos($query)
    {
        return $query->where(function ($q) {
            $q->where('efetuou_pagamento', 'NAO')
              ->orWhere('status', 'nao_pago')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'concluido')->where('pago', false);
              });
        })->orderByDesc('data_inicio');
    }
}
