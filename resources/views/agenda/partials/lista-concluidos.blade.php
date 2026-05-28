<div id="aba-concluidos" class="hidden">
    <div class="mb-4 flex gap-2">
        <button onclick="toggleFiltro()" id="btn-filtrar"
            class="px-3 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 touch-target">
            🔍 Filtrar
        </button>
        <a href="{{ route('agenda.relatorio-pdf', array_merge(['aba' => 'concluidos'], request()->only(['filtro_inicio', 'filtro_fim', 'veiculo_id']))) }}"
            class="px-3 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 touch-target">
            📄 Gerar PDF
        </a>
    </div>

    <div id="filtro-concluidos" class="hidden mb-4 p-3 bg-white rounded-lg shadow text-sm">
        <form method="GET" action="/agenda/dashboard" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="aba" value="concluidos">
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
            <a href="/agenda/dashboard"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1.5 rounded text-sm">
                Limpar
            </a>
            @endif
        </form>
    </div>

    <div class="space-y-3">
        @forelse($concluidos as $a)
        @php
            $inicio = $a->data_inicio;
            $fim = $a->data_fim;
            $cor = 'border-green-500';
            if ($a->efetuou_pagamento === 'NAO') {
                $cor = 'border-red-500';
            }
        @endphp
        <div onclick="window.location.href='/agenda/agendamentos/{{ $a->id }}'" style="cursor: pointer;" class="block">
            <div class="bg-green-50 rounded-lg shadow p-4 border-l-4 {{ $cor }} flex items-center gap-3">
                <div class="flex-1 min-w-0">
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
                <span class="inline-block px-3 py-1 rounded text-xs font-semibold whitespace-nowrap
                    @if($a->pago) bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $a->pago ? 'Pago' : 'Não Pago' }}
                </span>
                <form method="POST" action="/agenda/agendamentos/{{ $a->id }}/excluir" onclick="event.stopPropagation();" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este registro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">Nenhum serviço concluído.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function toggleFiltro() {
    const el = document.getElementById('filtro-concluidos');
    el.classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if ((params.get('aba') || 'agendados') === 'concluidos' && (params.get('filtro_inicio') || params.get('filtro_fim'))) {
        const el = document.getElementById('filtro-concluidos');
        if (el) el.classList.remove('hidden');
    }
});
</script>
@endpush
