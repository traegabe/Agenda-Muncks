@extends('agenda.layouts.app')

@section('title', 'Detalhes - '.$agendamento->cliente)

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-900">{{ $agendamento->cliente }}</h1>
        <span class="px-3 py-1 rounded text-sm font-semibold
            @if($agendamento->status === 'agendado') bg-blue-100 text-blue-800
            @elseif($agendamento->status === 'pendente') bg-yellow-100 text-yellow-800
            @elseif($agendamento->status === 'concluido') bg-green-100 text-green-800
            @else bg-red-100 text-red-800 @endif">
            {{ ucfirst($agendamento->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Contato</p>
            <p class="font-semibold">{{ $agendamento->contato }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Período</p>
            <p class="font-semibold">{{ $agendamento->data_inicio->format('d/m/Y H:i') }}{{ $agendamento->data_fim ? ' às ' . $agendamento->data_fim->format('H:i') : ' (fim não definido)' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Valor da Hora</p>
            <p class="font-semibold">{{ $agendamento->valor_hora ? 'R$ '.number_format($agendamento->valor_hora, 2, ',', '.') : '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Valor Total</p>
            <p class="font-semibold">{{ $agendamento->valor_total ? 'R$ '.number_format($agendamento->valor_total, 2, ',', '.') : ($agendamento->efetuou_pagamento === 'NAO' ? 'R$ '.number_format($agendamento->calcularValorTotalBase(), 2, ',', '.') : '-') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">NF-C</p>
            <p class="font-semibold">{{ $agendamento->nf_c ? 'Sim' : 'Não' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Motorista</p>
            <p class="font-semibold">{{ $agendamento->motorista }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Veículo</p>
            <p class="font-semibold">{{ $agendamento->veiculo->placa ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Tipo de Serviço</p>
            <p class="font-semibold">{{ $agendamento->tipo_servico }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Deslocamento (R$)</p>
            <p class="font-semibold">{{ $agendamento->deslocamento ? 'R$ '.number_format($agendamento->deslocamento, 2, ',', '.') : '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Horas extras</p>
            <p class="font-semibold">{{ $agendamento->hora_extra_funcionario ? number_format($agendamento->hora_extra_funcionario, 1, ',', '.') : '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Valor horas extras</p>
            <p class="font-semibold">{{ $agendamento->valor_hora_extra ? 'R$ '.number_format($agendamento->valor_hora_extra, 2, ',', '.') : '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Efetuou pagamento</p>
            <p class="font-semibold">{{ $agendamento->efetuou_pagamento ?? '-' }}</p>
        </div>
        @if($agendamento->motivo_pendencia)
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Motivo da Pendência</p>
            <p class="font-semibold text-red-600">{{ $agendamento->motivo_pendencia }}</p>
        </div>
        @endif
    </div>

    <div class="text-xs text-gray-400 mb-6">
        <p>Lançado por: {{ $agendamento->criador->name ?? 'Sistema' }} em {{ $agendamento->created_at->format('d/m/Y H:i') }}</p>
        @if($agendamento->editor)
        <p>Editado por: {{ $agendamento->editor->name }} em {{ $agendamento->updated_at->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    @if($agendamento->status === 'concluido')
    <div class="border-t pt-6 mb-6">
        <h2 class="text-lg font-bold mb-4">Status de Pagamento</h2>
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-block px-3 py-1 rounded text-sm font-semibold
                    @if($agendamento->pago) bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $agendamento->pago ? 'Pago' : 'Não Pago' }}
                </span>
            </div>
            @if(!$agendamento->pago)
            <form method="POST" action="/agenda/agendamentos/{{ $agendamento->id }}/pagar">
                @csrf
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold">
                    💰 Marcar como Pago
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif

    <div class="border-t pt-6">
        <h2 class="text-lg font-bold mb-4">Editar Agendamento</h2>
        <form method="POST" action="/agenda/agendamentos/{{ $agendamento->id }}" data-status="{{ $agendamento->status }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cliente</label>
                    <input type="text" name="cliente" value="{{ $agendamento->cliente }}"
                        class="w-full border rounded p-2 mt-1 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contato</label>
                    <input type="text" name="contato" value="{{ $agendamento->contato }}"
                        class="w-full border rounded p-2 mt-1 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data/Hora Início</label>
                    <input type="datetime-local" name="data_inicio"
                        value="{{ $agendamento->data_inicio->format('Y-m-d\TH:i') }}"
                        class="w-full border rounded p-2 mt-1 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data/Hora Fim</label>
                    <input type="datetime-local" name="data_fim"
                        value="{{ $agendamento->data_fim ? $agendamento->data_fim->format('Y-m-d\TH:i') : '' }}"
                        class="w-full border rounded p-2 mt-1 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor da Hora (R$)</label>
                    <input type="number" step="0.01" name="valor_hora" value="{{ $agendamento->valor_hora }}"
                        class="w-full border rounded p-2 mt-1 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">NF-C</label>
                    <select name="nf_c" class="w-full border rounded p-2 mt-1 text-sm">
                        <option value="0" {{ !$agendamento->nf_c ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ $agendamento->nf_c ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Motorista</label>
                    <select name="motorista" class="w-full border rounded p-2 mt-1 text-sm">
                        <option value="">Selecione...</option>
                        <option value="Jefferson Gomes" {{ $agendamento->motorista === 'Jefferson Gomes' ? 'selected' : '' }}>Jefferson Gomes</option>
                        <option value="João Paulo" {{ $agendamento->motorista === 'João Paulo' ? 'selected' : '' }}>João Paulo</option>
                        <option value="Macilio Vieira" {{ $agendamento->motorista === 'Macilio Vieira' ? 'selected' : '' }}>Macilio Vieira</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Veículo</label>
                    <select name="veiculo_id" class="w-full border rounded p-2 mt-1 text-sm">
                        @foreach($veiculos as $v)
                        <option value="{{ $v->id }}" {{ $agendamento->veiculo_id == $v->id ? 'selected' : '' }}>
                            {{ $v->placa }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deslocamento (R$)</label>
                    <input type="number" step="0.01" min="0" name="deslocamento"
                        value="{{ $agendamento->deslocamento }}"
                        class="w-full border rounded p-2 mt-1 text-sm" placeholder="0,00">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Horas extras</label>
                        <input type="number" step="0.5" min="0" name="hora_extra_funcionario"
                            value="{{ $agendamento->hora_extra_funcionario }}"
                            class="w-full border rounded p-2 mt-1 text-sm" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor horas extras</label>
                        <input type="number" step="0.01" min="0" name="valor_hora_extra"
                            value="{{ $agendamento->valor_hora_extra }}"
                            class="w-full border rounded p-2 mt-1 text-sm" placeholder="0,00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Efetuou pagamento</label>
                    <select name="efetuou_pagamento" id="efetuou_pagamento_edit" class="w-full border rounded p-2 mt-1 text-sm" {{ $agendamento->status == 'concluido' ? 'disabled' : '' }}>
                        <option value="">Selecione...</option>
                        <option value="SIM" {{ $agendamento->efetuou_pagamento === 'SIM' ? 'selected' : '' }}>SIM</option>
                        <option value="NAO" {{ $agendamento->efetuou_pagamento === 'NAO' ? 'selected' : '' }}>NÃO</option>
                    </select>
                    @if($agendamento->status == 'concluido')
                    <input type="hidden" name="efetuou_pagamento" value="{{ $agendamento->efetuou_pagamento }}">
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor total</label>
                    <input type="number" step="0.01" name="valor_total" id="valor_total_edit"
                        value="{{ $agendamento->valor_total ? number_format($agendamento->valor_total, 2, '.', '') : '' }}"
                        {{ $agendamento->status != 'concluido' && $agendamento->efetuou_pagamento !== 'SIM' ? 'disabled' : '' }}
                        class="w-full border rounded p-2 mt-1 text-sm {{ $agendamento->status != 'concluido' && $agendamento->efetuou_pagamento !== 'SIM' ? 'bg-gray-100' : '' }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Tipo de Serviço</label>
                    <textarea name="tipo_servico" rows="2"
                        class="w-full border rounded p-2 mt-1 text-sm">{{ $agendamento->tipo_servico }}</textarea>
                </div>
            </div>

            <div class="flex justify-between">
                @if(in_array($agendamento->status, ['agendado', 'pendente']))
                <button type="button" onclick="confirmarCancelamento('/agenda/agendamentos/{{ $agendamento->id }}')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Cancelar Agendamento
                </button>
                @endif
                <button type="submit"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded text-sm">
                    Confirmar Edição
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function confirmarCancelamento(url) {
    if (confirm('Tem certeza que deseja cancelar o agendamento?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const origem = params.get('origem');

    const status = form.dataset.status;
    const isConcluido = status === 'concluido';
    const valorTotal = document.getElementById('valor_total_edit');
    const selectEfetuou = document.getElementById('efetuou_pagamento_edit');
    let manualBase = null;
    let isUpdating = false;

    function sanitize(val) {
        if (val === '' || val === null || val === undefined) return 0;
        let s = String(val).replace(/R\$/gi, '').trim();
        if (s.includes(',')) {
            s = s.replace(/\./g, '').replace(',', '.');
        }
        const num = parseFloat(s);
        return isNaN(num) ? 0 : Math.max(num, 0);
    }

    if (isConcluido) {
        const initialDesl = sanitize(document.querySelector('input[name="deslocamento"]')?.value);
        const initialHe = sanitize(document.querySelector('input[name="hora_extra_funcionario"]')?.value);
        const initialVhe = sanitize(document.querySelector('input[name="valor_hora_extra"]')?.value);
        const initialValorHora = sanitize(document.querySelector('input[name="valor_hora"]')?.value);
        const initialTotal = sanitize(valorTotal?.value);
        manualBase = Math.max(initialTotal - initialValorHora - initialDesl - (initialHe * initialVhe), 0);

        document.querySelectorAll([
            'input[name="cliente"]',
            'select[name="motorista"]',
            'select[name="veiculo_id"]',
        ].join(',')).forEach(function (el) {
            if (el) el.disabled = true;
        });

        form.addEventListener('submit', function () {
            form.querySelectorAll('[disabled]').forEach(function (el) {
                el.disabled = false;
            });
        });
    }

    if (origem === 'agendados' || origem === 'naopagos' || origem === 'concluidos') {
        function recalcular() {
            const valorHora = sanitize(document.querySelector('input[name="valor_hora"]')?.value);
            const desl = sanitize(document.querySelector('input[name="deslocamento"]')?.value);
            const he = sanitize(document.querySelector('input[name="hora_extra_funcionario"]')?.value);
            const vhe = sanitize(document.querySelector('input[name="valor_hora_extra"]')?.value);

            const dataInicio = document.querySelector('input[name="data_inicio"]')?.value;
            const dataFim = document.querySelector('input[name="data_fim"]')?.value;
            let horasPeriodo = 0;
            if (dataInicio && dataFim) {
                const inicio = new Date(dataInicio);
                const fim = new Date(dataFim);
                if (fim > inicio) {
                    horasPeriodo = (fim - inicio) / (1000 * 60 * 60);
                }
            }

            const total = (horasPeriodo * valorHora) + desl + (he * vhe);
            valorTotal.value = total.toFixed(2);
        }

        ['valor_hora', 'deslocamento', 'hora_extra_funcionario', 'valor_hora_extra'].forEach(function(name) {
            const el = document.querySelector('input[name="' + name + '"]');
            if (el) el.addEventListener('input', recalcular);
        });

        ['data_inicio', 'data_fim'].forEach(function(name) {
            const el = document.querySelector('input[name="' + name + '"]');
            if (el) el.addEventListener('change', recalcular);
        });
    }
});
</script>
@endpush
@endsection
