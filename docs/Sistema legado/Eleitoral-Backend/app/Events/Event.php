<?php
/*
 * Event.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 *
 * @author Squadra Tecnologia
 */
abstract class Event
{
    use SerializesModels;
}
