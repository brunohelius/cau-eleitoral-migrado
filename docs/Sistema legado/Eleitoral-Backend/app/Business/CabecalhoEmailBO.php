<?php
/*
 * CabecalhoEmailBO.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\CabecalhoEmail;
use App\Entities\CabecalhoEmailUf;
use App\Entities\Uf;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\CabecalhoEmailRepository;
use App\Repository\CabecalhoEmailUfRepository;
use App\Repository\UfRepository;
use App\Service\ArquivoService;
use App\To\CabecalhoEmailFiltroTO;
use App\Util\ImageUtils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Cabeçalho de E-mail'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmailBO extends AbstractBO
{

    /**
     *
     * @var CabecalhoEmailRepository
     */
    private $cabecalhoEmailRepository;

    /**
     *
     * @var CabecalhoEmailUfRepository
     */
    private $cabecalhoEmailUfRepository;

    /**
     *
     * @var UfRepository
     */
    private $ufRepository;

    /**
     *
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     *
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->ufRepository = $this->getRepository(Uf::class);
        $this->cabecalhoEmailRepository = $this->getRepository(CabecalhoEmail::class);
        $this->cabecalhoEmailUfRepository = $this->getRepository(CabecalhoEmailUf::class);
    }

    /**
     * Retorna dados de Cabeçalho E-mail.
     *
     * @param integer $id
     * @throws Exception
     * @return array
     */
    public function getPorId($id)
    {
        $cabecalho = $this->cabecalhoEmailRepository->getPorId($id);

        $this->definirImagensBase64CabecalhoRodape($cabecalho);

        return $cabecalho;
    }

    /**
     * Salva o 'cabecalhoEmail' informado.
     *
     * @param CabecalhoEmail $cabecalhoEmail
     * @return CabecalhoEmail
     * @throws NegocioException
     * @throws Exception
     */
    public function salvar(CabecalhoEmail $cabecalhoEmail)
    {
        $this->validarCamposObrigatorios($cabecalhoEmail);
        try {
            $this->beginTransaction();

            $nomeCabecalho = $cabecalhoEmail->getImagemCabecalho() != null && ! is_string($cabecalhoEmail->getImagemCabecalho())
                ? Constants::NOME_ARQUIVO_CABECALHO . '.' . $cabecalhoEmail->getImagemCabecalho()->extension()
                : $cabecalhoEmail->getNomeImagemFisicaCabecalho();
            $cabecalhoEmail->setNomeImagemFisicaCabecalho($nomeCabecalho);

            $nomeRodape = $cabecalhoEmail->getImagemRodape() != null && ! is_string($cabecalhoEmail->getImagemRodape())
                ? Constants::NOME_ARQUIVO_RODAPE . '.' . $cabecalhoEmail->getImagemRodape()->extension()
                : $cabecalhoEmail->getNomeImagemFisicaRodape();
            $cabecalhoEmail->setNomeImagemFisicaRodape($nomeRodape);

            $cabecalhoEmailUfs = $cabecalhoEmail->getCabecalhoEmailUfs();

            $acao = Constants::HISTORICO_ACAO_INSERIR;
            $descricao = Constants::HISTORICO_DESCRICAO_ACAO_INSERIR;

            if (! empty($cabecalhoEmail->getId())) {
                $acao = Constants::HISTORICO_ACAO_ALTERAR;
                $descricao = Constants::HISTORICO_DESCRICAO_ACAO_ALTERAR;

                $ufs = $this->cabecalhoEmailUfRepository->getPorCabecalhoEmail($cabecalhoEmail->getId());
                $this->cabecalhoEmailUfRepository->deleteEmLote($ufs);
            }

            $cabecalhoEmail->setCabecalhoEmailUfs(null);
            $this->cabecalhoEmailRepository->persist($cabecalhoEmail);

            foreach ($cabecalhoEmailUfs as $cabecalhoEmailUf) {
                $cabecalhoEmailUf->setId(null);
                $this->cabecalhoEmailUfRepository->persist($cabecalhoEmailUf);
            }

            $this->salvarHistorico($cabecalhoEmail, $acao, $descricao);
            $this->salvarArquivo($cabecalhoEmail, $nomeCabecalho,  $nomeRodape);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        $cabecalhoEmail->setImagemCabecalho(null);
        $cabecalhoEmail->setImagemRodape(null);

        return $cabecalhoEmail;
    }

    /**
     * Salva os arquivos referente ao cabeçalho de email.
     *
     * @param CabecalhoEmail $cabecalhoEmail
     * @param
     *            $nomeCabecalho
     * @param
     *            $nomeRodape
     * @throws NegocioException
     */
    private function salvarArquivo(CabecalhoEmail $cabecalhoEmail, $nomeCabecalho, $nomeRodape)
    {
        $this->validarArquivos($cabecalhoEmail);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioCabecalhoEmail($cabecalhoEmail->getId());
        if (! empty($cabecalhoEmail->getImagemCabecalho()) && ! is_string($cabecalhoEmail->getImagemCabecalho())) {
            $this->getArquivoService()->salvar($caminho, $nomeCabecalho, $cabecalhoEmail->getImagemCabecalho());
        }

        if (! empty($cabecalhoEmail->getImagemRodape()) && ! is_string($cabecalhoEmail->getImagemRodape())) {
            $this->getArquivoService()->salvar($caminho, $nomeRodape, $cabecalhoEmail->getImagemRodape());
        }
    }

    /**
     * Validação de arquivos de imagem de cabeçalho e rodapé.
     *
     * @param CabecalhoEmail $cabecalhoEmail
     * @throws NegocioException
     */
    private function validarArquivos(CabecalhoEmail $cabecalhoEmail)
    {
        if (! empty($cabecalhoEmail->getImagemCabecalho()) && ! is_string($cabecalhoEmail->getImagemCabecalho())) {
            $this->getArquivoService()->validarImagemCabecalhoEmail($cabecalhoEmail->getImagemCabecalho());
        }
        if (! empty($cabecalhoEmail->getImagemRodape()) && ! is_string($cabecalhoEmail->getImagemRodape())) {
            $this->getArquivoService()->validarImagemCabecalhoEmail($cabecalhoEmail->getImagemRodape());
        }
    }

    /**
     * Busca de cabecalhos de E-mail com Filtro
     *
     * @param CabecalhoEmailFiltroTO $cabecalhoEmailFiltroTO
     * @return array
     */
    public function getPorFiltro(CabecalhoEmailFiltroTO $cabecalhoEmailFiltroTO)
    {
        return $this->cabecalhoEmailRepository->getCabecalhoEmailPorFiltro($cabecalhoEmailFiltroTO);
    }

    /**
     * Retorna lista de UFs.
     */
    public function getUfs()
    {
        return $this->ufRepository->findAll();
    }

    /**
     * Retorna o total de E-mais vinculados ao cabeçalho.
     *
     * @param integer $idCabecalhoEmail
     * @return integer
     * @throws NonUniqueResultException
     */
    public function getTotalCorpoEmailVinculado($idCabecalhoEmail)
    {
        return $this->cabecalhoEmailRepository->getTotalCorpoEmailVinculado($idCabecalhoEmail);
    }

    /**
     * Valida o preenchimento dos campos obrigatórios.
     *
     * @param CabecalhoEmail $cabecalhoEmail
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(CabecalhoEmail $cabecalhoEmail)
    {
        $campos = [];

        if (empty($cabecalhoEmail->getTitulo())) {
            array_push($campos, 'LABEL_TITULO');
        }

        if (empty($cabecalhoEmail->getCabecalhoEmailUfs())) {
            array_push($campos, 'LABEL_UF');
        }

        if ($cabecalhoEmail->isCabecalhoAtivo()) {

            if (empty($cabecalhoEmail->getNomeImagemCabecalho())) {
                array_push($campos, 'LABEL_INSERIR_FIGURA');
            }
        }

        if ($cabecalhoEmail->isRodapeAtivo()) {
            if (empty($cabecalhoEmail->getNomeImagemRodape())) {
                array_push($campos, 'LABEL_INSERIR_FIGURA');
            }
        }

        if (! empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Retorna objeto responsável por encapsular implementações de Serviço de Arquivos
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * Retorna o valor em 'base64' referente ao cabeçalho e nome de imagem informados.
     *
     * @param$idCabecalho
     * @param$nomeImagem
     * @return string|null
     */
    private function getImagemBase64($idCabecalho, $nomeImagem)
    {
        $imagem = null;

        if (! empty($nomeImagem)) {
            $path = $this->getArquivoService()->getCaminhoRepositorioCabecalhoEmail($idCabecalho);
            $arquivoCabecalho = AppConfig::getRepositorio($path, $nomeImagem);
            $imagem = ImageUtils::getImageBase64($arquivoCabecalho);
        }

        return $imagem;
    }

    /**
     * Retorna objeto responsável por encapsular implementações de BO de Histórico
     *
     * @return HistoricoBO
     */
    private function getHistoricoBO()
    {
        if (empty($this->historicoBO)) {
            $this->historicoBO = app()->make(HistoricoBO::class);
        }
        return $this->historicoBO;
    }

    /**
     * Salvar o histórico conforme os valores informados.
     *
     * @param CabecalhoEmail $cabecalhoEmail
     * @param $responsavel
     * @param int $acao
     * @param string $descricao
     * @throws Exception
     */
    private function salvarHistorico(CabecalhoEmail $cabecalhoEmail, int $acao, string $descricao): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $cabecalhoEmail,
            Constants::HISTORICO_ID_TIPO_CABECALHO_EMAIL,
            $acao,
            $descricao
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Definir as imagens de cabeçalho e rodapé em base 64
     *
     * @param $cabecalho
     */
    public function definirImagensBase64CabecalhoRodape($cabecalho): void
    {
        if (!empty($cabecalho)) {
            $cabecalhoBase64 = $this->getImagemBase64($cabecalho->getId(), $cabecalho->getNomeImagemFisicaCabecalho());

            if (empty($cabecalhoBase64) && $cabecalho->getId() == 1) {
                $arquivoRodape = $this->getArquivoService()->getCaminhoDefaultCabecalho();
                $cabecalho->setImagemCabecalho(ImageUtils::getImageBase64($arquivoRodape));
            } else {
                $cabecalho->setImagemCabecalho($cabecalhoBase64);
            }

            $rodapeBase64 = $this->getImagemBase64($cabecalho->getId(), $cabecalho->getNomeImagemFisicaRodape());
            if (empty($rodapeBase64) && $cabecalho->getId() == 1) {
                $arquivoRodape = $this->getArquivoService()->getCaminhoDefaultRodape();
                $cabecalho->setImagemRodape(ImageUtils::getImageBase64($arquivoRodape));
            } else {
                $cabecalho->setImagemRodape($rodapeBase64);
            }

        }
    }

}

