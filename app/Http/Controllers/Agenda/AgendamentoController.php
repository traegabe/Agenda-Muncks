<?php

namespace App\Http\Controllers\Agenda;

use App\Http\Controllers\Controller;
use App\Models\Agenda\Agendamento;
use App\Models\Agenda\Veiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $abaAtiva = $request->query('aba', 'agendados');

        $agendados = Agendamento::agendados()->with('veiculo', 'criador');
        if ($abaAtiva === 'agendados') {
            if ($request->filled('filtro_inicio')) $agendados->where('data_inicio', '>=', $request->filtro_inicio . ' 00:00:00');
            if ($request->filled('filtro_fim')) $agendados->where('data_fim', '<=', $request->filtro_fim . ' 23:59:59');
            if ($request->filled('veiculo_id')) $agendados->where('veiculo_id', $request->veiculo_id);
        }
        $agendados = $agendados->get();

        $concluidos = Agendamento::concluidos()->with('veiculo', 'criador');
        if ($abaAtiva === 'concluidos') {
            if ($request->filled('filtro_inicio')) $concluidos->where('data_inicio', '>=', $request->filtro_inicio . ' 00:00:00');
            if ($request->filled('filtro_fim')) $concluidos->where('data_fim', '<=', $request->filtro_fim . ' 23:59:59');
            if ($request->filled('veiculo_id')) $concluidos->where('veiculo_id', $request->veiculo_id);
        }
        $concluidos = $concluidos->get();

        $naoPagos = Agendamento::naoPagos()->with('veiculo', 'criador');
        if ($abaAtiva === 'naopagos') {
            if ($request->filled('filtro_inicio')) $naoPagos->where('data_inicio', '>=', $request->filtro_inicio . ' 00:00:00');
            if ($request->filled('filtro_fim')) $naoPagos->where('data_fim', '<=', $request->filtro_fim . ' 23:59:59');
            if ($request->filled('veiculo_id')) $naoPagos->where('veiculo_id', $request->veiculo_id);
        }
        $naoPagos = $naoPagos->get();
        $veiculos = Veiculo::all();

        return view('agenda.dashboard', compact('agendados', 'concluidos', 'naoPagos', 'veiculos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente' => 'required|string|max:200',
            'contato' => 'required|string|max:50',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
            'valor_hora' => 'nullable|numeric|min:0',
            'valor_total' => 'nullable|numeric|min:0',
            'nf_c' => 'boolean',
            'motorista' => 'required|string|max:200',
            'veiculo_id' => 'required|exists:mysql_agenda.veiculos,id',
            'tipo_servico' => 'required|string',
            'deslocamento' => 'nullable|numeric|min:0',
            'hora_extra_funcionario' => 'nullable|numeric|min:0',
            'valor_hora_extra' => 'nullable|numeric|min:0',
            'efetuou_pagamento' => 'required|string|in:SIM,NAO',
        ]);

        $data['nf_c'] = $request->boolean('nf_c');
        $data['created_by'] = Auth::id();

        $conflito = $this->verificarConflito(
            $data['veiculo_id'],
            $data['data_inicio'],
            $request->filled('data_fim') ? $data['data_fim'] : null
        );
        if ($conflito) {
            return back()->withErrors(['conflito' => $this->getMensagemConflito($conflito)])->withInput();
        }

        Agendamento::create($data);

        return redirect('/agenda/dashboard')->with('success', 'Agendamento criado com sucesso!');
    }

    public function show(Agendamento $agendamento)
    {
        $agendamento->load('veiculo', 'criador', 'editor');
        $veiculos = Veiculo::all();
        return view('agenda.detalhes', compact('agendamento', 'veiculos'));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $data = $request->validate([
            'cliente' => 'required|string|max:200',
            'contato' => 'required|string|max:50',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
            'valor_hora' => 'nullable|numeric|min:0',
            'nf_c' => 'boolean',
            'motorista' => 'required|string|max:200',
            'veiculo_id' => 'required|exists:mysql_agenda.veiculos,id',
            'tipo_servico' => 'required|string',
            'deslocamento' => 'nullable|numeric|min:0',
            'hora_extra_funcionario' => 'nullable|numeric|min:0',
            'valor_hora_extra' => 'nullable|numeric|min:0',
            'efetuou_pagamento' => 'required|string|in:SIM,NAO',
        ]);

        $data['nf_c'] = $request->boolean('nf_c');
        $data['updated_by'] = Auth::id();

        $valorHora = (float)($data['valor_hora'] ?? 0);
        $deslocamento = (float)($data['deslocamento'] ?? 0);
        $horaExtra = (float)($data['hora_extra_funcionario'] ?? 0);
        $valorHoraExtra = (float)($data['valor_hora_extra'] ?? 0);

        $horasPeriodo = 0;
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $inicio = Carbon::parse($data['data_inicio']);
            $fim = Carbon::parse($data['data_fim']);
            if ($fim->greaterThan($inicio)) {
                $horasPeriodo = $inicio->diffInHours($fim);
            }
        }

        $data['valor_total'] = ($horasPeriodo * $valorHora) + $deslocamento + ($horaExtra * $valorHoraExtra);

        $conflito = $this->verificarConflito(
            $data['veiculo_id'],
            $data['data_inicio'],
            $request->filled('data_fim') ? $data['data_fim'] : null,
            $agendamento->id
        );
        if ($conflito) {
            return back()->withErrors(['conflito' => $this->getMensagemConflito($conflito)])->withInput();
        }

        if (!$request->filled('data_fim')) {
            $data['data_fim'] = null;
        }

        $agendamento->update($data);

        return redirect('/agenda/dashboard')->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Agendamento $agendamento)
    {
        $request->validate([
            'status' => 'required|in:agendado,concluido,cancelado',
        ]);

        $agendamento->status = $request->status;
        $agendamento->updated_by = Auth::id();

        if ($request->status === 'agendado') {
            $agendamento->motivo_pendencia = null;
        }

        $agendamento->save();

        return redirect('/agenda/dashboard')->with('success', 'Status atualizado!');
    }

    public function calendario()
    {
        $eventos = Agendamento::with('veiculo', 'criador')
            ->where('status', 'agendado')
            ->get();

        return view('agenda.calendario', compact('eventos'));
    }

    public function destroy(Agendamento $agendamento)
    {
        $agendamento->status = 'cancelado';
        $agendamento->updated_by = Auth::id();
        $agendamento->save();

        return redirect('/agenda/dashboard')->with('success', 'Agendamento cancelado.');
    }

    public function excluir(Agendamento $agendamento)
    {
        $agendamento->delete();

        return redirect('/agenda/dashboard?aba=concluidos')->with('success', 'Registro excluído com sucesso.');
    }

    public function marcarPago(Request $request, Agendamento $agendamento)
    {
        $request->validate([
            'valor_recebido' => 'required|numeric|min:0',
        ]);

        $agendamento->valor_total = (float)$request->valor_recebido
            + (float)($agendamento->deslocamento ?? 0)
            + ((float)($agendamento->hora_extra_funcionario ?? 0) * (float)($agendamento->valor_hora_extra ?? 0));
        $agendamento->status = 'concluido';
        $agendamento->pago = true;
        $agendamento->efetuou_pagamento = 'SIM';
        $agendamento->updated_by = Auth::id();
        $agendamento->save();

        return redirect('/agenda/dashboard?aba=concluidos')->with('success', 'Pagamento registrado com sucesso!');
    }

    public function fluxoPagamento(Request $request, Agendamento $agendamento)
    {
        $agendamento->servico_concluido = 'SIM';
        $agendamento->updated_by = Auth::id();
        $agendamento->motivo_pendencia = null;

        if ($agendamento->efetuou_pagamento === 'SIM') {
            $agendamento->status = 'concluido';
            $agendamento->pago = true;

            if (is_null($agendamento->valor_total)) {
                $agendamento->valor_total = $agendamento->calcularValorTotalBase();
            }
        } else {
            $agendamento->status = 'nao_pago';
            $agendamento->pago = false;
        }

        $agendamento->save();

        $tab = $agendamento->efetuou_pagamento === 'SIM' ? 'concluidos' : 'naopagos';

        return redirect('/agenda/dashboard?aba=' . $tab)->with('success', 'Serviço concluído com sucesso!');
    }

    public function gerarPdf(Request $request)
    {
        $aba = $request->query('aba', 'naopagos');

        if ($aba === 'naopagos') {
            $query = Agendamento::naoPagos()->with('veiculo', 'criador');
        } else {
            $query = Agendamento::concluidos()->with('veiculo', 'criador');
        }

        if ($request->filled('filtro_inicio')) {
            $query->where('data_inicio', '>=', $request->filtro_inicio . ' 00:00:00');
        }
        if ($request->filled('filtro_fim')) {
            $query->where('data_fim', '<=', $request->filtro_fim . ' 23:59:59');
        }
        if ($request->filled('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        $registros = $query->get();
        $totalValor = $registros->sum('valor_total');
        $filtroInicio = $request->filtro_inicio;
        $filtroFim = $request->filtro_fim;
        $veiculoFiltro = $request->filled('veiculo_id') ? Veiculo::find($request->veiculo_id) : null;

        $pdf = Pdf::loadView('agenda.partials.relatorio-pdf', compact(
            'registros', 'totalValor', 'aba', 'filtroInicio', 'filtroFim', 'veiculoFiltro'
        ));

        return $pdf->download('RELATÓRIO-ALUGUEL.pdf');
    }

    private function getMensagemConflito(Agendamento $conflito): string
    {
        $placa = $conflito->veiculo->placa;
        $modelo = $conflito->veiculo->modelo ?? '';
        $cliente = $conflito->cliente;
        $dia = $conflito->data_inicio->format('d/m/Y');
        $horaInicio = $conflito->data_inicio->format('H:i');
        $horaFim = $conflito->data_fim ? $conflito->data_fim->format('H:i') : '---';

        return "ATENÇÃO, O VEÍCULO: {$placa} {$modelo} JÁ ESTÁ AGENDADO PARA A EMPRESA: {$cliente} DIA {$dia} DE {$horaInicio} Á {$horaFim}";
    }

    private function verificarConflito($veiculoId, $dataInicio, $dataFim, $excluirId = null)
    {
        $query = Agendamento::with('veiculo')
            ->where('veiculo_id', $veiculoId)
            ->whereIn('status', ['agendado', 'pendente']);

        if ($dataFim) {
            $dataInicioDate = Carbon::parse($dataInicio)->toDateString();
            $query->where(function ($q) use ($dataInicio, $dataFim, $dataInicioDate) {
                $q->whereBetween('data_inicio', [$dataInicio, $dataFim])
                  ->orWhereBetween('data_fim', [$dataInicio, $dataFim])
                  ->orWhere(function ($q2) use ($dataInicio, $dataFim) {
                      $q2->where('data_inicio', '<=', $dataInicio)
                         ->where('data_fim', '>=', $dataFim);
                  })
                  ->orWhere(function ($q3) use ($dataInicio, $dataInicioDate) {
                      $q3->whereNull('data_fim')
                         ->where('data_inicio', '<=', $dataInicio)
                         ->whereDate('data_inicio', $dataInicioDate);
                  });
            });
        } else {
            $dataInicioCarbon = Carbon::parse($dataInicio);
            $query->where(function ($q) use ($dataInicioCarbon) {
                $q->whereDate('data_inicio', $dataInicioCarbon->toDateString())
                  ->orWhere(function ($q2) use ($dataInicioCarbon) {
                      $q2->where('data_inicio', '<=', $dataInicioCarbon)
                         ->whereNotNull('data_fim')
                         ->where('data_fim', '>=', $dataInicioCarbon);
                  });
            });
        }

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->first();
    }
}
