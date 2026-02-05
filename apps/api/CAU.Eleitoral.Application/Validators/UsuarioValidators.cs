using FluentValidation;
using CAU.Eleitoral.Application.DTOs.Usuarios;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Validators;

/// <summary>
/// Validator para criacao de usuario
/// </summary>
public class CreateUsuarioDtoValidator : AbstractValidator<CreateUsuarioDto>
{
    public CreateUsuarioDtoValidator()
    {
        RuleFor(x => x.Email)
            .NotEmpty().WithMessage("Email eh obrigatorio")
            .EmailAddress().WithMessage("Email invalido")
            .MaximumLength(200).WithMessage("Email deve ter no maximo 200 caracteres");

        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(2).WithMessage("Nome deve ter pelo menos 2 caracteres")
            .MaximumLength(100).WithMessage("Nome deve ter no maximo 100 caracteres");

        RuleFor(x => x.NomeCompleto)
            .MaximumLength(200).WithMessage("NomeCompleto deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.NomeCompleto));

        RuleFor(x => x.Cpf)
            .Matches(@"^\d{11}$").WithMessage("CPF deve conter 11 digitos numericos")
            .Must(BeAValidCpf).WithMessage("CPF invalido")
            .When(x => !string.IsNullOrEmpty(x.Cpf));

        RuleFor(x => x.Telefone)
            .MaximumLength(20).WithMessage("Telefone deve ter no maximo 20 caracteres")
            .Matches(@"^[\d\s\-\(\)\+]+$").WithMessage("Telefone contem caracteres invalidos")
            .When(x => !string.IsNullOrEmpty(x.Telefone));

        RuleFor(x => x.Password)
            .NotEmpty().WithMessage("Senha eh obrigatoria")
            .MinimumLength(8).WithMessage("Senha deve ter pelo menos 8 caracteres")
            .MaximumLength(100).WithMessage("Senha deve ter no maximo 100 caracteres")
            .Matches(@"[A-Z]").WithMessage("Senha deve conter pelo menos uma letra maiuscula")
            .Matches(@"[a-z]").WithMessage("Senha deve conter pelo menos uma letra minuscula")
            .Matches(@"[0-9]").WithMessage("Senha deve conter pelo menos um numero")
            .Matches(@"[@$!%*?&]").WithMessage("Senha deve conter pelo menos um caractere especial (@$!%*?&)");

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de usuario invalido");

        RuleForEach(x => x.Roles)
            .NotEmpty().WithMessage("Role nao pode ser vazia")
            .MaximumLength(50).WithMessage("Role deve ter no maximo 50 caracteres")
            .When(x => x.Roles != null);
    }

    private bool BeAValidCpf(string? cpf)
    {
        if (string.IsNullOrEmpty(cpf)) return true;
        if (cpf.Length != 11 || !cpf.All(char.IsDigit)) return false;

        if (cpf.Distinct().Count() == 1) return false;

        int[] multiplicador1 = { 10, 9, 8, 7, 6, 5, 4, 3, 2 };
        int soma = 0;
        for (int i = 0; i < 9; i++)
            soma += int.Parse(cpf[i].ToString()) * multiplicador1[i];
        int resto = soma % 11;
        int digito1 = resto < 2 ? 0 : 11 - resto;

        if (int.Parse(cpf[9].ToString()) != digito1) return false;

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
/// Validator para atualizacao de usuario
/// </summary>
public class UpdateUsuarioDtoValidator : AbstractValidator<UpdateUsuarioDto>
{
    public UpdateUsuarioDtoValidator()
    {
        RuleFor(x => x.Nome)
            .MinimumLength(2).WithMessage("Nome deve ter pelo menos 2 caracteres")
            .MaximumLength(100).WithMessage("Nome deve ter no maximo 100 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Nome));

        RuleFor(x => x.NomeCompleto)
            .MaximumLength(200).WithMessage("NomeCompleto deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.NomeCompleto));

        RuleFor(x => x.Telefone)
            .MaximumLength(20).WithMessage("Telefone deve ter no maximo 20 caracteres")
            .Matches(@"^[\d\s\-\(\)\+]+$").WithMessage("Telefone contem caracteres invalidos")
            .When(x => !string.IsNullOrEmpty(x.Telefone));

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de usuario invalido")
            .When(x => x.Tipo.HasValue);

        RuleForEach(x => x.Roles)
            .NotEmpty().WithMessage("Role nao pode ser vazia")
            .MaximumLength(50).WithMessage("Role deve ter no maximo 50 caracteres")
            .When(x => x.Roles != null);
    }
}

/// <summary>
/// Validator para alteracao de senha
/// </summary>
public class ChangePasswordDtoValidator : AbstractValidator<ChangePasswordDto>
{
    public ChangePasswordDtoValidator()
    {
        RuleFor(x => x.CurrentPassword)
            .NotEmpty().WithMessage("Senha atual eh obrigatoria");

        RuleFor(x => x.NewPassword)
            .NotEmpty().WithMessage("Nova senha eh obrigatoria")
            .MinimumLength(8).WithMessage("Senha deve ter pelo menos 8 caracteres")
            .MaximumLength(100).WithMessage("Senha deve ter no maximo 100 caracteres")
            .Matches(@"[A-Z]").WithMessage("Senha deve conter pelo menos uma letra maiuscula")
            .Matches(@"[a-z]").WithMessage("Senha deve conter pelo menos uma letra minuscula")
            .Matches(@"[0-9]").WithMessage("Senha deve conter pelo menos um numero")
            .Matches(@"[@$!%*?&]").WithMessage("Senha deve conter pelo menos um caractere especial (@$!%*?&)")
            .NotEqual(x => x.CurrentPassword).WithMessage("Nova senha deve ser diferente da senha atual");

        RuleFor(x => x.ConfirmPassword)
            .NotEmpty().WithMessage("Confirmacao de senha eh obrigatoria")
            .Equal(x => x.NewPassword).WithMessage("As senhas nao conferem");
    }
}

/// <summary>
/// Validator para reset de senha pelo admin
/// </summary>
public class AdminResetPasswordDtoValidator : AbstractValidator<AdminResetPasswordDto>
{
    public AdminResetPasswordDtoValidator()
    {
        RuleFor(x => x.NewPassword)
            .NotEmpty().WithMessage("Nova senha eh obrigatoria")
            .MinimumLength(8).WithMessage("Senha deve ter pelo menos 8 caracteres")
            .MaximumLength(100).WithMessage("Senha deve ter no maximo 100 caracteres");
    }
}

/// <summary>
/// Validator para solicitacao de reset de senha
/// </summary>
public class RequestPasswordResetDtoValidator : AbstractValidator<RequestPasswordResetDto>
{
    public RequestPasswordResetDtoValidator()
    {
        RuleFor(x => x.Email)
            .NotEmpty().WithMessage("Email eh obrigatorio")
            .EmailAddress().WithMessage("Email invalido");
    }
}

/// <summary>
/// Validator para reset de senha com token
/// </summary>
public class ResetPasswordDtoValidator : AbstractValidator<ResetPasswordDto>
{
    public ResetPasswordDtoValidator()
    {
        RuleFor(x => x.Token)
            .NotEmpty().WithMessage("Token eh obrigatorio");

        RuleFor(x => x.NewPassword)
            .NotEmpty().WithMessage("Nova senha eh obrigatoria")
            .MinimumLength(8).WithMessage("Senha deve ter pelo menos 8 caracteres")
            .MaximumLength(100).WithMessage("Senha deve ter no maximo 100 caracteres")
            .Matches(@"[A-Z]").WithMessage("Senha deve conter pelo menos uma letra maiuscula")
            .Matches(@"[a-z]").WithMessage("Senha deve conter pelo menos uma letra minuscula")
            .Matches(@"[0-9]").WithMessage("Senha deve conter pelo menos um numero")
            .Matches(@"[@$!%*?&]").WithMessage("Senha deve conter pelo menos um caractere especial (@$!%*?&)");

        RuleFor(x => x.ConfirmPassword)
            .NotEmpty().WithMessage("Confirmacao de senha eh obrigatoria")
            .Equal(x => x.NewPassword).WithMessage("As senhas nao conferem");
    }
}

/// <summary>
/// Validator para alteracao de status
/// </summary>
public class ChangeStatusDtoValidator : AbstractValidator<ChangeStatusDto>
{
    public ChangeStatusDtoValidator()
    {
        RuleFor(x => x.Status)
            .IsInEnum().WithMessage("Status invalido");

        RuleFor(x => x.Motivo)
            .MaximumLength(500).WithMessage("Motivo deve ter no maximo 500 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Motivo));
    }
}

/// <summary>
/// Validator para bloqueio de usuario
/// </summary>
public class BloquearUsuarioDtoValidator : AbstractValidator<BloquearUsuarioDto>
{
    public BloquearUsuarioDtoValidator()
    {
        RuleFor(x => x.Motivo)
            .NotEmpty().WithMessage("Motivo eh obrigatorio")
            .MinimumLength(10).WithMessage("Motivo deve ter pelo menos 10 caracteres")
            .MaximumLength(500).WithMessage("Motivo deve ter no maximo 500 caracteres");

        RuleFor(x => x.BloqueadoAte)
            .GreaterThan(DateTime.UtcNow).WithMessage("BloqueadoAte deve ser uma data futura")
            .When(x => x.BloqueadoAte.HasValue);
    }
}

/// <summary>
/// Validator para atribuicao de roles
/// </summary>
public class AssignRolesDtoValidator : AbstractValidator<AssignRolesDto>
{
    public AssignRolesDtoValidator()
    {
        RuleFor(x => x.Roles)
            .NotEmpty().WithMessage("Pelo menos uma role deve ser informada");

        RuleForEach(x => x.Roles)
            .NotEmpty().WithMessage("Role nao pode ser vazia")
            .MaximumLength(50).WithMessage("Role deve ter no maximo 50 caracteres");
    }
}

/// <summary>
/// Validator para habilitacao de 2FA
/// </summary>
public class Enable2FADtoValidator : AbstractValidator<Enable2FADto>
{
    public Enable2FADtoValidator()
    {
        RuleFor(x => x.Code)
            .NotEmpty().WithMessage("Codigo eh obrigatorio")
            .Length(6).WithMessage("Codigo deve ter 6 digitos")
            .Matches(@"^\d{6}$").WithMessage("Codigo deve conter apenas numeros");
    }
}

/// <summary>
/// Validator para criacao de profissional
/// </summary>
public class CreateProfissionalDtoValidator : AbstractValidator<CreateProfissionalDto>
{
    public CreateProfissionalDtoValidator()
    {
        RuleFor(x => x.RegistroCAU)
            .NotEmpty().WithMessage("RegistroCAU eh obrigatorio")
            .MinimumLength(5).WithMessage("RegistroCAU deve ter pelo menos 5 caracteres")
            .MaximumLength(20).WithMessage("RegistroCAU deve ter no maximo 20 caracteres")
            .Matches(@"^[A-Za-z]\d{5,6}-[A-Za-z]{2}$").WithMessage("RegistroCAU deve estar no formato A000000-UF");

        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(3).WithMessage("Nome deve ter pelo menos 3 caracteres")
            .MaximumLength(200).WithMessage("Nome deve ter no maximo 200 caracteres");

        RuleFor(x => x.Cpf)
            .NotEmpty().WithMessage("CPF eh obrigatorio")
            .Matches(@"^\d{11}$").WithMessage("CPF deve conter 11 digitos numericos")
            .Must(BeAValidCpf).WithMessage("CPF invalido");

        RuleFor(x => x.Email)
            .EmailAddress().WithMessage("Email invalido")
            .When(x => !string.IsNullOrEmpty(x.Email));

        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo de profissional invalido");
    }

    private bool BeAValidCpf(string? cpf)
    {
        if (string.IsNullOrEmpty(cpf)) return true;
        if (cpf.Length != 11 || !cpf.All(char.IsDigit)) return false;

        if (cpf.Distinct().Count() == 1) return false;

        int[] multiplicador1 = { 10, 9, 8, 7, 6, 5, 4, 3, 2 };
        int soma = 0;
        for (int i = 0; i < 9; i++)
            soma += int.Parse(cpf[i].ToString()) * multiplicador1[i];
        int resto = soma % 11;
        int digito1 = resto < 2 ? 0 : 11 - resto;

        if (int.Parse(cpf[9].ToString()) != digito1) return false;

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
/// Validator para criacao de role
/// </summary>
public class CreateRoleDtoValidator : AbstractValidator<CreateRoleDto>
{
    public CreateRoleDtoValidator()
    {
        RuleFor(x => x.Nome)
            .NotEmpty().WithMessage("Nome eh obrigatorio")
            .MinimumLength(2).WithMessage("Nome deve ter pelo menos 2 caracteres")
            .MaximumLength(50).WithMessage("Nome deve ter no maximo 50 caracteres")
            .Matches(@"^[A-Za-z][A-Za-z0-9_]*$").WithMessage("Nome deve comecar com letra e conter apenas letras, numeros e underscore");

        RuleFor(x => x.Descricao)
            .MaximumLength(200).WithMessage("Descricao deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Descricao));

        RuleForEach(x => x.Permissoes)
            .NotEmpty().WithMessage("Permissao nao pode ser vazia")
            .MaximumLength(100).WithMessage("Permissao deve ter no maximo 100 caracteres")
            .When(x => x.Permissoes != null);
    }
}

/// <summary>
/// Validator para filtro de usuarios
/// </summary>
public class UsuarioFilterDtoValidator : AbstractValidator<UsuarioFilterDto>
{
    public UsuarioFilterDtoValidator()
    {
        RuleFor(x => x.Tipo)
            .IsInEnum().WithMessage("Tipo invalido")
            .When(x => x.Tipo.HasValue);

        RuleFor(x => x.Status)
            .IsInEnum().WithMessage("Status invalido")
            .When(x => x.Status.HasValue);

        RuleFor(x => x.Search)
            .MaximumLength(200).WithMessage("Search deve ter no maximo 200 caracteres")
            .When(x => !string.IsNullOrEmpty(x.Search));

        RuleFor(x => x.Page)
            .GreaterThan(0).WithMessage("Page deve ser maior que 0");

        RuleFor(x => x.PageSize)
            .InclusiveBetween(1, 100).WithMessage("PageSize deve ser entre 1 e 100");

        RuleFor(x => x.CriadoAntes)
            .GreaterThanOrEqualTo(x => x.CriadoApos)
            .WithMessage("CriadoAntes deve ser maior ou igual a CriadoApos")
            .When(x => x.CriadoApos.HasValue && x.CriadoAntes.HasValue);
    }
}
