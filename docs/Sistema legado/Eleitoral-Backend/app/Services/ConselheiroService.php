<?php
/*
 * ConselheiroService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Services;

use App\Exceptions\NegocioException;
use Illuminate\Http\Request;
use App\Service\AbstractService;
use Exception;
use App\Models\Conselheiro;
use Illuminate\Support\Facades\DB;

/**
 * Classe responsável por encapsular as implementações de serviço referente à entidade 'Tipo Finalizacao Mandato'.
 */
class ConselheiroService extends AbstractService
{

    public function __construct()
    {
    }

    /**
     * Cadastra o Conselheiro
     *
     * @param Request $request
     * @return Conselheiro
     * @throws NegocioException
     */
    public function create(Request $request): Conselheiro
    {   
        DB::beginTransaction();
        try {
            $anoInicio = $request['membro']['ano']+1;
            $anofim = $request['membro']['ano']+3;

            $requestConselheiro = [
                'pessoa_id' => $request['membro']['pessoa_id'],
                'dt_inicio_mandato' => date('01-01-'.$anoInicio),
                'dt_fim_mandato' => date('31-12-'.$anofim),
                'tipo_conselheiro_id' => $request['membro']['idTipo'],
                'representacao_conselheiro_id' => $request['membro']['idRepresentacao'] == 3 ? 1 : $request['membro']['idRepresentacao'],
                'filial_id' => $request['membro']['idFilial'],
                'ies' => $request['membro']['idRepresentacao'] == 3 ? true : false,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'email' => $request['membro']['email'],
                'processo_eleitoral_id' => null,
                'recomposicao_mandato' => false,
                'ano_eleicao' => $request['membro']['ano'],
                'ativo' => true,
            ];

            $conselheiro = Conselheiro::create($requestConselheiro);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $conselheiro;
    }
 
    // /**
    //  * Atualizar o TipoFinalizacaoMandato
    //  *
    //  * @param Request $request
    //  * @param int $id
    //  * @return TipoFinalizacaoMandato
    //  * @throws NegocioException
    //  */
    // public function update(int $id, Request $request): TipoFinalizacaoMandato
    // {   
    //     DB::beginTransaction();
    //     try {
    //         $requestTipoFinalizacao = [
    //             'descricao' => $request['descricao'],
    //             'dt_alteracao' => date('Y-m-d h:i:s'),
    //             'ativo' => $request['ativo'],
    //             'id_usuario_alterar' => $request['idUsuario']
    //         ];

    //         TipoFinalizacaoMandato::where('id', $id)->update($requestTipoFinalizacao);
            
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
       
    //     return $this->getById($id);
    // }
    
    // /**
    //  * Busca o Tipo de FInalização pelo Id
    //  *
    //  * @param int $id
    //  * @return TipoFinalizacaoMandato
    //  */
    // public function getById(int $id): TipoFinalizacaoMandato
    // {
    //     $tipoFinalizacaoMandato = TipoFinalizacaoMandato::where('id', $id)->first();
        
    //     return $tipoFinalizacaoMandato;
    // }
}
