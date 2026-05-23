<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('mysql_agenda')->statement("
            UPDATE agendamentos 
            SET deslocamento = CASE 
                WHEN deslocamento = 'Sim' THEN '0' 
                WHEN deslocamento = 'Não' THEN NULL 
                ELSE deslocamento 
            END
        ");

        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->decimal('deslocamento', 10, 2)->nullable()->change();
            $table->decimal('valor_hora_extra', 10, 2)->nullable()->after('hora_extra_funcionario');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->string('deslocamento', 10)->nullable()->change();
            $table->dropColumn('valor_hora_extra');
        });

        DB::connection('mysql_agenda')->statement("
            UPDATE agendamentos 
            SET deslocamento = CASE 
                WHEN deslocamento IS NOT NULL AND CAST(deslocamento AS DECIMAL(10,2)) > 0 THEN 'Sim' 
                ELSE NULL 
            END
        ");
    }
};
