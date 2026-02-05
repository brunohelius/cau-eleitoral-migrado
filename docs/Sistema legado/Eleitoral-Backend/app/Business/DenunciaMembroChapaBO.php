<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 09:07
 */

namespace App\Business;

use App\Entities\ArquivoDenuncia;
use App\Entities\DenunciaMembroChapa;
use App\Entities\Profissional;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AtividadePrincipalCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroChapaBO extends AbstractBO
{
    /**
     * @var \App\Repository\DenunciaMembroChapaRepository
     */
    private $denunciaMembroChapaRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaRepository
     */
    private $arquivoDenunciaRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->denunciaMembroChapaRepository = $this->getRepository(DenunciaMembroChapa::class);
        $this->arquivoDenunciaRepository = $this->getRepository(ArquivoDenuncia::class);
    }

    /**
     * @return string
     */
    public function getTotalDenunciaMembroChapaPorUF($idPessoa)
    {
        return $this->denunciaMembroChapaRepository->getTotalDenunciaMembroChapaPorUF($idPessoa);
    }

    /**
     * @return string
     */
    public function getListaDenunciaMembroChapaPorUF($idPessoa, $idUF)
    {
        if(empty($idPessoa) && (empty($idUF)))
        {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
      return $this->denunciaMembroChapaRepository->getListaDenunciaMembroChapaPorUF($idPessoa,$idUF);
    }

    /**
     * @return string
     */
    public function getDadosDenunciaMembroChapa($idDenuncia)
    {
        if(empty($idDenuncia))
        {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
        return $this->denunciaMembroChapaRepository->getDadosDenuncia($idDenuncia);
    }

    /**
     * @return string
     */
    public function getArquivosDenuncia($idDenuncia)
    {
        if(empty($idDenuncia))
        {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
        return $this->arquivoDenunciaRepository->findBy(['denuncia' => $idDenuncia]);
    }

    /**
     * @param $idDenuncia
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDadosDenunciante($idDenuncia)
    {
        if(empty($idDenuncia))
        {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
        return $this->denunciaMembroChapaRepository->getDadosDenunciadoPorDenuncia($idDenuncia);
    }

}

?>
