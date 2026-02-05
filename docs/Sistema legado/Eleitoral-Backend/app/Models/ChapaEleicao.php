<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Chapa_eleicao'
 *
 * @OA\Schema(schema="eleitoral")
 */
class ChapaEleicao extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_chapa_eleicao';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_chapa_eleicao',
        'id_cau_uf',
        'id_ativ_secundaria'
    ];

    public function atividadeSecundaria()
    {
        return $this->hasOne(AtividadeSecundaria::class, 'id_ativ_secundaria', 'id_ativ_secundaria');
    }

    public function filial()
    {
        return $this->hasOne(Filial::class, 'id', 'id_cau_uf');
    }
}
