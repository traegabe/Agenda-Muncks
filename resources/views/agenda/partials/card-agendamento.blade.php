@props([
    'agendamento' => null,
    'origem' => 'agendados',
    'alertType' => 'prazo',
    'botaoTexto' => 'SERVIÇO CONCLUÍDO',
    'bgCor' => 'bg-white',
    'cor' => 'border-blue-500',
])

@php
    $a = $agendamento;
    $dataInicioDia = $a->data_inicio->copy()->startOfDay();
    $dataFimDia = $a->data_fim ? $a->data_fim->copy()->startOfDay() : $dataInicioDia;
    $dataHoje = \Carbon\Carbon::now()->startOfDay();
    $estaAtrasado = $dataFimDia->lessThan($dataHoje);
    $ehAmanha = !$estaAtrasado && $dataFimDia->isTomorrow();
    $alertHoje = !$estaAtrasado && !$ehAmanha && $dataFimDia->isToday();
    $diasAtraso = $estaAtrasado ? $dataFimDia->diffInDays($dataHoje) : 0;
@endphp

<div onclick="window.location.href='/agenda/agendamentos/{{ $a->id }}?origem={{ $origem }}'" style="cursor: pointer;" class="{{ $bgCor }} rounded-lg shadow p-4 border-l-4 {{ $cor }} flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="flex-1 min-w-0">
        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-8">
            <div class="min-w-0">
                <h3 class="font-bold text-lg">{{ $a->cliente }}</h3>
                <div class="text-sm text-gray-600 mt-1 space-y-0.5">
                    <p><span class="text-gray-400">Contato:</span> {{ $a->contato }}</p>
                    <p data-card-info="data-{{ $a->id }}"><span class="text-gray-400">Data:</span> {{ $a->data_inicio->format('d/m/Y') }}</p>
                    <p data-card-info="horario-{{ $a->id }}"><span class="text-gray-400">Horário:</span> {{ $a->data_inicio->format('H:i') }}{{ $a->data_fim ? ' às '.$a->data_fim->format('H:i') : '' }}</p>
                    <p><span class="text-gray-400">Motorista:</span> {{ $a->motorista }}</p>
                    <p data-card-info="valor-{{ $a->id }}"><span class="text-gray-400">Valor:</span> R$ {{ $a->valor_total ? number_format($a->valor_total, 2, ',', '.') : ($a->calcularValorTotalBase() > 0 ? number_format($a->calcularValorTotalBase(), 2, ',', '.') : '0,00') }}</p>
                    <p class="{{ $origem === 'naopagos' ? 'text-xs mt-2' : '' }}"><span class="text-gray-400">Lançado por:</span> {{ $a->criador->name ?? 'Sistema' }}</p>
                </div>
            </div>
        </div>

        @if($estaAtrasado)
        <div class="card-alerta-centralizado mt-2">
            <span style="color: #ff0000; font-weight: bold; white-space: nowrap;">
                @if($alertType === 'prazo')
                    🔻 Atenção! O munck está com prazo atrasado {{ $diasAtraso }} {{ $diasAtraso == 1 ? 'dia' : 'dias' }} 🔻
                @else
                    ⚠️ Atenção! O pagamento está atrasado {{ $diasAtraso }} {{ $diasAtraso == 1 ? 'dia' : 'dias' }} ⚠️
                @endif
            </span>
        </div>
        @endif

        @if($alertHoje && $alertType === 'prazo')
        <div class="card-alerta-centralizado mt-2">
            <span style="color: #ff8c00; font-weight: bold; white-space: nowrap;">
                ⚠️ Atenção! O agendamento do Munck é para HOJE ⚠️
            </span>
        </div>
        @endif

        @if($ehAmanha && $alertType === 'prazo')
        <div class="card-alerta-centralizado mt-2">
            <span style="color: #ffa500; font-weight: bold; white-space: nowrap;">
                ⚠️ Atenção! O agendamento do Munck é para AMANHÃ ⚠️
            </span>
        </div>
        @endif
    </div>

    <div onclick="event.stopPropagation();" class="card-botao-acoes flex sm:flex-col gap-2 sm:min-w-[140px]">
        <form method="POST" action="/agenda/agendamentos/{{ $a->id }}/fluxo-pagamento"
            data-agendamento-id="{{ $a->id }}"
            data-efetuou-pagamento="{{ $a->efetuou_pagamento }}"
            data-data-inicio="{{ $a->data_inicio->format('Y-m-d') }}"
            data-deslocamento="{{ $a->deslocamento ?? 0 }}"
            data-horas-extras-qtd="{{ $a->hora_extra_funcionario ?? 0 }}"
            data-horas-extras-valor="{{ $a->valor_hora_extra ?? 0 }}"
            data-data-hora-inicio="{{ $a->data_inicio->format('Y-m-d\TH:i') }}"
            data-data-hora-fim="{{ $a->data_fim ? $a->data_fim->format('Y-m-d\TH:i') : '' }}"
            data-valor-hora="{{ $a->valor_hora ?? 0 }}"
            data-cliente="{{ $a->cliente }}">
            @csrf
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded text-sm font-semibold whitespace-nowrap touch-target">
                {{ $botaoTexto }}
            </button>
        </form>
    </div>
</div>
