<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'DiplomaTermo'
 *
 * @OA\Schema(schema="DiplomaTermo")
 */
class DiplomaTermo extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_diploma_termo_conselheiro';

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
        'conselheiro_id',
        'dt_eleicao',
        'dt_eleicao_2',
        'numero_resolucao',
        'dt_resolucao',
        'nome_arquiteto',
        'dt_emissao',
        'preposicao_cidade',
        'cidade_emissao',
        'uf_emissao',
        'cpf_coordenador',
        'nome_coordenador',
        'cpf_presidente',
        'uf_presidente',
        'nome_presidente',
        'uf_comissao',
        'tipo_diploma_termo',
        'dt_cadastro',
        'co_autenticidade',
        'acessor_cen'
    ];

    public function conselheiro()
    {
        return $this->hasOne(Conselheiro::class, 'id', 'conselheiro_id');
    }
    
    public function assinatura()
    {
        return $this->hasOne(DiplomaTermoAssinatura::class, 'diploma_termo_id', 'id');
    }
}
