<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->string('deslocamento', 10)->nullable()->after('tipo_servico');
            $table->decimal('hora_extra_funcionario', 10, 2)->nullable()->after('deslocamento');
            $table->dateTime('data_fim')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('deslocamento');
            $table->dropColumn('hora_extra_funcionario');
            $table->dateTime('data_fim')->nullable(false)->change();
        });
    }
};
