<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 09:07
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Repository\TipoDenunciaRepository;
use App\Repository\DenunciaRepository;
use App\Entities\TipoDenuncia;
use App\Entities\Denuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AtividadePrincipalCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroComissaoBO extends AbstractBO
{
    /**
     * @var \App\Repository\DenunciaMembroComissaoRepository
     */
    private $denunciaMembroComissaoRepository;

    /**
     * @var \App\Repository\DenunciaRepository
     */
    private $denunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->denunciaRepository = $this->getRepository(Denuncia::class);
        $this->denunciaMembroComissaoRepository = $this->getRepository(DenunciaMembroComissao::class);
    }

    /**
     * @return string
     */
    public function getTotalMembroComissaoPorUF($idPessoa)
    {
      return $this->denunciaMembroComissaoRepository->getTotalMembroComissaoPorUF($idPessoa);
    }

    /**
     * @return string
     */
    public function getListaDenunciaMembroComissaoPorUF($idPessoa,$idUF)
    {
      return $this->denunciaMembroComissaoRepository->getListaDenunciaMembroComissaoPorUF($idPessoa,$idUF);
    }

    /**
     * @return mixed
     */
    public function getDadosDenunciante($idDenuncia)
    {
      return $this->denunciaMembroComissaoRepository->getDadosDenunciadoPorDenuncia($idDenuncia);
    }

}
?>
