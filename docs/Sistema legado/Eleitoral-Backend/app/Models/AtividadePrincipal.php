<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'AtividadePrincipal'
 *
 * @OA\Schema(schema="eleitoral")
 */
class AtividadePrincipal extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_ativ_principal';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_ativ_principal',
        'id_calendario'
    ];

    public function calendario()
    {
        return $this->hasOne(Calendario::class, 'id_calendario', 'id_calendario');
    }
}
