<?php


namespace App\To;

use App\Entities\JulgamentoFinal;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use App\Util\Utils;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Self_;

/**
 * Classe de transferência para a Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoFinalTO
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
     * @var string|null $retificacaoJustificativa
     */
    private $retificacaoJustificativa;

    /**
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var ChapaEleicaoTO|null
     */
    private $chapaEleicao;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusJulgamentoFinal;

    /**
     * @var integer|null $idStatusJulgamentoFinal
     */
    private $idStatusJulgamentoFinal;

    /**
     * @var integer|null $idChapaEleicao
     */
    private $idChapaEleicao;

    /**
     * @var UsuarioTO|null $usuario
     */
    private $usuario;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var IndicacaoJulgamentoFinalTO[]|null
     */
    private $indicacoes;

    /**
     * @var JulgamentoFinalTO[]|null
     */
    private $retificacoes;

    /**
     * @var integer|null $idJulgamentoFinalPai
     */
    private $idJulgamentoFinalPai;

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
     * @return string|null
     */
    public function getRetificacaoJustificativa(): ?string
    {
        return $this->retificacaoJustificativa;
    }

    /**
     * @param string|null $retificacaoJustificativa
     */
    public function setRetificacaoJustificativa(?string $retificacaoJustificativa): void
    {
        $this->retificacaoJustificativa = $retificacaoJustificativa;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|null $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return ChapaEleicaoTO|null
     */
    public function getChapaEleicao(): ?ChapaEleicaoTO
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicaoTO|null $chapaEleicao
     */
    public function setChapaEleicao(?ChapaEleicaoTO $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusJulgamentoFinal(): ?StatusGenericoTO
    {
        return $this->statusJulgamentoFinal;
    }

    /**
     * @param StatusGenericoTO|null $statusJulgamentoFinal
     */
    public function setStatusJulgamentoFinal(?StatusGenericoTO $statusJulgamentoFinal): void
    {
        $this->statusJulgamentoFinal = $statusJulgamentoFinal;
    }

    /**
     * @return int|null
     */
    public function getIdStatusJulgamentoFinal(): ?int
    {
        return $this->idStatusJulgamentoFinal;
    }

    /**
     * @param int|null $idStatusJulgamentoFinal
     */
    public function setIdStatusJulgamentoFinal(?int $idStatusJulgamentoFinal): void
    {
        $this->idStatusJulgamentoFinal = $idStatusJulgamentoFinal;
    }

    /**
     * @return int|null
     */
    public function getIdChapaEleicao(): ?int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param int|null $idChapaEleicao
     */
    public function setIdChapaEleicao(?int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return UsuarioTO|null
     */
    public function getUsuario(): ?UsuarioTO
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO|null $usuario
     */
    public function setUsuario(?UsuarioTO $usuario): void
    {
        $this->usuario = $usuario;
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
     * @return IndicacaoJulgamentoFinalTO[]|null
     */
    public function getIndicacoes(): ?array
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoFinalTO[]|null $indicacoes
     */
    public function setIndicacoes(?array $indicacoes): void
    {
        $this->indicacoes = $indicacoes;
    }

    /**
     * @return integer|null
     */
    public function getIdJulgamentoFinalPai(): ?Int
    {
        return $this->idJulgamentoFinalPai;
    }

    /**
     * @param int|null idJulgamentoFinalPai
     */
    public function setIdJulgamentoFinalPai(?Int $idJulgamentoFinalPai): void
    {
        $this->idJulgamentoFinalPai = $idJulgamentoFinalPai;
    }

    /**
     * @return JulgamentoFinalTO[]|null
     */
    public function getRetificacoes(): ?array
    {
        return $this->retificacoes;
    }

    /**
     * @param JulgamentoFinalTO[]|null $retificacoes
     */
    public function setRetificacoes(?array $retificacoes): void
    {
        $this->retificacoes = $retificacoes;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return JulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoFinalTO = new JulgamentoFinalTO();

        if ($data != null) {
            $julgamentoFinalTO->setId(Arr::get($data, 'id'));
            $julgamentoFinalTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoFinalTO->setRetificacaoJustificativa(Utils::getValue('retificacaoJustificativa', $data));
            $julgamentoFinalTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoFinalTO->setIdChapaEleicao(Arr::get($data, 'idChapaEleicao'));
            $julgamentoFinalTO->setIdStatusJulgamentoFinal(Arr::get($data, 'idStatusJulgamentoFinal'));

            $chapaEleicao = Arr::get($data, 'chapaEleicao');
            if (!empty($chapaEleicao)) {
                $julgamentoFinalTO->setChapaEleicao(ChapaEleicaoTO::newInstance($chapaEleicao));
            }

            $status = Arr::get($data, 'statusJulgamentoFinal');
            if (!empty($status)) {
                $julgamentoFinalTO->setStatusJulgamentoFinal(StatusGenericoTO::newInstance($status));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoFinalTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $indicacoesArray = Utils::getValue('indicacoes', $data);
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoFinalTO::newInstance($indicacao));
                }
                $julgamentoFinalTO->setIndicacoes($indicacoes);
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $julgamentoFinalTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $julgamentoFinalTO->setArquivos([$arquivo]);
                }
            }

            $julgamentoFinalTO->setIdJulgamentoFinalPai(Arr::get($data, 'idJulgamentoFinalPai'));
        }

        return $julgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param JulgamentoFinal $julgamentoFinal
     * @param bool $isResumo
     * @return JulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoFinal, $isResumo = false)
    {
        $julgamentoFinalTO = new JulgamentoFinalTO();

        if (!empty($julgamentoFinal)) {
            $julgamentoFinalTO->setId($julgamentoFinal->getId());
            $julgamentoFinalTO->setDescricao($julgamentoFinal->getDescricao());
            $julgamentoFinalTO->setDataCadastro($julgamentoFinal->getDataCadastro());
            $julgamentoFinalTO->setRetificacaoJustificativa($julgamentoFinal->getRetificacaoJustificativa());

            if (!$isResumo) {

                if (!empty($julgamentoFinal->getUsuario())) {
                    $julgamentoFinalTO->setUsuario(UsuarioTO::newInstanceFromEntity($julgamentoFinal->getUsuario()));
                }

                $status = $julgamentoFinal->getStatusJulgamentoFinal();
                if (!empty($status)) {
                    $julgamentoFinalTO->setStatusJulgamentoFinal(StatusGenericoTO::newInstance([
                        'id' => $status->getId(),
                        'descricao' => $status->getDescricao()
                    ]));
                }

                $usuario = $julgamentoFinal->getUsuario();
                if (!empty($usuario)) {
                    $julgamentoFinalTO->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
                }

                $indicacoesArray = $julgamentoFinal->getIndicacoes();
                if (!empty($indicacoesArray)) {
                    $indicacoes = [];
                    foreach ($indicacoesArray as $indicacao) {
                        array_push($indicacoes, IndicacaoJulgamentoFinalTO::newInstanceFromEntity($indicacao));
                    }
                    $julgamentoFinalTO->setIndicacoes($indicacoes);
                }

                $arquivos = [];
                if (!empty($julgamentoFinal->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $julgamentoFinal->getNomeArquivo(),
                        'nomeFisico' => $julgamentoFinal->getNomeArquivoFisico()
                    ]);
                }
                $julgamentoFinalTO->setArquivos($arquivos);

                if(!empty($julgamentoFinal->getJulgamentoFinalPai())){
                    $julgamentoFinalTO->setIdJulgamentoFinalPai($julgamentoFinal->getJulgamentoFinalPai()->getId());
                }
            }
        }
        return $julgamentoFinalTO;
    }

}
