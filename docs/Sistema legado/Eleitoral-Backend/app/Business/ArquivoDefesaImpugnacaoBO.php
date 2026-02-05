<?php
/*
 * PedidoImpugnacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoDefesaImpugnacao;
use App\Entities\DefesaImpugnacao;
use App\Entities\Entity;
use App\Exceptions\NegocioException;
use App\Repository\ArquivoDefesaImpugnacaoRepository;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ArquivoDefesaImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDefesaImpugnacaoBO extends AbstractBO
{

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var ArquivoDefesaImpugnacaoRepository
     */
    private $arquivoDefesaImpugnacaoRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar arquivos de Defesa de pedido de impugnação.
     *
     * @param Entity|DefesaImpugnacao $defesaImpugnacao
     * @param ArquivoDefesaImpugnacao[] $arquivos
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvar(DefesaImpugnacao $defesaImpugnacao ,$arquivos): void
    {
        try {
            $this->beginTransaction();

            $arquivos = $arquivos ? $arquivos : [];
            $caminho = $this->getArquivoService()->getCaminhoRepositorioDefesaImpugnacao($defesaImpugnacao->getId());

            /** @var  ArquivoDefesaImpugnacao $arquivo */
            foreach ($arquivos as $arquivo) {
                if(!$arquivo->getId())
                {

                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_ARQ_DEFESA_IMPUGNACAO
                    );

                    $this->getArquivoService()->salvar($caminho, $nomeArquivoFisico, $arquivo->getArquivo());

                    $arquivo->setNomeFisico($nomeArquivoFisico);
                    $arquivo->setDefesaImpugnacao( DefesaImpugnacao::newInstance(['id'=>$defesaImpugnacao->getId()]));
                    $arquivo->setArquivo(null);
                    //print_R($arquivo);
                    $this->getArquivoDefesaImpugnacaoRepository()->persist($arquivo);
                }
            }

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

    }

    /**
     * Apagar arquivos por array de id.
     *
     * @param $ids
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removerPorIds($ids)
    {
        try {
            $this->beginTransaction();
            $arquivos = $this->getArquivoDefesaImpugnacaoRepository()->findBy(['id' => $ids]);
            $this->getArquivoDefesaImpugnacaoRepository()->deleteEmLote($arquivos);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $idArquivoDefesaImpugnacao
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoDefesaImpugnacao($idArquivoDefesaImpugnacao)
    {
        /** @var ArquivoDefesaImpugnacao $arquivoDefesaImpugnacao */
        $arquivoDefesaImpugnacao = $this->getArquivoDefesaImpugnacaoRepository()->find($idArquivoDefesaImpugnacao);

        if (!empty($arquivoDefesaImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioDefesaImpugnacao(
                $arquivoDefesaImpugnacao->getDefesaImpugnacao()->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $arquivoDefesaImpugnacao->getNomeFisico(),
                $arquivoDefesaImpugnacao->getNome()
            );
        }
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
     * Retorna uma nova instância de 'ArquivoDefesaImpugnacaoRepository'.
     *
     * @return ArquivoDefesaImpugnacaoRepository|mixed
     */
    private function getArquivoDefesaImpugnacaoRepository()
    {
        if (empty($this->arquivoDefesaImpugnacaoRepository)) {
            $this->arquivoDefesaImpugnacaoRepository = $this->getRepository(ArquivoDefesaImpugnacao::class);
        }

        return $this->arquivoDefesaImpugnacaoRepository;
    }

}




