<?php
/*
 * FilialBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Util\Utils;
use App\Util\ImageUtils;
use App\Config\Constants;
use App\Entities\Filial;
use App\Config\AppConfig;
use App\Exceptions\Message;
use App\Entities\ModalidadeRrt;
use App\Service\CorporativoService;
use App\Exceptions\NegocioException;
use App\Repository\FilialRepository;
use App\Service\ArquivoService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Filial'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class FilialBO extends AbstractBO
{

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var FilialRepository
     */
    private $filialRepository;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var InformacaoComissaoMembroBO
     */
    private $informacaoComissaoMembroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialRepository = $this->getRepository(Filial::class);
    }

    /**
     * Retorna a lista de Filiais + IES
     *
     * @return array|null
     */
    public function getFiliaisIES()
    {
        $filiais = $this->filialRepository->getFiliais();

        $filiais[] = $this->getFilialIES();

        $novaLista = [];
        if (!empty($filiais)) {
            $CEN = null;
            $IES = null;

            foreach ($filiais as $i => $filial) {

                if ($filial->getId() == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    $filial->setPrefixo("CEN");
                    $filial->setDescricao("CEN");
                    $CEN = $filial;
                    unset($filiais[$i]);
                }else if($filial->getId() == Constants::IES_ID) {
                    $filial->setPrefixo("IES");
                    $filial->setDescricao("IES");
                    $IES = $filial;
                    unset($filiais[$i]);
                }
                else {
                    $novaLista[] = $filial;
                }
            }

            $novaLista[] = $CEN;
            $novaLista[] = $IES;
        }
        return $novaLista;
    }

    /**
     * Retorna a filial estática IES.
     *
     *
     * @return Filial|object|null
     */
    public function getFilialIES()
    {
        return Filial::newInstance([
            "id" => Constants::IES_ID,
            "prefixo" => Constants::PREFIXO_IES,
            "descricao" => Constants::PREFIXO_IES
        ]);
    }

    /**
     * Retorna a filial conforme o 'id' informado.
     *
     * @param $id
     * @return Filial|object|null
     */
    public function getPorId($id)
    {
        $filial = $this->filialRepository->find($id);

        return $filial;
    }

    /**
     * Retorna todas as filiais disponíveis
     *
     * @return array
     */
    public function getFiliais()
    {
        return $this->filialRepository->getFiliais();
    }

    /**
     * Recupera uma lista com as filiais.
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getListaFiliaisFormata()
    {
        $filiais = $this->getFiliais();

        $filiaisFormatadas = [];
        if (!empty($filiais) && is_array($filiais)) {
            /** @var Filial $filial */
            foreach ($filiais as $filial) {
                $filiaisFormatadas[$filial->getId()] = $filial;
            }
        }
        return Arr::sort($filiaisFormatadas);
    }

    /**
     * Retorna a instância do 'Filial' conforme o prefixo informado.
     *
     * @param $prefixo
     * @return Filial|null
     * @throws NonUniqueResultException
     */
    public function getPorPrefixo($prefixo)
    {
        return $this->filialRepository->getPorPrefixo($prefixo);
    }

    /**
     * Retorna o prefixo da filial informada. Caso o prefixo da filial seja CAU/BR
     * será retornado apenas a sigla do Brasil BR
     *
     * @param $filial
     * @return string
     */
    private function getPrefixo($filial): string
    {
        /** @var Filial $filial */
        $prefixo = $filial->getPrefixo();

        if ($prefixo == Constants::SIGLA_CAU_BR) {
            $prefixo = Constants::SIGLA_BRASIL;
        }

        return $prefixo;
    }

    /**
     * Retorna todas as filiais disponíveis
     *
     * @return Filial|null
     * @throws NegocioException
     */
    public function getFilialComBandeira($id)
    {
        // Quando passar '0' o sistema deve considerar o id da CAU/BR
        if ($id == '0') {
            $id = Constants::COMISSAO_MEMBRO_CAU_BR_ID;
        }

        $filial = $this->getPorId($id);

        if (!empty($filial)) {
            $this->atribuirImagensBandeirasFiliais([$filial]);
        }

        return $filial;
    }

    /**
     * Retorna todas as filiais disponíveis
     *
     * @return array
     * @throws NegocioException
     */
    public function getFiliaisComBandeiras()
    {
        $filiais = $this->getFiliais();

        $filiaisRetorno = [];
        if (!empty($filiais)) {
            $this->atribuirImagensBandeirasFiliais($filiais);
            $this->atualizarPrefixoFilialCauBr($filiais);

            $filialCauBr = null;
            foreach ($filiais as $filial) {
                if($filial->getId() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    $filiaisRetorno[] = $filial;
                } else {
                    $filialCauBr = $filial;
                }
            }

            if(!empty($filialCauBr)) {
                $filiaisRetorno[] = $filialCauBr;
            }
        }
        return $filiaisRetorno;
    }

    /**
     * Retorna as filiais associadas ao calendário informado.
     *
     * @param int $idCalendario
     * @return Filial[]
     * @throws NegocioException
     */
    public function getFiliaisComBandeirasPorCalendario($idCalendario)
    {
        $filiais = $this->filialRepository->getFiliaisComBandeirasPorCalendario($idCalendario, true);

        if (!empty($filiais)) {
            $this->atribuirImagensBandeirasFiliais($filiais);
            $this->atualizarPrefixoFilialCauBr($filiais);
        }
        return $filiais;
    }

    /**
     * Retorna a lista de filiais, associadas às UF's que ainda não tiveram Membros de Comissão cadastradas, para um
     * determinado calendário.
     * Obs.: Somente o Assessor CEN por acessar as UF's que ainda não tiveram Membros de Comissão cadastrados.
     *
     * @param int $idCalendario
     * @param int $idInformacaoComissao
     * @return Filial[]
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getFiliaisMembrosNaoCadastradosPorCalendario($idCalendario, $idInformacaoComissao = null)
    {
        $filiais = [];

        if ($this->getUsuarioFactory()->getUsuarioLogado()->idCauUf != Constants::ID_CAU_BR)
            return $filiais;

        if (empty($idInformacaoComissao)) {
            $informacaoComissao = $this->getInformacaoComissaoMembroBO()->getPorCalendario($idCalendario);
            $idInformacaoComissao = !empty($informacaoComissao) ? $informacaoComissao->getId() : null;
        }

        if (!empty($idInformacaoComissao)) {
            $filiais = $this->filialRepository
                ->getFiliaisMembrosNaoCadastradosPorCalendarioInformacaoComissao($idCalendario, $idInformacaoComissao);
            $this->atualizarPrefixoFilialCauBr($filiais);
        }
        return $filiais;
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * @return array
     * @throws NegocioException
     */
    private function salvarRecuperarBandeirasFiliais()
    {
        $bandeiras = [];
        $filiaisPortal = $this->getCorporativoService()->getFiliaisComBandeiras();

        if (!empty($filiaisPortal)) {
            foreach ($filiaisPortal as $filialPortal) {
                if (!empty($filialPortal->imagemBandeira)) {
                    $prefixo = $filialPortal->prefixo == Constants::SIGLA_CAU_BR
                        ? Constants::SIGLA_BRASIL
                        : $filialPortal->prefixo;

                    $bandeiras[$prefixo] = $filialPortal->imagemBandeira;

                    $this->getArquivoService()->salvarBase64ToArquivo(
                        $bandeiras[$prefixo],
                        $this->getArquivoService()->getCaminhoRepositorioBandeirasFiliais(),
                        $prefixo,
                        Constants::EXTENSAO_ARQUIVO_BANDEIRAS
                    );
                }
            }
        }
        return $bandeiras;
    }

    /**
     * @param array $filiais
     * @throws NegocioException
     */
    private function atribuirImagensBandeirasFiliais(array $filiais): void
    {
        $bandeiras = [];

        /** @var Filial $filial */
        foreach ($filiais as $filial) {

            $prefixo = $filial->getPrefixo() == Constants::SIGLA_CAU_BR ? Constants::SIGLA_BRASIL : $filial->getPrefixo();

            $bandeira = Utils::getValue($prefixo, $bandeiras, null);

            if (empty($bandeira)) {
                $nomeArquivoBandeira = "{$prefixo}." . Constants::EXTENSAO_ARQUIVO_BANDEIRAS;

                $path = AppConfig::getRepositorio(
                    $this->getArquivoService()->getCaminhoRepositorioBandeirasFiliais(),
                    $nomeArquivoBandeira
                );
                $bandeira = ImageUtils::getImageBase64($path);

                if (empty($bandeira)) {
                    $bandeiras = $this->salvarRecuperarBandeirasFiliais();

                    $bandeira = Utils::getValue($prefixo, $bandeiras, null);
                }
            }
            $filial->setImagemBandeira($bandeira);
        }
    }

    /**
     * Atualiza a descrição do 'Prefixo' da filial referente à 'CAU/BR'.
     *
     * @param Filial[] $filiais
     */
    private function atualizarPrefixoFilialCauBr($filiais)
    {
        array_map(function ($filial) {
            if ($filial->getId() == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $filial->setPrefixo(Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL);
            }
        }, $filiais);
    }

    /**
     * Retorna uma nova instância de 'InformacaoComissaoMembroBO'.
     *
     * @return InformacaoComissaoMembroBO|mixed
     */
    private function getInformacaoComissaoMembroBO()
    {
        if (empty($this->eleicaoBO)) {
            $this->informacaoComissaoMembroBO = app()->make(InformacaoComissaoMembroBO::class);
        }
        return $this->informacaoComissaoMembroBO;
    }
}
