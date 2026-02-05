<?php


namespace App\To;

use App\Entities\AlegacaoFinal;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\RecursoSubstituicao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Recurso de Substituição
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoFinalTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string|null $descricao
     */
    private $descricao;

    /**
     * @var integer|null $idEncaminhamentoDenuncia
     */
    private $idEncaminhamentoDenuncia;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var ArquivoDescricaoTO[]|null
     */
    private $descricaoArquivo;

    /**
     * @var string|null $destinatario
     */
    private $destinatario;

    /**
     * @var \DateTime|null $dataHora
     */
    private $dataHora;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int|null
     */
    public function getIdEncaminhamentoDenuncia(): ?int
    {
        return $this->idEncaminhamentoDenuncia;
    }

    /**
     * @param int|null $idEncaminhamentoDenuncia
     */
    public function setIdEncaminhamentoDenuncia(?int $idEncaminhamentoDenuncia): void
    {
        $this->idEncaminhamentoDenuncia = $idEncaminhamentoDenuncia;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDescricaoArquivo(): ?array
    {
        return $this->descricaoArquivo;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $descricaoArquivo
     */
    public function setDescricaoArquivo(?array $descricaoArquivo): void
    {
        $this->descricaoArquivo = $descricaoArquivo;
    }

    /**
     * @return string|null
     */
    public function getDestinatario(): ?string
    {
        return $this->destinatario;
    }

    /**
     * @param string|null $destinatario
     */
    public function setDestinatario(?string $destinatario): void
    {
        $this->destinatario = $destinatario;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataHora(): ?\DateTime
    {
        return $this->dataHora;
    }

    /**
     * @param \DateTime|null $dataHora
     */
    public function setDataHora(?\DateTime $dataHora): void
    {
        $this->dataHora = $dataHora;
    }

    /**
     * Retorna uma nova instância de 'AlegacaoFinalTO'.
     *
     * @param null $data
     * @return AlegacaoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $alegacaoFinalTO = new AlegacaoFinalTO();

        if ($data != null) {
            $alegacaoFinalTO->setId(Arr::get($data, 'id'));
            $alegacaoFinalTO->setDataHora(Arr::get($data,'dataHora'));
            $alegacaoFinalTO->setDescricao(Arr::get($data, 'descricao'));
            $alegacaoFinalTO->setIdEncaminhamentoDenuncia(Arr::get($data,'idEncaminhamentoDenuncia'));

            $arquivos = Arr::get($data, 'arquivoAlegacaoFinal');
            if(!empty($arquivos)) {
                $alegacaoFinalTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            }
        }

        return $alegacaoFinalTO;
    }

    /**
     * Fabricação estática de 'AlegacaoFinalTO'.
     *
     * @param AlegacaoFinal $alegacaoFinal
     * @return AlegacaoFinalTO
     */
    public static function newInstanceFromEntity(AlegacaoFinal $alegacaoFinal)
    {
        $alegacaoFinalTO = new AlegacaoFinalTO();

        if (!empty($alegacaoFinalTO)) {
            $alegacaoFinalTO->setId($alegacaoFinal->getId());
            $alegacaoFinalTO->setDataHora($alegacaoFinal->getDataHora());
            $alegacaoFinalTO->setDescricao($alegacaoFinal->getDescricaoAlegacaoFinal());
        }

        return $alegacaoFinalTO;
    }
}
