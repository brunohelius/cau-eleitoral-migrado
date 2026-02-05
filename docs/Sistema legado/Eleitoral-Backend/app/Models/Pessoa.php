<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
/**
 * Entidade de representação da 'Pessoa'
 *
 * @OA\Schema(schema="Pessoa")
 */
class Pessoa extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * @var string
     */
    protected $table = 'tb_pessoa';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'homepage',
        'enderecocorrespondencia',
        'recebemaladireta',
        'atendido',
        'ativo',
        'funcionalidade_audit_id',
        'user_ultima_alteracao_id',
        'dt_ultimo_acesso',
        'cpf_cnpj',
        'senha',
        'firsttime',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'senha',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_cadastro' => 'datetime',
    ];
}
