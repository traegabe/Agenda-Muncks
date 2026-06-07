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
        @include('agenda.partials.card-agendamento', [
            'agendamento' => $a,
            'origem' => 'naopagos',
            'alertType' => 'pagamento',
            'botaoTexto' => '💰 Registrar Pagamento',
            'bgCor' => 'bg-red-50',
            'cor' => 'border-red-500',
        ])
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

document.querySelectorAll('#aba-naopagos form[action*="fluxo-pagamento"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        window.abrirModalPagamento(form);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if ((params.get('aba') || 'agendados') === 'naopagos' && (params.get('filtro_inicio') || params.get('filtro_fim'))) {
        const el = document.getElementById('filtro-naopagos');
        if (el) el.classList.remove('hidden');
    }
});
</script>
@endpush
