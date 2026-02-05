<?php


namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao envio de e-mail para membros incluidos na chapa da eleição
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class EnvioEmailMembroIncuidoChapaTO
{
    /**
     * @var integer
     */
    private $idAtividadeSecundaria;

    /**
     * @var string
     */
    private $nomeMembro;

    /**
     * @var string
     */
    private $nomeResponsavel;

    /**
     * @var string
     */
    private $descricaoTitular;

    /**
     * @var string
     */
    private $nomeTitular;

    /**
     * @var string
     */
    private $descricaoSuplente;

    /**
     * @var string
     */
    private $nomeSuplente;

    /**
     * @var integer
     */
    private $anoEleicao;

    /**
     * @var string
     */
    private $emailDestinatario;

    /**
     * @var integer
     */
    private $posicao;

    /**
     * Retorna uma nova instância de 'EnvioEmailMembroIncuidoChapaTO'.
     *
     * @param null $data
     * @return EnvioEmailMembroIncuidoChapaTO
     */
    public static function newInstance($data = null)
    {
        $envioEmailMembroIncuidoChapaTO = new EnvioEmailMembroIncuidoChapaTO();

        if ($data != null) {
            $envioEmailMembroIncuidoChapaTO->setPosicao(Utils::getValue('posicao', $data));
            $envioEmailMembroIncuidoChapaTO->setNomeMembro(Utils::getValue('nomeMembro', $data));
            $envioEmailMembroIncuidoChapaTO->setAnoEleicao(Utils::getValue('anoEleicao', $data));
            $envioEmailMembroIncuidoChapaTO->setNomeTitular(Utils::getValue('nomeTitular', $data));
            $envioEmailMembroIncuidoChapaTO->setNomeSuplente(Utils::getValue('nomeSuplente', $data));
            $envioEmailMembroIncuidoChapaTO->setNomeResponsavel(Utils::getValue('nomeResponsavel', $data));
            $envioEmailMembroIncuidoChapaTO->setDescricaoTitular(Utils::getValue('descricaoTitular', $data));
            $envioEmailMembroIncuidoChapaTO->setEmailDestinatario(Utils::getValue('emailDestinatario', $data));
            $envioEmailMembroIncuidoChapaTO->setDescricaoSuplente(Utils::getValue('descricaoSuplente', $data));
            $envioEmailMembroIncuidoChapaTO->setIdAtividadeSecundaria(Utils::getValue(
                'idAtividadeSecundaria',
                $data
            ));
        }

        return $envioEmailMembroIncuidoChapaTO;
    }

    public function toArrayParams()
    {
        $params = [
            'anoEleicao' => $this->getAnoEleicao(),
            'nomeMembro' => $this->getNomeMembro(),
            'posicao' => $this->getNomeResponsavel(),
            'nomeTitular' => $this->getNomeTitular(),
            'nomeSuplente' => $this->getNomeSuplente(),
            'nomeResponsavel' => $this->getNomeResponsavel(),
            'descricaoTitular' => $this->getDescricaoTitular(),
            'descricaoSuplente' => $this->getDescricaoSuplente(),
        ];

        return $params;
    }

    /**
     * @return int
     */
    public function getIdAtividadeSecundaria(): ?int
    {
        return $this->idAtividadeSecundaria;
    }

    /**
     * @param int $idAtividadeSecundaria
     */
    public function setIdAtividadeSecundaria(?int $idAtividadeSecundaria): void
    {
        $this->idAtividadeSecundaria = $idAtividadeSecundaria;
    }

    /**
     * @return string
     */
    public function getNomeMembro(): ?string
    {
        return $this->nomeMembro;
    }

    /**
     * @param string $nomeMembro
     */
    public function setNomeMembro(?string $nomeMembro): void
    {
        $this->nomeMembro = $nomeMembro;
    }

    /**
     * @return mixed
     */
    public function getNomeResponsavel()
    {
        return $this->nomeResponsavel;
    }

    /**
     * @param mixed $nomeResponsavel
     */
    public function setNomeResponsavel($nomeResponsavel): void
    {
        $this->nomeResponsavel = $nomeResponsavel;
    }

    /**
     * @return string
     */
    public function getDescricaoTitular(): ?string
    {
        return $this->descricaoTitular;
    }

    /**
     * @param string $descricaoTitular
     */
    public function setDescricaoTitular(?string $descricaoTitular): void
    {
        $this->descricaoTitular = $descricaoTitular;
    }

    /**
     * @return mixed
     */
    public function getNomeTitular()
    {
        return $this->nomeTitular;
    }

    /**
     * @param mixed $nomeTitular
     */
    public function setNomeTitular($nomeTitular): void
    {
        $this->nomeTitular = $nomeTitular;
    }

    /**
     * @return string
     */
    public function getDescricaoSuplente(): ?string
    {
        return $this->descricaoSuplente;
    }

    /**
     * @param string $descricaoSuplente
     */
    public function setDescricaoSuplente(?string $descricaoSuplente): void
    {
        $this->descricaoSuplente = $descricaoSuplente;
    }

    /**
     * @return mixed
     */
    public function getNomeSuplente()
    {
        return $this->nomeSuplente;
    }

    /**
     * @param mixed $nomeSuplente
     */
    public function setNomeSuplente($nomeSuplente): void
    {
        $this->nomeSuplente = $nomeSuplente;
    }

    /**
     * @return mixed
     */
    public function getAnoEleicao()
    {
        return $this->anoEleicao;
    }

    /**
     * @param mixed $anoEleicao
     */
    public function setAnoEleicao($anoEleicao): void
    {
        $this->anoEleicao = $anoEleicao;
    }

    /**
     * @return mixed
     */
    public function getEmailDestinatario()
    {
        return $this->emailDestinatario;
    }

    /**
     * @param mixed $emailDestinatario
     */
    public function setEmailDestinatario($emailDestinatario): void
    {
        $this->emailDestinatario = $emailDestinatario;
    }

    /**
     * @return int
     */
    public function getPosicao(): ?int
    {
        return $this->posicao;
    }

    /**
     * @param int $posicao
     */
    public function setPosicao(?int $posicao): void
    {
        $this->posicao = $posicao;
    }
}