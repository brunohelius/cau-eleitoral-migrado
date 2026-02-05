<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'item_declaracao'
 *
 * @OA\Schema(schema="item_declaracao")
 */
class ItemDeclaracao extends Model
{
    /**
     * @var string
     */
    protected $table = 'portal.tb_item_declaracao';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_item_declaracao',
        'id_declacarao',
        'ds_item_declaracao',
        'nr_seq'
    ];
}
