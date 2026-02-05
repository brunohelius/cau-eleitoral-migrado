<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Resposta_declaracao_representatividade'
 *
 * @OA\Schema(schema="Resposta_declaracao_representatividade")
 */
class RespostaDeclaracaoRepresentatividade extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_resposta_declaracao_representatividade';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'id_membro_chapa',
        'id_item_declaracao'
    ];

    public function item()
    {
        return $this->hasMany(ItemDeclaracao::class, 'id_item_declaracao', 'id_item_declaracao');
    }
}
