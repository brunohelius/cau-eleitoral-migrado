<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entidade de representação da 'Profissional'
 *
 * @OA\Schema(schema="public")
 */
class Profissional extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_profissional';

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
        'cpf',
        'nome',
        'pessoa_id'
    ];

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'id', 'pessoa_id');
    }
}
