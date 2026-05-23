<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_agenda')->create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->string('cliente', 200);
            $table->string('contato', 50);
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim');
            $table->decimal('valor_hora', 10, 2);
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->boolean('nf_c')->default(false);
            $table->string('motorista', 200);
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->text('tipo_servico');
            $table->string('status', 20)->default('agendado');
            $table->text('motivo_pendencia')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_agenda')->dropIfExists('agendamentos');
    }
};
