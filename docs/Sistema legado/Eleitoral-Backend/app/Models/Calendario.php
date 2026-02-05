<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Calendario'
 *
 * @OA\Schema(schema="eleitoral")
 */
class Calendario extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_calendario';

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

    public function eleicao()
    {
        return $this->hasOne(Eleicao::class, 'id_calendario', 'id_calendario');
    }
}
