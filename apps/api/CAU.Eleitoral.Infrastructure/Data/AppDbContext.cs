using Microsoft.EntityFrameworkCore;
using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Denuncias;
using CAU.Eleitoral.Domain.Entities.Impugnacoes;
using CAU.Eleitoral.Domain.Entities.Julgamentos;
using CAU.Eleitoral.Domain.Entities.Documentos;

namespace CAU.Eleitoral.Infrastructure.Data;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options)
    {
    }

    // Core
    public DbSet<Eleicao> Eleicoes { get; set; }
    public DbSet<Calendario> Calendarios { get; set; }
    public DbSet<AtividadePrincipalCalendario> AtividadesPrincipaisCalendario { get; set; }
    public DbSet<AtividadeSecundariaCalendario> AtividadesSecundariasCalendario { get; set; }
    public DbSet<Configuracao> Configuracoes { get; set; }
    public DbSet<ConfiguracaoEleicao> ConfiguracoesEleicao { get; set; }
    public DbSet<ParametroEleicao> ParametrosEleicao { get; set; }
    public DbSet<RegionalCAU> RegionaisCAU { get; set; }
    public DbSet<Filial> Filiais { get; set; }
    public DbSet<Circunscricao> Circunscricoes { get; set; }
    public DbSet<ZonaEleitoral> ZonasEleitorais { get; set; }
    public DbSet<SecaoEleitoral> SecoesEleitorais { get; set; }
    public DbSet<Voto> Votos { get; set; }
    public DbSet<Eleitor> Eleitores { get; set; }
    public DbSet<EleicaoSituacao> EleicaoSituacoes { get; set; }
    public DbSet<CalendarioSituacao> CalendarioSituacoes { get; set; }
    public DbSet<TipoEleicaoConfig> TiposEleicaoConfig { get; set; }
    public DbSet<FaseEleicaoConfig> FasesEleicaoConfig { get; set; }
    public DbSet<EtapaEleicao> EtapasEleicao { get; set; }
    public DbSet<RegiaoPleito> RegioesPleito { get; set; }
    public DbSet<UrnaEletronica> UrnasEletronicas { get; set; }
    public DbSet<MesaReceptora> MesasReceptoras { get; set; }
    public DbSet<FiscalEleicao> FiscaisEleicao { get; set; }
    public DbSet<ApuracaoResultado> ApuracaoResultados { get; set; }
    public DbSet<ApuracaoResultadoChapa> ApuracaoResultadosChapa { get; set; }

    // Usuarios
    public DbSet<Usuario> Usuarios { get; set; }
    public DbSet<Role> Roles { get; set; }
    public DbSet<UsuarioRole> UsuarioRoles { get; set; }
    public DbSet<Permissao> Permissoes { get; set; }
    public DbSet<RolePermissao> RolePermissoes { get; set; }
    public DbSet<Profissional> Profissionais { get; set; }
    public DbSet<Conselheiro> Conselheiros { get; set; }
    public DbSet<HistoricoExtratoConselheiro> HistoricosExtratoConselheiro { get; set; }
    public DbSet<LogAcesso> LogsAcesso { get; set; }

    // Chapas
    public DbSet<ChapaEleicao> Chapas { get; set; }
    public DbSet<MembroChapa> MembrosChapa { get; set; }
    public DbSet<DocumentoChapa> DocumentosChapa { get; set; }
    public DbSet<ConfirmacaoMembroChapa> ConfirmacoesMembrosChapa { get; set; }
    public DbSet<SubstituicaoMembroChapa> SubstituicoesMembrosChapa { get; set; }
    public DbSet<HistoricoChapaEleicao> HistoricosChapa { get; set; }
    public DbSet<PlataformaEleitoral> PlataformasEleitorais { get; set; }
    public DbSet<ComposicaoChapa> ComposicoesChapa { get; set; }

    // Denuncias
    public DbSet<Denuncia> Denuncias { get; set; }
    public DbSet<ProvaDenuncia> ProvasDenuncia { get; set; }
    public DbSet<DefesaDenuncia> DefesasDenuncia { get; set; }
    public DbSet<ArquivoDefesa> ArquivosDefesa { get; set; }
    public DbSet<AdmissibilidadeDenuncia> AdmissibilidadesDenuncia { get; set; }
    public DbSet<JulgamentoDenuncia> JulgamentosDenuncia { get; set; }
    public DbSet<VotacaoJulgamentoDenuncia> VotacoesJulgamentoDenuncia { get; set; }
    public DbSet<RecursoDenuncia> RecursosDenuncia { get; set; }
    public DbSet<ContrarrazoesRecursoDenuncia> ContrarrazoesRecursoDenuncia { get; set; }
    public DbSet<JulgamentoRecursoDenuncia> JulgamentosRecursoDenuncia { get; set; }
    public DbSet<HistoricoDenuncia> HistoricosDenuncia { get; set; }
    public DbSet<NotificacaoDenuncia> NotificacoesDenuncia { get; set; }
    public DbSet<DenunciaChapa> DenunciaChapas { get; set; }
    public DbSet<DenunciaMembro> DenunciaMembros { get; set; }
    public DbSet<ArquivoDenuncia> ArquivosDenuncia { get; set; }
    public DbSet<AnaliseDenuncia> AnalisesDenuncia { get; set; }
    public DbSet<AlegacoesDenuncia> AlegacoesDenuncia { get; set; }
    public DbSet<ContraAlegacoesDenuncia> ContraAlegacoesDenuncia { get; set; }
    public DbSet<EncaminhamentoDenuncia> EncaminhamentosDenuncia { get; set; }
    public DbSet<ParecerDenuncia> PareceresDenuncia { get; set; }
    public DbSet<DespachoDenuncia> DespachosDenuncia { get; set; }
    public DbSet<VistaDenuncia> VistasDenuncia { get; set; }
    public DbSet<AnexoDenuncia> AnexosDenuncia { get; set; }

    // Impugnacoes
    public DbSet<ImpugnacaoResultado> Impugnacoes { get; set; }
    public DbSet<PedidoImpugnacao> PedidosImpugnacao { get; set; }
    public DbSet<ArquivoPedidoImpugnacao> ArquivosPedidoImpugnacao { get; set; }
    public DbSet<AlegacaoImpugnacaoResultado> AlegacoesImpugnacao { get; set; }
    public DbSet<ContraAlegacaoImpugnacao> ContraAlegacoesImpugnacao { get; set; }
    public DbSet<ProvaImpugnacao> ProvasImpugnacao { get; set; }
    public DbSet<DefesaImpugnacao> DefesasImpugnacao { get; set; }
    public DbSet<JulgamentoImpugnacao> JulgamentosImpugnacao { get; set; }
    public DbSet<VotacaoJulgamentoImpugnacao> VotacoesJulgamentoImpugnacao { get; set; }
    public DbSet<RecursoImpugnacao> RecursosImpugnacao { get; set; }
    public DbSet<ContrarrazoesRecursoImpugnacao> ContrarrazoesRecursoImpugnacao { get; set; }
    public DbSet<JulgamentoRecursoImpugnacao> JulgamentosRecursoImpugnacao { get; set; }
    public DbSet<HistoricoImpugnacao> HistoricosImpugnacao { get; set; }

    // Julgamentos
    public DbSet<ComissaoJulgadora> ComissoesJulgadoras { get; set; }
    public DbSet<MembroComissaoJulgadora> MembrosComissaoJulgadora { get; set; }
    public DbSet<SessaoJulgamento> SessoesJulgamento { get; set; }
    public DbSet<PautaSessao> PautasSessao { get; set; }
    public DbSet<AtaSessao> AtasSessao { get; set; }
    public DbSet<JulgamentoFinal> JulgamentosFinais { get; set; }
    public DbSet<VotoJulgamentoFinal> VotosJulgamentoFinal { get; set; }
    public DbSet<AcordaoJulgamento> AcordaosJulgamento { get; set; }
    public DbSet<DecisaoJulgamento> DecisoesJulgamento { get; set; }
    public DbSet<PublicacaoJulgamento> PublicacoesJulgamento { get; set; }
    public DbSet<RecursoJulgamentoFinal> RecursosJulgamentoFinal { get; set; }
    public DbSet<RecursoSegundaInstancia> RecursosSegundaInstancia { get; set; }
    public DbSet<JulgamentoRecursoSegundaInstancia> JulgamentosRecursoSegundaInstancia { get; set; }
    public DbSet<SubstituicaoJulgamentoFinal> SubstituicoesJulgamentoFinal { get; set; }
    public DbSet<AlegacaoFinal> AlegacoesFinais { get; set; }
    public DbSet<ContraAlegacaoFinal> ContraAlegacoesFinais { get; set; }
    public DbSet<ProvaJulgamento> ProvasJulgamento { get; set; }
    public DbSet<ArquivoJulgamento> ArquivosJulgamento { get; set; }
    public DbSet<NotificacaoJulgamento> NotificacoesJulgamento { get; set; }
    public DbSet<IntimacaoJulgamento> IntimacoesJulgamento { get; set; }
    public DbSet<CertidaoJulgamento> CertidoesJulgamento { get; set; }
    public DbSet<RelatorioJulgamento> RelatoriosJulgamento { get; set; }
    public DbSet<VotoPlenario> VotosPlenario { get; set; }
    public DbSet<PareceristaProcurador> PareceristaProcuradores { get; set; }
    public DbSet<VotoRelator> VotosRelator { get; set; }
    public DbSet<VotoRevisor> VotosRevisor { get; set; }
    public DbSet<VotoVogal> VotosVogal { get; set; }
    public DbSet<EmendaJulgamento> EmendasJulgamento { get; set; }
    public DbSet<VotoEmenda> VotosEmenda { get; set; }
    public DbSet<ObservacaoJulgamento> ObservacoesJulgamento { get; set; }
    public DbSet<DiligenciaJulgamento> DiligenciasJulgamento { get; set; }
    public DbSet<SuspensaoJulgamento> SuspensoesJulgamento { get; set; }
    public DbSet<ArquivamentoJulgamento> ArquivamentosJulgamento { get; set; }

    // Documentos - Base
    public DbSet<Documento> Documentos { get; set; }
    public DbSet<ArquivoDocumento> ArquivosDocumento { get; set; }
    public DbSet<Publicacao> Publicacoes { get; set; }
    public DbSet<Edital> Editais { get; set; }
    public DbSet<ResultadoEleicao> ResultadosEleicao { get; set; }
    public DbSet<VotoChapa> VotosChapa { get; set; }
    public DbSet<AssinaturaDigital> AssinaturasDigitais { get; set; }
    public DbSet<CertificadoDigital> CertificadosDigitais { get; set; }
    public DbSet<CarimboTempo> CarimbosTempo { get; set; }

    // Documentos - Categoria e Publicacao
    public DbSet<CategoriaDocumentoEntity> CategoriasDocumento { get; set; }
    public DbSet<PublicacaoOficial> PublicacoesOficiais { get; set; }

    // Documentos - Normativos
    public DbSet<Resolucao> Resolucoes { get; set; }
    public DbSet<Normativa> Normativas { get; set; }
    public DbSet<Portaria> Portarias { get; set; }
    public DbSet<Deliberacao> Deliberacoes { get; set; }
    public DbSet<Ato> Atos { get; set; }

    // Documentos - Comunicacoes
    public DbSet<Comunicado> Comunicados { get; set; }
    public DbSet<Aviso> Avisos { get; set; }
    public DbSet<Convocacao> Convocacoes { get; set; }

    // Documentos - Termos e Certificados
    public DbSet<Termo> Termos { get; set; }
    public DbSet<TermoPosse> TermosPosse { get; set; }
    public DbSet<Diploma> Diplomas { get; set; }
    public DbSet<Certificado> Certificados { get; set; }
    public DbSet<Declaracao> Declaracoes { get; set; }

    // Documentos - Atas
    public DbSet<AtaReuniao> AtasReuniao { get; set; }
    public DbSet<AtaApuracao> AtasApuracao { get; set; }

    // Documentos - Votacao e Apuracao
    public DbSet<BoletimUrna> BoletinsUrna { get; set; }
    public DbSet<MapaVotacao> MapasVotacao { get; set; }
    public DbSet<ResultadoParcial> ResultadosParciais { get; set; }
    public DbSet<ResultadoFinal> ResultadosFinais { get; set; }
    public DbSet<RegistroApuracaoVotos> RegistrosApuracaoVotos { get; set; }
    public DbSet<TotalVotos> TotaisVotos { get; set; }
    public DbSet<VotoBranco> VotosBrancos { get; set; }
    public DbSet<VotoNulo> VotosNulos { get; set; }
    public DbSet<VotoAnulado> VotosAnulados { get; set; }

    // Documentos - Relatorios e Estatisticas
    public DbSet<RelatorioVotacao> RelatoriosVotacao { get; set; }
    public DbSet<RelatorioApuracao> RelatoriosApuracao { get; set; }
    public DbSet<EstatisticaEleicao> EstatisticasEleicao { get; set; }
    public DbSet<GraficoResultado> GraficosResultado { get; set; }

    // Documentos - Importacao/Exportacao
    public DbSet<ExportacaoDados> ExportacoesDados { get; set; }
    public DbSet<ImportacaoDados> ImportacoesDados { get; set; }

    // Documentos - Modelos e Templates
    public DbSet<ModeloDocumento> ModelosDocumento { get; set; }
    public DbSet<TemplateDocumento> TemplatesDocumento { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // Apply all configurations from assembly
        modelBuilder.ApplyConfigurationsFromAssembly(typeof(AppDbContext).Assembly);

        // Configure AssinaturaDigital - CarimboTempo relationship
        modelBuilder.Entity<AssinaturaDigital>()
            .HasOne(a => a.CarimboTempo)
            .WithMany(c => c.Assinaturas)
            .HasForeignKey(a => a.CarimboTempoId)
            .OnDelete(DeleteBehavior.SetNull);

        // Configure CertificadoDigital relationship
        modelBuilder.Entity<AssinaturaDigital>()
            .HasOne(a => a.CertificadoDigitalUsado)
            .WithMany()
            .HasForeignKey(a => a.CertificadoDigitalId)
            .OnDelete(DeleteBehavior.SetNull);

        // Configure RecursoJulgamentoFinal - RecursoSegundaInstancia relationships
        modelBuilder.Entity<RecursoJulgamentoFinal>()
            .HasOne(r => r.RecursoSegundaInstancia)
            .WithOne()
            .HasForeignKey<RecursoJulgamentoFinal>(r => r.RecursoSegundaInstanciaId)
            .OnDelete(DeleteBehavior.SetNull);

        modelBuilder.Entity<RecursoSegundaInstancia>()
            .HasOne(r => r.RecursoJulgamentoFinalOrigem)
            .WithOne()
            .HasForeignKey<RecursoSegundaInstancia>(r => r.RecursoJulgamentoFinalOrigemId)
            .OnDelete(DeleteBehavior.SetNull);

        // Configure soft delete filter for all entities
        foreach (var entityType in modelBuilder.Model.GetEntityTypes())
        {
            if (typeof(BaseEntity).IsAssignableFrom(entityType.ClrType))
            {
                var method = typeof(AppDbContext)
                    .GetMethod(nameof(SetSoftDeleteFilter), System.Reflection.BindingFlags.NonPublic | System.Reflection.BindingFlags.Static)!
                    .MakeGenericMethod(entityType.ClrType);
                method.Invoke(null, new object[] { modelBuilder });
            }
        }
    }

    private static void SetSoftDeleteFilter<T>(ModelBuilder modelBuilder) where T : BaseEntity
    {
        modelBuilder.Entity<T>().HasQueryFilter(e => !e.IsDeleted);
    }

    public override int SaveChanges()
    {
        UpdateAuditFields();
        return base.SaveChanges();
    }

    public override Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
    {
        UpdateAuditFields();
        return base.SaveChangesAsync(cancellationToken);
    }

    private void UpdateAuditFields()
    {
        var entries = ChangeTracker.Entries<BaseEntity>();

        foreach (var entry in entries)
        {
            switch (entry.State)
            {
                case EntityState.Added:
                    entry.Entity.CreatedAt = DateTime.UtcNow;
                    if (entry.Entity.Id == Guid.Empty)
                        entry.Entity.Id = Guid.NewGuid();
                    break;
                case EntityState.Modified:
                    entry.Entity.UpdatedAt = DateTime.UtcNow;
                    break;
            }
        }
    }
}
