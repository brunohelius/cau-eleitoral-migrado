<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Eleicao'
 *
 * @OA\Schema(schema="eleitoral")
 */
class Eleicao extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_eleicao';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_calendario',
        'id_eleicao',
        'nu_ano'
    ];
}
