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
 * Classe de transferência para o extrato de profissionais
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ExtratoProfissionaisTO
{
    /**
     * @var int $totalProfissionais
     */
    private $totalProfissionais;

    /**
     * @var int $totalProfissionaisAtivos
     */
    private $totalProfissionaisAtivos;

    /**
     * @var ProfissionalTO[] $profissionais
     */
    private $profissionais;

    /**
     * @return int
     */
    public function getTotalProfissionais(): int
    {
        return $this->totalProfissionais;
    }

    /**
     * @param int $totalProfissionais
     */
    public function setTotalProfissionais(int $totalProfissionais): void
    {
        $this->totalProfissionais = $totalProfissionais;
    }

    /**
     * @return int
     */
    public function getTotalProfissionaisAtivos(): int
    {
        return $this->totalProfissionaisAtivos;
    }

    /**
     * @param int $totalProfissionaisAtivos
     */
    public function setTotalProfissionaisAtivos(int $totalProfissionaisAtivos): void
    {
        $this->totalProfissionaisAtivos = $totalProfissionaisAtivos;
    }

    /**
     * @return ProfissionalTO[]
     */
    public function getProfissionais(): array
    {
        return $this->profissionais;
    }

    /**
     * @param ProfissionalTO[] $profissionais
     */
    public function setProfissionais(array $profissionais): void
    {
        $this->profissionais = $profissionais;
    }

    /**
     * Retorna uma nova instância de 'ExtratoProfissionaisTO'.
     *
     * @param null $data
     * @return ExtratoProfissionaisTO
     */
    public static function newInstance($data = null)
    {
        $extratoProfissionaisTO = new ExtratoProfissionaisTO();

        if ($data != null) {
            $extratoProfissionaisTO->setNome(Utils::getValue('arquivo', $data));
            $extratoProfissionaisTO->setTotalProfissionais(Utils::getValue('tamanho', $data));
            $extratoProfissionaisTO->setTotalProfissionaisAtivos(Utils::getValue('nomeArquivo', $data));
        }

        return $extratoProfissionaisTO;
    }

}