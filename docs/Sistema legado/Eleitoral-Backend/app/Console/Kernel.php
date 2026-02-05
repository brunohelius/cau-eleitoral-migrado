<?php
/*
 * Kernel.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Console;

use App\Console\Commands\AlertarAntesFimPeriodoChapaCommand;
use App\Console\Commands\AlertarCadastroJulgamentoFinalFimPeriodoCommand;
use App\Console\Commands\AlertarCadastroJulgamentoImpugFimPeriodoCommand;
use App\Console\Commands\AlertarCadastroJulgamentoRecursoImpugFimPeriodoCommand;
use App\Console\Commands\AlertarCadastroJulgamentoRecursoSubstFimPeriodoCommand;
use App\Console\Commands\AlertarConvitesAConfirmarMembroChapaCommand;
use App\Console\Commands\AlertarDenunciaSemRelatorCommand;
use App\Console\Commands\AlertarFimPeriodoDefinicaoComissaoCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoFinalCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoFinalSegundaInstanciaCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoImpugnacaoCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoRecursoImpugCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoRecursoSubstCommand;
use App\Console\Commands\AlertarFimPeriodoJulgamentoSubstituicaoCommand;
use App\Console\Commands\AlertarImpugnanteFimPeriodoDefesaImpugnacaoCommand;
use App\Console\Commands\AlertarInicioPeriodoCadastroDefesaImpugnacaoCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoFinalCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoFinalSegundaInstanciaCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoImpugnacaoCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoRecursoImpugCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoRecursoSubstCommand;
use App\Console\Commands\AlertarInicioPeriodoJulgamentoSubstituicaoCommand;
use App\Console\Commands\AlertarInicioPeriodoMembroComissaoCommand;
use App\Console\Commands\AlterarStatusImpugnacaoResultadoFimAtivRecursoCommand;
use App\Console\Commands\AlterarStatusImpugnacaoResultadoInicioAtivContrarrazaoCommand;
use App\Console\Commands\AlterarStatusImpugnacaoResultadoInicioAtivJulgSegInstanciaCommand;
use App\Console\Commands\AlterarStatusImpugnacaoResultadoInicioAtivRecursoCommand;
use App\Console\Commands\AlteraStatusDenunciaApresentacaoDefesaCommand;
use App\Console\Commands\AlteraStatusDenunciaContrarrazaoRecursoCommand;
use App\Console\Commands\AlteraStatusDenunciaRecursoCommand;
use App\Console\Commands\AlteraStatusEncaminhamentoDenunciaProvasCommand;
use App\Console\Commands\AtualizarConselheirosAutomaticoCommand;
use App\Console\Commands\AtualizarSituacaoEnviarEmailEncaminhamentoPrazoEncerradoCommand;
use App\Console\Commands\EnviarConvitesPendentesMembroComissaoCommand;
use App\Console\Commands\EnviarEmailDefesaDenunciaExpiradaCommand;
use App\Console\Commands\EnviarEmailDenunciasAudienciaInstrucaoPendentesCommand;
use App\Console\Commands\EnviarEmailJulgamentoSubstituicaoInicioRecursoCommand;
use App\Console\Commands\EnviarEmailPrazoEncerradoJulgamentoRecursoDenunciaCommand;
use App\Console\Commands\ExcluirChapasNaoConfirmadasCommand;
use App\Console\Commands\GerarExtratoChapaCommand;
use App\Console\Commands\RejeitarConvitesExpiradoPeriodoCadastroChapaCommand;
use App\Console\Commands\RejeitarConvitesExpiradoPeriodoSubstituicaoChapaCommand;
use App\Console\Commands\ValidarChapaEleicaoEMembrosCommand;
use App\Console\Commands\AlterarSituacaoDenunciaJulgamentoAdmissiblidadeSemRecursoCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

/**
 * @author Squadra Tecnologia
 */
