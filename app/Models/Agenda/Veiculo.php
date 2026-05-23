<?php

namespace App\Models\Agenda;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $connection = 'mysql_agenda';
    protected $fillable = ['placa', 'modelo', 'marca'];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
