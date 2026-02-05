<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ChapaEleicaoBO;
use App\Business\EleicaoBO;
use App\Business\MembroChapaBO;

/**
 * Classe de controle referente a entidade PedidoSubstituicaoChapa
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class SubstituicaoChapaEleicaoController extends Controller
{

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        $this->membroChapaBO = app()->make(MembroChapaBO::class);
        $this->eleicaoBO = app()->make(EleicaoBO::class);
    }
}
