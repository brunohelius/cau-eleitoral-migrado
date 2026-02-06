using System.Security.Claims;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Produces("application/json")]
public abstract class BaseController : ControllerBase
{
    protected Guid GetUserId()
    {
        var userIdClaim = User.FindFirst("sub")
            ?? User.FindFirst("userId")
            ?? User.FindFirst(ClaimTypes.NameIdentifier);
        return userIdClaim != null ? Guid.Parse(userIdClaim.Value) : Guid.Empty;
    }

    protected string GetUserEmail()
    {
        var emailClaim = User.FindFirst("email");
        return emailClaim?.Value ?? string.Empty;
    }

    protected IEnumerable<string> GetUserRoles()
    {
        return User.FindAll("role").Select(c => c.Value);
    }

    protected IActionResult HandleException(Exception ex)
    {
        return ex switch
        {
            KeyNotFoundException => NotFound(new { message = ex.Message }),
            UnauthorizedAccessException => Unauthorized(new { message = ex.Message }),
            InvalidOperationException => BadRequest(new { message = ex.Message }),
            ArgumentException => BadRequest(new { message = ex.Message }),
            _ => StatusCode(500, new { message = "Erro interno do servidor" })
        };
    }

    protected ObjectResult InternalError(string message)
    {
        return StatusCode(500, new { message });
    }
}
