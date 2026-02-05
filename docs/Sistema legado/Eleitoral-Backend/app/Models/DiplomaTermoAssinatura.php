<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'DiplomaTermoAssinatura'
 *
 * @OA\Schema(schema="DiplomaTermoAssinatura")
 */
class DiplomaTermoAssinatura extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_diploma_termo_assinatura';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'nome_arquivo',
        'nome_gerado',
        'descricao',
        'caminho',
        'dt_cadastro',
        'diploma_termo_id'
    ];
}
