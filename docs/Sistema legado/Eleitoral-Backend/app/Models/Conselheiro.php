<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Conselheiro'
 *
 * @OA\Schema(schema="eleitoral")
 */
class Conselheiro extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_conselheiro';

    /**
     * @var bool
     */
    public $timestamps = false;

      /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'pessoa_id',
        'dt_inicio_mandato',
        'dt_fim_mandato',
        'tipo_conselheiro_id',
        'representacao_conselheiro_id',
        'filial_id',
        'ies',
        'dt_cadastro',
        'email',
        'processo_eleitoral_id',
        'recomposicao_mandato',
        'ano_eleicao',
        'ativo'
    ];

    public function diploma()
    {
        return $this->hasMany(DiplomaTermo::class, 'conselheiro_id', 'id');
    }

    public function profissional()
    {
        return $this->hasOne(Profissional::class, 'pessoa_id', 'pessoa_id');
    }

    public function filial()
    {
        return $this->hasOne(Filial::class, 'id', 'filial_id');
    }
}
