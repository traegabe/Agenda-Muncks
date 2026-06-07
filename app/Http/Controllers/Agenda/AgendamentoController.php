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

        $data['cliente'] = mb_strtoupper($data['cliente'], 'UTF-8');
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

        $efetuouPagamento = $request->input('efetuou_pagamento', $agendamento->efetuou_pagamento);

        if ($efetuouPagamento === 'SIM') {
            $agendamento->efetuou_pagamento = 'SIM';
            $agendamento->status = 'concluido';
            $agendamento->pago = true;

            if ($request->filled('valor_total')) {
                $agendamento->valor_total = $request->valor_total;
            } elseif (is_null($agendamento->valor_total)) {
                $agendamento->valor_total = $agendamento->calcularValorTotalBase();
            }

            if ($request->filled('data_inicio')) {
                $agendamento->data_inicio = $request->data_inicio;
            }
            if ($request->filled('data_fim')) {
                $agendamento->data_fim = $request->data_fim;
            }
            if ($request->filled('valor_hora')) {
                $agendamento->valor_hora = $request->valor_hora;
            }
            if ($request->filled('deslocamento')) {
                $agendamento->deslocamento = $request->deslocamento;
            }
            if ($request->filled('hora_extra_funcionario')) {
                $agendamento->hora_extra_funcionario = $request->hora_extra_funcionario;
            }
            if ($request->filled('valor_hora_extra')) {
                $agendamento->valor_hora_extra = $request->valor_hora_extra;
            }
        } else {
            $agendamento->status = 'nao_pago';
            $agendamento->pago = false;
        }

        $agendamento->save();

        $tab = $agendamento->efetuou_pagamento === 'SIM' ? 'concluidos' : 'naopagos';

        return redirect('/agenda/dashboard?aba=' . $tab)->with('success', 'Serviço concluído com sucesso!');
    }

    public function atualizarValores(Request $request, Agendamento $agendamento)
    {
        $data = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'valor_hora' => 'nullable|numeric|min:0',
            'deslocamento' => 'nullable|numeric|min:0',
            'hora_extra_funcionario' => 'nullable|numeric|min:0',
            'valor_hora_extra' => 'nullable|numeric|min:0',
        ]);

        $registro = Agendamento::where('id', $agendamento->id)->firstOrFail();

        $registro->data_inicio = $data['data_inicio'];
        $registro->data_fim = $request->filled('data_fim') ? $data['data_fim'] : null;
        $registro->valor_hora = $data['valor_hora'] ?? $registro->valor_hora;
        $registro->deslocamento = $data['deslocamento'] ?? $registro->deslocamento;
        $registro->hora_extra_funcionario = $data['hora_extra_funcionario'] ?? $registro->hora_extra_funcionario;
        $registro->valor_hora_extra = $data['valor_hora_extra'] ?? $registro->valor_hora_extra;

        $registro->valor_total = $registro->calcularValorTotalBase();
        $registro->updated_by = Auth::id();

        $registro->save();

        $registro->load('veiculo', 'criador');

        return response()->json([
            'sucesso' => true,
            'registroAtualizado' => [
                'id' => $registro->id,
                'data_inicio' => $registro->data_inicio->format('d/m/Y H:i'),
                'data_fim' => $registro->data_fim ? $registro->data_fim->format('d/m/Y H:i') : '',
                'data_inicio_raw' => $registro->data_inicio->format('d/m/Y'),
                'horario_inicio' => $registro->data_inicio->format('H:i'),
                'horario_fim' => $registro->data_fim ? $registro->data_fim->format('H:i') : '',
                'valor_hora' => $registro->valor_hora,
                'deslocamento' => $registro->deslocamento,
                'hora_extra_funcionario' => $registro->hora_extra_funcionario,
                'valor_hora_extra' => $registro->valor_hora_extra,
                'valor_total' => number_format($registro->valor_total, 2, ',', '.'),
                'valor_total_raw' => $registro->valor_total,
                'status' => $registro->status,
            ]
        ]);
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
        $novoInicio = Carbon::parse($dataInicio);
        $novoFim = $dataFim ? Carbon::parse($dataFim) : null;

        $query = Agendamento::with('veiculo')
            ->where('veiculo_id', $veiculoId)
            ->whereIn('status', ['agendado', 'pendente'])
            ->where(function ($q) use ($novoInicio, $novoFim) {
                $q->where(function ($q2) use ($novoInicio, $novoFim) {
                    $q2->whereNotNull('data_fim');
                    if ($novoFim) {
                        $q2->where('data_inicio', '<', $novoFim)
                           ->where('data_fim', '>', $novoInicio);
                    } else {
                        $q2->where('data_inicio', '<=', $novoInicio)
                           ->where('data_fim', '>', $novoInicio);
                    }
                })->orWhere(function ($q2) use ($novoInicio, $novoFim) {
                    $q2->whereNull('data_fim');
                    if ($novoFim) {
                        $q2->where('data_inicio', '<', $novoFim);
                    } else {
                        $q2->whereDate('data_inicio', $novoInicio->toDateString());
                    }
                });
            });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->first();
    }
}
