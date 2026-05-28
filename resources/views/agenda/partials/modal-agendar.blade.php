<div id="modal-agendar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
    <div class="min-h-screen flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg sm:text-xl font-bold text-blue-900">Novo Agendamento</h2>
                <button onclick="document.getElementById('modal-agendar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-2xl touch-target">&times;</button>
            </div>

            <form method="POST" action="/agenda/agendamentos" autocomplete="off">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <input type="text" name="cliente" required
                            class="w-full border rounded p-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contato</label>
                        <input type="text" name="contato" id="contato" required inputmode="numeric"
                            class="w-full border rounded p-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data/Hora Início</label>
                        <input type="datetime-local" name="data_inicio" id="data_inicio" required autocomplete="off" autocorrect="off" spellcheck="false"
                            class="w-full border rounded p-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data/Hora Fim</label>
                        <input type="datetime-local" name="data_fim" id="data_fim" autocomplete="off" autocorrect="off" spellcheck="false"
                            class="w-full border rounded p-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor da Hora (R$)</label>
                        <input type="number" step="0.01" name="valor_hora"
                            class="w-full border rounded p-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NF-C</label>
                        <select name="nf_c" class="w-full border rounded p-2 mt-1 text-sm">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Motorista</label>
                        <select name="motorista" required class="w-full border rounded p-2 mt-1 text-sm">
                            <option value="">Selecione...</option>
                            <option value="Jefferson Gomes">Jefferson Gomes</option>
                            <option value="João Paulo">João Paulo</option>
                            <option value="Macilio Vieira">Macilio Vieira</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Veículo</label>
                        <select name="veiculo_id" required class="w-full border rounded p-2 mt-1 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($veiculos as $v)
                            <option value="{{ $v->id }}">{{ $v->placa }} {{ $v->modelo ? '- '.$v->modelo : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deslocamento (R$)</label>
                        <input type="number" step="0.01" min="0" name="deslocamento"
                            class="w-full border rounded p-2 mt-1 text-sm" placeholder="0,00">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Horas extras</label>
                            <input type="number" step="0.5" min="0" name="hora_extra_funcionario"
                                class="w-full border rounded p-2 mt-1 text-sm" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor horas extras</label>
                            <input type="number" step="0.01" min="0" name="valor_hora_extra"
                                class="w-full border rounded p-2 mt-1 text-sm" placeholder="0,00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Efetuou pagamento</label>
                        <select name="efetuou_pagamento" id="efetuou_pagamento" required class="w-full border rounded p-2 mt-1 text-sm">
                            <option value="">Selecione...</option>
                            <option value="SIM">SIM</option>
                            <option value="NAO">NÃO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor total</label>
                        <input type="number" step="0.01" name="valor_total" id="valor_total" disabled
                            class="w-full border rounded p-2 mt-1 text-sm bg-gray-100">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tipo de Serviço</label>
                        <textarea name="tipo_servico" required rows="2"
                            class="w-full border rounded p-2 mt-1 text-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-2">
                    <button type="button"
                        onclick="document.getElementById('modal-agendar').classList.add('hidden')"
                        class="bg-gray-300 text-gray-700 px-4 py-3 sm:py-2 rounded text-sm touch-target w-full sm:w-auto">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-3 sm:py-2 rounded text-sm touch-target w-full sm:w-auto">
                        Confirmar Agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function isMobile() {
    return window.innerWidth < 640 || 'ontouchstart' in window;
}

document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('efetuou_pagamento');
    const valorTotal = document.getElementById('valor_total');
    let isUpdating = false;
    let manualBase = null;

    const inputsCalc = {
        valorHora: document.querySelector('input[name="valor_hora"]'),
        dataInicio: document.getElementById('data_inicio'),
        dataFim: document.getElementById('data_fim'),
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
        if (!select.value) {
            valorTotal.disabled = true;
            valorTotal.classList.add('bg-gray-100');
            return;
        }

        valorTotal.disabled = false;
        valorTotal.classList.remove('bg-gray-100');

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
    toggleValorTotal();

    if (isMobile()) {
        const contato = document.getElementById('contato');
        if (contato) {
            contato.addEventListener('keydown', function (e) {
                const allowed = [
                    'Backspace', 'Delete', 'Tab',
                    'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                    'Home', 'End'
                ];
                if (allowed.includes(e.key)) return;
                if (e.ctrlKey || e.metaKey) return;
                if (e.key >= '0' && e.key <= '9') return;
                if (e.key === 'v' && (e.ctrlKey || e.metaKey)) return;
                e.preventDefault();
            });

            contato.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        }
    }
});
</script>
@endpush
