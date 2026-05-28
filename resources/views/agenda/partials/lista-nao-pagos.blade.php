<div id="aba-naopagos" class="hidden">
    <div class="mb-4 flex gap-2">
        <button onclick="toggleFiltroNaoPagos()" id="btn-filtrar-naopagos"
            class="px-3 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 touch-target">
            🔍 Filtrar
        </button>
        <a href="{{ route('agenda.relatorio-pdf', array_merge(['aba' => 'naopagos'], request()->only(['filtro_inicio', 'filtro_fim', 'veiculo_id']))) }}"
            class="px-3 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 touch-target">
            📄 Gerar PDF
        </a>
    </div>

    <div id="filtro-naopagos" class="hidden mb-4 p-3 bg-white rounded-lg shadow text-sm">
        <form method="GET" action="/agenda/dashboard" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="aba" value="naopagos">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Data Início</label>
                <input type="date" name="filtro_inicio" value="{{ request('filtro_inicio') }}"
                    class="border rounded p-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Data Fim</label>
                <input type="date" name="filtro_fim" value="{{ request('filtro_fim') }}"
                    class="border rounded p-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Veículo</label>
                <select name="veiculo_id" class="border rounded p-1.5 text-sm">
                    <option value="">Todos</option>
                    @foreach($veiculos as $v)
                    <option value="{{ $v->id }}" {{ request('veiculo_id') == $v->id ? 'selected' : '' }}>{{ $v->placa }} {{ $v->modelo ? '- '.$v->modelo : '' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-1.5 rounded text-sm">
                Aplicar Filtro
            </button>
            @if(request('filtro_inicio') || request('filtro_fim') || request('veiculo_id'))
            <a href="/agenda/dashboard?aba=naopagos"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1.5 rounded text-sm">
                Limpar
            </a>
            @endif
        </form>
    </div>

    <div class="space-y-3">
        @forelse($naoPagos as $a)
        @php
            $inicio = $a->data_inicio;
            $fim = $a->data_fim;
        @endphp
        <div onclick="window.location.href='/agenda/agendamentos/{{ $a->id }}?origem=naopagos'" style="cursor: pointer;" class="bg-red-50 rounded-lg shadow p-4 border-l-4 border-red-500 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center sm:gap-8">
                <div class="min-w-0">
                    <h3 class="font-bold text-lg">{{ $a->cliente }}</h3>
                    <div class="text-sm text-gray-600 mt-1 space-y-0.5">
                        <p><span class="text-gray-400">Contato:</span> {{ $a->contato }}</p>
                        <p><span class="text-gray-400">Data:</span> {{ $inicio->format('d/m/Y') }}</p>
                        <p><span class="text-gray-400">Horário:</span> {{ $inicio->format('H:i') }}{{ $fim ? ' às '.$fim->format('H:i') : '' }}</p>
                        <p><span class="text-gray-400">Motorista:</span> {{ $a->motorista }}</p>
                        <p><span class="text-gray-400">Valor:</span> R$ {{ $a->valor_total ? number_format($a->valor_total, 2, ',', '.') : '0,00' }}</p>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Lançado por: {{ $a->criador->name ?? 'Sistema' }}</p>
                </div>
                @php
                    $dataAgendamento = $a->data_inicio->copy()->startOfDay();
                    $dataHoje = \Carbon\Carbon::now()->startOfDay();
                @endphp
                @if($dataAgendamento->isPast())
                <div class="sm:flex-shrink-0">
                    @php
                        $diasAtraso = $dataAgendamento->diffInDays($dataHoje);
                    @endphp
                    <span style="color: #ff0000; font-weight: bold; white-space: nowrap;">
                        ⚠️ Atenção! O pagamento está atrasado {{ $diasAtraso }} {{ $diasAtraso == 1 ? 'dia' : 'dias' }} ⚠️
                    </span>
                </div>
                @endif
            </div>
            <div onclick="event.stopPropagation();" class="sm:min-w-[180px]" x-data="{ step: 'button' }">
                <template x-if="step === 'button'">
                    <button @click="step = 'form'"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded text-sm font-semibold touch-target">
                        💰 Registrar Pagamento
                    </button>
                </template>
                <template x-if="step === 'form'">
                    <form method="POST" action="/agenda/agendamentos/{{ $a->id }}/pagar" class="flex flex-col gap-2">
                        @csrf
                        <input type="number" name="valor_recebido" step="0.01" min="0" required
                            placeholder="Valor recebido"
                            class="w-full border rounded p-2 text-sm">
                        <div class="flex gap-2">
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded text-sm font-semibold touch-target">
                                Confirmar
                            </button>
                            <button type="button" @click="step = 'button'"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-3 rounded text-sm touch-target">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">Nenhum serviço com pagamento pendente.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function toggleFiltroNaoPagos() {
    const el = document.getElementById('filtro-naopagos');
    el.classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if ((params.get('aba') || 'agendados') === 'naopagos' && (params.get('filtro_inicio') || params.get('filtro_fim'))) {
        const el = document.getElementById('filtro-naopagos');
        if (el) el.classList.remove('hidden');
    }
});
</script>
@endpush
