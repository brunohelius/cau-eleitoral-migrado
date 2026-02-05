<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Declaracao'
 *
 * @OA\Schema(schema="portal")
 */
class Declaracao extends Model
{
    /**
     * @var string
     */
    protected $table = 'portal.tb_declaracao';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'id_declaracao';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_declaracao',
        'ds_texto_inicial',
    ];
}
