using FluentValidation;
using CAU.Eleitoral.Application.DTOs.Chapas;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Validators;

/// <summary>
/// Validator para criacao de chapa
/// </summary>
public class CreateChapaDtoValidator : AbstractValidator<CreateChapaDto>
{
    public CreateChapaDtoValidator()
    {
        RuleFor(x => x.EleicaoId)
            .NotEmpty().WithMessage("EleicaoId eh obrigatorio");

        RuleFor(x => x.Numero)
            .NotEmpty().WithMessage("Numero eh obrigatorio")
            .MaximumLength(10).WithMessage("Numero deve ter no maximo 10 caracteres")
            .Matches(@"^\d+$").WithMessage("Numero deve conter apenas digitos");

        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres");

        RuleFor(x => x.Sigla)
            .MaximumLength(20).WithMessage("Sigla deve ter no maximo 20 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Sigla));

        RuleFor(x => x.Slogan)
            .MaximumLength(500).WithMessage("Slogan deve ter no maximo 500 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Slogan));

        RuleFor(x => x.CorPrimaria)
            .Matches(@"^#[0-9A-Fa-f]{6}$").WithMessage("CorPrimaria deve ser um codigo hexadecimal valido (ex: #FF0000)")
            .When(x => !string.IsNullOrEmpty(x.CorPrimaria));

        RuleFor(x => x.CorSecundaria)
            .Matches(@"^#[0-9A-Fa-f]{6}$").WithMessage("CorSecundaria deve ser um codigo hexadecimal valido (ex: #FF0000)")
            .When(x => !string.IsNullOrEmpty(x.CorSecundaria));
    }
}

/// <summary>
/// Validator para atualizacao de chapa
/// </summary>
public class UpdateChapaDtoValidator : AbstractValidator<UpdateChapaDto>
{
    public UpdateChapaDtoValidator()
    {
        RuleFor(x => x.Numero)
            .MaximumLength(10).WithMessage("Numero deve ter no maximo 10 caracteres")
            .Matches(@"^\d+$").WithMessage("Numero deve conter apenas digitos")
            .When(x => !string.IsNullOrEmpty(x.Numero));

        RuleFor(x => x.Nome)
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Nome));

        RuleFor(x => x.Sigla)
            .MaximumLength(20).WithMessage("Sigla deve ter no maximo 20 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Sigla));

        RuleFor(x => x.Slogan)
            .MaximumLength(500).WithMessage("Slogan deve ter no maximo 500 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Slogan));

        RuleFor(x => x.CorPrimaria)
            .Matches(@"^#[0-9A-Fa-f]{6}$").WithMessage("CorPrimaria deve ser um codigo hexadecimal valido")
            .When(x => !string.IsNullOrEmpty(x.CorPrimaria));

        RuleFor(x => x.CorSecundaria)
            .Matches(@"^#[0-9A-Fa-f]{6}$").WithMessage("CorSecundaria deve ser um codigo hexadecimal valido")
            .When(x => !string.IsNullOrEmpty(x.CorSecundaria));

        RuleFor(x => x.LogoUrl)
            .Must(BeAValidUrl).WithMessage("LogoUrl deve ser uma URL valida")
            .When(x => !string.IsNullOrEmpty(x.LogoUrl));

        RuleFor(x => x.FotoUrl)
            .Must(BeAValidUrl).WithMessage("FotoUrl deve ser uma URL valida")
            .When(x => !string.IsNullOrEmpty(x.FotoUrl));
    }

    private bool BeAValidUrl(string? url)
    {
        if (string.IsNullOrEmpty(url)) return true;
        return Uri.TryCreate(url, UriKind.Absolute, out var result) &&
               (result.Scheme == Uri.UriSchemeHttp || result.Scheme == Uri.UriSchemeHttps);
    }
}

/// <summary>
/// Validator para criacao de membro de chapa
/// </summary>
public class CreateMembroChapaDtoValidator : AbstractValidator<CreateMembroChapaDto>
{
    public CreateMembroChapaDtoValidator()
    {
        RuleFor(x => x.TipoMembro)
            .IsInEnum().WithMessage("TipoMembro invalido");

        RuleFor(x => x.Nome)
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Nome));

        RuleFor(x => x.Cpf)
            .Matches(@"^\d{11}$").WithMessage("CPF deve conter 11 digitos numericos")
            .Must(BeAValidCpf).WithMessage("CPF invalido")
            .When(x => !string.IsNullOrEmpty(x.Cpf));

        RuleFor(x => x.RegistroCAU)
            .MaximumLength(20).WithMessage("RegistroCAU deve ter no maximo 20 caracteres")
            .When(x => !string.IsNullOrEmpty(x.RegistroCAU));

        RuleFor(x => x.Email)
            .EmailAddress().WithMessage("Email invalido")
            .When(x => !string.IsNullOrEmpty(x.Email));

        RuleFor(x => x.Cargo)
            .MaximumLength(100).WithMessage("Cargo deve ter no maximo 100 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Cargo));

        RuleFor(x => x.CurriculoResumo)
            .MaximumLength(2000).WithMessage("CurriculoResumo deve ter no maximo 2000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.CurriculoResumo));

        // Validacao: ou tem ProfissionalId ou tem Nome+CPF
        RuleFor(x => x)
            .Must(x => x.ProfissionalId.HasValue || (!string.IsNullOrEmpty(x.Nome) && !string.IsNullOrEmpty(x.Cpf)))
            .WithMessage("Deve informar ProfissionalId ou Nome e CPF");
    }

    private bool BeAValidCpf(string? cpf)
    {
        if (string.IsNullOrEmpty(cpf)) return true;
        if (cpf.Length != 11 || !cpf.All(char.IsDigit)) return false;

        // Verifica se todos os digitos sao iguais
        if (cpf.Distinct().Count() == 1) return false;

        // Calculo do primeiro digito verificador
        int[] multiplicador1 = { 10, 9, 8, 7, 6, 5, 4, 3, 2 };
        int soma = 0;
        for (int i = 0; i < 9; i++)
            soma += int.Parse(cpf[i].ToString()) * multiplicador1[i];
        int resto = soma % 11;
        int digito1 = resto < 2 ? 0 : 11 - resto;

        if (int.Parse(cpf[9].ToString()) != digito1) return false;

        // Calculo do segundo digito verificador
        int[] multiplicador2 = { 11, 10, 9, 8, 7, 6, 5, 4, 3, 2 };
        soma = 0;
        for (int i = 0; i < 10; i++)
            soma += int.Parse(cpf[i].ToString()) * multiplicador2[i];
        resto = soma % 11;
        int digito2 = resto < 2 ? 0 : 11 - resto;

        return int.Parse(cpf[10].ToString()) == digito2;
    }
}

/// <summary>
/// Validator para analise de chapa
/// </summary>
public class AnaliseChapaValidator : AbstractValidator<AnaliseChapaDto>
{
    public AnaliseChapaValidator()
    {
        RuleFor(x => x.Parecer)
            .NotEmpty().WithMessage("Parecer eh obrigatorio")
            .MinimumLength(10).WithMessage("Parecer deve ter pelo menos 10 caracteres")
            .MaximumLength(5000).WithMessage("Parecer deve ter no maximo 5000 caracteres");

        RuleFor(x => x.MotivoIndeferimento)
            .NotEmpty().WithMessage("Motivo do indeferimento eh obrigatorio quando a chapa eh indeferida")
            .MaximumLength(2000).WithMessage("Motivo deve ter no maximo 2000 caracteres")
            .When(x => !x.Deferido);
    }
}

/// <summary>
/// Validator para criacao de documento de chapa
/// </summary>
public class CreateDocumentoChapaDtoValidator : AbstractValidator<CreateDocumentoChapaDto>
{
    public CreateDocumentoChapaDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de documento invalido");

        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres");

        RuleFor(x => x.Descricao)
            .MaximumLength(500).WithMessage("Descricao deve ter no maximo 500 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Descricao));

        RuleFor(x => x.ArquivoUrl)
            .NotEmpty().WithMessage("ArquivoUrl eh obrigatorio")
            .Must(BeAValidUrl).WithMessage("ArquivoUrl deve ser uma URL valida");

        RuleFor(x => x.ArquivoNome)
            .NotEmpty().WithMessage("ArquivoNome eh obrigatorio")
            .MaximumLength(200).WithMessage("ArquivoNome deve ter no maximo 200 caracteres");

        RuleFor(x => x.ArquivoTamanho)
            .GreaterThan(0).WithMessage("ArquivoTamanho deve ser maior que zero")
            .LessThanOrEqualTo(50 * 1024 * 1024).WithMessage("Arquivo deve ter no maximo 50MB")
            .When(x => x.ArquivoTamanho.HasValue);
    }

    private bool BeAValidUrl(string? url)
    {
        if (string.IsNullOrEmpty(url)) return false;
        return Uri.TryCreate(url, UriKind.Absolute, out var result) &&
               (result.Scheme == Uri.UriSchemeHttp || result.Scheme == Uri.UriSchemeHttps);
    }
}

/// <summary>
/// Validator para criacao de plataforma eleitoral
/// </summary>
public class CreatePlataformaEleitoralDtoValidator : AbstractValidator<CreatePlataformaEleitoralDto>
{
    public CreatePlataformaEleitoralDtoValidator()
    {
        RuleFor(x => x.Titulo)
            .NotEmpty().WithMessage("Titulo eh obrigatorio")
            .MinimumLength(5).WithMessage("Titulo deve ter pelo menos 5 caracteres")
            .MaximumLength(200).WithMessage("Titulo deve ter no maximo 200 caracteres");

        RuleFor(x => x.Resumo)
            .NotEmpty().WithMessage("Resumo eh obrigatorio")
            .MinimumLength(50).WithMessage("Resumo deve ter pelo menos 50 caracteres")
            .MaximumLength(2000).WithMessage("Resumo deve ter no maximo 2000 caracteres");

        RuleFor(x => x.ConteudoCompleto)
            .MaximumLength(50000).WithMessage("ConteudoCompleto deve ter no maximo 50000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.ConteudoCompleto));

        RuleFor(x => x.ArquivoUrl)
            .Must(BeAValidUrl).WithMessage("ArquivoUrl deve ser uma URL valida")
            .When(x => !string.IsNullOrEmpty(x.ArquivoUrl));
    }

    private bool BeAValidUrl(string? url)
    {
        if (string.IsNullOrEmpty(url)) return true;
        return Uri.TryCreate(url, UriKind.Absolute, out var result) &&
               (result.Scheme == Uri.UriSchemeHttp || result.Scheme == Uri.UriSchemeHttps);
    }
}
