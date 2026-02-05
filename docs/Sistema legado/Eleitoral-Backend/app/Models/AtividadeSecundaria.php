<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'AtividadeSecundaria'
 *
 * @OA\Schema(schema="eleitoral")
 */
class AtividadeSecundaria extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_ativ_secundaria';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_ativ_secundaria',
        'id_ativ_principal'
    ];

    public function atividadePrincipal()
    {
        return $this->hasOne(AtividadePrincipal::class, 'id_ativ_principal', 'id_ativ_principal');
    }
}
