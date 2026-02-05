<?php
/*
 * Entity.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Entities;

use App\Util\JsonUtils;

/**
 * Classe abstrata Entity.
 *
 * @package App\Entities
 * @author Squadra Tecnologia
 */
abstract class Entity
{

    /**
     * Retorna o 'id' da 'Entidade'.
     *
     * @return integer
     */
    abstract public function getId();

    /**
     *
     * @return mixed|string
     */
    public function __toString()
    {
        return JsonUtils::toJson($this);
    }
}
