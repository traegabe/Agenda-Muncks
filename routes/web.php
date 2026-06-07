<?php

use App\Http\Controllers\Agenda\AuthController;
use App\Http\Controllers\Agenda\AgendamentoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/agenda/login');

Route::prefix('agenda')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('agenda.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('agenda.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AgendamentoController::class, 'index'])->name('agenda.dashboard');
        Route::get('/calendario', [AgendamentoController::class, 'calendario'])->name('agenda.calendario');

        Route::post('/agendamentos', [AgendamentoController::class, 'store'])->name('agendamentos.store');
        Route::get('/agendamentos/{agendamento}', [AgendamentoController::class, 'show'])->name('agendamentos.show');
        Route::put('/agendamentos/{agendamento}', [AgendamentoController::class, 'update'])->name('agendamentos.update');
        Route::delete('/agendamentos/{agendamento}', [AgendamentoController::class, 'destroy'])->name('agendamentos.destroy');
        Route::post('/agendamentos/{agendamento}/status', [AgendamentoController::class, 'updateStatus'])->name('agendamentos.status');
        Route::post('/agendamentos/{agendamento}/pagar', [AgendamentoController::class, 'marcarPago'])->name('agendamentos.pagar');
        Route::post('/agendamentos/{agendamento}/fluxo-pagamento', [AgendamentoController::class, 'fluxoPagamento'])->name('agendamentos.fluxo-pagamento');
        Route::post('/agendamentos/{agendamento}/atualizar-valores', [AgendamentoController::class, 'atualizarValores'])->name('agendamentos.atualizar-valores');
        Route::delete('/agendamentos/{agendamento}/excluir', [AgendamentoController::class, 'excluir'])->name('agendamentos.excluir');
        Route::get('/relatorio-pdf', [AgendamentoController::class, 'gerarPdf'])->name('agenda.relatorio-pdf');
    });
});
