@extends('agenda.layouts.app')

@section('title', 'Calendário - Agenda Muncks')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-blue-900 mb-6">Calendário de Agendamentos</h1>
    <div id="calendar"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        events: [
            @foreach($eventos as $e)
            @php
                $corBg = $e->status === 'pendente' ? '#EAB308' : '#3B82F6';
                $corBorda = $e->status === 'pendente' ? '#CA8A04' : '#2563EB';
                if ($e->data_inicio->isTomorrow()) {
                    $corBg = '#EAB308';
                    $corBorda = '#CA8A04';
                }
            @endphp
            {
                title: '{{ $e->cliente }}',
                start: '{{ $e->data_inicio->format('Y-m-d\TH:i:s') }}',
                @if($e->data_fim)
                end: '{{ $e->data_fim->format('Y-m-d\TH:i:s') }}',
                @endif
                url: '/agenda/agendamentos/{{ $e->id }}',
                backgroundColor: '{{ $corBg }}',
                borderColor: '{{ $corBorda }}',
                textColor: '#ffffff'
            },
            @endforeach
        ],
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        }
    });
    calendar.render();
});
</script>
@endpush
