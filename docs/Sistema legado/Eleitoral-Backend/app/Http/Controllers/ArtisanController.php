<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 22/08/2019
 * Time: 16:17
 */

namespace App\Http\Controllers;

use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\EmailAtividadeSecundariaBO;
use App\Business\MembroComissaoBO;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Classe de controle referente a entidade 'Arquivo'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ArtisanController extends Controller
{
    /**
     *
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;


    /**
     *
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     *
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
    }

    public function chamarComandoSchedule()
    {
        //Redis::connection()->set('name', 'Diego teste');
        Log::info("php artisan schedule:run");
        Log::info("php artisan queue:work --sleep=5 --tries=3 --timeout=3600");
        Artisan::call("schedule:run");
        Artisan::call("queue:work --sleep=5 --tries=3 --timeout=3600");
    }

    public function verificaAgendamentos()
    {
        Log::info("verificaAgendamentos");
    }

    public function verificaMenusPortal(EntityManagerInterface $entityManager)
    {
        $sql = "select * from portal.tb_menu tm 
            inner join portal.tb_menu_permissao tmp on tmp.id_menu = tm.id_menu 
            order by tm.id_menu";

        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $this->toJson($stmt->fetchAll());
    }

    public function verificaPermissoesUsuario(EntityManagerInterface $entityManager)
    {
        $sql = "select * from portal.tb_menu tm 
            inner join portal.tb_menu_permissao tmp on tmp.id_menu = tm.id_menu 
            order by tm.id_menu";

        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $this->toJson($stmt->fetchAll());
    }

    public function recuperaInformacaoTabela(string $tabela, EntityManagerInterface $entityManager)
    {
        $sql = "select column_name, data_type from information_schema.columns
            where table_name = '{$tabela}' ";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        $retorno['colunas'] = $stmt->fetchAll();

        $sql = "select * from eleitoral.{$tabela} ";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        $retorno['dados'] = $stmt->fetchAll();

        return $this->toJson($retorno);
    }
}
