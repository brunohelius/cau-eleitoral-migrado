<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 15:26
 */

namespace App\Entities;

use App\Config\Constants;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\ChapaEleicaoTO;
use App\To\TipoCandidaturaTO;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Atividade Secundaria do Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AtividadeSecundariaCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ATIV_SECUNDARIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class AtividadeSecundariaCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ATIV_SECUNDARIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_ativ_secundaria_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_INI", type="date", nullable=true)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataInicio;

    /**
     * @ORM\Column(name="DT_FIM", type="date", nullable=true)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataFim;

    /**
     * @ORM\Column(name="NR_NIVEL", type="integer", nullable=false)
     *
     * @var integer
     */
    private $nivel;

    /**
     * @ORM\Column(name="DS_ATIV_SECUNDARIA", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadePrincipalCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_PRINCIPAL", referencedColumnName="ID_ATIV_PRINCIPAL", nullable=false)
     *
     * @var \App\Entities\AtividadePrincipalCalendario
     */
    private $atividadePrincipalCalendario;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\InformacaoComissaoMembro", mappedBy="atividadeSecundaria", fetch="EXTRA_LAZY")
     *
     * @var \App\Entities\InformacaoComissaoMembro
     */
    private $informacaoComissaoMembro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\EmailAtividadeSecundaria", mappedBy="atividadeSecundaria", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $emailsAtividadeSecundaria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DeclaracaoAtividade", mappedBy="atividadeSecundaria", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $declaracoesAtividadeSecundaria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\HistoricoExtratoConselheiro", mappedBy="atividadeSecundaria", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $historicosExtratoConselheiro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\HistoricoParametroConselheiro", mappedBy="atividadeSecundaria", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $historicoParametroConselheiro;

    /**
     * @ORM\Column(name="ID_DECLARACAO", type="integer", nullable=true)
     *
     * @var integer
     */
    private $idDeclaracao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ChapaEleicao", mappedBy="atividadeSecundariaCalendario", fetch="EXTRA_LAZY")
     *
     * @var ArrayCollection
     */
    private $chapasEleicao;

    /**
     * @var integer
     */
    private $statusAtividade;

    /**
     * @var bool
     */
    private $isPrazoVigente;

    /**
     * Fábrica de instância de Atividade Secundaria do Calendario'.
     *
     * @param array $data
     * @return AtividadeSecundariaCalendario
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $atividadeSecundaria = new AtividadeSecundariaCalendario();

        if ($data != null) {
            $atividadeSecundaria->setId(Utils::getValue('id', $data));
            $atividadeSecundaria->setDataInicio(Utils::getValue('dataInicio', $data));
            $atividadeSecundaria->setDataFim(Utils::getValue('dataFim', $data));
            $atividadeSecundaria->setNivel(Utils::getValue('nivel', $data));
            $atividadeSecundaria->setDescricao(Utils::getValue('descricao', $data));
            $atividadeSecundaria->setIdDeclaracao(Utils::getValue('idDeclaracao', $data));

            $atividadePrincipal = Utils::getValue('atividadePrincipalCalendario', $data);
            if (!empty($atividadePrincipal)) {
                $atividadeSecundaria->setAtividadePrincipalCalendario(
                    AtividadePrincipalCalendario::newInstance($atividadePrincipal)
                );
            }

            $informacaoComissaoMembro = Utils::getValue('informacaoComissaoMembro', $data);
            if (!empty($informacaoComissaoMembro)) {
                $atividadeSecundaria->setInformacaoComissaoMembro(
                    InformacaoComissaoMembro::newInstance($informacaoComissaoMembro)
                );
            }

            $emailsAtividadeSecundaria = Utils::getValue('emailsAtividadeSecundaria', $data);
            if (!empty($emailsAtividadeSecundaria)) {
                foreach ($emailsAtividadeSecundaria as $emailAtividadeSecundaria) {
                    $atividadeSecundaria->adicionarEmailAtividadeSecundaria(EmailAtividadeSecundaria::newInstance($emailAtividadeSecundaria));
                }
            }

            $declaracoesAtividade = Utils::getValue('declaracoesAtividadeSecundaria', $data);
            if (!empty($declaracoesAtividade)) {
                foreach ($declaracoesAtividade as $declaracaoAtividade) {
                    $atividadeSecundaria->adicionarDeclaracaoAtividadeSecundaria(DeclaracaoAtividade::newInstance(
                        $declaracaoAtividade
                    ));
                }
            }

            $historicosExtratoConselhieros = Utils::getValue('historicosExtratoConselheiro', $data);
            if (!empty($historicosExtratoConselhieros)) {
                foreach ($historicosExtratoConselhieros as $historicoExtratoConselhieros) {
                    $atividadeSecundaria->adicionarHistoricoExtratoConselheiro(HistoricoExtratoConselheiro::newInstance(
                        $historicoExtratoConselhieros
                    ));
                }
            }

            $historicosParametroConselheiro = Utils::getValue('historicoParametroConselheiro', $data);
            if (!empty($historicosParametroConselheiro)) {
                foreach ($historicosParametroConselheiro as $historicoParametroConselheiro) {
                    $atividadeSecundaria->adicionarHistoricoParametroConselheiro(HistoricoParametroConselheiro::newInstance(
                        $historicoParametroConselheiro
                    ));
                }
            }

            $chapasEleicao = Utils::getValue('chapasEleicao', $data);
            if (!empty($chapasEleicao)) {
                foreach ($chapasEleicao as $chapaEleicao) {
                    $atividadeSecundaria->addChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
                }
            }
        }

        return $atividadeSecundaria;
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param DateTime $dataInicio
     * @throws Exception
     */
    public function setDataInicio($dataInicio): void
    {
        if (is_string($dataInicio)) {
            $dataInicio = new DateTime($dataInicio);
        }
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param DateTime $dataFim
     * @throws Exception
     */
    public function setDataFim($dataFim): void
    {
        if (is_string($dataFim)) {
            $dataFim = new DateTime($dataFim);
        }
        $this->dataFim = $dataFim;
    }

    /**
     * @return AtividadePrincipalCalendario
     */
    public function getAtividadePrincipalCalendario()
    {
        return $this->atividadePrincipalCalendario;
    }

    /**
     * @param AtividadePrincipalCalendario $atividadePrincipalCalendario
     */
    public function setAtividadePrincipalCalendario($atividadePrincipalCalendario): void
    {
        $this->atividadePrincipalCalendario = $atividadePrincipalCalendario;
    }

    /**
     * @return int
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param int $nivel
     */
    public function setNivel($nivel): void
    {
        $this->nivel = $nivel;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return InformacaoComissaoMembro
     */
    public function getInformacaoComissaoMembro()
    {
        return $this->informacaoComissaoMembro;
    }

    /**
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     */
    public function setInformacaoComissaoMembro($informacaoComissaoMembro)
    {
        $this->informacaoComissaoMembro = $informacaoComissaoMembro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getEmailsAtividadeSecundaria()
    {
        return $this->emailsAtividadeSecundaria;
    }

    /**
     * @param array|ArrayCollection $emailsAtividadeSecundaria
     */
    public function setEmailsAtividadeSecundaria($emailsAtividadeSecundaria): void
    {
        $this->emailsAtividadeSecundaria = $emailsAtividadeSecundaria;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getDeclaracoesAtividadeSecundaria()
    {
        return $this->declaracoesAtividadeSecundaria;
    }

    /**
     * @param array|ArrayCollection $declaracoesAtividadeSecundaria
     */
    public function setDeclaracoesAtividadeSecundaria($declaracoesAtividadeSecundaria): void
    {
        $this->declaracoesAtividadeSecundaria = $declaracoesAtividadeSecundaria;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getHistoricosExtratoConselheiro()
    {
        return $this->historicosExtratoConselheiro;
    }

    /**
     * @param array|ArrayCollection $historicosExtratoConselheiro
     */
    public function setHistoricosExtratoConselheiro($historicosExtratoConselheiro): void
    {
        $this->historicosExtratoConselheiro = $historicosExtratoConselheiro;
    }

    /**
     * @param array|ArrayCollection $historicoParametroConselheiro
     */
    public function setHistoricoParametroConselheiro($historicoParametroConselheiro): void
    {
        $this->historicoParametroConselheiro = $historicoParametroConselheiro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getHistoricoParametroConselheiro()
    {
        return $this->historicoParametroConselheiro;
    }

    /**
     * @return int
     */
    public function getIdDeclaracao()
    {
        return $this->idDeclaracao;
    }

    /**
     * @param int $idDeclaracao
     */
    public function setIdDeclaracao($idDeclaracao): void
    {
        $this->idDeclaracao = $idDeclaracao;
    }

    /**
     * @return ArrayCollection
     */
    public function getChapasEleicao()
    {
        return $this->chapasEleicao;
    }

    /**
     * @param ArrayCollection $chapasEleicao
     */
    public function setChapasEleicao($chapasEleicao)
    {
        $this->chapasEleicao = $chapasEleicao;
    }

    /**
     * @return integer
     */
    public function getStatusAtividade()
    {
        return $this->statusAtividade;
    }

    /**
     * @param integer $statusAtividade
     */
    public function setStatusAtividade($statusAtividade): void
    {
        $this->statusAtividade = $statusAtividade;
    }

    /**
     * Adiciona a 'HistoricoExtratoConselheiro' à sua respectiva coleção.
     *
     * @param HistoricoExtratoConselheiro $historicoExtratoConselheiro
     */
    private function adicionarHistoricoExtratoConselheiro(HistoricoExtratoConselheiro $historicoExtratoConselheiro)
    {
        if ($this->getHistoricosExtratoConselheiro() == null) {
            $this->setHistoricosExtratoConselheiro(new ArrayCollection());
        }

        if (!empty($historicoExtratoConselheiro)) {
            $historicoExtratoConselheiro->setAtividadeSecundaria($this);
            $this->getHistoricosExtratoConselheiro()->add($historicoExtratoConselheiro);
        }
    }

    /**
     * Adiciona a 'HistoricoParametroConselheiro' à sua respectiva coleção.
     *
     * @param HistoricoParametroConselheiro $historicoParametroConselheiro
     */
    private function adicionarHistoricoParametroConselheiro(HistoricoParametroConselheiro $historicoParametroConselheiro)
    {
        if ($this->getHistoricoParametroConselheiro() == null) {
            $this->setHistoricoParametroConselheiro(new ArrayCollection());
        }

        if (!empty($historicoParametroConselheiro)) {
            $historicoParametroConselheiro->setAtividadeSecundaria($this);
            $this->getHistoricoParametroConselheiro()->add($historicoParametroConselheiro);
        }
    }

    /**
     * Adiciona o 'E-mail Atividade Secundária' à sua respectiva coleção.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     */
    private function adicionarEmailAtividadeSecundaria(EmailAtividadeSecundaria $emailAtividadeSecundaria)
    {
        if ($this->getEmailsAtividadeSecundaria() == null) {
            $this->setEmailsAtividadeSecundaria(new ArrayCollection());
        }

        if (!empty($emailAtividadeSecundaria)) {
            $emailAtividadeSecundaria->setAtividadeSecundaria($this);
            $this->getEmailsAtividadeSecundaria()->add($emailAtividadeSecundaria);
        }
    }

    /**
     * Adiciona o 'E-mail Atividade Secundária' à sua respectiva coleção.
     *
     * @param DeclaracaoAtividade $declaracaoAtividade
     */
    private function adicionarDeclaracaoAtividadeSecundaria(DeclaracaoAtividade $declaracaoAtividade)
    {
        if ($this->getDeclaracoesAtividadeSecundaria() == null) {
            $this->setDeclaracoesAtividadeSecundaria(new ArrayCollection());
        }

        if (!empty($declaracaoAtividade)) {
            $declaracaoAtividade->setAtividadeSecundaria($this);
            $this->getDeclaracoesAtividadeSecundaria()->add($declaracaoAtividade);
        }
    }

    /**
     * Adiciona o 'Chapa Eleição' à sua respectiva coleção.
     *
     * @param ChapaEleicao $chapaEleicao
     */
    private function addChapaEleicao(ChapaEleicao $chapaEleicao)
    {
        if ($this->getChapasEleicao() == null) {
            $this->setChapasEleicao(new ArrayCollection());
        }

        if (!empty($emailAtividadeSecundaria)) {
            $chapaEleicao->setAtividadeSecundariaCalendario($this);
            $this->getChapasEleicao()->add($chapaEleicao);
        }
    }

    /**
     * @return bool
     */
    public function isPrazoVigente(): bool
    {
        return $this->isPrazoVigente;
    }

    /**
     * @throws Exception
     */
    public function setIsPrazoVigente(): void
    {
        if(!empty($this->getDataFim())){
            $dataAtual = Utils::getDataHoraZero();
            $dataInicioZero = Utils::getDataHoraZero($this->getDataInicio());
            $dataFimZero = Utils::getDataHoraZero($this->getDataFim());

            $this->isPrazoVigente = ($dataAtual >= $dataInicioZero && $dataAtual <= $dataFimZero);
        }
    }

    /**
     * Seleciona o histórico mais recente
     */
    public function selecionarHistoricoParametroRecente($idCauUf)
    {
        $ultimoHistorico = null;
        $maiorId = 0;
        if (!empty($this->historicoParametroConselheiro)) {
            foreach ($this->historicoParametroConselheiro as $historico) {
                if($historico->getIdCauUf() == $idCauUf){
                    if ($historico->getId() > $maiorId) {
                        $maiorId = $historico->getId();
                        $ultimoHistorico = $historico;
                    }
                }
            }
        }
        return $ultimoHistorico;
    }
}
