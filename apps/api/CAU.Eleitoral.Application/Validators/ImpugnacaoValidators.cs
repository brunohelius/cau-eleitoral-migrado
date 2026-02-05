using FluentValidation;
using CAU.Eleitoral.Application.DTOs.Impugnacoes;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Validators;

/// <summary>
/// Validator para criacao de impugnacao
/// </summary>
public class CreateImpugnacaoDtoValidator : AbstractValidator<CreateImpugnacaoDto>
{
    public CreateImpugnacaoDtoValidator()
    {
        RuleFor(x => x.EleicaoId)
            .NotEmpty().WithMessage("EleicaoId eh obrigatorio");

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de impugnacao invalido");

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

        // Validacao baseada no tipo
        RuleFor(x => x.ChapaImpugnadaId)
            .NotEmpty().WithMessage("ChapaImpugnadaId eh obrigatorio para impugnacao de chapa")
            .When(x => x.Tipo == TipoImpugnacao.ImpugnacaoChapa);

        RuleFor(x => x.MembroImpugnadoId)
            .NotEmpty().WithMessage("MembroImpugnadoId eh obrigatorio para impugnacao de membro")
            .When(x => x.Tipo == TipoImpugnacao.ImpugnacaoMembro);

        // Deve ter impugnante (chapa ou profissional)
        RuleFor(x => x)
            .Must(x => x.ChapaImpugnanteId.HasValue || x.ImpugnanteId.HasValue)
            .WithMessage("Deve informar ChapaImpugnante ou Impugnante");

        // Validar pedidos se houver
        RuleForEach(x => x.Pedidos)
            .SetValidator(new CreatePedidoImpugnacaoDtoValidator())
            .When(x => x.Pedidos != null && x.Pedidos.Any());
    }
}

/// <summary>
/// Validator para atualizacao de impugnacao
/// </summary>
public class UpdateImpugnacaoDtoValidator : AbstractValidator<UpdateImpugnacaoDto>
{
    public UpdateImpugnacaoDtoValidator()
    {
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

        RuleFor(x => x.PrazoAlegacoes)
            .GreaterThan(DateTime.UtcNow).WithMessage("PrazoAlegacoes deve ser uma data futura")
            .When(x => x.PrazoAlegacoes.HasValue);

        RuleFor(x => x.PrazoContraAlegacoes)
            .GreaterThan(DateTime.UtcNow).WithMessage("PrazoContraAlegacoes deve ser uma data futura")
            .When(x => x.PrazoContraAlegacoes.HasValue);
    }
}

/// <summary>
/// Validator para criacao de pedido de impugnacao
/// </summary>
public class CreatePedidoImpugnacaoDtoValidator : AbstractValidator<CreatePedidoImpugnacaoDto>
{
    public CreatePedidoImpugnacaoDtoValidator()
    {
        RuleFor(x => x.Descricao)
            .NotEmpty().WithMessage("Descricao eh obrigatoria")
            .MinimumLength(20).WithMessage("Descricao deve ter pelo menos 20 caracteres")
            .MaximumLength(5000).WithMessage("Descricao deve ter no maximo 5000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(10000).WithMessage("Fundamentacao deve ter no maximo 10000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));
    }
}

/// <summary>
/// Validator para criacao de alegacao
/// </summary>
public class CreateAlegacaoDtoValidator : AbstractValidator<CreateAlegacaoDto>
{
    public CreateAlegacaoDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de alegacao invalido");

        RuleFor(x => x.Descricao)
            .NotEmpty().WithMessage("Descricao eh obrigatoria")
            .MinimumLength(20).WithMessage("Descricao deve ter pelo menos 20 caracteres")
            .MaximumLength(10000).WithMessage("Descricao deve ter no maximo 10000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));

        RuleForEach(x => x.Arquivos)
            .SetValidator(new CreateArquivoAlegacaoDtoValidator())
            .When(x => x.Arquivos != null && x.Arquivos.Any());
    }
}

