<?php


namespace App\To;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\MembroChapa;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\StatusSubstituicaoChapa;
use App\Util\Utils;
use Illuminate\Support\Facades\Log;

/**
 * Classe de transferência para arquivos
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ArquivoTO
{
    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $type
     */
    public $type;

    /**
     * @var string $file
     */
    public $file;

}