<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Historico_chapa_eleicao'
 *
 * @OA\Schema(schema="eleitoral")
 */
class HistoricoChapaEleicao extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_hist_chapa_eleicao';

    /**
     * @var bool
     */
    public $timestamps = false;

      /**
     * @var string
     */
    protected $primaryKey = 'id_hist_chapa_eleicao';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_hist_chapa_eleicao',
        'id_usuario',
        'id_chapa_eleicao',
        'dt_historico',
        'ds_acao',
        'ds_origem'
    ];
}
