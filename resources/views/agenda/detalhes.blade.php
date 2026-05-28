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
        <form method="POST" action="/agenda/agendamentos/{{ $agendamento->id }}">
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
    const select = document.getElementById('efetuou_pagamento_edit');
    const valorTotal = document.getElementById('valor_total_edit');
    let isUpdating = false;
    let manualBase = null;

    const inputsCalc = {
        valorHora: document.querySelector('input[name="valor_hora"]'),
        dataInicio: document.querySelector('input[name="data_inicio"]'),
        dataFim: document.querySelector('input[name="data_fim"]'),
        deslocamento: document.querySelector('input[name="deslocamento"]'),
        valorHoraExtra: document.querySelector('input[name="valor_hora_extra"]'),
        horaExtraFuncionario: document.querySelector('input[name="hora_extra_funcionario"]'),
    };

    function calcularBaseAutomatica() {
        const vh = Math.max(parseFloat(inputsCalc.valorHora?.value) || 0, 0);
        if (inputsCalc.dataInicio?.value && inputsCalc.dataFim?.value) {
            const inicio = new Date(inputsCalc.dataInicio.value);
            const fim = new Date(inputsCalc.dataFim.value);
            if (fim > inicio) {
                const diffMs = fim - inicio;
                const horas = Math.max(diffMs / (1000 * 60 * 60), 0);
                return vh * horas;
            }
        }
        return 0;
    }

    function calcularValorFinal() {
        if (select.value !== 'SIM' && !select.disabled) return;

        const desl = Math.max(parseFloat(inputsCalc.deslocamento?.value) || 0, 0);
        const he = Math.max(parseFloat(inputsCalc.horaExtraFuncionario?.value) || 0, 0);
        const vhe = Math.max(parseFloat(inputsCalc.valorHoraExtra?.value) || 0, 0);
        const base = manualBase !== null ? manualBase : calcularBaseAutomatica();
        const total = base + desl + (he * vhe);

        isUpdating = true;
        valorTotal.value = total > 0 ? total.toFixed(2) : '';
        isUpdating = false;
    }

    function toggleValorTotal() {
        manualBase = null;
        calcularValorFinal();
    }

    valorTotal.addEventListener('input', function () {
        if (isUpdating) return;
        manualBase = parseFloat(this.value) || 0;
        calcularValorFinal();
    });

    select.addEventListener('change', toggleValorTotal);
    Object.values(inputsCalc).forEach(function (el) {
        if (el) {
            el.addEventListener(el.type === 'datetime-local' ? 'change' : 'input', function () {
                if (manualBase === null) calcularValorFinal();
                else calcularValorFinal();
            });
        }
    });
});
</script>
@endpush
@endsection
