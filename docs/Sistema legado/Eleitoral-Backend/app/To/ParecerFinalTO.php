<?php
/*
 * ParecerFinalTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\ArquivoEncaminhamentoDenuncia;
use App\Entities\ParecerFinal;
use App\Entities\Profissional;
use App\Util\Utils;

/**
 * Classe de transferência para Parecer final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ParecerFinalTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var integer|null $id
     */
    private $idTipoJulgamento;

    /**
     * @var integer|null $id
     */
    private $idTipoSentencaJulgamento;

    /**
     * @var integer|null $id
     */
    private $quantidadeDias;

    /**
     * @var bool|null $id
     */
    private $multa;

    /**
     * @var integer|null $id
     */
    private $valorPercentual;

    /**
     * @var string|null $descricao
     */
    private $descricao;

    /**
     * @var integer|null $idDenuncia
     */
    private $idDenuncia;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var ProfissionalTO|Profissional|null
     */
    private $usuarioCadastro;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var string|null
     */
    private $descricaoTipoJulgamento;

    /**
     * @var string|null
     */
    private $descricaoTipoSentenca;

    /**
     * @var int | null
     */
    private $statusEcaminhamento;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getIdTipoJulgamento()
    {
        return $this->idTipoJulgamento;
    }

    /**
     * @param int|null $idTipoJulgamento
     */
    public function setIdTipoJulgamento(?int $idTipoJulgamento): void
    {
        $this->idTipoJulgamento = $idTipoJulgamento;
    }

    /**
     * @return int|null
     */
    public function getIdTipoSentencaJulgamento()
    {
        return $this->idTipoSentencaJulgamento;
    }

    /**
     * @param int|null $idTipoSentencaJulgamento
     */
    public function setIdTipoSentencaJulgamento(?int $idTipoSentencaJulgamento): void
    {
        $this->idTipoSentencaJulgamento = $idTipoSentencaJulgamento;
    }

    /**
     * @return int|null
     */
    public function getQuantidadeDias()
    {
        return $this->quantidadeDias;
    }

    /**
     * @param int|null $quantidadeDias
     */
    public function setQuantidadeDias(?int $quantidadeDias): void
    {
        $this->quantidadeDias = $quantidadeDias;
    }

    /**
     * @return bool|null
     */
    public function getMulta()
    {
        return $this->multa;
    }

    /**
     * @param bool|null $multa
     */
    public function setMulta(?bool $multa): void
    {
        $this->multa = $multa;
    }

    /**
     * @return int|null
     */
    public function getValorPercentual()
    {
        return $this->valorPercentual;
    }

    /**
     * @param int|null $valorPercentual
     */
    public function setValorPercentual($valorPercentual): void
    {
        $this->valorPercentual = $valorPercentual;
    }

    /**
     * @return string|null
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int|null
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param int|null $idDenuncia
     */
    public function setIdDenuncia(?int $idDenuncia): void
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return Profissional|ProfissionalTO|null
     */
    public function getUsuarioCadastro()
    {
        return $this->usuarioCadastro;
    }

    /**
     * @param Profissional|ProfissionalTO|null $usuarioCadastro
     */
    public function setUsuarioCadastro($usuarioCadastro): void
    {
        $this->usuarioCadastro = $usuarioCadastro;
    }

    /**
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoJulgamento(): ?string
    {
        return $this->descricaoTipoJulgamento;
    }

    /**
     * @param string|null $descricaoTipoJulgamento
     */
    public function setDescricaoTipoJulgamento(?string $descricaoTipoJulgamento): void
    {
        $this->descricaoTipoJulgamento = $descricaoTipoJulgamento;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoSentenca(): ?string
    {
        return $this->descricaoTipoSentenca;
    }

    /**
     * @param string|null $descricaoTipoSentenca
     */
    public function setDescricaoTipoSentenca(?string $descricaoTipoSentenca): void
    {
        $this->descricaoTipoSentenca = $descricaoTipoSentenca;
    }

    /**
     * @return int|null
     */
    public function getStatusEcaminhamento(): ?int
    {
        return $this->statusEcaminhamento;
    }

    /**
     * @param int|null $statusEcaminhamento
     */
    public function setStatusEcaminhamento(?int $statusEcaminhamento): void
    {
        $this->statusEcaminhamento = $statusEcaminhamento;
    }

    /**
     * Retorna uma nova instância de 'AlegacaoFinalTO'.
     *
     * @param null $data
     * @return AlegacaoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $parecerFinalTO = new ParecerFinalTO();

        if ($data != null) {
            $parecerFinalTO->setId(Utils::getValue('id', $data));
            $parecerFinalTO->setIdTipoJulgamento(Utils::getValue('idTipoJulgamento', $data));
            $parecerFinalTO->setIdTipoSentencaJulgamento(Utils::getValue('idTipoSentencaJulgamento', $data));
            $parecerFinalTO->setQuantidadeDias(Utils::getValue('qtDiasSuspensaoPropaganda', $data));
            $parecerFinalTO->setMulta(Utils::getBooleanValue('multa', $data));
            $parecerFinalTO->setValorPercentual(Utils::getValue('vlPercentualMulta', $data));
            $parecerFinalTO->setDescricao(Utils::getValue('descricao', $data));
            $parecerFinalTO->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $parecerFinalTO->setDescricaoTipoJulgamento(Utils::getValue('descricaoTipoJulgamento', $data));
            $parecerFinalTO->setDescricaoTipoSentenca(Utils::getValue('descricaoTipoSentenca', $data));

            $arquivos = Utils::getValue('arquivosParecerFinal', $data);
            if(!empty($arquivos)) {
                $parecerFinalTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            }
        }

        return $parecerFinalTO;
    }

    /**
     * Fabricação estática de 'ParecerFinalTO'.
     *
     * @param ParecerFinal $parecerFinal
     * @return ParecerFinalTO
     */
    public static function newInstanceFromEntity(ParecerFinal $parecerFinal)
    {
        $parecerFinalTO = new ParecerFinalTO();

        if (!empty($parecerFinalTO)) {
            $parecerFinalTO->setId($parecerFinal->getId());
            $parecerFinalTO->setIdTipoJulgamento($parecerFinal->getTipoJulgamento()->getId());
            $parecerFinalTO->setQuantidadeDias($parecerFinal->getQuantidadeDiasSuspensaoPropaganda());
            $parecerFinalTO->setMulta($parecerFinal->isMulta());
            $parecerFinalTO->setValorPercentual($parecerFinal->getValorPercentualMulta());
            $parecerFinalTO->setDescricao($parecerFinal->getEncaminhamentoDenuncia()->getDescricao());
            $parecerFinalTO->setIdDenuncia($parecerFinal->getEncaminhamentoDenuncia()->getDenuncia()->getId());
            $parecerFinalTO->setStatusEcaminhamento($parecerFinal->getEncaminhamentoDenuncia()->getTipoSituacaoEncaminhamento()->getId());
            $parecerFinalTO->setDescricaoTipoJulgamento($parecerFinal->getTipoJulgamento()->getDescricao());

            $parecerFinalTO->setIdTipoSentencaJulgamento(
                !empty($parecerFinal->getTipoSentencaJulgamento()) ?
                    $parecerFinal->getTipoSentencaJulgamento()->getId() : null
            );

            $parecerFinalTO->setDescricaoTipoSentenca(
                !empty($parecerFinal->getTipoSentencaJulgamento()) ?
                    $parecerFinal->getTipoSentencaJulgamento()->getDescricao() : null
            );

            $arquivoParecer = $parecerFinal->getEncaminhamentoDenuncia()->getArquivoEncaminhamento() ?? [];
            if (!is_array($arquivoParecer)) {
                $arquivoParecer = $arquivoParecer->toArray();
            }
            if (!empty($arquivoParecer)) {
                $parecerFinalTO->setArquivos(array_map(static function (ArquivoEncaminhamentoDenuncia $arquivo) {
                    return ArquivoGenericoTO::newInstance([
                        'id' => $arquivo->getId(),
                        'nome' => $arquivo->getNome(),
                        'nomeFisico' => $arquivo->getNomeFisico()
                    ]);
                }, $arquivoParecer));
            } else {
                $parecerFinalTO->setArquivos([]);
            }
        }

        return $parecerFinalTO;
    }
}
