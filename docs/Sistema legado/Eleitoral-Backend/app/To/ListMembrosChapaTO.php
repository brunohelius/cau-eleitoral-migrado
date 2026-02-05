<?php


namespace App\To;

use App\Entities\MembroChapa;

/**
 * Classe de transferência para a lista de membros chapa
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ListMembrosChapaTO
{

    /**
     * @var array|null
     */
    private $membrosSemPendencia;

    /**
     * @var array|null
     */
    private $membrosComPendencia;

    /**
     * @var array|null
     */
    private $membrosChapa;

    /**
     * @param array|null $membrosSemPendencia
     */
    public function setMembrosSemPendencia(?array $membrosSemPendencia): void
    {
        $this->membrosSemPendencia = $membrosSemPendencia;
    }

    /**
     * @param array|null $membrosComPendencia
     */
    public function setMembrosComPendencia(?array $membrosComPendencia): void
    {
        $this->membrosComPendencia = $membrosComPendencia;
    }

    /**
     * @param array|null $membrosChapa
     */
    public function setMembrosChapa(?array $membrosChapa): void
    {
        $this->membrosChapa = $membrosChapa;
    }

    /**
     * @return array|null
     */
    public function getMembrosSemPendencia(): ?array
    {
        return $this->membrosSemPendencia;
    }

    /**
     * @param MembroChapaTO|null $membroChapa
     */
    public function addMembrosSemPendencia(MembroChapaTO $membroChapa = null)
    {
        $this->membrosSemPendencia[] = $membroChapa;
    }

    /**
     * @return array|null
     */
    public function getMembrosComPendencia(): ?array
    {
        return $this->membrosComPendencia;
    }

    /**
     * @param MembroChapaTO|null $membroChapa
     */
    public function addMembrosComPendencia(MembroChapaTO $membroChapa = null)
    {
        $this->membrosComPendencia[] = $membroChapa;
    }

    /**
     * @return array|null
     */
    public function getMembrosChapa(): ?array
    {
        return $this->membrosChapa;
    }

    /**
     * @param MembroChapaTO|null $membroChapa
     */
    public function addMembroChapa(MembroChapaTO $membroChapa = null): void
    {
        $this->membrosChapa[] = $membroChapa;
    }

    /**
     * Retorna uma nova instância de 'ListMembrosChapaTO'.
     *
     * @param null $data
     * @return ListMembrosChapaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $listMembrosChapaTO = new ListMembrosChapaTO();

        if ($data != null) {
        }

        return $listMembrosChapaTO;
    }

}
