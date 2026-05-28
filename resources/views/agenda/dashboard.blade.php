@extends('agenda.layouts.app')

@section('title', 'Painel - Agenda Muncks')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="w-full sm:w-auto">
        <div class="grid grid-cols-3 sm:flex sm:gap-2 gap-2">
            <button onclick="mostrarAba('agendados')" id="tab-agendados-btn"
                class="px-4 py-3 sm:py-2 rounded-lg text-sm font-semibold bg-blue-900 text-white touch-target">Agendados</button>
            <button onclick="mostrarAba('naopagos')" id="tab-naopagos-btn"
                class="px-4 py-3 sm:py-2 rounded-lg text-sm font-semibold bg-gray-300 text-gray-700 hover:bg-gray-400 touch-target">Não Pagos</button>
            <button onclick="mostrarAba('concluidos')" id="tab-concluidos-btn"
                class="px-4 py-3 sm:py-2 rounded-lg text-sm font-semibold bg-gray-300 text-gray-700 hover:bg-gray-400 touch-target">Concluídos</button>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('modal-agendar').classList.remove('hidden')"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold touch-target w-full sm:w-auto whitespace-nowrap">
            + Agendar Aluguel
        </button>
    </div>
</div>

@include('agenda.partials.lista-agendados', ['agendados' => $agendados])
@include('agenda.partials.lista-nao-pagos', ['naoPagos' => $naoPagos])
@include('agenda.partials.lista-concluidos', ['concluidos' => $concluidos])
@include('agenda.partials.modal-agendar', ['veiculos' => $veiculos])

<div id="modal-pagamento-pendente" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
    <div class="min-h-screen flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-2 sm:mx-4 p-4 sm:p-6">
            <div class="text-center mb-4">
                <p class="text-red-600 font-bold text-lg">O PAGAMENTO AINDA NÃO FOI EFETUADO</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">REGISTRAR PAGAMENTO</label>
                    <input type="text" id="input-valor-pago" inputmode="decimal"
                        class="w-full border rounded p-2 mt-1 text-sm text-right"
                        placeholder="0,00">
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>Deslocamento: <span id="display-deslocamento" class="font-semibold">R$ 0,00</span></p>
                    <p>Horas extras (Quantidade): <span id="display-horas-extras-qtd" class="font-semibold">0,00h</span></p>
                    <p>Valor Horas Extras (Por Hora): <span id="display-horas-extras-valor" class="font-semibold">R$ 0,00</span></p>
                    <hr class="my-1">
                    <p class="text-base font-bold text-blue-900">Valor Total: <span id="display-total" class="text-lg">R$ 0,00</span></p>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-2">
                <button type="button" id="btn-cancelar-pagamento"
                    class="bg-gray-300 text-gray-700 px-4 py-3 sm:py-2 rounded text-sm touch-target w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="button" id="btn-salvar-pagamento"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-3 sm:py-2 rounded text-sm font-semibold touch-target w-full sm:w-auto">
                    Salvar e Concluir
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarAba(aba) {
    document.querySelectorAll('[id^="aba-"]').forEach(el => el.classList.add('hidden'));
    document.getElementById('aba-' + aba).classList.remove('hidden');
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('bg-blue-900', 'text-white');
        el.classList.add('bg-gray-300', 'text-gray-700');
    });
    const btn = document.getElementById('tab-' + aba + '-btn');
    btn.classList.remove('bg-gray-300', 'text-gray-700');
    btn.classList.add('bg-blue-900', 'text-white');
}

function parseBrNumber(v) {
    if (!v) return 0;
    const s = String(v).replace(/[^0-9,\-]/g, '').replace('.', '').replace(',', '.').replace(/[^0-9.\-]/g, '');
    return parseFloat(s) || 0;
}

function formatBr(n) {
    return 'R$ ' + n.toFixed(2).replace('.', ',');
}

(function () {
    let formAtivo = null;
    let deslocamentoAtual = 0;
    let horasExtrasQtdAtual = 0;
    let horasExtrasValorAtual = 0;

    const modal = document.getElementById('modal-pagamento-pendente');
    const inputValorPago = document.getElementById('input-valor-pago');
    const displayDeslocamento = document.getElementById('display-deslocamento');
    const displayHorasExtrasQtd = document.getElementById('display-horas-extras-qtd');
    const displayHorasExtrasValor = document.getElementById('display-horas-extras-valor');
    const displayTotal = document.getElementById('display-total');

    function atualizarTotal() {
        const valorPago = parseBrNumber(inputValorPago.value);
        const total = valorPago + deslocamentoAtual + (horasExtrasQtdAtual * horasExtrasValorAtual);
        displayTotal.textContent = formatBr(total);
    }

    function abrirModal(form) {
        formAtivo = form;
        deslocamentoAtual = parseFloat(form.dataset.deslocamento) || 0;
        horasExtrasQtdAtual = parseFloat(form.dataset.horasExtrasQtd) || 0;
        horasExtrasValorAtual = parseFloat(form.dataset.horasExtrasValor) || 0;
        displayDeslocamento.textContent = formatBr(deslocamentoAtual);
        displayHorasExtrasQtd.textContent = horasExtrasQtdAtual.toFixed(2).replace('.', ',') + 'h';
        displayHorasExtrasValor.textContent = formatBr(horasExtrasValorAtual);
        inputValorPago.value = '';
        atualizarTotal();
        modal.classList.remove('hidden');
    }

    function fecharModal() {
        formAtivo = null;
        modal.classList.add('hidden');
    }

    window.abrirModalPagamento = abrirModal;
    window.fecharModalPagamento = fecharModal;

    inputValorPago.addEventListener('input', atualizarTotal);
    document.getElementById('btn-cancelar-pagamento').addEventListener('click', fecharModal);
    document.getElementById('btn-salvar-pagamento').addEventListener('click', function () {
        if (!formAtivo) return;
        const valorPago = parseBrNumber(inputValorPago.value);
        const total = valorPago + deslocamentoAtual + (horasExtrasQtdAtual * horasExtrasValorAtual);

        const hf1 = document.createElement('input');
        hf1.type = 'hidden';
        hf1.name = 'efetuou_pagamento';
        hf1.value = 'SIM';
        formAtivo.appendChild(hf1);

        const hf2 = document.createElement('input');
        hf2.type = 'hidden';
        hf2.name = 'valor_total';
        hf2.value = total.toFixed(2);
        formAtivo.appendChild(hf2);

        formAtivo.submit();
    });
})();

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const aba = params.get('aba') || 'agendados';
    mostrarAba(aba);
});
</script>
@endpush
@endsection
