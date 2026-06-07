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

<div id="modal-pagamento-pendente" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto modal-container">
    <div class="min-h-screen flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-2 sm:mx-4 p-4 sm:p-6">
            <button type="button" id="btn-modal-close-x" class="modal-close-x-btn">&times;</button>
            <div class="text-center mb-2">
                <p id="modal-titulo-pagamento" class="text-red-600 font-bold text-lg">O PAGAMENTO AINDA NÃO FOI EFETUADO</p>
            </div>
            <div class="mb-3">
                <span class="text-gray-500 text-xs">Cliente:</span>
                <strong id="modal-cliente-nome" class="text-gray-900 font-bold" style="text-transform: uppercase;"></strong>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">REGISTRAR PAGAMENTO</label>
                    <input type="text" id="input-valor-pago" inputmode="decimal"
                        class="w-full border rounded p-2 mt-1 text-sm text-right"
                        placeholder="0,00">
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex justify-between items-center">
                        <span>Data/Hora Início:</span>
                        <input type="text" id="display-data-hora-inicio" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field modal-date-input input-data-inicio"
                            placeholder="dd/mm/aaaa --:--" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Data/Hora Fim:</span>
                        <input type="text" id="display-data-hora-fim" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field modal-date-input input-data-fim"
                            placeholder="dd/mm/aaaa --:--" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Valor da Hora (R$):</span>
                        <input type="text" id="display-valor-hora" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field" />
                    </div>
                    <hr class="my-1">
                    <div class="flex justify-between items-center">
                        <span>Deslocamento:</span>
                        <input type="text" id="display-deslocamento" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Horas extras (Quantidade):</span>
                        <input type="text" id="display-horas-extras-qtd" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Valor Horas Extras (Por Hora):</span>
                        <input type="text" id="display-horas-extras-valor" readonly
                            class="font-semibold text-right bg-transparent border-none p-0 w-auto modal-input-field" />
                    </div>
                    <hr class="my-1">
                    <p class="text-base font-bold text-blue-900">Valor Total: <span id="display-total" class="text-lg">R$ 0,00</span></p>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-2">
                <button type="button" id="btn-cancelar-pagamento"
                    class="bg-gray-300 text-gray-700 px-4 py-3 sm:py-2 rounded text-sm touch-target w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="button" id="btn-editar-pagamento"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 sm:py-2 rounded text-sm font-semibold touch-target w-full sm:w-auto">
                    Editar
                </button>
                <button type="button" id="btn-salvar-pagamento"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-3 sm:py-2 rounded text-sm font-semibold touch-target w-full sm:w-auto">
                    Salvar e Concluir
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-salvar-edicao-active {
    background-color: #f97316 !important;
}
.modal-close-x-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
    z-index: 1;
}
.modal-close-x-btn:hover {
    color: #374151;
}
</style>

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
    let editavel = false;
    let idAtual = null;
    var snapshotsPorId = {};
    var salvosPorId = {};

    const modal = document.getElementById('modal-pagamento-pendente');
    const inputValorPago = document.getElementById('input-valor-pago');
    const displayDataHoraInicio = document.getElementById('display-data-hora-inicio');
    const displayDataHoraFim = document.getElementById('display-data-hora-fim');
    const displayValorHora = document.getElementById('display-valor-hora');
    const displayDeslocamento = document.getElementById('display-deslocamento');
    const displayHorasExtrasQtd = document.getElementById('display-horas-extras-qtd');
    const displayHorasExtrasValor = document.getElementById('display-horas-extras-valor');
    const displayTotal = document.getElementById('display-total');

    const allFields = [displayDataHoraInicio, displayDataHoraFim, displayValorHora,
                       displayDeslocamento, displayHorasExtrasQtd, displayHorasExtrasValor];

    const todosCampos = [inputValorPago, displayDataHoraInicio, displayDataHoraFim, displayValorHora,
                         displayDeslocamento, displayHorasExtrasQtd, displayHorasExtrasValor];

    function formatBrDateTime(isoStr) {
        if (!isoStr) return '';
        const d = new Date(isoStr);
        if (isNaN(d.getTime())) return isoStr;
        const pad = (n) => String(n).padStart(2, '0');
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function parseBrDateTimeToDate(str) {
        if (!str || !str.trim()) return null;
        const m = str.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
        if (m) return new Date(m[3], m[2] - 1, m[1], m[4], m[5]);
        return null;
    }

    function brDateTimeToIso(str) {
        if (!str || !str.trim()) return '';
        const m = str.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
        if (m) return m[3] + '-' + m[2] + '-' + m[1] + ' ' + m[4] + ':' + m[5] + ':00';
        return str;
    }

    function calcularHorasTrabalhadas() {
        const inicio = parseBrDateTimeToDate(displayDataHoraInicio.value);
        const fim = parseBrDateTimeToDate(displayDataHoraFim.value);
        if (!inicio || !fim || fim <= inicio) return 0;
        return (fim - inicio) / (1000 * 60 * 60);
    }

    function calcularValorTotalDoRegistro() {
        const vh = parseBrNumber(displayValorHora.value);
        const desl = parseBrNumber(displayDeslocamento.value);
        const qtd = parseBrNumber(displayHorasExtrasQtd.value);
        const vhe = parseBrNumber(displayHorasExtrasValor.value);
        const horasTrab = calcularHorasTrabalhadas();
        return (horasTrab * vh) + desl + (qtd * vhe);
    }

    function atualizarTotal() {
        const valorPago = parseBrNumber(inputValorPago.value);
        const total = calcularValorTotalDoRegistro() + valorPago;
        displayTotal.textContent = formatBr(total);
    }

    function aplicarEstadoReadonly(apenasLeitura) {
        allFields.forEach(function (el) {
            if (apenasLeitura) {
                el.readOnly = true;
                el.classList.remove('border', 'rounded', 'p-1', 'bg-white', 'flex-1', 'min-w-0', 'ml-2');
                el.classList.add('bg-transparent', 'border-none', 'p-0', 'w-auto');
            } else {
                el.readOnly = false;
                el.classList.remove('bg-transparent', 'border-none', 'p-0', 'w-auto');
                el.classList.add('border', 'rounded', 'p-1', 'bg-white', 'flex-1', 'min-w-0', 'ml-2');
            }
        });
    }

    function salvarSnapshot() {
        if (!idAtual) return;
        snapshotsPorId[idAtual] = {};
        allFields.forEach(function (el) {
            snapshotsPorId[idAtual][el.id] = el.value;
        });
    }

    function restaurarSnapshot() {
        if (!idAtual || !snapshotsPorId[idAtual]) return;
        const snap = snapshotsPorId[idAtual];
        allFields.forEach(function (el) {
            if (snap[el.id] !== undefined) {
                el.value = snap[el.id];
            }
        });
        atualizarTotal();
    }

    function limparEstadoPorId() {
        if (!idAtual) return;
        delete snapshotsPorId[idAtual];
        delete salvosPorId[idAtual];
    }

    function sincronizarCardFundo() {
        if (!formAtivo) return;
        const id = formAtivo.dataset.agendamentoId;
        if (!id) return;

        document.querySelectorAll('[data-card-info="data-' + id + '"]').forEach(function (el) {
            const raw = displayDataHoraInicio.value;
            const m = raw.match(/(\d{2}\/\d{2}\/\d{4})/);
            el.childNodes[1].textContent = m ? ' ' + m[1] : ' ' + raw;
        });
        document.querySelectorAll('[data-card-info="horario-' + id + '"]').forEach(function (el) {
            const rawInicio = displayDataHoraInicio.value;
            const rawFim = displayDataHoraFim.value;
            const mi = rawInicio.match(/(\d{2}:\d{2})/);
            const mf = rawFim.match(/(\d{2}:\d{2})/);
            var novoTxt = '';
            if (mi) novoTxt += mi[1];
            if (mf) novoTxt += ' às ' + mf[1];
            el.childNodes[1].textContent = novoTxt ? ' ' + novoTxt : '';
        });
        document.querySelectorAll('[data-card-info="valor-' + id + '"]').forEach(function (el) {
            const totalTxt = displayTotal.textContent.replace('Valor Total: ', '');
            el.childNodes[1].textContent = ' ' + totalTxt;
        });
    }

    function sincronizarComponentesIsolados(id, dados) {
        const viewDetalhe = document.getElementById('view-detalhe-container-' + id);
        if (viewDetalhe) {
            var periodoEl = viewDetalhe.querySelector('.txt-periodo');
            var totalEl = viewDetalhe.querySelector('.txt-total');
            if (periodoEl) periodoEl.textContent = dados.data_inicio + ' às ' + dados.data_fim;
            if (totalEl) totalEl.textContent = 'R$ ' + dados.valor_total;
        }
        const cardElemento = document.getElementById('card-agendamento-' + id);
        if (cardElemento) {
            var horarioEl = cardElemento.querySelector('.card-txt-horario');
            var valorEl = cardElemento.querySelector('.card-txt-valor');
            if (horarioEl) horarioEl.textContent = dados.data_inicio + ' às ' + dados.data_fim;
            if (valorEl) valorEl.textContent = 'R$ ' + dados.valor_total;
        }
        document.querySelectorAll('[data-card-info="data-' + id + '"]').forEach(function (el) {
            var child = el.childNodes[1];
            if (child) child.textContent = ' ' + dados.data_inicio_raw;
        });
        document.querySelectorAll('[data-card-info="horario-' + id + '"]').forEach(function (el) {
            var txt = dados.horario_inicio;
            if (dados.horario_fim) txt += ' às ' + dados.horario_fim;
            var child = el.childNodes[1];
            if (child) child.textContent = ' ' + txt;
        });
        document.querySelectorAll('[data-card-info="valor-' + id + '"]').forEach(function (el) {
            var child = el.childNodes[1];
            if (child) child.textContent = ' R$ ' + dados.valor_total;
        });
    }

    function toggleEditar() {
        editavel = !editavel;
        aplicarEstadoReadonly(!editavel);
        document.getElementById('btn-editar-pagamento').textContent = editavel ? 'Salvar edição' : 'Editar';
        document.getElementById('btn-editar-pagamento').classList.toggle('btn-salvar-edicao-active', editavel);
        if (!editavel) {
            const dadosEditados = {
                data_inicio: brDateTimeToIso(displayDataHoraInicio.value),
                data_fim: brDateTimeToIso(displayDataHoraFim.value),
                valor_hora: parseBrNumber(displayValorHora.value),
                deslocamento: parseBrNumber(displayDeslocamento.value),
                hora_extra_funcionario: parseBrNumber(displayHorasExtrasQtd.value),
                valor_hora_extra: parseBrNumber(displayHorasExtrasValor.value),
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                editavel = true;
                aplicarEstadoReadonly(false);
                document.getElementById('btn-editar-pagamento').textContent = 'Salvar edição';
                document.getElementById('btn-editar-pagamento').classList.add('btn-salvar-edicao-active');
                return;
            }

            fetch('/agenda/agendamentos/' + idAtual + '/atualizar-valores', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: JSON.stringify(dadosEditados)
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.sucesso) {
                    salvosPorId[idAtual] = true;
                    salvarSnapshot();
                    sincronizarComponentesIsolados(idAtual, data.registroAtualizado);
                }
            })
            .catch(function () {
                editavel = true;
                aplicarEstadoReadonly(false);
                document.getElementById('btn-editar-pagamento').textContent = 'Salvar edição';
                document.getElementById('btn-editar-pagamento').classList.add('btn-salvar-edicao-active');
                alert('Erro ao salvar os dados. Tente novamente.');
            });
        }
    }

    function abrirModal(form) {
        formAtivo = form;
        editavel = false;
        idAtual = form.dataset.agendamentoId || null;
        document.getElementById('btn-editar-pagamento').textContent = 'Editar';
        aplicarEstadoReadonly(true);

        const temSnapshot = idAtual && snapshotsPorId[idAtual];

        if (!temSnapshot) {
            const inicioRaw = form.dataset.dataHoraInicio || '';
            const fimRaw = form.dataset.dataHoraFim || '';
            const vh = parseFloat(form.dataset.valorHora) || 0;
            const desl = parseFloat(form.dataset.deslocamento) || 0;
            const qtd = parseFloat(form.dataset.horasExtrasQtd) || 0;
            const vhe = parseFloat(form.dataset.horasExtrasValor) || 0;

            displayDataHoraInicio.value = formatBrDateTime(inicioRaw);
            displayDataHoraFim.value = formatBrDateTime(fimRaw);
            displayValorHora.value = formatBr(vh);
            displayDeslocamento.value = formatBr(desl);
            displayHorasExtrasQtd.value = qtd.toFixed(2).replace('.', ',') + 'h';
            displayHorasExtrasValor.value = formatBr(vhe);
        } else {
            restaurarSnapshot();
        }

        document.getElementById('modal-cliente-nome').textContent = form.dataset.cliente || '';

        const titulo = document.getElementById('modal-titulo-pagamento');
        const btnSalvar = document.getElementById('btn-salvar-pagamento');
        if (form.dataset.efetuouPagamento === 'SIM') {
            titulo.textContent = 'Pagamento efetuado';
            titulo.className = 'text-green-600 font-bold text-lg';
            btnSalvar.textContent = 'Concluir';
        } else {
            titulo.textContent = 'O PAGAMENTO AINDA NÃO FOI EFETUADO';
            titulo.className = 'text-red-600 font-bold text-lg';
            btnSalvar.textContent = 'Salvar e Concluir';
        }

        inputValorPago.value = '';
        atualizarTotal();
        salvarSnapshot();
        modal.classList.remove('hidden');
    }

    function fecharModal() {
        if (!idAtual || !salvosPorId[idAtual]) {
            restaurarSnapshot();
        }
        sincronizarCardFundo();
        limparEstadoPorId();
        formAtivo = null;
        idAtual = null;
        modal.classList.add('hidden');
    }

    window.abrirModalPagamento = abrirModal;
    window.fecharModalPagamento = fecharModal;

    todosCampos.forEach(function (el) {
        el.addEventListener('input', atualizarTotal);
        el.addEventListener('change', atualizarTotal);
    });

    document.getElementById('btn-cancelar-pagamento').addEventListener('click', fecharModal);
    document.getElementById('btn-modal-close-x').addEventListener('click', fecharModal);
    document.getElementById('btn-editar-pagamento').addEventListener('click', toggleEditar);
    document.getElementById('btn-salvar-pagamento').addEventListener('click', function () {
        if (!formAtivo) return;

        const valorPago = parseBrNumber(inputValorPago.value);
        const total = calcularValorTotalDoRegistro() + valorPago;

        if (!displayDataHoraFim.value.trim()) {
            alert('O campo Data/Hora Fim é obrigatório para concluir o agendamento.');
            return;
        }

        const vh = parseBrNumber(displayValorHora.value);
        if (!vh || vh <= 0) {
            alert('O campo Valor da Hora é obrigatório e deve ser maior que zero para concluir o agendamento.');
            return;
        }

        function addHidden(name, value) {
            var h = document.createElement('input');
            h.type = 'hidden';
            h.name = name;
            h.value = value;
            formAtivo.appendChild(h);
        }

        addHidden('efetuou_pagamento', 'SIM');
        addHidden('valor_total', total.toFixed(2));
        addHidden('data_inicio', brDateTimeToIso(displayDataHoraInicio.value));
        addHidden('data_fim', brDateTimeToIso(displayDataHoraFim.value));
        addHidden('valor_hora', parseBrNumber(displayValorHora.value));
        addHidden('deslocamento', parseBrNumber(displayDeslocamento.value));
        addHidden('hora_extra_funcionario', parseBrNumber(displayHorasExtrasQtd.value));
        addHidden('valor_hora_extra', parseBrNumber(displayHorasExtrasValor.value));

        formAtivo.submit();
    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('input-data-inicio') || e.target.classList.contains('input-data-fim')) {
            var valor = e.target.value.replace(/\D/g, '');
            var tamanho = valor.length;

            if (e.target.type === 'text' && tamanho > 0) {
                if (tamanho <= 2) valor = valor;
                else if (tamanho <= 4) valor = valor.substring(0, 2) + '/' + valor.substring(2);
                else if (tamanho <= 8) valor = valor.substring(0, 2) + '/' + valor.substring(2, 4) + '/' + valor.substring(4);
                else if (tamanho <= 10) valor = valor.substring(0, 2) + '/' + valor.substring(2, 4) + '/' + valor.substring(4, 8) + ' ' + valor.substring(8);
                else valor = valor.substring(0, 2) + '/' + valor.substring(2, 4) + '/' + valor.substring(4, 8) + ' ' + valor.substring(8, 10) + ':' + valor.substring(10, 12);
                e.target.value = valor;
            }

            if (tamanho >= 12) {
                const inputInicio = document.getElementById('display-data-hora-inicio').value;
                const inputFim = document.getElementById('display-data-hora-fim').value;
                if (inputInicio && inputFim) {
                    const dataInicio = parseBrDateTimeToDate(inputInicio);
                    const dataFim = parseBrDateTimeToDate(inputFim);
                    if (dataInicio && dataFim && dataFim > dataInicio) {
                        atualizarTotal();
                    }
                }
            }
        }
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
