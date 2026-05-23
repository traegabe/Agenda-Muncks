# Aba "Não Pagos" Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a "Não Pagos" tab to filter completed agendamentos with pending payment status.

**Architecture:** New `pago` boolean column on `agendamentos` table, new scope on `Agendamento` model, query in controller, new partial view for the tab UI, updated dashboard with tab button and JS.

**Tech Stack:** Laravel 12, MySQL, Blade, Tailwind CSS, Alpine.js

---

### Task 1: Migration — Add `pago` column

**Files:**
- Create: `database/migrations/2026_05_13_000001_add_pago_to_agendamentos_table.php`

- [ ] **Step 1: Create migration**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->boolean('pago')->default(false)->after('motivo_pendencia');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('pago');
        });
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`

Expected: `2026_05_13_000001_add_pago_to_agendamentos_table ................... DONE`

### Task 2: Model — Add scope and cast

**Files:**
- Modify: `app/Models/Agenda/Agendamento.php`

- [ ] **Step 1: Add `pago` to fillable and casts**

Add `'pago'` to `$fillable` array.
Add `'pago' => 'boolean'` to `casts()` method.

- [ ] **Step 2: Add scopeNaoPagos**

```php
public function scopeNaoPagos($query)
{
    return $query->where('status', 'concluido')->where('pago', false)->orderByDesc('data_inicio');
}
```

### Task 3: Controller — Add `$naoPagos` query

**Files:**
- Modify: `app/Http/Controllers/Agenda/AgendamentoController.php`

- [ ] **Step 1: Add query for nao_pagos in index()**

After the `$concluidos` query block, add identical date-filtered query for `$naoPagos`:

```php
$naoPagosQuery = Agendamento::naoPagos()->with('veiculo', 'criador');

if ($request->filled('filtro_inicio')) {
    $naoPagosQuery->where('data_inicio', '>=', $request->filtro_inicio . ' 00:00:00');
}
if ($request->filled('filtro_fim')) {
    $naoPagosQuery->where('data_fim', '<=', $request->filtro_fim . ' 23:59:59');
}

$naoPagos = $naoPagosQuery->get();
```

Add `'naoPagos'` to the `compact()` in the return view.

- [ ] **Step 2: Add endpoint to mark as paid**

Add a new method `marcarPago()`:

```php
public function marcarPago(Agendamento $agendamento)
{
    $agendamento->pago = true;
    $agendamento->updated_by = Auth::id();
    $agendamento->save();

    return redirect('/agenda/dashboard?aba=naopagos')->with('success', 'Pagamento registrado com sucesso!');
}
```

Add route: `POST /agenda/agendamentos/{agendamento}/pagar` → `AgendamentoController@marcarPago` named `agendamentos.pagar`

### Task 4: View — Create `lista-nao-pagos.blade.php`

**Files:**
- Create: `resources/views/agenda/partials/lista-nao-pagos.blade.php`

Cards identical to `lista-concluidos` but:
- Border color: red-500
- No link wrapper on the card (add actions like other non-concluidos tabs)
- "Registrar Pagamento" button via POST to the new route
- Show `valor_total` as "R$ XX,XX"

### Task 5: View — Update `dashboard.blade.php`

**Files:**
- Modify: `resources/views/agenda/dashboard.blade.php`

- [ ] **Step 1: Add "Não Pagos" tab button after "Concluídos"**
- [ ] **Step 2: Add include for `lista-nao-pagos` after `lista-concluidos`**

### Task 6: View — Update `detalhes.blade.php`

**Files:**
- Modify: `resources/views/agenda/detalhes.blade.php`

- [ ] **Step 1: Show payment status in details**
- [ ] **Step 2: Add "Marcar como Pago" button when `status === 'concluido' && !pago`**

### Task 7: Routes — Add marcarPago route

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Add route**

```php
Route::post('/agenda/agendamentos/{agendamento}/pagar', [AgendamentoController::class, 'marcarPago'])->name('agendamentos.pagar');
```
