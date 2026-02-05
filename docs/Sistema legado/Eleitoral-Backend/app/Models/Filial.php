<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Filial'
 *
 * @OA\Schema(schema="eleitoral")
 */
class Filial extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_filial';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'prefixo',
    ];
}
