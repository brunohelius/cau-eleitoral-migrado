<?php
/*
 * TipoFinalizacaoMandatoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Models\TipoFinalizacaoMandato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Tipo Finalização Mandato'.
 *
 * @package App\Repositories
 * @author Squadra Tecnologia S/A.
 */
class TipoFinalizacaoMandatoRepository 
{
    public function __construct(){    
    }

    public function getByFilter(Request $request): Collection
    {
        $tipoFinalizacaoMandato = TipoFinalizacaoMandato::orderByRaw("ativo DESC, id ASC")
            ->get();

        return $tipoFinalizacaoMandato;
    }
}
