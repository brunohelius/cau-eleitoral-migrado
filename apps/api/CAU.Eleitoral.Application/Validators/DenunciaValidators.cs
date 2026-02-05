using FluentValidation;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Validators;

/// <summary>
/// Validator para criacao de denuncia
/// </summary>
public class CreateDenunciaDtoValidator : AbstractValidator<CreateDenunciaDto>
{
    public CreateDenunciaDtoValidator()
    {
        RuleFor(x => x.EleicaoId)
            .NotEmpty().WithMessage("EleicaoId eh obrigatorio");

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de denuncia invalido");

        RuleFor(x => x.Titulo)
            .NotEmpty().WithMessage("Titulo eh obrigatorio")
            .MinimumLength(5).WithMessage("Titulo deve ter pelo menos 5 caracteres")
            .MaximumLength(200).WithMessage("Titulo deve ter no maximo 200 caracteres");

        RuleFor(x => x.Descricao)
            .NotEmpty().WithMessage("Descricao eh obrigatoria")
            .MinimumLength(20).WithMessage("Descricao deve ter pelo menos 20 caracteres")
            .MaximumLength(10000).WithMessage("Descricao deve ter no maximo 10000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));

        // Se nao for anonima, deve ter denunciante
        RuleFor(x => x.DenuncianteId)
            .NotEmpty().WithMessage("DenuncianteId eh obrigatorio para denuncias nao anonimas")
            .When(x => !x.Anonima);

        // Deve ter pelo menos uma chapa ou membro denunciado
        RuleFor(x => x)
            .Must(x => x.ChapaId.HasValue || x.MembroId.HasValue)
            .WithMessage("Deve informar a Chapa ou Membro denunciado");

        // Validar provas se houver
        RuleForEach(x => x.Provas)
            .SetValidator(new CreateProvaDenunciaDtoValidator())
            .When(x => x.Provas != null && x.Provas.Any());
    }
}

/// <summary>
/// Validator para atualizacao de denuncia
/// </summary>
public class UpdateDenunciaDtoValidator : AbstractValidator<UpdateDenunciaDto>
{
    public UpdateDenunciaDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de denuncia invalido")
            .When(x => x.Tipo.HasValue);

        RuleFor(x => x.Titulo)
            .MinimumLength(5).WithMessage("Titulo deve ter pelo menos 5 caracteres")
            .MaximumLength(200).WithMessage("Titulo deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Titulo));

        RuleFor(x => x.Descricao)
            .MinimumLength(20).WithMessage("Descricao deve ter pelo menos 20 caracteres")
            .MaximumLength(10000).WithMessage("Descricao deve ter no maximo 10000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Descricao));

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));

        RuleFor(x => x.PrazoDefesa)
            .GreaterThan(DateTime.UtcNow).WithMessage("PrazoDefesa deve ser uma data futura")
            .When(x => x.PrazoDefesa.HasValue);

        RuleFor(x => x.PrazoRecurso)
            .GreaterThan(DateTime.UtcNow).WithMessage("PrazoRecurso deve ser uma data futura")
            .When(x => x.PrazoRecurso.HasValue);
    }
}

/// <summary>
/// Validator para criacao de prova
/// </summary>
public class CreateProvaDenunciaDtoValidator : AbstractValidator<CreateProvaDenunciaDto>
{
    public CreateProvaDenunciaDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de prova invalido");

        RuleFor(x => x.Descricao)
            .NotEmpty().WithMessage("Descricao eh obrigatoria")
            .MinimumLength(10).WithMessage("Descricao deve ter pelo menos 10 caracteres")
            .MaximumLength(2000).WithMessage("Descricao deve ter no maximo 2000 caracteres");

        RuleFor(x => x.ArquivoUrl)
            .Must(BeAValidUrl).WithMessage("ArquivoUrl deve ser uma URL valida")
            .When(x => !string.IsNullOrEmpty(x.ArquivoUrl));

        RuleFor(x => x.ArquivoNome)
            .MaximumLength(200).WithMessage("ArquivoNome deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.ArquivoNome));

        RuleFor(x => x.ArquivoTamanho)
            .GreaterThan(0).WithMessage("ArquivoTamanho deve ser maior que zero")
            .LessThanOrEqualTo(100 * 1024 * 1024).WithMessage("Arquivo deve ter no maximo 100MB")
            .When(x => x.ArquivoTamanho.HasValue);
    }

    private bool BeAValidUrl(string? url)
    {
        if (string.IsNullOrEmpty(url)) return true;
        return Uri.TryCreate(url, UriKind.Absolute, out var result) &&
               (result.Scheme == Uri.UriSchemeHttp || result.Scheme == Uri.UriSchemeHttps);
    }
}

/// <summary>
/// Validator para criacao de defesa
/// </summary>
public class CreateDefesaDtoValidator : AbstractValidator<CreateDefesaDto>
{
    public CreateDefesaDtoValidator()
    {
        RuleFor(x => x.Conteudo)
            .NotEmpty().WithMessage("Conteudo eh obrigatorio")
            .MinimumLength(50).WithMessage("Conteudo deve ter pelo menos 50 caracteres")
            .MaximumLength(50000).WithMessage("Conteudo deve ter no maximo 50000 caracteres");

        RuleForEach(x => x.Arquivos)
            .SetValidator(new CreateArquivoDefesaDtoValidator())
            .When(x => x.Arquivos != null && x.Arquivos.Any());
    }
}

