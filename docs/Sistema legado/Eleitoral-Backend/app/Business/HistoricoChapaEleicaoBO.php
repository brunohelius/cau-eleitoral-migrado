<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 10:35
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\HistoricoChapaEleicao;
use App\Entities\Profissional;
use App\Entities\Usuario;
use App\Exceptions\NegocioException;
use App\Repository\HistoricoChapaEleicaoRepository;
use App\Repository\UsuarioRepository;
use App\Service\CorporativoService;
use App\To\HistoricoChapaEleicaoTO;
use App\To\UsuarioTO;
use App\Util\Utils;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoChapaEleicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoChapaEleicaoBO extends AbstractBO
{
    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var HistoricoChapaEleicaoRepository
     */
    private $historicoChapaEleicaoRepository;

    /**
     * @var UsuarioRepository
     */
    private $usuarioRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoChapaEleicaoRepository = $this->getRepository(HistoricoChapaEleicao::class);
        $this->usuarioRepository = $this->getRepository(Usuario::class);
    }

    /**
     * Retorna o objeto HistoricoChapaEleicao construído para salvar o histórico
     *
     * @param ChapaEleicao $chapaEleicao
     * @param $origem
     * @param $descricaoAcao
     * @param null $justificativa
     *
     * @return HistoricoChapaEleicao
     * @throws Exception
     */
    public function criarHistorico(ChapaEleicao $chapaEleicao, $origem, $descricaoAcao, $justificativa = null)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        // Tratativa para o usuário tipo profissional, o mesmo não possuí id de usuário
        $idUsuario = ($origem == Constants::ORIGEM_PROFISSIONAL) ? $usuarioLogado->idProfissional : $usuarioLogado->id;

        $historico = HistoricoChapaEleicao::newInstance();
        $historico->setData(Utils::getData());
        $historico->setChapaEleicao($chapaEleicao);
        $historico->setIdUsuario($idUsuario);
        $historico->setUsuario(Usuario::newInstance(['id' => $idUsuario]));
        $historico->setProfissional(Profissional::newInstance(['id' => $idUsuario]));
        $historico->setDescricaoOrigem($origem);
        $historico->setDescricaoAcao($descricaoAcao);
        $historico->setDescricaoJustificativa($justificativa);

        return $historico;
    }

    /**
     * Salva o histórico da chapa da eleição
     *
     * @param HistoricoChapaEleicao $historicoChapaEleicao
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvar(HistoricoChapaEleicao $historicoChapaEleicao)
    {
        $this->historicoChapaEleicaoRepository->persist($historicoChapaEleicao);
    }

    /**
     * Recupera o histórico referente ao 'id' do calendario informado.
     *
     * @param int $idCalendario
     * @param $ufsFiliais
     *
     * @return HistoricoChapaEleicao[]
     * @throws NegocioException
     */
    public function getHistoricoPorCalendario(int $idCalendario)
    {
        return $this->historicoChapaEleicaoRepository->getHistoricoPorCalendario($idCalendario);
    }

    /**
     * Método auxiliar para remover todas os Históricos associados a uma Chapa Eleção
     *
     * @param integer $idChapaEleicao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function excluirHistoricoChapaEleicao($idChapaEleicao)
    {
        $historicosChapaEleicao = $this->historicoChapaEleicaoRepository->findBy(['chapaEleicao' => $idChapaEleicao]);

        if (!empty($historicosChapaEleicao)) {
            $this->historicoChapaEleicaoRepository->deleteEmLote($historicosChapaEleicao);
        }
    }

    /**
     * @param array $historicosChapaEleicaoTO
     * @return array
     * @throws NegocioException
     */
    private function getNomesUsuariosHistorico(array $historicosChapaEleicaoTO): array
    {
        $nomesUsuarios = [];

        if(!empty($historicosChapaEleicaoTO)){
            $idsUsuarios = [];
            $idsProfissionais = [];

            /** @var HistoricoChapaEleicaoTO $historicoChapaEleicaoTO */
            foreach ($historicosChapaEleicaoTO as $historicoChapaEleicaoTO) {
                if(!in_array($historicoChapaEleicaoTO->getIdUsuario(), $idsProfissionais) && !in_array($historicoChapaEleicaoTO->getIdUsuario(), $idsUsuarios)) {
                    if ($historicoChapaEleicaoTO->getDescricaoOrigem() == Constants::ORIGEM_PROFISSIONAL) {
                        $idsProfissionais[] = $historicoChapaEleicaoTO->getIdUsuario();
                    } else {
                        $idsUsuarios[] = $historicoChapaEleicaoTO->getIdUsuario();
                    }
                }
            }

            $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsProfissionais);
            if(!empty($profissionais)) {
                foreach ($profissionais as $profissional) {
                    $nomesUsuarios[$profissional->getId()] = $profissional->getNome();
                }
            }
            $idsUsuarios = array_unique($idsUsuarios, SORT_NUMERIC);
            $usuarios = $this->usuarioRepository->getUsuariosPorIds($idsUsuarios);
            if(!empty($usuarios)) {
                /** @var UsuarioTO $usuario */
                foreach ($usuarios as $usuario) {
                    $nomesUsuarios[$usuario->getId()] = $usuario->getNome();
                }
            }
        }

        return $nomesUsuarios;
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
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO|mixed
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }
}
