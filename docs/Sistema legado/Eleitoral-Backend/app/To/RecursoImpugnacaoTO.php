<?php


namespace App\To;

use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Recurso de Impugnação
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoTO
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
     * @var string|null
     */
    private $nomeArquivo;

    /**
     * @var string|null
     */
    private $nomeArquivoFisico;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var JulgamentoImpugnacaoTO
     */
    private $julgamentoImpugnacao;

    /**
     * @var TipoGenericoTO
     */
    private $tipoSolicitacaoRecursoImpugnacao;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idJulgamentoImpugnacao
     */
    private $idJulgamentoImpugnacao;

    /**
     * @var $idTipoSolicitacaoRecursoImpugnacao
     */
    private $idTipoSolicitacaoRecursoImpugnacao;

    /**
     * @var ProfissionalTO $profissional
     */
    private $profissional;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var ContrarrazaoRecursoImpugnacaoTO
     */
    private $contrarrazaoRecursoImpugnacao;

    /**
     * @var bool
     */
    private $podeCadastrarContrarrazao;

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
    public function getNomeArquivo(): ?string
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string|null $nomeArquivo
     */
    public function setNomeArquivo(?string $nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string|null
     */
    public function getNomeArquivoFisico(): ?string
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param string|null $nomeArquivoFisico
     */
    public function setNomeArquivoFisico(?string $nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
    }

    /**
     * @return \DateTime
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return TipoGenericoTO
     */
    public function getTipoSolicitacaoRecursoImpugnacao(): ?TipoGenericoTO
    {
        return $this->tipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @param TipoGenericoTO $tipoSolicitacaoRecursoImpugnacao
     */
    public function setTipoSolicitacaoRecursoImpugnacao(?TipoGenericoTO $tipoSolicitacaoRecursoImpugnacao): void
    {
        $this->tipoSolicitacaoRecursoImpugnacao = $tipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @return JulgamentoImpugnacaoTO
     */
    public function getJulgamentoImpugnacao(): ?JulgamentoImpugnacaoTO
    {
        return $this->julgamentoImpugnacao;
    }

    /**
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacao
     */
    public function setJulgamentoImpugnacao(?JulgamentoImpugnacaoTO $julgamentoImpugnacao): void
    {
        $this->julgamentoImpugnacao = $julgamentoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * @return mixed
     */
    public function getIdJulgamentoImpugnacao()
    {
        return $this->idJulgamentoImpugnacao;
    }

    /**
     * @param mixed $idJulgamentoImpugnacao
     */
    public function setIdJulgamentoImpugnacao($idJulgamentoImpugnacao): void
    {
        $this->idJulgamentoImpugnacao = $idJulgamentoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getIdTipoSolicitacaoRecursoImpugnacao()
    {
        return $this->idTipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @param mixed $idTipoSolicitacaoRecursoImpugnacao
     */
    public function setIdTipoSolicitacaoRecursoImpugnacao($idTipoSolicitacaoRecursoImpugnacao): void
    {
        $this->idTipoSolicitacaoRecursoImpugnacao = $idTipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @return ProfissionalTO
     */
    public function getProfissional(): ?ProfissionalTO
    {
        return $this->profissional;
    }

    /**
     * @param ProfissionalTO $profissional
     */
    public function setProfissional(?ProfissionalTO $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return mixed
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param mixed $arquivos
     */
    public function setArquivos($arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return ContrarrazaoRecursoImpugnacaoTO
     */
    public function getContrarrazaoRecursoImpugnacao(): ?ContrarrazaoRecursoImpugnacaoTO
    {
        return $this->contrarrazaoRecursoImpugnacao;
    }

    /**
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacao
     */
    public function setContrarrazaoRecursoImpugnacao(?ContrarrazaoRecursoImpugnacaoTO
                                                       $contrarrazaoRecursoImpugnacao): void
    {
        $this->contrarrazaoRecursoImpugnacao = $contrarrazaoRecursoImpugnacao;
    }

    /**
     * @return bool
     */
    public function isPodeCadastrarContrarrazao(): ?bool
    {
        return $this->podeCadastrarContrarrazao;
    }

    /**
     * @param bool $podeCadastrarContrarrazao
     */
    public function setPodeCadastrarContrarrazao(?bool $podeCadastrarContrarrazao): void
    {
        $this->podeCadastrarContrarrazao = $podeCadastrarContrarrazao;
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoTO'.
     *
     * @param null $data
     * @return RecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoImpugnacaoTO = new RecursoImpugnacaoTO();

         if ($data != null) {
            $recursoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $recursoImpugnacaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $recursoImpugnacaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $recursoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $recursoImpugnacaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $recursoImpugnacaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $recursoImpugnacaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $recursoImpugnacaoTO->setIdJulgamentoImpugnacao(Arr::get($data,'idJulgamentoImpugnacao'));

            $recursoImpugnacaoTO->setIdTipoSolicitacaoRecursoImpugnacao(
                Arr::get($data, 'idTipoSolicitacaoRecursoImpugnacao')
            );

            $julgamentoImpugnacao = Arr::get($data, 'julgamentoImpugnacao');
            if (!empty($julgamentoImpugnacao)) {
                $recursoImpugnacaoTO->setJulgamentoImpugnacao(
                    JulgamentoImpugnacaoTO::newInstance($julgamentoImpugnacao)
                );
            }

            $tipoSolicitacaoRecursoImpugnacao = Arr::get($data, 'tipoSolicitacaoRecursoImpugnacao');
            if (!empty($tipoSolicitacaoRecursoImpugnacao)) {
                $recursoImpugnacaoTO->setTipoSolicitacaoRecursoImpugnacao(
                    TipoGenericoTO::newInstance($tipoSolicitacaoRecursoImpugnacao)
                );
            }

             $contrarrazaoRecursoImpugnacao = Arr::get($data, 'contrarrazaoRecursoImpugnacao');
             if (!empty($contrarrazaoRecursoImpugnacao)) {
                 $recursoImpugnacaoTO->setContrarrazaoRecursoImpugnacao(
                     ContrarrazaoRecursoImpugnacaoTO::newInstance($contrarrazaoRecursoImpugnacao)
                 );
             }

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $recursoImpugnacaoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $recursoImpugnacaoTO->setArquivos(array_map(function($arquivo){
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else if(!empty(Arr::get($data, 'nomeArquivo'))){
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $recursoImpugnacaoTO->setArquivos([$arquivo]);
            }
        }

        return $recursoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoTO'.
     *
     * @param RecursoImpugnacao $recursoImpugnacao
     * @return RecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($recursoImpugnacao, $isResumo = false)
    {
        $recursoImpugnacaoTO = new RecursoImpugnacaoTO();

        if (!empty($recursoImpugnacao)) {
            $recursoImpugnacaoTO->setId($recursoImpugnacao->getId());
            $recursoImpugnacaoTO->setDescricao($recursoImpugnacao->getDescricao());

            if (!$isResumo) {
                $recursoImpugnacaoTO->setDataCadastro($recursoImpugnacao->getDataCadastro());
                $recursoImpugnacaoTO->setNomeArquivo($recursoImpugnacao->getNomeArquivo());
                $recursoImpugnacaoTO->setNomeArquivoFisico($recursoImpugnacao->getNomeArquivoFisico());

                if(!empty($recursoImpugnacao->getJulgamentoImpugnacao())) {
                    $recursoImpugnacaoTO->setJulgamentoImpugnacao(JulgamentoImpugnacaoTO::newInstanceFromEntity(
                        $recursoImpugnacao->getJulgamentoImpugnacao()
                    ));
                }

                if(!empty($recursoImpugnacao->getProfissional())) {
                    $recursoImpugnacaoTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                        $recursoImpugnacao->getProfissional()
                    ));
                }

                if(!empty($recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao())) {
                    $recursoImpugnacaoTO->setTipoSolicitacaoRecursoImpugnacao(TipoGenericoTO::newInstance([
                        'id' => $recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao()->getId(),
                        'descricao' => $recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao()->getDescricao()
                    ]));
                }

                $arquivos = [];
                if(!empty($recursoImpugnacao->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $recursoImpugnacao->getNomeArquivo(),
                        'nomeFisico' => $recursoImpugnacao->getNomeArquivoFisico()
                    ]);
                }
                $recursoImpugnacaoTO->setArquivos($arquivos);
            }
        }

        return $recursoImpugnacaoTO;
    }

}
