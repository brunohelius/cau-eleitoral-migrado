<?php


namespace App\To;

use App\Util\Utils;
use OpenApi\Annotations as OA;
use App\Entities\MembroComissao;
use App\Entities\ArquivoDecMembroComissao;

/**
 * Classe de transferência associada a 'ConviteStatusFiltroTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ConviteStatusFiltroComissao")
 */
class ConviteStatusComissaoFiltroTO
{
    /**
     * @var integer
     * @OA\Property()
     */
    private $idMembroComissao;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idDeclaracao;

    /**
     * @var bool
     * @OA\Property()
     */
    private $isParticipanteComissao;

    /**
     * @var bool
     * @OA\Property()
     */
    private $isConviteAceito;

    /**
     * @var
     */
    private $idProfissional;

    /**
     * @var array
     */
    private $itensDeclaracao =[];

    /**
     * @var array
     */
    private $arquivosDecMembroComissao = [];

    /**
     * @var
     */
    private $membroComissao;
    /**
     * Fabricação estática de 'ConviteMembroComissaoTO'.
     *
     * @param array|null $data
     *
     * @return ConviteStatusComissaoFiltroTO
     */
    public static function newInstance($data = null) {
        $instance = new self();

        if($data != null) {
            $instance->setIdDeclaracao(Utils::getValue('idDeclaracao', $data));
            $instance->setIdProfissional(Utils::getValue('idProfissional', $data));
            $instance->setItensDeclaracao(Utils::getValue('itensDeclaracao', $data));
            $instance->setIdMembroComissao(intval(Utils::getValue('idMembroComissao', $data)));
            $instance->setArquivosDecMembroComissao(Utils::getValue('arquivos', $data));
            $instance->setIsParticipanteComissao(Utils::getValue('isParticipanteComissao', $data));

            $isParticipanteComissao = Utils::getValue('isParticipanteComissao', $data);
            if(!empty($isParticipanteComissao)) {
                if(!is_bool($isParticipanteComissao)) {
                    $isParticipanteComissao == 'true' ? $instance->setIsParticipanteComissao(true) : $instance->setIsParticipanteComissao(false);
                } else {
                    $instance->setIsParticipanteComissao($isParticipanteComissao);
                }
            }

            $isConviteAceito = Utils::getValue('isConviteAceito', $data);
            if(!empty($isConviteAceito)) {
                if(!is_bool($isConviteAceito)) {
                    $isConviteAceito == 'true' ? $instance->setIsConviteAceito(true) : $instance->setIsConviteAceito(false);
                } else {
                    $instance->setIsConviteAceito($isConviteAceito);
                }
            }

        }
        return $instance;
    }

    /**
     * @param $idMembroComissao
     */
    public function setIdMembroComissao($idMembroComissao)
    {
        $this->idMembroComissao = $idMembroComissao;
    }

    /**
     * @return int
     */
    public function getIdMembroComissao()
    {
        return $this->idMembroComissao;
    }

    /**
     * @param $idDeclaracao
     */
    public function setIdDeclaracao($idDeclaracao)
    {
        $this->idDeclaracao =  $idDeclaracao;
    }

    /**
     * @return int
     */
    public function getIdDeclaracao()
    {
        return $this->idDeclaracao;
    }

    /**
     * @return int
     */
    public function getDeclaracao()
    {
        return $this->idDeclaracao;
    }

    /**
     * @param $isParticipanteComissao
     */
    public function setIsParticipanteComissao($isParticipanteComissao)
    {
        $this->isParticipanteComissao = $isParticipanteComissao;
    }

    /**
     * @return bool
     */
    public function getIsParticipanteComissao()
    {
        return $this->isParticipanteComissao;
    }

    /**
     * @param $isConviteAceito
     */
    public function setIsConviteAceito($isConviteAceito)
    {
        $this->isConviteAceito = $isConviteAceito;
    }

    /**
     * @return bool
     */
    public function getIsConviteAceito()
    {
        return $this->isConviteAceito;
    }

    /**
     * @param $idProfissional
     */
    public function setIdProfissional($idProfissional)
    {
        $this->idProfissional = $idProfissional;
    }

    /**
     * @return int
     */
    public function getIdProfissional()
    {
        return $this->idProfissional;
    }

    /**
     * @param $itensDeclaracao
     */
    public function setItensDeclaracao($itensDeclaracao)
    {

        if(!empty($itensDeclaracao)) {
            foreach($itensDeclaracao as $itemDeclaracao) {
                array_push($this->itensDeclaracao, $itemDeclaracao);
            }
        }
    }

    /**
     * @return array
     */
    public function getItensDeclaracao()
    {
        return $this->itensDeclaracao;
    }

    /**
     * @param $arquivosDecMembroComissao
     */
    public function setArquivosDecMembroComissao($arquivosDecMembroComissao)
    {
        if(!empty($arquivosDecMembroComissao)) {
            foreach($arquivosDecMembroComissao as $arquivoDecMembroComissao) {
                $arquivo = ArquivoDecMembroComissao::newInstance($arquivoDecMembroComissao);
                array_push($this->arquivosDecMembroComissao, $arquivo);
            }
        }
    }

    public function getArquivosDecMembroComissao()
    {
        return $this->arquivosDecMembroComissao;
    }

}