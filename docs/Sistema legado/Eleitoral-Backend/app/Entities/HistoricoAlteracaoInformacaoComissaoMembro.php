<?php
/*
 * HistoricoAlteracaoInformacaoComissaoMembro.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entities\HistoricoInformacaoComissaoMembro;

/**
 * Entidade de representação de 'Alterações dos campos no Histórico de Informação de Comissão Membro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoInformacaoComissaoMembroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HIST_ALT_COMISSAO_MEMBRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoAlteracaoInformacaoComissaoMembro extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HIST_ALT_COMISSAO_MEMBRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_alt_comissao_membro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ALTERACAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\HistoricoInformacaoComissaoMembro")
     * @ORM\JoinColumn(name="ID_HIST_COMISSAO_MEMBRO", referencedColumnName="ID_HIST_COMISSAO_MEMBRO", nullable=false)
     *
     * @var \App\Entities\HistoricoInformacaoComissaoMembro
     */
    private $historicoInformacaoComissaoMembro;

    /**
     * Cria uma nova instância de 'HistoricoAlteracaoInformacaoComissaoMembro'.
     *
     * @param null $data
     * @return HistoricoAlteracaoInformacaoComissaoMembro
     */
    public static function newInstance($data = null)
    {
        $historicoAlteracaoInformacaoMembro = new HistoricoAlteracaoInformacaoComissaoMembro();

        if ($data != null) {
            $historicoInformacaoMembro = HistoricoInformacaoComissaoMembro::newInstance(
                Utils::getValue('historicoInformacaoComissaoMembro', $data)
            );

            $historicoAlteracaoInformacaoMembro->setId(Utils::getValue('id', $data));
            $historicoAlteracaoInformacaoMembro->setDescricao(Utils::getValue('descricao', $data));
            $historicoAlteracaoInformacaoMembro->setHistoricoInformacaoComissaoMembro($historicoInformacaoMembro);
        }

        return $historicoAlteracaoInformacaoMembro;
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
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return HistoricoInformacaoComissaoMembro
     */
    public function getHistoricoInformacaoComissaoMembro(): HistoricoInformacaoComissaoMembro
    {
        return $this->historicoInformacaoComissaoMembro;
    }

    /**
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     */
    public function setHistoricoInformacaoComissaoMembro(HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro): void
    {
        $this->historicoInformacaoComissaoMembro = $historicoInformacaoComissaoMembro;
    }
}
