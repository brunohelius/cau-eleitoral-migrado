<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
/**
 * Entidade de representação da 'FinalizacaoMandato'
 *
 * @OA\Schema(schema="FinalizacaoMandato")
 */
class TipoFinalizacaoMandato extends Model
{
/**
     * @var string
     */
    protected $table = 'tb_tipo_finalizacao_mandato';

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
        'descricao',
        'dt_cadastro',
        'dt_alteracao',
        'ativo',
        'id_usuario',
        'id_usuario_alterar'
    ];
}
