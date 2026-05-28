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
            } elseif (!$ehHoje && $a->efetuou_pagamento === 'NAO') {
                $cor = 'border-red-500';
                $bgCor = 'bg-red-50';
            }
        @endphp
        <div onclick="window.location.href='/agenda/agendamentos/{{ $a->id }}?origem=agendados'" style="cursor: pointer;" class="{{ $bgCor }} rounded-lg shadow p-4 border-l-4 {{ $cor }} flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center sm:gap-8">
                <div class="min-w-0">
                    <h3 class="font-bold text-lg">{{ $a->cliente }}</h3>
                    <div class="text-sm text-gray-600 mt-1 space-y-0.5">
                        <p><span class="text-gray-400">Contato:</span> {{ $a->contato }}</p>
                        <p><span class="text-gray-400">Data:</span> {{ $a->data_inicio->format('d/m/Y') }}</p>
                        <p><span class="text-gray-400">Horário:</span> {{ $a->data_inicio->format('H:i') }}{{ $a->data_fim ? ' às '.$a->data_fim->format('H:i') : '' }}</p>
                        <p><span class="text-gray-400">Motorista:</span> {{ $a->motorista }}</p>
                        <p><span class="text-gray-400">Valor:</span> R$ {{ $a->valor_total ? number_format($a->valor_total, 2, ',', '.') : ($a->calcularValorTotalBase() > 0 ? number_format($a->calcularValorTotalBase(), 2, ',', '.') : '0,00') }}</p>
                        <p><span class="text-gray-400">Lançado por:</span> {{ $a->criador->name ?? 'Sistema' }}</p>
                    </div>
                </div>
                @php
                    $dataAgendamento = $a->data_inicio->copy()->startOfDay();
                    $dataHoje = \Carbon\Carbon::now()->startOfDay();
                @endphp
                @if($dataAgendamento->isToday())
                <div class="sm:flex-shrink-0">
                    <span class="card-alerta-hoje" style="color: #ff6600; font-weight: bold; white-space: nowrap;">
                        🔴 Atenção! O agendamento do Munck é <strong>HOJE!</strong> 🔴
                    </span>
                </div>
                @elseif($dataAgendamento->isTomorrow())
                <div class="sm:flex-shrink-0">
                    <span class="card-alerta-hoje" style="color: #ff6600; font-weight: bold; white-space: nowrap;">
                        ⚠️ Atenção! O agendamento do Munck é para <strong>AMANHÃ</strong> ⚠️
                    </span>
                </div>
                @elseif($dataAgendamento->isPast())
                <div class="sm:flex-shrink-0">
                    @php
                        $diasAtraso = $dataAgendamento->diffInDays($dataHoje);
                    @endphp
                    <span class="card-alerta-hoje" style="color: #ff0000; font-weight: bold; white-space: nowrap;">
                        🔻 Atenção! O munck está com prazo atrasado {{ $diasAtraso }} {{ $diasAtraso == 1 ? 'dia' : 'dias' }} 🔻
                    </span>
                </div>
                @endif
            </div>
            <div onclick="event.stopPropagation();" class="card-botao-acoes flex sm:flex-col gap-2 sm:min-w-[140px]">
                <form method="POST" action="/agenda/agendamentos/{{ $a->id }}/fluxo-pagamento"
                    data-efetuou-pagamento="{{ $a->efetuou_pagamento }}"
                    data-data-inicio="{{ $a->data_inicio->format('Y-m-d') }}"
                    data-deslocamento="{{ $a->deslocamento ?? 0 }}"
                    data-horas-extras-qtd="{{ $a->hora_extra_funcionario ?? 0 }}"
                    data-horas-extras-valor="{{ $a->valor_hora_extra ?? 0 }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded text-sm font-semibold whitespace-nowrap touch-target">
                        SERVIÇO CONCLUÍDO
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">Nenhum agendamento ativo.</p>
        @endforelse
    </div>
</div>

<style>
@media (max-width: 767px) {
    .card-alerta-hoje {
        white-space: nowrap !important;
        font-size: 0.85rem !important;
        display: block;
        text-align: center;
        width: 100%;
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
        if (form.dataset.efetuouPagamento !== 'NAO') return;
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        const dataAgendamento = new Date(form.dataset.dataInicio + 'T00:00:00');
        if (dataAgendamento >= hoje) {
            e.preventDefault();
            window.abrirModalPagamento(form);
        }
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
