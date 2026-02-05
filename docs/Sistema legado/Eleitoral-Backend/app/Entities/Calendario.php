<?php
/*
 * Calendario.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

/**
 * Entidade de representação de 'Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CALENDARIO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Calendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CALENDARIO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_CALENDARIO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ST_IES", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoIES;

    /**
     * @ORM\Column(name="DT_INI_VIGENCIA", type="date", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataInicioVigencia;

    /**
     * @ORM\Column(name="DT_FIM_VIGENCIA", type="date", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataFimVigencia;

    /**
     * @ORM\Column(name="NU_IDADE_INI", type="integer", length=2, nullable=false)
     *
     * @var integer
     */
    private $idadeInicio;

    /**
     * @ORM\Column(name="NU_IDADE_FIM", type="integer", length=2, nullable=false)
     *
     * @var integer
     */
    private $idadeFim;

    /**
     * @ORM\Column(name="DT_INI_MANDATO", type="date", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataInicioMandato;

    /**
     * @ORM\Column(name="DT_FIM_MANDATO", type="date", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataFimMandato;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\CalendarioSituacao", mappedBy="calendario", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $situacoes;

    /**
     * @ORM\Column(name="ST_ATIVO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $ativo;

    /**
     * @ORM\Column(name="ST_EXCLUIDO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $excluido;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Eleicao", mappedBy="calendario")
     *
     * @var \App\Entities\Eleicao
     */
    private $eleicao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoCalendario", mappedBy="calendario")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\AtividadePrincipalCalendario", mappedBy="calendario", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $atividadesPrincipais;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\UfCalendario", mappedBy="calendario", fetch="EXTRA_LAZY"))
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $cauUf;
//
//    /**
//     * @ORM\OneToMany(targetEntity="App\Entities\ImpugnacaoResultado", mappedBy="calendario", fetch="EXTRA_LAZY")
//     *
//     * @var array|\Doctrine\Common\Collections\ArrayCollection
//     */
//    private $impugnacoesResultado;

    /**
     * Id da situação vigente
     *
     * @var integer
     */
    private $idSituacaoVigente;

    /**
     * @var stdClass
     */
    private $situacaoVigente;

    /**
     * @var boolean
     */
    private $cadastroRealizado;

    /**
     * @var boolean
     */
    private $atividadesDefinidas;

    /**
     * @var boolean
     */
    private $prazosDefinidos;

    /**
     * @var boolean
     */
    private $calendarioConcluido;

    /**
     * @var boolean
     */
    private $rascunho;

    /**
     * Fábrica de instância de 'Calendario'.
     *
     * @param array $data
     * @return Calendario
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $calendario = new Calendario();

        if ($data != null) {
            $calendario->setId(Utils::getValue('id', $data));
            $calendario->setAtivo(Utils::getValue('ativo', $data));
            $calendario->setSituacaoIES(Utils::getValue('situacaoIES', $data));
            $calendario->setDataInicioVigencia(Utils::getValue('dataInicioVigencia', $data));
            $calendario->setDataFimVigencia(Utils::getValue('dataFimVigencia', $data));
            $calendario->setIdadeInicio(Utils::getValue('idadeInicio', $data));
            $calendario->setIdadeFim(Utils::getValue('idadeFim', $data));
            $calendario->setDataInicioMandato(Utils::getValue('dataInicioMandato', $data));
            $calendario->setDataFimMandato(Utils::getValue('dataFimMandato', $data));
            $calendario->setSequenciaAno(Utils::getValue('sequenciaAno', $data));
            $calendario->setExcluido(Utils::getValue('excluido', $data));
            $calendario->setIdSituacaoVigente(Utils::getValue('idSituacao', $data));

            $calendario->setCadastroRealizado($calendario->hasId());
            $calendario->setAtividadesDefinidas($calendario->hasAtividadesPrincipais());

            $eleicao = Eleicao::newInstance(Utils::getValue('eleicao', $data));
            $calendario->setEleicao($eleicao);

            $calendario->setRascunho(Utils::getBooleanValue('rascunho', $data));

            $arquivos = Utils::getValue('arquivos', $data);
            if (!empty($arquivos)) {
                foreach ($arquivos as $dataArquivo) {
                    $calendario->adicionarArquivo(ArquivoCalendario::newInstance($dataArquivo));
                }
            }

            $atividadesPrincipais = Utils::getValue('atividadesPrincipais', $data);
            if (!empty($atividadesPrincipais)) {
                foreach ($atividadesPrincipais as $dataAtividadePrincipal) {
                    $calendario->adicionarAtividade(
                        AtividadePrincipalCalendario::newInstance($dataAtividadePrincipal)
                    );
                }
            }

            $situacoes = Utils::getValue('situacoes', $data);
            if (!empty($situacoes)) {
                foreach ($situacoes as $dataSituacao) {
                    $calendario->adicionarSituacao(CalendarioSituacao::newInstance($dataSituacao));
                }
            }

            $cauUfs = Utils::getValue('cauUf', $data);
            if (!empty($cauUfs)) {
                foreach ($cauUfs as $cauUf) {
                    $calendario->adicionarCauUf(UfCalendario::newInstance($cauUf));
                }
            }
        }

        return $calendario;
    }

    /**
     * Preenche o valor de situacao vigente para o calendario
     */
    public function setSituacaoVigente()
    {
        $situacoes = $this->getSituacoesOrdenadasPorData($this->getSituacoes());

        if (!empty($situacoes) && count($situacoes) > 0){
            $situacao = $situacoes->last();

            $situacaoVigente = new stdClass();
            $situacaoVigente->id = $situacao->getId();

            $situacaoCalendario = $situacao->getSituacaoCalendario();
            $situacaoVigente->descricao = !empty($situacaoCalendario) ? $situacaoCalendario->getDescricao() : null;

            $this->situacaoVigente = $situacaoVigente;
            $this->situacoes = null;
        }
    }

    /**
     * Método para filtrar os prazos repetidos
     */
    public function filtrarPrazos()
    {
        if (!empty($this->atividadesPrincipais)) {
            /** @var  $atividadePrincipal AtividadePrincipalCalendario */
            foreach ($this->atividadesPrincipais as $atividadePrincipal) {
                $prazos = $atividadePrincipal->getPrazos();
                if (!empty($prazos)) {
                    /** @var  $prazo PrazoCalendario */
                    foreach ($prazos as $prazo) {
                        if (!empty($prazo->getPrazoPai())) {
                            $prazos->removeElement($prazo);
                        }
                    }
                }
            }
        }
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivos)) {
            foreach ($this->arquivos as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }

    /**
     * Verifica se a situação do calendário é 'concluído'
     *
     * @return boolean
     */
    private function isSituacaoConcluida()
    {
        $situacaoCalendarioVigente = $this->getSituacaoVigente();

        return !empty($situacaoCalendarioVigente)
            && Constants::SITUACAO_CALENDARIO_CONCLUIDO == $situacaoCalendarioVigente->getId();
    }

    /**
     * Define os valores que determinam o progresso do cadastro do calendário.
     *
     */
    public function definirProgresso()
    {
        $this->setCadastroRealizado($this->hasId());
        $this->setAtividadesDefinidas($this->hasAtividadesPrincipais());
        $this->setCalendarioConcluido($this->isSituacaoConcluida());
    }

    /**
     * Define o valor de Eleição mediante a existencia de Ano e Sequencia
     */
    public function definirEleicao()
    {
        if (!empty($this->sequenciaAno) and !empty($this->ano)) {
            $this->eleicao = $this->ano . '/' . str_pad($this->sequenciaAno, 3, "0", STR_PAD_LEFT);
        }
    }

    /**
     * Verifica se o calendário tem algum valor definido para o id.
     *
     * @return boolean
     */
    public function hasId()
    {
        return !empty($this->getId());
    }

    /**
     * Verifica se o calendário tem alguma atividade principal vinculada.
     *
     * @return boolean
     */
    public function hasAtividadesPrincipais()
    {
        if ($this->getAtividadesPrincipais() != null) {
            return $this->getAtividadesPrincipais()->count() > 0;
        }
        return false;
    }

    /**
     * Retorna a situação vigente do calendário.
     *
     * @return CalendarioSituacao |null
     */
    private function getSituacaoVigente()
    {
        $situacaoCalendario = null;

        $situacoes = $this->getSituacoesOrdenadasPorData($this->getSituacoes());

        if (!empty($situacoes) && count($situacoes) > 0) {
            $calendarioSituacao = $situacoes->last();
            $situacaoCalendario = $calendarioSituacao->getSituacaoCalendario();
        }

        return $situacaoCalendario;
    }

    /**
     * Retorna as situações ordenadas por data.
     *
     * @param $situacoes
     * @return mixed
     */
    private function getSituacoesOrdenadasPorData($situacoes)
    {
        $situacoesOrdenadas = new ArrayCollection();

        if (!empty($situacoes)) {
            $iterator = $situacoes->getIterator();

            $iterator->uasort(function ($a, $b) {
                return ($a->getData() < $b->getData()) ? -1 : 1;
            });

            $situacoesOrdenadas = new ArrayCollection(iterator_to_array($iterator));
        }

        return $situacoesOrdenadas;
    }

    /**
     * Adiciona o 'CalendarioSituacao' à sua respectiva coleção.
     *
     * @param CalendarioSituacao $calendarioSituacao
     */
    private function adicionarSituacao(CalendarioSituacao $calendarioSituacao)
    {
        if ($this->getSituacoes() == null) {
            $this->setSituacoes(new ArrayCollection());
        }

        if (!empty($calendarioSituacao)) {
            $calendarioSituacao->setCalendario($this);
            $this->getSituacoes()->add($calendarioSituacao);
        }
    }

    /**
     * Adiciona o 'UfCalendario' à sua respectiva coleção.
     *
     * @param UfCalendario $cauUf
     */
    private function adicionarCauUf(UfCalendario $cauUf)
    {
        if ($this->getCauUf() == null) {
            $this->setCauUf(new ArrayCollection());
        }

        if (!empty($cauUf)) {
            $cauUf->setCalendario($this);
            $this->getCauUf()->add($cauUf);
        }
    }

    /**
     * Adiciona a 'AtividadePrincipalCalendario' à sua respectiva coleção.
     *
     * @param AtividadePrincipalCalendario $atividadePrincipal
     */
    private function adicionarAtividade(AtividadePrincipalCalendario $atividadePrincipal)
    {
        if ($this->getAtividadesPrincipais() == null) {
            $this->setAtividadesPrincipais(new ArrayCollection());
        }

        if (!empty($atividadePrincipal)) {
            $atividadePrincipal->setCalendario($this);
            $this->getAtividadesPrincipais()->add($atividadePrincipal);
        }
    }

    /**
     * Adiciona o 'ArquivoCalendario' à sua respectiva coleção.
     *
     * @param ArquivoCalendario $arquivo
     */
    private function adicionarArquivo(ArquivoCalendario $arquivo)
    {
        if ($this->getArquivos() == null) {
            $this->setArquivos(new ArrayCollection());
        }

        if (!empty($arquivo)) {
            $arquivo->setCalendario($this);
            $this->getArquivos()->add($arquivo);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id == 0) {
            $this->id = null;
        }
    }

    /**
     * @return bool
     */
    public function isSituacaoIES()
    {
        return $this->situacaoIES;
    }

    /**
     * @param bool $situacaoIES
     */
    public function setSituacaoIES($situacaoIES)
    {
        $this->situacaoIES = $situacaoIES;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getCauUf()
    {
        return $this->cauUf;
    }

    /**
     * @param array|ArrayCollection $cauUf
     */
    public function setCauUf($cauUf): void
    {
        $this->cauUf = $cauUf;
    }

    /**
     * @return DateTime
     */
    public function getDataInicioVigencia()
    {
        return $this->dataInicioVigencia;
    }

    /**
     * @param DateTime $dataInicioVigencia
     * @throws Exception
     */
    public function setDataInicioVigencia($dataInicioVigencia)
    {
        if (is_string($dataInicioVigencia)) {
            $dataInicioVigencia = new DateTime($dataInicioVigencia);
        }
        $this->dataInicioVigencia = $dataInicioVigencia;
    }

    /**
     * @return DateTime
     */
    public function getDataFimVigencia()
    {
        return $this->dataFimVigencia;
    }

    /**
     * @param DateTime $dataFimVigencia
     * @throws Exception
     */
    public function setDataFimVigencia($dataFimVigencia)
    {
        if (is_string($dataFimVigencia)) {
            $dataFimVigencia = new DateTime($dataFimVigencia);
        }
        $this->dataFimVigencia = $dataFimVigencia;
    }

    /**
     * @return int
     */
    public function getIdadeInicio()
    {
        return $this->idadeInicio;
    }

    /**
     * @param int $idadeInicio
     */
    public function setIdadeInicio($idadeInicio)
    {
        $this->idadeInicio = $idadeInicio;
    }

    /**
     * @return int
     */
    public function getIdadeFim()
    {
        return $this->idadeFim;
    }

    /**
     * @param int $idadeFim
     */
    public function setIdadeFim($idadeFim)
    {
        $this->idadeFim = $idadeFim;
    }

    /**
     * @return DateTime
     */
    public function getDataInicioMandato()
    {
        return $this->dataInicioMandato;
    }

    /**
     * @param DateTime $dataInicioMandato
     * @throws Exception
     */
    public function setDataInicioMandato($dataInicioMandato)
    {
        if (is_string($dataInicioMandato)) {
            $dataInicioMandato = new DateTime($dataInicioMandato);
        }
        $this->dataInicioMandato = $dataInicioMandato;
    }

    /**
     * @return DateTime
     */
    public function getDataFimMandato()
    {
        return $this->dataFimMandato;
    }

    /**
     * @param DateTime $dataFimMandato
     * @throws Exception
     */
    public function setDataFimMandato($dataFimMandato)
    {
        if (is_string($dataFimMandato)) {
            $dataFimMandato = new DateTime($dataFimMandato);
        }
        $this->dataFimMandato = $dataFimMandato;
    }

    /**
     * @return int
     */
    public function getSequenciaAno()
    {
        return $this->sequenciaAno;
    }

    /**
     * @param int $sequenciaAno
     */
    public function setSequenciaAno($sequenciaAno)
    {
        $this->sequenciaAno = $sequenciaAno;
        if (!empty($this->sequenciaAno) and !empty($this->ano)) {
            $this->eleicao = $this->ano . '/' . str_pad($this->sequenciaAno, 3, "0", STR_PAD_LEFT);
        }
    }

    /**
     * @return array|ArrayCollection
     */
    public function getSituacoes()
    {
        return $this->situacoes;
    }

    /**
     * @param array|ArrayCollection $situacoes
     */
    public function setSituacoes($situacoes)
    {
        $this->situacoes = $situacoes;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return bool
     */
    public function isExcluido()
    {
        return $this->excluido;
    }

    /**
     * @param bool $excluido
     */
    public function setExcluido($excluido)
    {
        $this->excluido = $excluido;
    }

    /**
     * @return Eleicao
     */
    public function getEleicao()
    {
        return $this->eleicao;
    }

    /**
     * @param Eleicao $eleicao
     */
    public function setEleicao($eleicao)
    {
        $this->eleicao = $eleicao;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array|ArrayCollection $arquivos
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getAtividadesPrincipais()
    {
        return $this->atividadesPrincipais;
    }

    /**
     * @param array|ArrayCollection $atividadesPrincipais
     */
    public function setAtividadesPrincipais($atividadesPrincipais)
    {
        $this->atividadesPrincipais = $atividadesPrincipais;
    }

    /**
     * @return int
     */
    public function getIdSituacaoVigente()
    {
        return $this->idSituacaoVigente;
    }

    /**
     * @param int $idSituacaoVigente
     */
    public function setIdSituacaoVigente($idSituacaoVigente): void
    {
        $this->idSituacaoVigente = $idSituacaoVigente;
    }

    /**
     * @return bool
     */
    public function isCadastroRealizado()
    {
        return $this->cadastroRealizado;
    }

    /**
     * @param bool $cadastroRealizado
     */
    public function setCadastroRealizado($cadastroRealizado)
    {
        $this->cadastroRealizado = $cadastroRealizado;
    }

    /**
     * @return bool
     */
    public function isAtividadesDefinidas()
    {
        return $this->atividadesDefinidas;
    }

    /**
     * @param bool $atividadesDefinidas
     */
    public function setAtividadesDefinidas($atividadesDefinidas): void
    {
        $this->atividadesDefinidas = $atividadesDefinidas;
    }

    /**
     * @return bool
     */
    public function isPrazosDefinidos()
    {
        return $this->prazosDefinidos;
    }

    /**
     * @param bool $prazosDefinidos
     */
    public function setPrazosDefinidos($prazosDefinidos): void
    {
        $this->prazosDefinidos = $prazosDefinidos;
    }

    /**
     * @return boolean
     */
    public function isCalendarioConcluido()
    {
        return $this->calendarioConcluido;
    }

    /**
     * @param boolean $calendarioConcluido
     */
    public function setCalendarioConcluido($calendarioConcluido): void
    {
        $this->calendarioConcluido = $calendarioConcluido;
    }

    /**
     * @return bool
     */
    public function isRascunho(): ?bool
    {
        return $this->rascunho;
    }

    /**
     * @param bool $rascunho
     */
    public function setRascunho(bool $rascunho): void
    {
        $this->rascunho = $rascunho;
    }
//
//    /**
//     * @return array|ArrayCollection
//     */
//    public function getImpugnacoesResultado()
//    {
//        return $this->impugnacoesResultado;
//    }
//
//    /**
//     * @param array|ArrayCollection $impugnacoesResultado
//     */
//    public function setImpugnacoesResultado($impugnacoesResultado): void
//    {
//        $this->impugnacoesResultado = $impugnacoesResultado;
//    }

}
