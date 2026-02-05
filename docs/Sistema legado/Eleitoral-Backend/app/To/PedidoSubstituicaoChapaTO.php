<?php


namespace App\To;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\MembroChapa;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\StatusSubstituicaoChapa;
use App\Util\Utils;
use Illuminate\Support\Facades\Log;

/**
 * Classe de transferência para os dados do pedido de substituição chapa
 *
 *
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PedidoSubstituicaoChapaTO
{
    /**
     *
     * @var $id
     *
     */
    private $id;

    /**
     * @var $id
     */
    private $idProfissionalInclusao;

    /**
     * @var $id
     */
    private $nomeProfissionalInclusao;

    /**
     * @var $dataCadastro
     */
    private $dataCadastro;

    /**
     * @var $numeroProtocolo
     */
    private $numeroProtocolo;

    /**
     * @var $idChapaEleicao
     */
    private $idChapaEleicao;

    /**
     * @var $justificativa
     */
    private $justificativa;

    /**
     * @var $nomeArquivo
     */
    private $nomeArquivo;

    /**
     * @var $nomeArquivo
     */
    private $nomeArquivoFisico;

    /**
     * @var $arquivo
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var ChapaEleicao|null
     */
    private $chapaEleicao;

    /**
     * @var MembroChapa $membroSubstituidoTitular
     */
    private $membroSubstituidoTitular;

    /**
     * @var MembroChapa $membroSubstituidoSuplente
     */
    private $membroSubstituidoSuplente;

    /**
     * @var MembroChapa $membroSubstitutoTitular
     */
    private $membroSubstitutoTitular;

    /**
     * @var MembroChapa $membroSubstitutoSuplente
     */
    private $membroSubstitutoSuplente;

    /**
     * @var StatusSubstituicaoChapa
     */
    private $statusSubstituicaoChapa;

    /**
     * @var bool
     */
    private $isPermissaoJulgamento;

    /**
     * @var JulgamentoSubstituicaoTO
     */
    private $julgamentoSubstituicao;

    /**
     * @var bool|null
     */
    private $isIniciadoAtividadeRecurso;

    /**
     * @var bool|null
     */
    private $isFinalizadoAtividadeRecurso;

    /**
     * @var bool|null
     */
    private $isIniciadoAtividadeJulgamentoRecurso;

    /**
     * @var bool|null
     */
    private $isFinalizadoAtividadeJulgamentoRecurso;

    /**
     * Retorna uma nova instância de 'PedidoSubstituicaoChapaTO'.
     *
     * @param null $data
     * @return PedidoSubstituicaoChapaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidoSubstituicaoChapaTO = new PedidoSubstituicaoChapaTO();

        if ($data != null) {
            $pedidoSubstituicaoChapaTO->setId(Utils::getValue('id', $data));
            $pedidoSubstituicaoChapaTO->setArquivo(Utils::getValue('arquivo', $data));
            $pedidoSubstituicaoChapaTO->setTamanho(Utils::getValue('tamanho', $data));
            $pedidoSubstituicaoChapaTO->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $pedidoSubstituicaoChapaTO->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $pedidoSubstituicaoChapaTO->setJustificativa(Utils::getValue('justificativa', $data));
            $pedidoSubstituicaoChapaTO->setIdChapaEleicao(Utils::getValue('idChapaEleicao', $data));
            $pedidoSubstituicaoChapaTO->setNumeroProtocolo(Utils::getValue('numeroProtocolo', $data));
            $pedidoSubstituicaoChapaTO->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
            $pedidoSubstituicaoChapaTO->setIsPermissaoJulgamento(Utils::getValue('isPermissaoJulgamento', $data));
            $pedidoSubstituicaoChapaTO->setIdProfissionalInclusao(Utils::getValue('idProfissionalInclusao', $data));
            $pedidoSubstituicaoChapaTO->setNomeProfissionalInclusao(Utils::getValue('nomeProfissionalInclusao', $data));

            $statusSubstituicaoChapa = Utils::getValue('statusSubstituicaoChapa', $data);
            if (!empty($statusSubstituicaoChapa)) {
                $pedidoSubstituicaoChapaTO->setStatusSubstituicaoChapa(
                    StatusSubstituicaoChapa::newInstance($statusSubstituicaoChapa)
                );
            }

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if (!empty($chapaEleicao)) {
                $pedidoSubstituicaoChapaTO->setChapaEleicao(
                    ChapaEleicao::newInstance($chapaEleicao)
                );
            }

            $julgamentoSubstituicao = Utils::getValue('julgamentoSubstituicao', $data);
            if (!empty($julgamentoSubstituicao)) {
                $pedidoSubstituicaoChapaTO->setJulgamentoSubstituicao(
                    JulgamentoSubstituicaoTO::newInstance($julgamentoSubstituicao)
                );
            }

            $membroSubstitutoTitular = Utils::getValue('membroSubstitutoTitular', $data);
            $pedidoSubstituicaoChapaTO->atribuirMembroSubstitutoTitular($membroSubstitutoTitular);

            $membroSubstitutoSuplente = Utils::getValue('membroSubstitutoSuplente', $data);
            $pedidoSubstituicaoChapaTO->atribuirMembroSubstitutoSuplente($membroSubstitutoSuplente);

            $membrosChapaSubstituicao = Utils::getValue('membrosChapaSubstituicao', $data);
            $pedidoSubstituicaoChapaTO->atribuirMembrosSubstituicao($membrosChapaSubstituicao);
        }

        return $pedidoSubstituicaoChapaTO;
    }

    /**
     * Fabricação estática de 'PedidoSubstituicaoChapaTO'.
     *
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @return PedidoSubstituicaoChapaTO
     */
    public static function newInstanceFromEntity(PedidoSubstituicaoChapa $pedidoSubstituicaoChapa)
    {
        $pedidoSubstituicaoChapaTO = new PedidoSubstituicaoChapaTO();

        if (!empty($pedidoSubstituicaoChapaTO)) {
            $pedidoSubstituicaoChapaTO->setId($pedidoSubstituicaoChapa->getId());
            $pedidoSubstituicaoChapaTO->setNumeroProtocolo($pedidoSubstituicaoChapa->getNumeroProtocolo());
        }

        return $pedidoSubstituicaoChapaTO;
    }

    /**
     * @param $membrosChapaSubstituicao
     */
    private function atribuirMembrosSubstituicao($membrosChapaSubstituicao)
    {
        if (!empty($membrosChapaSubstituicao)) {
            foreach ($membrosChapaSubstituicao as $membroChapaSubstituicao) {

                $membroChapaSubstituido = Utils::getValue('membroChapaSubstituido', $membroChapaSubstituicao);
                if (!empty($membroChapaSubstituido)) {
                    $tipoParticipacao = Utils::getValue('tipoParticipacaoChapa', $membroChapaSubstituido);
                    $tipo = $tipoParticipacao['id'] == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR ? 'Titular' : 'Suplente';

                    $metodoAtribuirSubstituido = "atribuirMembroSubstituido" . $tipo;
                    $this->$metodoAtribuirSubstituido($membroChapaSubstituido);
                }

                $membroChapaSubstituto = Utils::getValue('membroChapaSubstituto', $membroChapaSubstituicao);
                if (!empty($membroChapaSubstituto)) {
                    $tipoParticipacao = Utils::getValue('tipoParticipacaoChapa', $membroChapaSubstituto);
                    $tipo = $tipoParticipacao['id'] == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR ? 'Titular' : 'Suplente';

                    $metodoAtribuirSubstituto = "atribuirMembroSubstituto" . $tipo;
                    $this->$metodoAtribuirSubstituto($membroChapaSubstituto);
                } elseif (!empty($membroChapaSubstituido)) {
                    // Se o substituto seja o mesmo do substituido
                    $metodoAtribuirSubstituto = "atribuirMembroSubstituto" . $tipo;
                    $this->$metodoAtribuirSubstituto($membroChapaSubstituido);
                }
            }
        }
    }

    /**
     * @param $membroSubstitutoTitular
     */
    private function atribuirMembroSubstitutoTitular($membroSubstitutoTitular)
    {
        if (!empty($membroSubstitutoTitular)) {
            $this->setMembroSubstitutoTitular(
                MembroChapa::newInstance($membroSubstitutoTitular)
            );
        }
    }

    /**
     * @param $membroSubstitutoSuplente
     */
    private function atribuirMembroSubstitutoSuplente($membroSubstitutoSuplente)
    {
        if (!empty($membroSubstitutoSuplente)) {
            $this->setMembroSubstitutoSuplente(
                MembroChapa::newInstance($membroSubstitutoSuplente)
            );
        }
    }

    /**
     * @param $membroSubstituidoTitular
     */
    private function atribuirMembroSubstituidoTitular($membroSubstituidoTitular)
    {
        if (!empty($membroSubstituidoTitular)) {
            $this->setMembroSubstituidoTitular(
                MembroChapa::newInstance($membroSubstituidoTitular)
            );
        }
    }

    /**
     * @param $membroSubstituidoSuplente
     */
    private function atribuirMembroSubstituidoSuplente($membroSubstituidoSuplente)
    {
        if (!empty($membroSubstituidoSuplente)) {
            $this->setMembroSubstituidoSuplente(
                MembroChapa::newInstance($membroSubstituidoSuplente)
            );
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param mixed $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return mixed
     */
    public function getNumeroProtocolo()
    {
        return $this->numeroProtocolo;
    }

    /**
     * @param mixed $numeroProtocolo
     */
    public function setNumeroProtocolo($numeroProtocolo): void
    {
        if (!empty($numeroProtocolo)) {
            $this->numeroProtocolo = str_pad($numeroProtocolo, 5, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return mixed
     */
    public function getIdChapaEleicao()
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param mixed $idChapaEleicao
     */
    public function setIdChapaEleicao($idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return mixed
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param mixed $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return mixed
     */
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * @param mixed $nomeArquivo
     */
    public function setNomeArquivo($nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return mixed
     */
    public function getNomeArquivoFisico()
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param mixed $nomeArquivoFisico
     */
    public function setNomeArquivoFisico($nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * @return ChapaEleicao|null
     */
    public function getChapaEleicao(): ?ChapaEleicao
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao|null $chapaEleicao
     */
    public function setChapaEleicao(?ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroSubstituidoTitular()
    {
        return $this->membroSubstituidoTitular;
    }

    /**
     * @param MembroChapa $membroSubstituidoTitular
     */
    public function setMembroSubstituidoTitular($membroSubstituidoTitular): void
    {
        $this->membroSubstituidoTitular = $membroSubstituidoTitular;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroSubstituidoSuplente()
    {
        return $this->membroSubstituidoSuplente;
    }

    /**
     * @param MembroChapa $membroSubstituidoSuplente
     */
    public function setMembroSubstituidoSuplente($membroSubstituidoSuplente): void
    {
        $this->membroSubstituidoSuplente = $membroSubstituidoSuplente;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroSubstitutoTitular()
    {
        return $this->membroSubstitutoTitular;
    }

    /**
     * @param MembroChapa $membroSubstitutoTitular
     */
    public function setMembroSubstitutoTitular($membroSubstitutoTitular): void
    {
        $this->membroSubstitutoTitular = $membroSubstitutoTitular;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroSubstitutoSuplente()
    {
        return $this->membroSubstitutoSuplente;
    }

    /**
     * @param MembroChapa $membroSubstitutoSuplente
     */
    public function setMembroSubstitutoSuplente($membroSubstitutoSuplente): void
    {
        $this->membroSubstitutoSuplente = $membroSubstitutoSuplente;
    }

    /**
     * @return StatusSubstituicaoChapa
     */
    public function getStatusSubstituicaoChapa()
    {
        return $this->statusSubstituicaoChapa;
    }

    /**
     * @param StatusSubstituicaoChapa $statusSubstituicaoChapa
     */
    public function setStatusSubstituicaoChapa($statusSubstituicaoChapa): void
    {
        $this->statusSubstituicaoChapa = $statusSubstituicaoChapa;
    }

    /**
     * @return mixed
     */
    public function getIdProfissionalInclusao()
    {
        return $this->idProfissionalInclusao;
    }

    /**
     * @param mixed $idProfissionalInclusao
     */
    public function setIdProfissionalInclusao($idProfissionalInclusao): void
    {
        $this->idProfissionalInclusao = $idProfissionalInclusao;
    }

    /**
     * @return mixed
     */
    public function getNomeProfissionalInclusao()
    {
        return $this->nomeProfissionalInclusao;
    }

    /**
     * @param mixed $nomeProfissionalInclusao
     */
    public function setNomeProfissionalInclusao($nomeProfissionalInclusao): void
    {
        $this->nomeProfissionalInclusao = $nomeProfissionalInclusao;
    }

    /**
     * @return bool
     */
    public function isPermissaoJulgamento(): ?bool
    {
        return $this->isPermissaoJulgamento;
    }

    /**
     * @param bool $isPermissaoJulgamento
     */
    public function setIsPermissaoJulgamento(?bool $isPermissaoJulgamento): void
    {
        $this->isPermissaoJulgamento = $isPermissaoJulgamento;
    }

    /**
     * @return JulgamentoSubstituicaoTO
     */
    public function getJulgamentoSubstituicao()
    {
        return $this->julgamentoSubstituicao;
    }

    /**
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicao
     */
    public function setJulgamentoSubstituicao($julgamentoSubstituicao)
    {
        $this->julgamentoSubstituicao = $julgamentoSubstituicao;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtividadeRecurso(): ?bool
    {
        return $this->isIniciadoAtividadeRecurso;
    }

    /**
     * @param bool|null $isIniciadoAtividadeRecurso
     */
    public function setIsIniciadoAtividadeRecurso(?bool $isIniciadoAtividadeRecurso): void
    {
        $this->isIniciadoAtividadeRecurso = $isIniciadoAtividadeRecurso;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtividadeRecurso(): ?bool
    {
        return $this->isFinalizadoAtividadeRecurso;
    }

    /**
     * @param bool|null $isFinalizadoAtividadeRecurso
     */
    public function setIsFinalizadoAtividadeRecurso(?bool $isFinalizadoAtividadeRecurso): void
    {
        $this->isFinalizadoAtividadeRecurso = $isFinalizadoAtividadeRecurso;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtividadeJulgamentoRecurso(): ?bool
    {
        return $this->isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool|null $isIniciadoAtividadeJulgamentoRecurso
     */
    public function setIsIniciadoAtividadeJulgamentoRecurso(?bool $isIniciadoAtividadeJulgamentoRecurso): void
    {
        $this->isIniciadoAtividadeJulgamentoRecurso = $isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtividadeJulgamentoRecurso(): ?bool
    {
        return $this->isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool|null $isFinalizadoAtividadeJulgamentoRecurso
     */
    public function setIsFinalizadoAtividadeJulgamentoRecurso(?bool $isFinalizadoAtividadeJulgamentoRecurso): void
    {
        $this->isFinalizadoAtividadeJulgamentoRecurso = $isFinalizadoAtividadeJulgamentoRecurso;
    }

    public function iniciarFlags()
    {
        $this->setIsIniciadoAtividadeRecurso(false);
        $this->setIsFinalizadoAtividadeRecurso(false);
        $this->setIsIniciadoAtividadeJulgamentoRecurso(false);
        $this->setIsFinalizadoAtividadeJulgamentoRecurso(false);
    }
}
