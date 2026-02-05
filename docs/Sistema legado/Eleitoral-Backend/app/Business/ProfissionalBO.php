<?php
/*
 * ProfissionalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\To\ProfissionalTO;
use App\Util\Utils;
use Exception;
use Illuminate\Support\Facades\Lang;
use stdClass;
use App\Entities\Filial;
use App\Entities\Profissional;
use App\Repository\ProfissionalRepository;
use App\Service\IntegracaoSiccauService;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Profissional'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ProfissionalBO extends AbstractBO
{

    /**
     * @var ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * @var IntegracaoSiccauService
     */
    private $integracaoSiccauService;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->profissionalRepository = $this->getRepository(Profissional::class);
    }

    /**
     * Retorna o profissional conforme o identificador informado.
     *
     * @param $idPessoa
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getPorPessoa($idPessoa)
    {
        return $this->profissionalRepository->getPorPessoa($idPessoa);
    }

    /**
     * Retorna a instância do 'Profissional' conforme o 'id' informado.
     *
     * @param integer $id
     * @param bool $addDadosComplementaresRetorno
     *
     * @return ProfissionalTO|Profissional|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id, $addDadosComplementaresRetorno = false, $returnTO = true)
    {
        $dados = $this->profissionalRepository->getPorId($id);
        $profissional = null;

        if (!is_null($dados)) {

            $uf = null;
            if ($returnTO) {
                $profissional = ProfissionalTO::newInstance($dados);
                $uf = $profissional->getUf();
            } else {
                $profissional = Profissional::newInstance($dados);
                $uf = $profissional->getPessoa()->getEndereco()->getUf();
            }
            $this->atribuirIdCauUf($profissional, $uf);

            if ($addDadosComplementaresRetorno) {
                $this->complementarDados($profissional);
            }
        }

        return $profissional;
    }

    /**
     * Retorna o profissional conforme o CPF informado.
     *
     * @param $cpf
     * @return Profissional|null
     */
    public function getPorCPF($cpf)
    {
        $profissional = $this->profissionalRepository->getPorCpf($cpf);

        if (!is_null($profissional)) {
            $this->complementarDados($profissional);
            $profissional->definirNomes();
        }

        return $profissional;
    }

    /**
     * Recupera os 'Profissionais' de acordo com os ids informados.
     *
     * @param array $ids
     * @param bool $addDadosComplementaresRetorno
     * @return ProfissionalTO[]
     */
    public function getListaProfissionaisFormatadaPorIds($ids, $addDadosComplementaresRetorno = false)
    {
        $filtroTO = new stdClass();
        $filtroTO->ids = $ids;
        $filtroTO->addDadosComplementaresRetorno = $addDadosComplementaresRetorno;

        $profissionais = $this->getProfissionaisPorFiltro($filtroTO);

        $profissionaisRetorno = [];
        if (!empty($profissionais)) {
            foreach ($profissionais as $profissional) {
                $profissionaisRetorno[$profissional->getId()] = $profissional;
            }
        }
        return $profissionaisRetorno;
    }

    /**
     * Recupera os 'Profissionais' de acordo com o 'Filtro' informado.
     *
     * @param stdClass $profissionalFiltroTO
     * @return ProfissionalTO[]
     */
    public function getProfissionaisPorFiltro($profissionalFiltroTO, $limite = null)
    {
        $profissionalFiltroTO = $this->distinguirFiltros($profissionalFiltroTO);

        $profissionais = $this->profissionalRepository->getProfissionaisPorFiltro($profissionalFiltroTO, $limite);

        if (
            !empty($profissionais)
            && !empty($profissionalFiltroTO->addDadosComplementaresRetorno)
            && $profissionalFiltroTO->addDadosComplementaresRetorno
        ) {
            $this->complementarDadosProfissionais($profissionais);
        }

        if (empty($profissionais)) {
            if (!empty($profissionalFiltroTO->cpfNome)) {
                throw new NegocioException(Message::MSG_CPF_NOME_NAO_ENCONTRADO_SICCAU);
            } elseif (!empty($profissionalFiltroTO->registroNome)) {
                throw new NegocioException(Lang::get('messages.profissional.registro_nome_nao_encrontrado'));
            }
        }

        return $profissionais;
    }

    /**
     * Retorna a quantidade de Profissionais ativos por UF.
     *
     * @return array|null
     * @throws Exception
     */
    public function getQtdAtivosPorUf()
    {
        return $this->profissionalRepository->getQtdAtivosPorUf();
    }

    /**
     * Retorna a quantidade de Profissionais.
     *
     * @return integer
     */
    public function getQtdProfissionais()
    {
        return $this->profissionalRepository->getQtdProfissionais();
    }

    /**
     * Retorna todos os Conselheiros em ordem de CAU/UF
     *
     * @return array|null
     */
    public function getProfissionaisConselheiros()
    {
        return $this->profissionalRepository->getProfissionaisConselheiros();
    }

    /**
     * Consulta os dados complementares do profissional e os adiciona ao objeto informado.
     *
     * @param ProfissionalTO|Profissional|null $profissional
     */
    private function complementarDados($profissional): void
    {

        $pessoaId = $profissional instanceof ProfissionalTO ? $profissional->getPessoaId() : $profissional->getPessoa()->getId();
        if (!empty($pessoaId)) {
            $dadosProfissional = $this->getIntegacaoSiccauService()->getDadosComplementaresProfissionais(
                [$pessoaId]
            );

            if (!empty($dadosProfissional) && is_array($dadosProfissional)) {
                $this->preencherDadosComplementares($profissional, $dadosProfissional[0]);
            }
        }
    }

    /**
     * Retorna a instância de 'IntegracaoSiccauService' conforme o padrão lazy initialization.
     *
     * @return IntegracaoSiccauService|mixed
     */
    private function getIntegacaoSiccauService()
    {
        if ($this->integracaoSiccauService == null) {
            $this->integracaoSiccauService = app()->make(IntegracaoSiccauService::class);
        }

        return $this->integracaoSiccauService;
    }

    /**
     * Retorna a instância de 'FilialBO' conforme o padrão lazy initialization.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if ($this->filialBO == null) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    /**
     * Realiza a distinção do filtro 'CPF/Nome' do 'Profissional'.
     *
     * @param stdClass $profissionalFiltroTO
     * @return stdClass
     */
    private function distinguirFiltros($profissionalFiltroTO)
    {
        if (!empty($profissionalFiltroTO->cpfNome)) {
            $profissionalFiltroTO->cpfNome = str_replace(['.', '-'], '', $profissionalFiltroTO->cpfNome);
            $atributo = (is_numeric(trim($profissionalFiltroTO->cpfNome))) ? "cpf" : "nome";
            $profissionalFiltroTO->{$atributo} = $profissionalFiltroTO->cpfNome;
            $profissionalFiltroTO->cpfNome = null;
        }
        if (!empty($profissionalFiltroTO->registroNome)) {
            $profissionalFiltroTO->registroNome = str_replace(['.', '-'], '', $profissionalFiltroTO->registroNome);
        }

        return $profissionalFiltroTO;
    }

    /**
     * @param array|null $profissionais
     */
    private function complementarDadosProfissionais(?array $profissionais): void
    {
        $idsPessoa = array_map(function ($profissional) {
            /** @var ProfissionalTO $profissional */
            return $profissional->getPessoaId();
        }, $profissionais);

        $dadosComplementares = $this->getIntegacaoSiccauService()->getDadosComplementaresProfissionais(
            $idsPessoa
        );

        if (!empty($dadosComplementares) && is_array($dadosComplementares)) {

            $dadosProfissionais = [];
            foreach ($dadosComplementares as $dadosComplementar) {
                $dadosProfissionais[$dadosComplementar->id] = $dadosComplementar;
            }

            /** @var ProfissionalTO $profissionalTO */
            foreach ($profissionais as $profissionalTO) {
                $dadosProfissional = $dadosProfissionais[$profissionalTO->getPessoaId()] ?? null;

                $this->preencherDadosComplementares($profissionalTO, $dadosProfissional);
            }
        }
    }

    /**
     *
     * @param Profissional|null $profissional
     * @param stdClass $dadosProfissional
     */
    private function preencherDadosComplementares($profissional, $dadosProfissional)
    {
        //dd($dadosProfissional->conselheiro);
        if (!empty($dadosProfissional)) {
            $profissional->setConselheiro($dadosProfissional->conselheiro);
            $profissional->setConselheiroSubsequente($dadosProfissional->conselheiroSubsequente);
            $profissional->setPerdaMandatoConselheiro($dadosProfissional->perdaMandatoConselheiro);
            $profissional->setAdimplente($dadosProfissional->adimplente);
            $profissional->setSituacaoRegistro($dadosProfissional->situacao_registro);
            $profissional->setRegistroProvisorio($dadosProfissional->registroProvisorio);
            $profissional->setDataFimRegistro($dadosProfissional->dataFimRegistro);
            $profissional->setTempoRegistroAtivo($dadosProfissional->tempoRegistroAtivo);
            $profissional->setInfracaoEtica($dadosProfissional->infracaoEtica);
            $profissional->setInfracaoEtica($dadosProfissional->infracaoEtica);
            if (isset($dadosProfissional->multaEtica)) {
                $profissional->setMultaEtica($dadosProfissional->multaEtica);
            }
            $profissional->setSancionadoInfracaoEticaDisciplinar(
                $dadosProfissional->sancionadoInfracaoEticaDisciplinar
            );
            $profissional->setInfracaoRelacionadaExercicioProfissao(
                $dadosProfissional->infracaoRelacionadaExercicioProfissao
            );
        }
    }

    /**
     * Método auxiliar para atribuir id cau uf a um profissional
     *
     * @param ProfissionalTO|Profissional|null $profissional
     * @throws NonUniqueResultException
     */
    private function atribuirIdCauUf($profissional, $uf): void
    {
        if (!empty($uf)) {
            $filial = $this->getFilialBO()->getPorPrefixo($uf);

            if (!empty($filial)) {
                $profissional->setIdCauUf($filial->getId());
            }
        }
    }

    /**
     * Retorna a quantidade de profissionais
     *
     * @param bool $isApenasAtivos
     * @return int
     * @throws Exception
     */
    public function quantidadeTodosProfissionais($isApenasAtivos = false)
    {
        return $this->profissionalRepository->getQuantidadeProfissionais($isApenasAtivos);
    }

    /**
     * Retorna a quantidade de profissionais
     *
     * @param string $sigla
     * @param bool $isApenasAtivos
     * @return int
     * @throws Exception
     */
    public function quantidadeTodosProfissionaisPorUf(string $sigla, $isApenasAtivos = false)
    {
        return $this->profissionalRepository->getQuantidadeProfissionais($isApenasAtivos, $sigla);
    }

    /**
     * Retorna a quantidade de profissionais
     *
     * @return ProfissionalTO[]
     * @throws Exception
     */
    public function getTodosProfissionais()
    {
        return $this->profissionalRepository->getTodosProfissionais();
    }

    /**
     * Retorna a quantidade de profissionais de uma UF
     *
     * @param string $sigla
     * @return ProfissionalTO[]
     * @throws Exception
     */
    public function getTodosProfissionaisPorUf(string $sigla)
    {
        return $this->profissionalRepository->getTodosProfissionais($sigla);
    }

    /**
     * Retorna a quantidade de profissionais por UF
     *
     * @param bool $isApenasAtivos
     * @return array
     * @throws Exception
     */
    public function quantidadeTodosProfissionaisAgrupadoPorUf($isApenasAtivos = false)
    {
        $qtdProfissionais = $this->profissionalRepository->getQuantidadeProfissionaisAgrupadoPorUf($isApenasAtivos);

        return array_map(function ($item) {
            return $this->getQtdProfissionalTO($item);
        }, $qtdProfissionais);
    }

    /**
     * Retorna uma instancia de ProfissionalTO.
     *
     * @param $profissional
     * @return array|null
     */
    private function getQtdProfissionalTO($profissional)
    {
        if ($profissional['uf'] != 'XX' and !empty($profissional['uf'])) {
            $profissionalTO = array();
            $profissionalTO['idCauUf'] = Utils::getValue('uf', $profissional);
            $profissionalTO['qtdProfissional'] = Utils::getValue('quantidade', $profissional);
            return $profissionalTO;
        }
        return null;
    }
}
