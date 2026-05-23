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

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const aba = params.get('aba') || 'agendados';
    mostrarAba(aba);
});
</script>
@endpush
@endsection
