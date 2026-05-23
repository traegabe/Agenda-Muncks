<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->string('efetuou_pagamento', 3)->nullable()->after('hora_extra_funcionario');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_agenda')->table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('efetuou_pagamento');
        });
    }
};
