<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'membro_comissao'
 *
 * @OA\Schema(schema="eleitoral")
 */
class MembroComissao extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_membro_comissao';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_membro_comissao',
        'id_membro_substituto',
        'id_pessoa'
    ];
}