/// <summary>
/// Validator para criacao de arquivo de alegacao
/// </summary>
public class CreateArquivoAlegacaoDtoValidator : AbstractValidator<CreateArquivoAlegacaoDto>
{
    public CreateArquivoAlegacaoDtoValidator()
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
/// Validator para criacao de contra-alegacao
/// </summary>
public class CreateContraAlegacaoDtoValidator : AbstractValidator<CreateContraAlegacaoDto>
{
    public CreateContraAlegacaoDtoValidator()
    {
        RuleFor(x => x.Descricao)
            .NotEmpty().WithMessage("Descricao eh obrigatoria")
            .MinimumLength(20).WithMessage("Descricao deve ter pelo menos 20 caracteres")
            .MaximumLength(10000).WithMessage("Descricao deve ter no maximo 10000 caracteres");

        RuleFor(x => x.Fundamentacao)
            .MaximumLength(20000).WithMessage("Fundamentacao deve ter no maximo 20000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Fundamentacao));
    }
}

/// <summary>
/// Validator para criacao de defesa de impugnacao
/// </summary>
public class CreateDefesaImpugnacaoDtoValidator : AbstractValidator<CreateDefesaImpugnacaoDto>
{
    public CreateDefesaImpugnacaoDtoValidator()
    {
        RuleFor(x => x.Conteudo)
            .NotEmpty().WithMessage("Conteudo eh obrigatorio")
            .MinimumLength(50).WithMessage("Conteudo deve ter pelo menos 50 caracteres")
            .MaximumLength(50000).WithMessage("Conteudo deve ter no maximo 50000 caracteres");

        RuleForEach(x => x.Arquivos)
            .SetValidator(new CreateArquivoDefesaImpugnacaoDtoValidator())
            .When(x => x.Arquivos != null && x.Arquivos.Any());
    }
}

/// <summary>
/// Validator para criacao de arquivo de defesa de impugnacao
/// </summary>
public class CreateArquivoDefesaImpugnacaoDtoValidator : AbstractValidator<CreateArquivoDefesaImpugnacaoDto>
{
    public CreateArquivoDefesaImpugnacaoDtoValidator()
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
/// Validator para julgamento de impugnacao
/// </summary>
public class JulgarImpugnacaoDtoValidator : AbstractValidator<JulgarImpugnacaoDto>
{
    public JulgarImpugnacaoDtoValidator()
    {
        RuleFor(x => x.Resultado)
            .IsInEnum().WithMessage("Resultado invalido")
            .Must(x => x == StatusImpugnacao.Procedente ||
                       x == StatusImpugnacao.Improcedente ||
                       x == StatusImpugnacao.ParcialmenteProcedente)
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
/// Validator para arquivamento de impugnacao
/// </summary>
public class ArquivarImpugnacaoDtoValidator : AbstractValidator<ArquivarImpugnacaoDto>
{
    public ArquivarImpugnacaoDtoValidator()
    {
        RuleFor(x => x.Motivo)
            .NotEmpty().WithMessage("Motivo eh obrigatorio")
            .MinimumLength(10).WithMessage("Motivo deve ter pelo menos 10 caracteres")
            .MaximumLength(2000).WithMessage("Motivo deve ter no maximo 2000 caracteres");
    }
}

/// <summary>
/// Validator para abertura de prazo de alegacoes
/// </summary>
public class AbrirPrazoAlegacoesDtoValidator : AbstractValidator<AbrirPrazoAlegacoesDto>
{
    public AbrirPrazoAlegacoesDtoValidator()
    {
        RuleFor(x => x.PrazoEmDias)
            .InclusiveBetween(1, 30).WithMessage("PrazoEmDias deve ser entre 1 e 30");

        RuleFor(x => x.Observacoes)
            .MaximumLength(1000).WithMessage("Observacoes deve ter no maximo 1000 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Observacoes));
    }
}

/// <summary>
/// Validator para criacao de recurso de impugnacao
/// </summary>
public class CreateRecursoImpugnacaoDtoValidator : AbstractValidator<CreateRecursoImpugnacaoDto>
{
    public CreateRecursoImpugnacaoDtoValidator()
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
/// Validator para filtro de impugnacoes
/// </summary>
public class FiltroImpugnacaoDtoValidator : AbstractValidator<FiltroImpugnacaoDto>
{
    public FiltroImpugnacaoDtoValidator()
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
