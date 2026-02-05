<?php
/*
 * Job.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 *
 * @author Squadra Tecnologia
 */
abstract class Job implements ShouldQueue
{
    /*
     * |--------------------------------------------------------------------------
     * | Queueable Jobs
     * |--------------------------------------------------------------------------
     * |
     * | This job base class provides a central location to place any logic that
     * | is shared across all of your jobs. The trait included with the class
     * | provides access to the "queueOn" and "delay" queue helper methods.
     * |
     */

    use InteractsWithQueue, Queueable, SerializesModels;
}
