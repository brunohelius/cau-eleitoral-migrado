<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Resposta da Declaracao de Representatividade'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RespostaDeclaracaoRepresentatividadeRepository")
 * @ORM\Table(schema="eleitoral", name="tb_resposta_declaracao_representatividade")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoRepresentatividade extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_resposta_declaracao_representatividade_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="id_membro_chapa", referencedColumnName="id_membro_chapa")
     *
     * @var \App\Entities\MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ItemDeclaracao")
     * @ORM\JoinColumn(name="id_item_declaracao", referencedColumnName="id_item_declaracao")
     *
     * @var \App\Entities\ItemDeclaracao
     */
    private $itemDeclaracao;

    /**
     * Fábrica de instância de 'Resposta da Declaracao de Representatividade'.
     *
     * @param array $data
     *
     * @return RespostaDeclaracaoRepresentatividade
     */
    public static function newInstance($data = null)
    {
        $respostaDeclaracaoRepresentatividade = new RespostaDeclaracaoRepresentatividade();

        if ($data != null) {
            $respostaDeclaracaoRepresentatividade->setId(Utils::getValue('id', $data));

            $itemDeclaracao = Utils::getValue('itemDeclaracao', $data);
            if (!empty($itemDeclaracao)) {
                $respostaDeclaracaoRepresentatividade->setItemDeclaracao(ItemDeclaracao::newInstance($itemDeclaracao));
            }

            $membroChapa = Utils::getValue('membroChapa', $data);
            if (!empty($membroChapa)) {
                $respostaDeclaracaoRepresentatividade->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }
        }
        return $respostaDeclaracaoRepresentatividade;
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
     * @return MembroChapa
     */
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapa $membroChapa
     */
    public function setMembroChapa(MembroChapa $membroChapa): void
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return ItemDeclaracao
     */
    public function getItemDeclaracao()
    {
        return $this->itemDeclaracao;
    }

    /**
     * @param ItemDeclaracao $itemDeclaracao
     */
    public function setItemDeclaracao(ItemDeclaracao $itemDeclaracao): void
    {
        $this->itemDeclaracao = $itemDeclaracao;
    }
}
