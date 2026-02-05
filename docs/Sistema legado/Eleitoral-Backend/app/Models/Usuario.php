<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Entidade de representação da 'Usuario'
 *
 * @OA\Schema(schema="Usuario")
 */
class Usuario extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * @var string
     */
    protected $table = 'tb_usuarios';

    /**
     * @var boolean
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'usuario',
        'senha',
        'cpf',
        'telefone',
        'email',
        'firsttime',
        'matricula',
        'apelido',
        'filial_id',
        'ativo',
        'id',
        'tecnotech',
        'administrador',
        'administradorfilial',
        'portaria',
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
        'datacadastro' => 'datetime',
        'datamodificacao' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getAuthPassword() {
        return $this->senha;
    }
}
