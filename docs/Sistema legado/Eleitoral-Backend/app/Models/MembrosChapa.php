<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Membros_chapa'
 *
 * @OA\Schema(schema="eleitoral")
 */
class MembrosChapa extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_membro_chapa';

    /**
     * @var bool
     */
    public $timestamps = false;

      /**
     * @var string
     */
    protected $primaryKey = 'id_chapa_eleicao';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_membro_chapa',
        'id_chapa_eleicao',
        'status_eleito',
        'id_tp_partic_chapa',
        'id_tp_membro_chapa',
        'id_profissional',
        'id_suplente'
    ];

    public function chapaEleicao()
    {
        return $this->hasOne(ChapaEleicao::class, 'id_chapa_eleicao', 'id_chapa_eleicao');
    }

    public function profissional()
    {
        return $this->hasOne(Profissional::class, 'id', 'id_profissional');
    }
}
