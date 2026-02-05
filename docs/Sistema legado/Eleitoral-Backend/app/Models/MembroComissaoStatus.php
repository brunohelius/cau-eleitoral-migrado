<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'membro_comissao_status'
 *
 * @OA\Schema(schema="eleitoral")
 */
class MembroComissaoStatus extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_membro_comissao_status';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_membro_comissao_status',
        'id_status_comissao_membro',
        'id_membro_comissao',
    ];
}
