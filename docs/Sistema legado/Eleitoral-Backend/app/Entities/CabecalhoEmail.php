<?php
/*
 * CabecalhoEmail.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Cabeçalho de E-mail'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CabecalhoEmailRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CABECALHO_EMAIL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmail extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CABECALHO_EMAIL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_cabecalho_email_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TITULO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $titulo;

    /**
     * @ORM\Column(name="NM_FIGURA_CABECALHO", type="string", length=200)
     *
     * @var string
     */
    private $nomeImagemCabecalho;

    /**
     * @ORM\Column(name="NM_FIS_FIGURA_CABECALHO", type="string", length=200)
     *
     * @var string
     */
    private $nomeImagemFisicaCabecalho;

    /**
     * Transient
     *
     * @var string
     */
    private $imagemCabecalho;

    /**
     * @ORM\Column(name="DS_CABECALHO", type="text")
     *
     * @var string
     */
    private $textoCabecalho;

    /**
     * @ORM\Column(name="ST_CABECALHO_ATIVO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $isCabecalhoAtivo;

    /**
     * @ORM\Column(name="NM_FIGURA_RODAPE", type="string", length=200)
     *
     * @var string
     */
    private $nomeImagemRodape;

    /**
     * @ORM\Column(name="NM_FIS_FIGURA_RODAPE", type="string", length=200)
     *
     * @var string
     */
    private $nomeImagemFisicaRodape;

    /**
     * Transient
     *
     * @var string
     */
    private $imagemRodape;

    /**
     * @ORM\Column(name="DS_RODAPE", type="text")
     *
     * @var string
     */
    private $textoRodape;

    /**
     * @ORM\Column(name="ST_RODAPE_ATIVO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $isRodapeAtivo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\CabecalhoEmailUf", mappedBy="cabecalhoEmail")
     *
     * @var array|ArrayCollection
     */
    private $cabecalhoEmailUfs;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entities\CorpoEmail", mappedBy="cabecalhoEmail", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $corpoEmails;
    
    /**
     * 
     * @var boolean
     */
    private $ativo;

    /**
     * Retorna uma nova instância de 'CabecalhoEmail'.
     *
     * @param null $data
     * @return CabecalhoEmail
     */
    public static function newInstance($data = null)
    {
        $cabecalhoEmail = new CabecalhoEmail();

        if ($cabecalhoEmail != null) {
           
            $cabecalhoEmail->setId(Utils::getValue('id', $data));
            $cabecalhoEmail->setTitulo(Utils::getValue('titulo', $data));
            $cabecalhoEmail->setTextoRodape(Utils::getValue('textRodape', $data));
            $cabecalhoEmail->setNomeImagemRodape(Utils::getValue('nomeImagemRodape', $data));
            $cabecalhoEmail->setTextoCabecalho(Utils::getValue('textoCabecalho', $data));
            $cabecalhoEmail->setTextoRodape(Utils::getValue('textoRodape', $data));
            $cabecalhoEmail->setIsRodapeAtivo(Utils::getBooleanValue('isRodapeAtivo', $data));
            $cabecalhoEmail->setNomeImagemCabecalho(Utils::getValue('nomeImagemCabecalho', $data));
            $cabecalhoEmail->setIsCabecalhoAtivo(Utils::getBooleanValue('isCabecalhoAtivo', $data));
            $cabecalhoEmail->setNomeImagemFisicaRodape(Utils::getValue('nomeImagemFisicaRodape', $data));
            $cabecalhoEmail->setNomeImagemFisicaCabecalho(Utils::getValue('nomeImagemFisicaCabecalho', $data));
            $cabecalhoEmail->setImagemCabecalho(Utils::getValue('imagemCabecalho', $data));
            $cabecalhoEmail->setImagemRodape(Utils::getValue('imagemRodape', $data));
            $cabecalhoEmail->definirAtivo();
            
            $corpoEmails = array_map(function ($data){
                return CorpoEmail::newInstance($data);
            }, Utils::getValue('corpoEmails', $data, []));
            $cabecalhoEmail->setCorpoEmails($corpoEmails);

            $cabecalhoEmailUfs = array_map(function ($data) use ($cabecalhoEmail) {
                $cabecalhoEmailUf = CabecalhoEmailUf::newInstance($data);
                $cabecalhoEmailUf->setCabecalhoEmail($cabecalhoEmail);
                return $cabecalhoEmailUf;
            }, Utils::getValue('cabecalhoEmailUfs', $data, []));

            $cabecalhoEmail->setCabecalhoEmailUfs($cabecalhoEmailUfs);
        }

        return $cabecalhoEmail;
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
    }

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return string
     */
    public function getNomeImagemCabecalho()
    {
        return $this->nomeImagemCabecalho;
    }

    /**
     * @param string $nomeImagemCabecalho
     */
    public function setNomeImagemCabecalho($nomeImagemCabecalho)
    {
        $this->nomeImagemCabecalho = $nomeImagemCabecalho;
    }

    /**
     * @return string
     */
    public function getNomeImagemFisicaCabecalho()
    {
        return $this->nomeImagemFisicaCabecalho;
    }

    /**
     * @param string $nomeImagemFisicaCabecalho
     */
    public function setNomeImagemFisicaCabecalho($nomeImagemFisicaCabecalho)
    {
        $this->nomeImagemFisicaCabecalho = $nomeImagemFisicaCabecalho;
    }

    /**
     * @return string
     */
    public function getImagemCabecalho()
    {
        return $this->imagemCabecalho;
    }

    /**
     * @param string $imagemCabecalho
     */
    public function setImagemCabecalho($imagemCabecalho)
    {
        $this->imagemCabecalho = $imagemCabecalho;
    }

    /**
     * @return string
     */
    public function getTextoCabecalho()
    {
        return $this->textoCabecalho;
    }

    /**
     * @param string $textoCabecalho
     */
    public function setTextoCabecalho($textoCabecalho)
    {
        $this->textoCabecalho = $textoCabecalho;
    }

    /**
     * @return bool
     */
    public function isCabecalhoAtivo()
    {
        return $this->isCabecalhoAtivo;
    }

    /**
     * @param bool $isCabecalhoAtivo
     */
    public function setIsCabecalhoAtivo($isCabecalhoAtivo)
    {
        $this->isCabecalhoAtivo = $isCabecalhoAtivo;
    }

    /**
     * @return string
     */
    public function getNomeImagemRodape()
    {
        return $this->nomeImagemRodape;
    }

    /**
     * @param string $nomeImagemRodape
     */
    public function setNomeImagemRodape($nomeImagemRodape)
    {
        $this->nomeImagemRodape = $nomeImagemRodape;
    }

    /**
     * @return string
     */
    public function getNomeImagemFisicaRodape()
    {
        return $this->nomeImagemFisicaRodape;
    }

    /**
     * @param string $nomeImagemFisicaRodape
     */
    public function setNomeImagemFisicaRodape($nomeImagemFisicaRodape)
    {
        $this->nomeImagemFisicaRodape = $nomeImagemFisicaRodape;
    }

    /**
     * @return string
     */
    public function getImagemRodape()
    {
        return $this->imagemRodape;
    }

    /**
     * @param string $imagemRodape
     */
    public function setImagemRodape($imagemRodape)
    {
        $this->imagemRodape = $imagemRodape;
    }

    /**
     * @return string
     */
    public function getTextoRodape()
    {
        return $this->textoRodape;
    }

    /**
     * @param string $textoRodape
     */
    public function setTextoRodape($textoRodape)
    {
        $this->textoRodape = $textoRodape;
    }

    /**
     * @return bool
     */
    public function isRodapeAtivo(): bool
    {
        return $this->isRodapeAtivo;
    }

    /**
     * @param bool $isRodapeAtivo
     */
    public function setIsRodapeAtivo($isRodapeAtivo): void
    {
        $this->isRodapeAtivo = $isRodapeAtivo;
    }


    /**
     * @return array|ArrayCollection
     */
    public function getCorpoEmails()
    {
        return $this->corpoEmails;
    }

    /**
     * @param ArrayCollection $corpoEmails
     */
    public function setCorpoEmails($corpoEmails)
    {
        $this->corpoEmails = $corpoEmails;
    }
    
    /**
     * Definição de status de Cabecalho.
     */
    public function definirAtivo(){
        $this->ativo = $this->isCabecalhoAtivo || $this->isRodapeAtivo;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getCabecalhoEmailUfs()
    {
        return $this->cabecalhoEmailUfs;
    }

    /**
     * @param array|ArrayCollection $cabecalhoEmailUfs
     */
    public function setCabecalhoEmailUfs($cabecalhoEmailUfs)
    {
        $this->cabecalhoEmailUfs = $cabecalhoEmailUfs;
    }

}