/// <summary>
/// Validator para criacao de arquivo de defesa
/// </summary>
public class CreateArquivoDefesaDtoValidator : AbstractValidator<CreateArquivoDefesaDto>
{
    public CreateArquivoDefesaDtoValidator()
    {
        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres");

        RuleFor(x => x.Descricao)
            .MaximumLength(500).WithMessage("Descricao deve ter no maximo 500 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Descricao));

        RuleFor(x => x.Url)
            .Must(BeAValidUrl).WithMessage("Url deve ser uma URL valida")
            .When(x => !string.IsNullOrEmpty(x.Url));
    }

    private bool BeAValidUrl(string? url)
    {
        if (string.IsNullOrEmpty(url)) return true;
        return Uri.TryCreate(url, UriKind.Absolute, out var result) &&
               (result.Scheme == Uri.UriSchemeHttp || result.Scheme == Uri.UriSchemeHttps);
    }
}

/// <summary>
/// Validator para registro de admissibilidade
/// </summary>
public class RegistrarAdmissibilidadeDtoValidator : AbstractValidator<RegistrarAdmissibilidadeDto>
{
    public RegistrarAdmissibilidadeDtoValidator()
    {
        RuleFor(x => x.Parecer)
            .NotEmpty().WithMessage("Parecer eh obrigatorio")
            .MinimumLength(20).WithMessage("Parecer deve ter pelo menos 20 caracteres")
            .MaximumLength(5000).WithMessage("Parecer deve ter no maximo 5000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(10000).WithMessage("Fundamentacao deve ter no maximo 10000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));
    }
}

/// <summary>
/// Validator para julgamento de denuncia
/// </summary>
public class JulgarDenunciaDtoValidator : AbstractValidator<JulgarDenunciaDto>
{
    public JulgarDenunciaDtoValidator()
    {
        RuleFor(x => x.Resultado)
            .IsInEnum().WithMessage("Resultado invalido")
            .Must(x => x == StatusDenuncia.Procedente ||
                       x == StatusDenuncia.Improcedente ||
                       x == StatusDenuncia.ParcialmenteProcedente)
            .WithMessage("Resultado deve ser Procedente, Improcedente ou ParcialmenteProcedente");

        RuleFor(x => x.Decisao)
            .NotEmpty().WithMessage("Decisao eh obrigatoria")
            .MinimumLength(20).WithMessage("Decisao deve ter pelo menos 20 caracteres")
            .MaximumLength(5000).WithMessage("Decisao deve ter no maximo 5000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));

        RuleFor(x => x.TipoDecisao)
            .IsInEnum().WithMessage("TipoDecisao invalido")
            .When(x => x.TipoDecisao.HasValue);
    }
}

/// <summary>
/// Validator para arquivamento de denuncia
/// </summary>
public class ArquivarDenunciaDtoValidator : AbstractValidator<ArquivarDenunciaDto>
{
    public ArquivarDenunciaDtoValidator()
    {
        RuleFor(x => x.Motivo)
            .NotEmpty().WithMessage("Motivo eh obrigatorio")
            .MinimumLength(10).WithMessage("Motivo deve ter pelo menos 10 caracteres")
            .MaximumLength(2000).WithMessage("Motivo deve ter no maximo 2000 caracteres");
    }
}

/// <summary>
/// Validator para solicitacao de defesa
/// </summary>
public class SolicitarDefesaDtoValidator : AbstractValidator<SolicitarDefesaDto>
{
    public SolicitarDefesaDtoValidator()
    {
        RuleFor(x => x.PrazoEmDias)
            .InclusiveBetween(1, 30).WithMessage("PrazoEmDias deve ser entre 1 e 30");

        RuleFor(x => x.Observacoes)
            .MaximumLength(1000).WithMessage("Observacoes deve ter no maximo 1000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Observacoes));
    }
}

/// <summary>
/// Validator para criacao de recurso
/// </summary>
public class CreateRecursoDenunciaDtoValidator : AbstractValidator<CreateRecursoDenunciaDto>
{
    public CreateRecursoDenunciaDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de recurso invalido");

        RuleFor(x => x.Fundamentacao)
            .NotEmpty().WithMessage("Fundamentacao eh obrigatoria")
            .MinimumLength(50).WithMessage("Fundamentacao deve ter pelo menos 50 caracteres")
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres");
    }
}

/// <summary>
/// Validator para filtro de denuncias
/// </summary>
public class FiltroDenunciaDtoValidator : AbstractValidator<FiltroDenunciaDto>
{
    public FiltroDenunciaDtoValidator()
    {
        RuleFor(x => x.Status)
            .IsInEnum().WithMessage("Status invalido")
            .When(x => x.Status.HasValue);

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo invalido")
            .When(x => x.Tipo.HasValue);

        RuleFor(x => x.Termo)
            .MaximumLength(200).WithMessage("Termo deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Termo));

        RuleFor(x => x.Pagina)
            .GreaterThan(0).WithMessage("Pagina deve ser maior que 0");

        RuleFor(x => x.TamanhoPagina)
            .InclusiveBetween(1, 100).WithMessage("TamanhoPagina deve ser entre 1 e 100");

        RuleFor(x => x.DataFim)
            .GreaterThanOrEqualTo(x => x.DataInicio)
            .WithMessage("DataFim deve ser maior ou igual a DataInicio")
            .When(x => x.DataInicio.HasValue && x.DataFim.HasValue);
    }
}
