<div id="aba-agendados">
    <div class="mb-4 flex gap-2">
        <button onclick="toggleFiltroAgendados()" id="btn-filtrar-agendados"
            class="px-3 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 touch-target">
            🔍 Filtrar
        </button>
    </div>

    <div id="filtro-agendados" class="hidden mb-4 p-3 bg-white rounded-lg shadow text-sm">
        <form method="GET" action="/agenda/dashboard" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="aba" value="agendados">
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
            <a href="/agenda/dashboard?aba=agendados"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1.5 rounded text-sm">
                Limpar
            </a>
            @endif
        </form>
    </div>

    <div class="space-y-3">
        @forelse($agendados as $a)
        @php
            $ehHoje = $a->data_inicio->isToday();
            $cor = $ehHoje ? 'border-orange-500' : 'border-blue-500';
            $bgCor = $ehHoje ? 'bg-orange-50' : 'bg-white';
            if (!$ehHoje && $a->data_inicio->isTomorrow()) {
                $cor = 'border-yellow-500';
                $bgCor = 'bg-yellow-50';
            } elseif (!$ehHoje && !$a->data_inicio->isTomorrow() && ($a->data_fim ? $a->data_fim->copy()->startOfDay() : $a->data_inicio->copy()->startOfDay())->lessThan(\Carbon\Carbon::now()->startOfDay())) {
                $cor = 'border-red-500';
                $bgCor = 'bg-red-50';
            }
        @endphp
        @include('agenda.partials.card-agendamento', [
            'agendamento' => $a,
            'origem' => 'agendados',
            'alertType' => 'prazo',
            'botaoTexto' => 'SERVIÇO CONCLUÍDO',
            'bgCor' => $bgCor,
            'cor' => $cor,
        ])
        @empty
        <p class="text-gray-500 text-center py-8">Nenhum agendamento ativo.</p>
        @endforelse
    </div>
</div>

<style>
.card-alerta-centralizado {
    width: 100%;
}
@media (max-width: 767px) {
    .card-alerta-centralizado {
        width: 100%;
        padding: 0.5rem 0;
    }
    .card-botao-acoes {
        width: 100%;
    }
    .card-botao-acoes form {
        width: 100%;
    }
}
</style>

@push('scripts')
<script>
function toggleFiltroAgendados() {
    const el = document.getElementById('filtro-agendados');
    el.classList.toggle('hidden');
}

document.querySelectorAll('#aba-agendados form[action*="fluxo-pagamento"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        window.abrirModalPagamento(form);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if ((params.get('aba') || 'agendados') === 'agendados' && (params.get('filtro_inicio') || params.get('filtro_fim'))) {
        const el = document.getElementById('filtro-agendados');
        if (el) el.classList.remove('hidden');
    }
});
</script>
@endpush
