using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Denuncias;
using CAU.Eleitoral.Domain.Entities.Julgamentos;
using CAU.Eleitoral.Domain.Entities.Documentos;
using CAU.Eleitoral.Domain.Enums;
using System.Security.Cryptography;

namespace CAU.Eleitoral.Infrastructure.Data;

public class DatabaseSeeder
{
    private readonly AppDbContext _context;
    private readonly ILogger<DatabaseSeeder> _logger;

    public DatabaseSeeder(AppDbContext context, ILogger<DatabaseSeeder> logger)
    {
        _context = context;
        _logger = logger;
    }

    public async Task SeedAsync()
    {
        try
        {
            _logger.LogInformation("Iniciando seed do banco de dados...");

            await SeedRolesAndPermissionsAsync();
            await SeedRegionaisAsync();
            await SeedUsuariosAsync();
            await SeedFiliaisAsync();
            await SeedProfissionaisAsync();
            await SeedEleicoesAsync();
            await SeedChapasAsync();
            await SeedDenunciasAsync();
            await SeedComissoesAsync();
            await SeedDocumentosAsync();

            _logger.LogInformation("Seed do banco de dados concluído com sucesso!");
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro durante o seed do banco de dados");
            throw;
        }
    }

    private async Task SeedRolesAndPermissionsAsync()
    {
        if (await _context.Roles.AnyAsync()) return;

        _logger.LogInformation("Criando roles e permissões...");

        var permissoes = new List<Permissao>
        {
            new() { Nome = "eleicao.criar", Descricao = "Criar eleições" },
            new() { Nome = "eleicao.editar", Descricao = "Editar eleições" },
            new() { Nome = "eleicao.excluir", Descricao = "Excluir eleições" },
            new() { Nome = "eleicao.visualizar", Descricao = "Visualizar eleições" },
            new() { Nome = "chapa.criar", Descricao = "Criar chapas" },
            new() { Nome = "chapa.editar", Descricao = "Editar chapas" },
            new() { Nome = "chapa.aprovar", Descricao = "Aprovar chapas" },
            new() { Nome = "denuncia.criar", Descricao = "Criar denúncias" },
            new() { Nome = "denuncia.julgar", Descricao = "Julgar denúncias" },
            new() { Nome = "usuario.gerenciar", Descricao = "Gerenciar usuários" },
            new() { Nome = "relatorio.visualizar", Descricao = "Visualizar relatórios" },
            new() { Nome = "auditoria.visualizar", Descricao = "Visualizar auditoria" },
            new() { Nome = "configuracao.gerenciar", Descricao = "Gerenciar configurações" },
            new() { Nome = "votacao.gerenciar", Descricao = "Gerenciar votação" },
            new() { Nome = "apuracao.executar", Descricao = "Executar apuração" },
        };

        await _context.Permissoes.AddRangeAsync(permissoes);

        var roles = new List<Role>
        {
            new() { Nome = "Administrador", Descricao = "Acesso total ao sistema", Ativo = true },
            new() { Nome = "ComissaoEleitoral", Descricao = "Membro da comissão eleitoral", Ativo = true },
            new() { Nome = "Fiscal", Descricao = "Fiscal de eleição", Ativo = true },
            new() { Nome = "Operador", Descricao = "Operador do sistema", Ativo = true },
            new() { Nome = "Auditor", Descricao = "Auditor do sistema", Ativo = true },
            new() { Nome = "Eleitor", Descricao = "Eleitor", Ativo = true },
            new() { Nome = "Candidato", Descricao = "Candidato", Ativo = true },
        };

        await _context.Roles.AddRangeAsync(roles);
        await _context.SaveChangesAsync();

        // Associar todas as permissões ao Administrador
        var adminRole = roles.First(r => r.Nome == "Administrador");
        var savedPermissoes = await _context.Permissoes.ToListAsync();
        foreach (var permissao in savedPermissoes)
        {
            await _context.RolePermissoes.AddAsync(new RolePermissao
            {
                RoleId = adminRole.Id,
                PermissaoId = permissao.Id
            });
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedRegionaisAsync()
    {
        if (await _context.RegionaisCAU.AnyAsync()) return;

        _logger.LogInformation("Criando regionais CAU...");

        var regionais = new List<RegionalCAU>
        {
            new() { Sigla = "CAU/BR", Nome = "CAU Brasil", UF = "DF", Ativo = true },
            new() { Sigla = "CAU/AC", Nome = "CAU Acre", UF = "AC", Ativo = true },
            new() { Sigla = "CAU/AL", Nome = "CAU Alagoas", UF = "AL", Ativo = true },
            new() { Sigla = "CAU/AP", Nome = "CAU Amapá", UF = "AP", Ativo = true },
            new() { Sigla = "CAU/AM", Nome = "CAU Amazonas", UF = "AM", Ativo = true },
            new() { Sigla = "CAU/BA", Nome = "CAU Bahia", UF = "BA", Ativo = true },
            new() { Sigla = "CAU/CE", Nome = "CAU Ceará", UF = "CE", Ativo = true },
            new() { Sigla = "CAU/DF", Nome = "CAU Distrito Federal", UF = "DF", Ativo = true },
            new() { Sigla = "CAU/ES", Nome = "CAU Espírito Santo", UF = "ES", Ativo = true },
            new() { Sigla = "CAU/GO", Nome = "CAU Goiás", UF = "GO", Ativo = true },
            new() { Sigla = "CAU/MA", Nome = "CAU Maranhão", UF = "MA", Ativo = true },
            new() { Sigla = "CAU/MT", Nome = "CAU Mato Grosso", UF = "MT", Ativo = true },
            new() { Sigla = "CAU/MS", Nome = "CAU Mato Grosso do Sul", UF = "MS", Ativo = true },
            new() { Sigla = "CAU/MG", Nome = "CAU Minas Gerais", UF = "MG", Ativo = true },
            new() { Sigla = "CAU/PA", Nome = "CAU Pará", UF = "PA", Ativo = true },
            new() { Sigla = "CAU/PB", Nome = "CAU Paraíba", UF = "PB", Ativo = true },
            new() { Sigla = "CAU/PR", Nome = "CAU Paraná", UF = "PR", Ativo = true },
            new() { Sigla = "CAU/PE", Nome = "CAU Pernambuco", UF = "PE", Ativo = true },
            new() { Sigla = "CAU/PI", Nome = "CAU Piauí", UF = "PI", Ativo = true },
            new() { Sigla = "CAU/RJ", Nome = "CAU Rio de Janeiro", UF = "RJ", Ativo = true },
            new() { Sigla = "CAU/RN", Nome = "CAU Rio Grande do Norte", UF = "RN", Ativo = true },
            new() { Sigla = "CAU/RS", Nome = "CAU Rio Grande do Sul", UF = "RS", Ativo = true },
            new() { Sigla = "CAU/RO", Nome = "CAU Rondônia", UF = "RO", Ativo = true },
            new() { Sigla = "CAU/RR", Nome = "CAU Roraima", UF = "RR", Ativo = true },
            new() { Sigla = "CAU/SC", Nome = "CAU Santa Catarina", UF = "SC", Ativo = true },
            new() { Sigla = "CAU/SP", Nome = "CAU São Paulo", UF = "SP", Ativo = true },
            new() { Sigla = "CAU/SE", Nome = "CAU Sergipe", UF = "SE", Ativo = true },
            new() { Sigla = "CAU/TO", Nome = "CAU Tocantins", UF = "TO", Ativo = true }
        };

        await _context.RegionaisCAU.AddRangeAsync(regionais);
        await _context.SaveChangesAsync();
    }

    private async Task SeedUsuariosAsync()
    {
        if (await _context.Usuarios.AnyAsync()) return;

        _logger.LogInformation("Criando usuários de teste...");

        var adminRole = await _context.Roles.FirstAsync(r => r.Nome == "Administrador");
        var comissaoRole = await _context.Roles.FirstAsync(r => r.Nome == "ComissaoEleitoral");
        var fiscalRole = await _context.Roles.FirstAsync(r => r.Nome == "Fiscal");
        var operadorRole = await _context.Roles.FirstAsync(r => r.Nome == "Operador");
        var auditorRole = await _context.Roles.FirstAsync(r => r.Nome == "Auditor");
        var eleitorRole = await _context.Roles.FirstAsync(r => r.Nome == "Eleitor");

        var usuarios = new List<(Usuario usuario, Guid roleId)>();

        // Helper para criar usuário com senha
        Usuario CreateUser(string nome, string email, string cpf, string senha, TipoUsuario tipo)
        {
            var (hash, salt) = HashPassword(senha);
            return new Usuario
            {
                Nome = nome,
                Email = email,
                Cpf = cpf,
                PasswordHash = hash,
                PasswordSalt = salt,
                Status = StatusUsuario.Ativo,
                EmailConfirmado = true,
                Tipo = tipo
            };
        }

        // Administradores
        usuarios.Add((CreateUser("Admin Sistema", "admin@cau.org.br", "11111111111", "Admin@123", TipoUsuario.Administrador), adminRole.Id));
        usuarios.Add((CreateUser("Super Admin", "superadmin@cau.org.br", "11111111112", "Admin@123", TipoUsuario.Administrador), adminRole.Id));

        // Comissão Eleitoral
        usuarios.Add((CreateUser("Dr. Carlos Alberto Oliveira", "carlos.oliveira@cau.org.br", "22222222221", "Comissao@123", TipoUsuario.ComissaoEleitoral), comissaoRole.Id));
        usuarios.Add((CreateUser("Dra. Maria Helena Santos", "maria.santos@cau.org.br", "22222222222", "Comissao@123", TipoUsuario.ComissaoEleitoral), comissaoRole.Id));
        usuarios.Add((CreateUser("Dr. Pedro Paulo Almeida", "pedro.almeida@cau.org.br", "22222222223", "Comissao@123", TipoUsuario.ComissaoEleitoral), comissaoRole.Id));
        usuarios.Add((CreateUser("Dra. Ana Beatriz Costa", "ana.costa@cau.org.br", "22222222224", "Comissao@123", TipoUsuario.ComissaoEleitoral), comissaoRole.Id));
        usuarios.Add((CreateUser("Dr. José Roberto Lima", "jose.lima@cau.org.br", "22222222225", "Comissao@123", TipoUsuario.ComissaoEleitoral), comissaoRole.Id));

        // Conselheiros
        usuarios.Add((CreateUser("Conselheiro Federal 1", "conselheiro1@cau.org.br", "23333333331", "Conselheiro@123", TipoUsuario.Conselheiro), comissaoRole.Id));
        usuarios.Add((CreateUser("Conselheiro Federal 2", "conselheiro2@cau.org.br", "23333333332", "Conselheiro@123", TipoUsuario.Conselheiro), comissaoRole.Id));
        usuarios.Add((CreateUser("Conselheiro Federal 3", "conselheiro3@cau.org.br", "23333333333", "Conselheiro@123", TipoUsuario.Conselheiro), comissaoRole.Id));

        // Fiscais
        usuarios.Add((CreateUser("Fernando Silva Fiscal", "fiscal1@cau.org.br", "33333333331", "Fiscal@123", TipoUsuario.Profissional), fiscalRole.Id));
        usuarios.Add((CreateUser("Juliana Mendes Fiscal", "fiscal2@cau.org.br", "33333333332", "Fiscal@123", TipoUsuario.Profissional), fiscalRole.Id));
        usuarios.Add((CreateUser("Ricardo Gomes Fiscal", "fiscal3@cau.org.br", "33333333333", "Fiscal@123", TipoUsuario.Profissional), fiscalRole.Id));

        // Operadores
        usuarios.Add((CreateUser("Operador Sistema 1", "operador1@cau.org.br", "44444444441", "Operador@123", TipoUsuario.Administrador), operadorRole.Id));
        usuarios.Add((CreateUser("Operador Sistema 2", "operador2@cau.org.br", "44444444442", "Operador@123", TipoUsuario.Administrador), operadorRole.Id));

        // Auditores
        usuarios.Add((CreateUser("Auditor Externo 1", "auditor1@cau.org.br", "55555555551", "Auditor@123", TipoUsuario.Administrador), auditorRole.Id));
        usuarios.Add((CreateUser("Auditor Externo 2", "auditor2@cau.org.br", "55555555552", "Auditor@123", TipoUsuario.Administrador), auditorRole.Id));

        // Candidatos
        usuarios.Add((CreateUser("Roberto Arquiteto Candidato", "candidato1@email.com", "45555555551", "Candidato@123", TipoUsuario.Candidato), eleitorRole.Id));
        usuarios.Add((CreateUser("Fernanda Urbanista Candidata", "candidato2@email.com", "45555555552", "Candidato@123", TipoUsuario.Candidato), eleitorRole.Id));
        usuarios.Add((CreateUser("Lucas Designer Candidato", "candidato3@email.com", "45555555553", "Candidato@123", TipoUsuario.Candidato), eleitorRole.Id));

        // Adicionar 50 eleitores
        for (int i = 1; i <= 50; i++)
        {
            var cpf = $"6{i:D10}";
            usuarios.Add((CreateUser($"Eleitor Teste {i:D3}", $"eleitor{i:D3}@teste.com", cpf, "Eleitor@123", TipoUsuario.Eleitor), eleitorRole.Id));
        }

        foreach (var (usuario, roleId) in usuarios)
        {
            await _context.Usuarios.AddAsync(usuario);
        }
        await _context.SaveChangesAsync();

        // Now create usuario roles
        foreach (var (usuario, roleId) in usuarios)
        {
            var savedUser = await _context.Usuarios.FirstAsync(u => u.Email == usuario.Email);
            await _context.UsuarioRoles.AddAsync(new UsuarioRole
            {
                UsuarioId = savedUser.Id,
                RoleId = roleId
            });
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedFiliaisAsync()
    {
        if (await _context.Filiais.AnyAsync()) return;

        _logger.LogInformation("Criando filiais...");

        var regionalSP = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/SP");
        var regionalRJ = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/RJ");
        var regionalMG = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/MG");

        if (regionalSP == null) return;

        var filiais = new List<Filial>
        {
            new() { Codigo = "SP-001", Nome = "Sede CAU/SP", RegionalId = regionalSP.Id, Cidade = "São Paulo", UF = "SP", Ativo = true },
            new() { Codigo = "SP-002", Nome = "Filial Campinas", RegionalId = regionalSP.Id, Cidade = "Campinas", UF = "SP", Ativo = true },
            new() { Codigo = "SP-003", Nome = "Filial Santos", RegionalId = regionalSP.Id, Cidade = "Santos", UF = "SP", Ativo = true }
        };

        if (regionalRJ != null)
        {
            filiais.Add(new Filial { Codigo = "RJ-001", Nome = "Sede CAU/RJ", RegionalId = regionalRJ.Id, Cidade = "Rio de Janeiro", UF = "RJ", Ativo = true });
            filiais.Add(new Filial { Codigo = "RJ-002", Nome = "Filial Niterói", RegionalId = regionalRJ.Id, Cidade = "Niterói", UF = "RJ", Ativo = true });
        }

        if (regionalMG != null)
        {
            filiais.Add(new Filial { Codigo = "MG-001", Nome = "Sede CAU/MG", RegionalId = regionalMG.Id, Cidade = "Belo Horizonte", UF = "MG", Ativo = true });
        }

        await _context.Filiais.AddRangeAsync(filiais);
        await _context.SaveChangesAsync();
    }

    private async Task SeedProfissionaisAsync()
    {
        if (await _context.Profissionais.AnyAsync()) return;

        _logger.LogInformation("Criando profissionais...");

        var usuariosProfissionais = await _context.Usuarios
            .Where(u => u.Tipo == TipoUsuario.Profissional || u.Tipo == TipoUsuario.Eleitor || u.Tipo == TipoUsuario.Candidato)
            .ToListAsync();

        var regionais = await _context.RegionaisCAU.ToListAsync();
        var counter = 1;

        foreach (var usuario in usuariosProfissionais)
        {
            var regional = regionais[counter % regionais.Count];
            await _context.Profissionais.AddAsync(new Profissional
            {
                UsuarioId = usuario.Id,
                RegistroCAU = $"A{counter:D6}-{regional.UF}",
                Nome = usuario.Nome,
                NomeCompleto = usuario.Nome,
                Cpf = usuario.Cpf ?? $"000{counter:D8}",
                Email = usuario.Email,
                Tipo = counter % 2 == 0 ? TipoProfissional.Arquiteto : TipoProfissional.ArquitetoUrbanista,
                Status = StatusProfissional.Ativo,
                RegionalId = regional.Id,
                EleitorApto = true,
                DataRegistro = DateTime.UtcNow.AddYears(-(counter % 10))
            });
            counter++;
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedEleicoesAsync()
    {
        if (await _context.Eleicoes.AnyAsync()) return;

        _logger.LogInformation("Criando eleições...");

        var regionalSP = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/SP");
        var regionalRJ = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/RJ");
        var regionalBR = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/BR");
        var regionalMG = await _context.RegionaisCAU.FirstOrDefaultAsync(r => r.Sigla == "CAU/MG");

        var eleicoes = new List<Eleicao>
        {
            new()
            {
                Nome = "Eleição CAU/BR 2026 - Conselheiros Federais",
                Descricao = "Eleição para renovação do Conselho Federal do CAU",
                Tipo = TipoEleicao.Ordinaria,
                Status = StatusEleicao.EmAndamento,
                FaseAtual = FaseEleicao.Votacao,
                Ano = 2026,
                Mandato = 3,
                DataInicio = new DateTime(2026, 1, 1, 8, 0, 0, DateTimeKind.Utc),
                DataFim = new DateTime(2026, 3, 15, 18, 0, 0, DateTimeKind.Utc),
                DataVotacaoInicio = new DateTime(2026, 3, 1, 8, 0, 0, DateTimeKind.Utc),
                DataVotacaoFim = new DateTime(2026, 3, 15, 18, 0, 0, DateTimeKind.Utc),
                RegionalId = regionalBR?.Id,
                ModoVotacao = ModoVotacao.Online,
                PermiteVotoOnline = true,
                PermiteVotoPresencial = false,
                QuantidadeVagas = 27,
                QuantidadeSuplentes = 27
            },
            new()
            {
                Nome = "Eleição CAU/SP 2026",
                Descricao = "Eleição para Conselheiros Estaduais de São Paulo",
                Tipo = TipoEleicao.Ordinaria,
                Status = StatusEleicao.EmAndamento,
                FaseAtual = FaseEleicao.Votacao,
                Ano = 2026,
                Mandato = 3,
                DataInicio = new DateTime(2026, 1, 1, 8, 0, 0, DateTimeKind.Utc),
                DataFim = new DateTime(2026, 3, 15, 18, 0, 0, DateTimeKind.Utc),
                DataVotacaoInicio = new DateTime(2026, 3, 1, 8, 0, 0, DateTimeKind.Utc),
                DataVotacaoFim = new DateTime(2026, 3, 15, 18, 0, 0, DateTimeKind.Utc),
                RegionalId = regionalSP?.Id,
                ModoVotacao = ModoVotacao.Hibrido,
                PermiteVotoOnline = true,
                PermiteVotoPresencial = true,
                QuantidadeVagas = 15,
                QuantidadeSuplentes = 15
            },
            new()
            {
                Nome = "Eleição CAU/RJ 2026",
                Descricao = "Eleição para Conselheiros Estaduais do Rio de Janeiro",
                Tipo = TipoEleicao.Ordinaria,
                Status = StatusEleicao.Agendada,
                FaseAtual = FaseEleicao.Preparatoria,
                Ano = 2026,
                Mandato = 3,
                DataInicio = new DateTime(2026, 4, 1, 8, 0, 0, DateTimeKind.Utc),
                DataFim = new DateTime(2026, 5, 15, 18, 0, 0, DateTimeKind.Utc),
                DataVotacaoInicio = new DateTime(2026, 5, 1, 8, 0, 0, DateTimeKind.Utc),
                DataVotacaoFim = new DateTime(2026, 5, 15, 18, 0, 0, DateTimeKind.Utc),
                RegionalId = regionalRJ?.Id,
                ModoVotacao = ModoVotacao.Online,
                PermiteVotoOnline = true,
                PermiteVotoPresencial = false,
                QuantidadeVagas = 10,
                QuantidadeSuplentes = 10
            },
            new()
            {
                Nome = "Eleição CAU/MG 2025",
                Descricao = "Eleição para Conselheiros Estaduais de Minas Gerais - Encerrada",
                Tipo = TipoEleicao.Ordinaria,
                Status = StatusEleicao.Finalizada,
                FaseAtual = FaseEleicao.Diplomacao,
                Ano = 2025,
                Mandato = 3,
                DataInicio = new DateTime(2025, 3, 1, 8, 0, 0, DateTimeKind.Utc),
                DataFim = new DateTime(2025, 6, 15, 18, 0, 0, DateTimeKind.Utc),
                DataVotacaoInicio = new DateTime(2025, 6, 1, 8, 0, 0, DateTimeKind.Utc),
                DataVotacaoFim = new DateTime(2025, 6, 15, 18, 0, 0, DateTimeKind.Utc),
                DataApuracao = new DateTime(2025, 6, 16, 8, 0, 0, DateTimeKind.Utc),
                RegionalId = regionalMG?.Id,
                ModoVotacao = ModoVotacao.Presencial,
                PermiteVotoOnline = false,
                PermiteVotoPresencial = true,
                QuantidadeVagas = 12,
                QuantidadeSuplentes = 12
            },
            new()
            {
                Nome = "Eleição Extraordinária CAU/SP 2025",
                Descricao = "Eleição extraordinária para vagas remanescentes",
                Tipo = TipoEleicao.Extraordinaria,
                Status = StatusEleicao.Encerrada,
                FaseAtual = FaseEleicao.Resultado,
                Ano = 2025,
                Mandato = 2,
                DataInicio = new DateTime(2025, 9, 1, 8, 0, 0, DateTimeKind.Utc),
                DataFim = new DateTime(2025, 10, 15, 18, 0, 0, DateTimeKind.Utc),
                DataVotacaoInicio = new DateTime(2025, 10, 1, 8, 0, 0, DateTimeKind.Utc),
                DataVotacaoFim = new DateTime(2025, 10, 15, 18, 0, 0, DateTimeKind.Utc),
                DataApuracao = new DateTime(2025, 10, 16, 8, 0, 0, DateTimeKind.Utc),
                RegionalId = regionalSP?.Id,
                ModoVotacao = ModoVotacao.Online,
                PermiteVotoOnline = true,
                PermiteVotoPresencial = false,
                QuantidadeVagas = 3,
                QuantidadeSuplentes = 3
            }
        };

        await _context.Eleicoes.AddRangeAsync(eleicoes);
        await _context.SaveChangesAsync();
    }

    private async Task SeedChapasAsync()
    {
        if (await _context.Chapas.AnyAsync()) return;

        _logger.LogInformation("Criando chapas...");

        var eleicoes = await _context.Eleicoes.Where(e => e.Status != StatusEleicao.Cancelada).ToListAsync();
        var profissionais = await _context.Profissionais.ToListAsync();

        if (!profissionais.Any()) return;

        var nomesChapas = new[] { "Renovação", "União", "Futuro", "Progresso", "Transformação", "Inovação", "Tradição", "Excelência" };
        var chapaIndex = 0;
        var profIndex = 0;

        foreach (var eleicao in eleicoes)
        {
            var qtdChapas = Random.Shared.Next(3, 6);

            for (int i = 0; i < qtdChapas; i++)
            {
                var chapa = new ChapaEleicao
                {
                    EleicaoId = eleicao.Id,
                    Nome = $"Chapa {nomesChapas[(chapaIndex + i) % nomesChapas.Length]}",
                    Numero = ((i + 1) * 10).ToString(),
                    Slogan = $"Por um CAU mais forte e unido - {nomesChapas[(chapaIndex + i) % nomesChapas.Length]}",
                    Sigla = nomesChapas[(chapaIndex + i) % nomesChapas.Length].Substring(0, 3).ToUpper(),
                    Status = i == 0 ? StatusChapa.Deferida : (i == 1 ? StatusChapa.Registrada : StatusChapa.AguardandoAnalise),
                    DataInscricao = eleicao.DataInicio.AddDays(-30),
                    DataHomologacao = i < 2 ? eleicao.DataInicio.AddDays(-15) : null,
                    CorPrimaria = i % 3 == 0 ? "#1E40AF" : (i % 3 == 1 ? "#047857" : "#B91C1C"),
                    CorSecundaria = i % 3 == 0 ? "#60A5FA" : (i % 3 == 1 ? "#34D399" : "#F87171"),
                    OrdemSorteio = i + 1
                };

                await _context.Chapas.AddAsync(chapa);
                await _context.SaveChangesAsync();

                // Adicionar membros à chapa
                var cargos = new[] { TipoMembroChapa.Presidente, TipoMembroChapa.VicePresidente, TipoMembroChapa.PrimeiroSecretario };

                for (int j = 0; j < cargos.Length && profIndex < profissionais.Count; j++)
                {
                    var prof = profissionais[profIndex % profissionais.Count];
                    await _context.MembrosChapa.AddAsync(new MembroChapa
                    {
                        ChapaId = chapa.Id,
                        ProfissionalId = prof.Id,
                        Nome = prof.Nome,
                        Cpf = prof.Cpf,
                        RegistroCAU = prof.RegistroCAU,
                        Email = prof.Email,
                        Tipo = cargos[j],
                        Status = StatusMembroChapa.Confirmado,
                        Ordem = j + 1,
                        Titular = j < 2
                    });
                    profIndex++;
                }

                // Adicionar documentos à chapa
                await _context.DocumentosChapa.AddRangeAsync(new[]
                {
                    new DocumentoChapa { ChapaId = chapa.Id, Tipo = TipoDocumentoChapa.AtaFundacao, Nome = "Ata de Fundação", ArquivoUrl = $"/uploads/ata_{chapa.Id}.pdf", Status = StatusDocumentoChapa.Aprovado, DataEnvio = eleicao.DataInicio.AddDays(-28) },
                    new DocumentoChapa { ChapaId = chapa.Id, Tipo = TipoDocumentoChapa.PlataformaEleitoral, Nome = "Plataforma Eleitoral", ArquivoUrl = $"/uploads/plataforma_{chapa.Id}.pdf", Status = StatusDocumentoChapa.Aprovado, DataEnvio = eleicao.DataInicio.AddDays(-27) },
                });

                // Adicionar plataforma eleitoral
                await _context.PlataformasEleitorais.AddAsync(new PlataformaEleitoral
                {
                    ChapaId = chapa.Id,
                    Titulo = $"Plataforma Eleitoral - {chapa.Nome}",
                    Conteudo = $"Nossa chapa {chapa.Nome} propõe: 1) Maior transparência; 2) Valorização profissional; 3) Fiscalização efetiva; 4) Apoio às regionais."
                });
            }

            chapaIndex += qtdChapas;
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedDenunciasAsync()
    {
        if (await _context.Denuncias.AnyAsync()) return;

        _logger.LogInformation("Criando denúncias...");

        var eleicoes = await _context.Eleicoes.ToListAsync();
        var chapas = await _context.Chapas.ToListAsync();
        var profissionais = await _context.Profissionais.Take(10).ToListAsync();

        if (!profissionais.Any() || !chapas.Any()) return;

        var tiposDenuncia = new[] { "Propaganda Irregular", "Abuso de Poder", "Captação Ilícita de Votos", "Uso Indevido de Recursos", "Falsidade Ideológica" };

        int protocolo = 1;
        foreach (var eleicao in eleicoes.Take(3))
        {
            var chapasEleicao = chapas.Where(c => c.EleicaoId == eleicao.Id).ToList();

            for (int i = 0; i < 5; i++)
            {
                var chapaAlvo = chapasEleicao.Count > 0 ? chapasEleicao[Random.Shared.Next(chapasEleicao.Count)] : null;
                var denunciante = profissionais[Random.Shared.Next(profissionais.Count)];

                var denuncia = new Denuncia
                {
                    EleicaoId = eleicao.Id,
                    ChapaId = chapaAlvo?.Id,
                    DenuncianteId = i % 3 == 0 ? null : denunciante.Id, // null for anonymous
                    Protocolo = $"DEN-{DateTime.UtcNow.Year}-{protocolo++:D5}",
                    Titulo = $"{tiposDenuncia[i % tiposDenuncia.Length]} - {chapaAlvo?.Nome ?? "Geral"}",
                    Descricao = $"Denúncia referente a possível irregularidade do tipo {tiposDenuncia[i % tiposDenuncia.Length].ToLower()} durante o processo eleitoral.",
                    Tipo = (TipoDenuncia)(i % 5),
                    Status = (StatusDenuncia)(i % 6),
                    DataDenuncia = eleicao.DataInicio.AddDays(-10 + i),
                    Anonima = i % 3 == 0
                };

                await _context.Denuncias.AddAsync(denuncia);
                await _context.SaveChangesAsync();

                // Adicionar provas
                await _context.ProvasDenuncia.AddRangeAsync(new[]
                {
                    new ProvaDenuncia { DenunciaId = denuncia.Id, Tipo = TipoProva.Documento, Nome = "Documento comprobatório", Descricao = "Documento comprobatório da denúncia", ArquivoUrl = $"/uploads/doc_{denuncia.Protocolo}.pdf", DataEnvio = denuncia.DataDenuncia },
                    new ProvaDenuncia { DenunciaId = denuncia.Id, Tipo = TipoProva.Foto, Nome = "Foto da irregularidade", Descricao = "Foto mostrando a irregularidade", ArquivoUrl = $"/uploads/foto_{denuncia.Protocolo}.jpg", DataEnvio = denuncia.DataDenuncia },
                });

                // Se a denúncia tem defesa
                if ((int)denuncia.Status >= 2)
                {
                    await _context.DefesasDenuncia.AddAsync(new DefesaDenuncia
                    {
                        DenunciaId = denuncia.Id,
                        Conteudo = "Apresentamos nossa defesa demonstrando que não houve qualquer irregularidade...",
                        DataApresentacao = denuncia.DataDenuncia.AddDays(5),
                        Status = StatusDefesa.Apresentada
                    });
                }

                // Vincular à chapa se existir
                if (chapaAlvo != null)
                {
                    await _context.DenunciaChapas.AddAsync(new DenunciaChapa
                    {
                        DenunciaId = denuncia.Id,
                        ChapaId = chapaAlvo.Id
                    });
                }
            }
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedComissoesAsync()
    {
        if (await _context.ComissoesJulgadoras.AnyAsync()) return;

        _logger.LogInformation("Criando comissões julgadoras...");

        var eleicoes = await _context.Eleicoes.ToListAsync();
        var profissionais = await _context.Profissionais.Take(10).ToListAsync();

        if (!profissionais.Any()) return;

        // Criar conselheiros para os profissionais
        var conselheiros = new List<Conselheiro>();
        foreach (var prof in profissionais.Take(5))
        {
            var conselheiro = new Conselheiro
            {
                ProfissionalId = prof.Id,
                NumeroConselheiro = $"C{Random.Shared.Next(10000, 99999)}",
                Cargo = "Conselheiro",
                MandatoAtivo = true,
                InicioMandato = DateTime.UtcNow.AddYears(-1),
                FimMandato = DateTime.UtcNow.AddYears(2)
            };
            conselheiros.Add(conselheiro);
            await _context.Conselheiros.AddAsync(conselheiro);
        }
        await _context.SaveChangesAsync();

        foreach (var eleicao in eleicoes)
        {
            var comissao = new ComissaoJulgadora
            {
                EleicaoId = eleicao.Id,
                Nome = $"Comissão Julgadora - {eleicao.Nome}",
                Sigla = $"CJ-{eleicao.Ano}",
                DataInicio = eleicao.DataInicio.AddDays(-60),
                DataFim = eleicao.DataFim.AddDays(30),
                Ativa = true
            };

            await _context.ComissoesJulgadoras.AddAsync(comissao);
            await _context.SaveChangesAsync();

            // Adicionar membros (usando conselheiros)
            var tipos = new[] { TipoMembroComissao.Presidente, TipoMembroComissao.Relator, TipoMembroComissao.Vogal, TipoMembroComissao.Vogal, TipoMembroComissao.Suplente };
            var savedConselheiros = await _context.Conselheiros.Take(5).ToListAsync();
            for (int i = 0; i < Math.Min(savedConselheiros.Count, 5); i++)
            {
                await _context.MembrosComissaoJulgadora.AddAsync(new MembroComissaoJulgadora
                {
                    ComissaoId = comissao.Id,
                    ConselheiroId = savedConselheiros[i].Id,
                    Tipo = tipos[i],
                    Ordem = i + 1,
                    DataInicio = eleicao.DataInicio.AddDays(-55),
                    Ativo = true
                });
            }

            // Criar sessões de julgamento
            for (int s = 1; s <= 3; s++)
            {
                var sessao = new SessaoJulgamento
                {
                    ComissaoId = comissao.Id,
                    Numero = $"{s:D3}/{eleicao.Ano}",
                    DataSessao = eleicao.DataInicio.AddDays(-30 + s * 7),
                    Tipo = TipoSessao.Ordinaria,
                    Status = s == 3 ? StatusSessao.Agendada : StatusSessao.Encerrada,
                    Local = "Sala de Reuniões CAU"
                };

                await _context.SessoesJulgamento.AddAsync(sessao);
            }
        }

        await _context.SaveChangesAsync();
    }

    private async Task SeedDocumentosAsync()
    {
        if (await _context.Editais.AnyAsync()) return;

        _logger.LogInformation("Criando documentos...");

        var eleicoes = await _context.Eleicoes.ToListAsync();

        foreach (var eleicao in eleicoes)
        {
            // Edital
            await _context.Editais.AddAsync(new Edital
            {
                EleicaoId = eleicao.Id,
                Numero = $"001/{eleicao.Ano}",
                Ano = eleicao.Ano,
                Titulo = $"Edital de Convocação - {eleicao.Nome}",
                Ementa = "Convocação para eleições do CAU",
                Conteudo = "O Conselho de Arquitetura e Urbanismo convoca os profissionais para participarem do processo eleitoral...",
                DataDocumento = eleicao.DataInicio.AddDays(-30),
                DataPublicacao = eleicao.DataInicio.AddDays(-30),
                Status = StatusDocumento.Publicado
            });

            // Resolução
            await _context.Resolucoes.AddAsync(new Resolucao
            {
                EleicaoId = eleicao.Id,
                Numero = $"RES-{eleicao.Ano}-001",
                Ano = eleicao.Ano,
                Titulo = "Resolução Normativa do Processo Eleitoral",
                Ementa = "Normas e procedimentos para o processo eleitoral",
                Conteudo = "Estabelece as normas e procedimentos para o processo eleitoral...",
                DataDocumento = eleicao.DataInicio.AddDays(-60),
                DataPublicacao = eleicao.DataInicio.AddDays(-55),
                DataVigencia = eleicao.DataFim.AddDays(90),
                Status = StatusDocumento.Publicado
            });

            // Comunicados
            for (int i = 1; i <= 5; i++)
            {
                await _context.Comunicados.AddAsync(new Comunicado
                {
                    EleicaoId = eleicao.Id,
                    Numero = $"COM-{i:D3}/{eleicao.Ano}",
                    Ano = eleicao.Ano,
                    Titulo = $"Comunicado {i} - {eleicao.Nome}",
                    Assunto = "Informativo sobre processo eleitoral",
                    Conteudo = $"Informamos aos profissionais que... (comunicado {i})",
                    DataDocumento = eleicao.DataInicio.AddDays(-20 + i * 3),
                    DataPublicacao = eleicao.DataInicio.AddDays(-20 + i * 3),
                    Status = StatusDocumento.Publicado
                });
            }
        }

        await _context.SaveChangesAsync();
    }

    private static (string Hash, string Salt) HashPassword(string password)
    {
        var saltBytes = RandomNumberGenerator.GetBytes(16);
        var salt = Convert.ToBase64String(saltBytes);
        using var pbkdf2 = new Rfc2898DeriveBytes(password, saltBytes, 100000, HashAlgorithmName.SHA256);
        var hash = Convert.ToBase64String(pbkdf2.GetBytes(32));
        return (hash, salt);
    }
}
