<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'ArquivoRepresentatividade'
 *
 * @OA\Schema(schema="arquivo_representatividade")
 */
class ArquivoRepresentatividade extends Model
{
    /**
     * @var string
     */
    protected $table = 'eleitoral.tb_arquivo_representatividade';

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
        'nm_fis_arquivo'
    ];
}