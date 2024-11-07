<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Classe criada para salvar os arquivos relacionados aos conteúdos do site
 */
class Archives extends Model
{
    use SoftDeletes;
    //
    protected $table = 'archives';
    protected $fillable = [
        'title',
        'description',
        'type',
        'hash',
        'path',
        'name',
        'extension',
        'pet_id'
    ];

    public static function boot()
    {
        parent::boot();

        // Define o fuso horário do Laravel para o horário local do Brasil
        date_default_timezone_set('America/Sao_Paulo');
    }
}
