<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Alugueres</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        .info { text-align: center; font-size: 11px; color: #555; margin-bottom: 16px; }
        .info p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #1e3a5f; color: white; padding: 6px 4px; text-align: center; font-size: 9px; font-weight: bold; }
        td { padding: 4px; border-bottom: 1px solid #ddd; font-size: 9px; text-align: center; vertical-align: middle; }
        tr:nth-child(even) { background: #f9f9f9; }
        .total-geral { text-align: right; font-size: 11px; font-weight: bold; margin-top: 8px; padding-top: 8px; border-top: 2px solid #333; }
        .footer { text-align: center; font-size: 9px; color: #999; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>RELATÓRIO DE ALUGUEL</h1>
    <div class="info">
        <p>Gerado em {{ now()->format('d/m/Y H:i') }}</p>
        @if($filtroInicio || $filtroFim)
        <p>Período: {{ $filtroInicio ? \Carbon\Carbon::parse($filtroInicio)->format('d/m/Y') : '---' }} até {{ $filtroFim ? \Carbon\Carbon::parse($filtroFim)->format('d/m/Y') : '---' }}</p>
        @endif
        @if($veiculoFiltro)
        <p>Veículo: {{ $veiculoFiltro->placa }} {{ $veiculoFiltro->modelo ? '- '.$veiculoFiltro->modelo : '' }}</p>
        @endif
        <p>Total de alugueres: {{ count($registros) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Contato</th>
                <th>Data/Hora Início</th>
                <th>Data/Hora Fim</th>
                <th>Valor Hora (R$)</th>
                <th style="white-space: nowrap;">NF-C</th>
                <th>Motorista</th>
                <th>Veículo</th>
                <th>Deslocamento</th>
                <th style="text-align: center; vertical-align: middle;">Hora<br>Extra</th>
                <th>Efetuou Pagamento</th>
                <th>Valor Total (R$)</th>
                <th>Tipo de Serviço</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $r)
            <tr>
                <td>{{ $r->cliente }}</td>
                <td>{{ $r->contato }}</td>
                <td>{{ $r->data_inicio->format('d/m/Y H:i') }}</td>
                <td>{{ $r->data_fim ? $r->data_fim->format('d/m/Y H:i') : '---' }}</td>
                <td>{{ $r->valor_hora ? number_format($r->valor_hora, 2, ',', '.') : '0,00' }}</td>
                <td>{{ $r->nf_c ? 'Sim' : 'Não' }}</td>
                <td>{{ $r->motorista }}</td>
                <td>{{ $r->veiculo->placa ?? '---' }}</td>
                <td>{{ $r->deslocamento ? 'R$ '.number_format($r->deslocamento, 2, ',', '.') : '---' }}</td>
                <td style="text-align: center; vertical-align: middle;">
                    @if($r->hora_extra_funcionario || $r->valor_hora_extra)
                        {{ $r->hora_extra_funcionario ? number_format($r->hora_extra_funcionario, 1, ',', '.') . 'h' : '-' }}<br>
                        {{ $r->valor_hora_extra ? number_format($r->valor_hora_extra, 2, ',', '.') : '0,00' }}$
                    @else
                        ---
                    @endif
                </td>
                <td>{{ $r->efetuou_pagamento === 'SIM' ? 'Sim' : ($r->efetuou_pagamento === 'NAO' ? 'Não' : '---') }}</td>
                <td>{{ $r->valor_total ? number_format($r->valor_total, 2, ',', '.') : '0,00' }}</td>
                <td>{{ $r->tipo_servico }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align:center; padding:20px; color:#999;">Nenhum registro encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-geral">
        VALOR TOTAL DOS ALUGUÉIS: {{ number_format($totalValor, 2, ',', '.') }}
    </div>

    <div class="footer">
        Relatório gerado pelo Sistema de Agenda Muncks - POLIFERRO
    </div>
</body>
</html>