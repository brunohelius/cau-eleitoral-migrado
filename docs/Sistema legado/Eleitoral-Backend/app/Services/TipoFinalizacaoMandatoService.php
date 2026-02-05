<?php
/*
 * TipoFinalizacaoMandatoService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Services;

use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\TipoFinalizacaoMandatoRepository;
use Illuminate\Http\Request;
use App\Service\AbstractService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\TipoFinalizacaoMandato;

/**
 * Classe responsável por encapsular as implementações de serviço referente à entidade 'Tipo Finalizacao Mandato'.
 */
class TipoFinalizacaoMandatoService extends AbstractService
{
    /**
     * @var TipoFinalizacaoMandatoRepository
     */
    protected $tipofinalizacaoMandatoRepository;

    /**
     * @param TipoFinalizacaoMandatoRepository $TipofinalizacaoMandatoRepository
     */
    public function __construct(TipoFinalizacaoMandatoRepository $tipofinalizacaoMandatoRepository)
    {
        $this->tipofinalizacaoMandatoRepository = $tipofinalizacaoMandatoRepository;
    }

    /**
     * Cadastra o TipoFinalizacaoMandato
     *
     * @param Request $request
     * @return TipoFinalizacaoMandato
     * @throws NegocioException
     */
    public function create(Request $request): TipoFinalizacaoMandato
    {   
        DB::beginTransaction();
        try {
            $requestTipoFinalizacao = [
                'descricao' => $request['descricao'],
                'ativo' => $request['ativo'],
                'id_usuario' => $request['idUsuario']
            ];
           
            $tipoFinalizacaoMandato = TipoFinalizacaoMandato::create($requestTipoFinalizacao);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $tipoFinalizacaoMandato;
    }
 
    /**
     * Atualizar o TipoFinalizacaoMandato
     *
     * @param Request $request
     * @param int $id
     * @return TipoFinalizacaoMandato
     * @throws NegocioException
     */
    public function update(int $id, Request $request): TipoFinalizacaoMandato
    {   
        DB::beginTransaction();
        try {
            $requestTipoFinalizacao = [
                'descricao' => $request['descricao'],
                'dt_alteracao' => date('Y-m-d h:i:s'),
                'ativo' => $request['ativo'],
                'id_usuario_alterar' => $request['idUsuario']
            ];

            TipoFinalizacaoMandato::where('id', $id)->update($requestTipoFinalizacao);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
       
        return $this->getById($id);
    }
    
    /**
     * Busca o Tipo de FInalização pelo Id
     *
     * @param int $id
     * @return TipoFinalizacaoMandato
     */
    public function getById(int $id): TipoFinalizacaoMandato
    {
        $tipoFinalizacaoMandato = TipoFinalizacaoMandato::where('id', $id)->first();
        
        return $tipoFinalizacaoMandato;
    }

     /**
     * Busca o tipoFInalizacaoMandato pelo filtro
     *
     * @param request $request
     * @return Collection
     * @throws NegocioException
     */
    public function getByFilter(request $request): Collection
    {   
        $tipoFinalizacao = $this->tipofinalizacaoMandatoRepository->getByFilter($request);
      
        if ($tipoFinalizacao->isEmpty()) {
            throw new NegocioException(Message::MSG_NENHUM_REGISTRO_ENCONTRADO);
        }

        return $tipoFinalizacao;
    }

    /**
     * Deleta um tipoFInalizacaoMandato
     *
     * @param int $id
     */
    public function delete(int $id): int
    {
        DB::beginTransaction();
        try {
            $result = TipoFinalizacaoMandato::where('id', $id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $result;
    }
}
