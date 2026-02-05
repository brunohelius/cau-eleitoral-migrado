<?php
/*
 * DeclaracaoFiltroTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\To;

use App\Util\Utils;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada a 'Declaracao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DeclaracaoFiltroTO")
 */
class DeclaracaoFiltroTO
{
    /**
     * ID do Módulo
     * @var integer
     * @OA\Property()
     */
    private $idModulo;

    /**
     * Nome da declaração
     * @var string
     * @OA\Property()
     */
    private $nome;

    /**
     * ids da declaração
     * @var array
     */
    private $ids;

    /**
     * Fabricação estática de 'ProfissionalTO'.
     *
     * @param array|null $data
     *
     * @return DeclaracaoFiltroTO
     */
    public static function newInstance($data = null)
    {
        $declaracaoFiltroTO = new DeclaracaoFiltroTO();

        if ($data != null) {
            $declaracaoFiltroTO->setIdModulo(Utils::getValue("idModulo", $data));
            $declaracaoFiltroTO->setNome(Utils::getValue("nome", $data));
            $declaracaoFiltroTO->setIds(Utils::getValue("ids", $data));
        }

        return $declaracaoFiltroTO;
    }

    /**
     * @return int
     */
    public function getIdModulo()
    {
        return $this->idModulo;
    }

    /**
     * @param int $idModulo
     */
    public function setIdModulo($idModulo): void
    {
        $this->idModulo = $idModulo;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return array
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     */
    public function setIds(?array $ids): void
    {
        $this->ids = $ids;
    }
}