class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AlertarFimPeriodoDefinicaoComissaoCommand::class,
        AlertarInicioPeriodoMembroComissaoCommand::class,
        EnviarConvitesPendentesMembroComissaoCommand::class,
        ValidarChapaEleicaoEMembrosCommand::class,
        ExcluirChapasNaoConfirmadasCommand::class,
        AlertarAntesFimPeriodoChapaCommand::class,
        AlertarConvitesAConfirmarMembroChapaCommand::class,
        RejeitarConvitesExpiradoPeriodoCadastroChapaCommand::class,
        AtualizarConselheirosAutomaticoCommand::class,
        AlertarInicioPeriodoCadastroDefesaImpugnacaoCommand::class,
        AlertarImpugnanteFimPeriodoDefesaImpugnacaoCommand::class,
        AlertarInicioPeriodoJulgamentoSubstituicaoCommand::class,
        AlertarFimPeriodoJulgamentoSubstituicaoCommand::class,
        EnviarEmailJulgamentoSubstituicaoInicioRecursoCommand::class,
        AlertarInicioPeriodoJulgamentoImpugnacaoCommand::class,
        AlertarFimPeriodoJulgamentoImpugnacaoCommand::class,
        AlertarInicioPeriodoJulgamentoRecursoSubstCommand::class,
        AlertarFimPeriodoJulgamentoRecursoSubstCommand::class,
        AlertarCadastroJulgamentoRecursoSubstFimPeriodoCommand::class,
        EnviarEmailDefesaDenunciaExpiradaCommand::class,
        AtualizarSituacaoEnviarEmailEncaminhamentoPrazoEncerradoCommand::class,
        RejeitarConvitesExpiradoPeriodoSubstituicaoChapaCommand::class,
        AlertarInicioPeriodoJulgamentoRecursoImpugCommand::class,
        AlertarFimPeriodoJulgamentoRecursoImpugCommand::class,
        AlertarCadastroJulgamentoRecursoImpugFimPeriodoCommand::class,
        AlertarCadastroJulgamentoImpugFimPeriodoCommand::class,
        AlteraStatusEncaminhamentoDenunciaProvasCommand::class,
        EnviarEmailDenunciasAudienciaInstrucaoPendentesCommand::class,
        AlteraStatusDenunciaRecursoCommand::class,
        AlteraStatusDenunciaContrarrazaoRecursoCommand::class,
        AlertarInicioPeriodoJulgamentoFinalCommand::class,
        AlertarFimPeriodoJulgamentoFinalCommand::class,
        AlertarCadastroJulgamentoFinalFimPeriodoCommand::class,
        EnviarEmailPrazoEncerradoJulgamentoRecursoDenunciaCommand::class,
        AlterarSituacaoDenunciaJulgamentoAdmissiblidadeSemRecursoCommand::class,
        AlertarInicioPeriodoJulgamentoFinalSegundaInstanciaCommand::class,
        AlertarFimPeriodoJulgamentoFinalSegundaInstanciaCommand::class,
        AlertarDenunciaSemRelatorCommand::class,
        AlterarSituacaoDenunciaJulgamentoAdmissiblidadeSemRecursoCommand::class,
        AlteraStatusDenunciaApresentacaoDefesaCommand::class,
        AlterarSituacaoDenunciaJulgamentoAdmissiblidadeSemRecursoCommand::class,
        AlterarStatusImpugnacaoResultadoInicioAtivRecursoCommand::class,
        AlterarStatusImpugnacaoResultadoInicioAtivContrarrazaoCommand::class,
        AlterarStatusImpugnacaoResultadoInicioAtivJulgSegInstanciaCommand::class,
        AlterarStatusImpugnacaoResultadoFimAtivRecursoCommand::class,
        GerarExtratoChapaCommand::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Rotinas relaiconados a membros da comissão
        $schedule->command('membrosComissao:enviarEmailInicioPeriodoCadastro')->dailyAt('08:00');
        $schedule->command('membrosComissao:enviarEmailFimPeriodoDefinicao')->dailyAt('08:00');
        $schedule->command('membrosComissao:enviarConvitesPendentesMembroComissao')->dailyAt('08:00');

        // Rotinas relacionados a proporção de conselheiros
        $schedule->command('paramConselheiro:atualizar')->dailyAt('00:00');

        // Rotinas relacionados a chapa da eleição
        $schedule->command('chapas:enviarEmailCincoDiasAntesFim')->dailyAt('08:00');
        $schedule->command('chapas:validarPeriodoVigente')->dailyAt('00:00');
        $schedule->command('chapas:excluirChapasNaoConfirmadas')->dailyAt('00:00');
        $schedule->command('chapas:gerarExtratoChapa')->dailyAt('00:00');

        // Rotinas relacionados a membros da chapa da eleição
        $schedule->command('membrosChapa:enviarEmailCincoDiasAntesFimConvite')->dailyAt('08:00');
        $schedule->command('membrosChapa:rejeitarConvitesExpiradosPeriodoCadastroChapa')->dailyAt('00:00');

        // Rotinas relacionados a julgamentos dos pedidos de substituição de membros da chapa da eleição
        $schedule->command('julgamentoSubstituicao:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoSubstituicao:alertarFimPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoSubstituicao:alertarNoInicioRecurso')->dailyAt('08:00');

        // Rotinas relacionados a defesa do pedido de impugnação
        $schedule->command('defesaImpugnacao:alertarInicioPeriodoCadastro')->dailyAt('08:00');
        $schedule->command('defesaImpugnacao:alertarFimPeriodoCadastro')->dailyAt('08:00');

        // Rotinas relacionados a julgamento do pedido de impugnação
        $schedule->command('julgamentoImpugnacao:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoImpugnacao:alertarFimPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoImpugnacao:alertarCadastroFimPeriodo')->dailyAt('08:00');

        // Rotinas relacionados a julgamentos de substituição 2ª instância
        $schedule->command('julgamentoRecursoSubstituicao:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoRecursoSubstituicao:alertarFimPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoRecursoSubstituicao:alertarCadastroFimPeriodo')->dailyAt('00:00');

        // Rotinas relacionados a Email de Defesa de Denuncia Expirada.
        $schedule->command('defesaDenuncia:enviarEmailDenunciasDefesaExpira')->dailyAt('00:15');

        // Rotinas relacionados Alteração de Status das Provas da Denuncia.
        $schedule->command('denunciaProvas:alteraStatusEncaminhamentoDenunciaProvas')->dailyAt('23:45');

        // Rotinas para Encaminhamentos de Audiencia de Instrução com mais de 24 Horas Pendentes.
        $schedule->command('denunciaAudienciaInstrucao:enviarEmailDenunciasAudienciaInstrucaoPendentes')->dailyAt('23:30');

        // Rotinas relacionados a mudança de situação e enviar Email de Encaminhamento Alegações Finais
        $schedule->command('alegacaoFinal:atualizarSituacaoEncaminhamento')->dailyAt('00:00');

        // Rotinas relacionada a substituição de impugnação
        $schedule->command('membrosChapa:rejeitarConvitesExpiradosPeriodoSubstituicaoChapa')->dailyAt('00:00');

        // Rotinas relacionados a julgamentos de substituição 2ª instância
        $schedule->command('julgamentoRecursoImpugnacao:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoRecursoImpugnacao:alertarFimPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoRecursoImpugnacao:alertarCadastroFimPeriodo')->dailyAt('00:00');

        // Rotinas relacionados a Alteração do status da denuncia de acordo com recurso da Denuncia.
        $schedule->command('denuncia:alteraStatusDenunciaRecurso')->dailyAt('00:15');
        $schedule->command('denuncia:alteraStatusDenunciaContrarrazaoRecurso')->dailyAt('00:15');

        // Rotinas relacionados a julgamentos final 1ª instância
        $schedule->command('julgamentoFinal:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoFinal:alertarFimPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoFinal:alertarCadastroFimPeriodo')->dailyAt('00:00');

        // Rotinas relacionados a julgamentos final 2ª instância
        $schedule->command('julgamentoFinalSegundaInstancia:alertarInicioPeriodo')->dailyAt('08:00');
        $schedule->command('julgamentoFinalSegundaInstancia:alertarFimPeriodo')->dailyAt('08:00');

        // Rotinas relacionados a julgamento recurso da denúncia prazo encerrado
        $schedule->command('julgamentoRecursoDenuncia:enviarEmailPrazoEncerradoJulgamentoRecursoDenuncia')->dailyAt('00:15');

        //Rotinas de recurso da admissibilidade
        $schedule->command('julgamentoaAdmissibilidadeSemRecurso:alterarSituacaoDenuncia')->dailyAt('00:00');

        // Rotinas de envio de email para denuncia sem relator
        $schedule->command('denuncia:alertarSemRelator')->dailyAt('08:00');

        // Rotinas relacionados a Alteração do status da denuncia de acordo com prazo de defesa da Denuncia.
        $schedule->command('denuncia:alteraStatusDenunciaApresentacaoDefesa')->dailyAt('00:15');

        // Rotinas relacionadas a impugnação de resultado.
        $schedule->command('impugnacaoResultado:alteraStatusEmRecurso')->dailyAt('00:00');
        $schedule->command('impugnacaoResultado:alteraStatusEmContrarrazao')->dailyAt('00:00');
        $schedule->command('impugnacaoResultado:alteraStatusInicioAtivJulgamentoRecurso')->dailyAt('00:00');
        $schedule->command('impugnacaoResultado:alteraStatusFimAtivRecurso')->dailyAt('00:00');

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
