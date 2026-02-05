<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Declaração Atividade'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DeclaracaoAtividadeRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DECLARACAO_ATIVIDADE")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoAtividade extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DECLARACAO_ATIVIDADE", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_declaracao_atividade_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @ORM\Column(name="ID_DECLARACAO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $idDeclaracao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoDeclaracaoAtividade")
     * @ORM\JoinColumn(name="ID_TP_DECLARACAO_ATIVIDADE", referencedColumnName="ID_TP_DECLARACAO_ATIVIDADE", nullable=false)
     *
     * @var \App\Entities\TipoDeclaracaoAtividade
     */
    private $tipoDeclaracaoAtividade;

    /**
     * Dados da declaração
     *
     * @var mixed
     */
    private $declaracao;

    /**
     * Fábrica de instância de 'Declaração Atividade'.
     *
     * @param array $data
     * @return DeclaracaoAtividade
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $declaracaoAtividade = new DeclaracaoAtividade();

        if ($data != null) {
            $declaracaoAtividade->setId(Utils::getValue('id', $data));
            $declaracaoAtividade->setDeclaracao(Utils::getValue('declaracao', $data));
            $declaracaoAtividade->setIdDeclaracao(Utils::getValue('idDeclaracao', $data));

            $atividadeSecundaria = Utils::getValue('atividadeSecundaria', $data);
            if (!empty($atividadeSecundaria)) {
                $declaracaoAtividade->setAtividadeSecundaria(
                    AtividadeSecundariaCalendario::newInstance($atividadeSecundaria)
                );
            }

            $tipoDeclaracaoAtividade = Utils::getValue('tipoDeclaracaoAtividade', $data);
            if (!empty($tipoDeclaracaoAtividade)) {
                $declaracaoAtividade->setTipoDeclaracaoAtividade(
                    TipoDeclaracaoAtividade::newInstance($tipoDeclaracaoAtividade)
                );
            }
        }
        return $declaracaoAtividade;
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
     * @return AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     */
    public function setAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria): void
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
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
    public function setIdDeclaracao(int $idDeclaracao): void
    {
        $this->idDeclaracao = $idDeclaracao;
    }

    /**
     * @return TipoDeclaracaoAtividade
     */
    public function getTipoDeclaracaoAtividade()
    {
        return $this->tipoDeclaracaoAtividade;
    }

    /**
     * @param TipoDeclaracaoAtividade $tipoDeclaracaoAtividade
     */
    public function setTipoDeclaracaoAtividade(TipoDeclaracaoAtividade $tipoDeclaracaoAtividade)
    {
        $this->tipoDeclaracaoAtividade = $tipoDeclaracaoAtividade;
    }

    /**
     * @return mixed
     */
    public function getDeclaracao()
    {
        return $this->declaracao;
    }

    /**
     * @param mixed $declaracao
     */
    public function setDeclaracao($declaracao): void
    {
        $this->declaracao = $declaracao;
    }
}
