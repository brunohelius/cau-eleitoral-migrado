<?php
/*
 * RetificacaoJulgamentoRecursoDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\RetificacaoJulgamentoRecursoDenuncia;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'RetificacaoJulgamentoRecursoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="RetificacaoJulgamentoRecursoDenuncia")
 */
class RetificacaoJulgamentoRecursoDenunciaTO
{

    /** @var \DateTime */
    private $data;

    /** @var UsuarioTO */
    private $usuario;

    /**
     * Retorna uma nova instância de 'RetificacaoJulgamentoRecursoDenunciaTO'.
     *
     * @param RetificacaoJulgamentoRecursoDenuncia $retificacaoJulgamentoRecursoDenuncia
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstanceFromEntity($retificacaoJulgamentoRecursoDenuncia = null)
    {
        $instance = new self();

        if (null !== $retificacaoJulgamentoRecursoDenuncia) {
            $instance->setData($retificacaoJulgamentoRecursoDenuncia->getData());

            $usuario = $retificacaoJulgamentoRecursoDenuncia->getUsuario();
            if (!is_null($usuario)) {
                $usuario->definirNomes();
                $instance->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
            }
        }

        return $instance;
    }

    /**
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return UsuarioTO
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO $usuario
     */
    public function setUsuario($usuario): void
    {
        $this->usuario = $usuario;
    }
}
