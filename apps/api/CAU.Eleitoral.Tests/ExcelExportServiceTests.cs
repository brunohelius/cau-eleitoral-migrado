using CAU.Eleitoral.Infrastructure.Services.Storage;
using FluentAssertions;

namespace CAU.Eleitoral.Tests;

public class ExcelExportServiceTests
{
    private readonly ExcelExportService _sut = new();

    [Fact]
    public void GenerateReport_WithData_ReturnsValidXlsx()
    {
        var summary = new Dictionary<string, string>
        {
            ["Total Eleitores"] = "500",
            ["Participacao"] = "75%"
        };
        var headers = new List<string> { "Chapa", "Votos", "Percentual" };
        var rows = new List<List<string>>
        {
            new() { "Chapa A", "200", "53.3%" },
            new() { "Chapa B", "175", "46.7%" }
        };

        var result = _sut.GenerateReport(
            "Relatorio de Votacao",
            "Eleicao CAU/SP 2026",
            new DateTime(2026, 2, 23, 12, 0, 0),
            summary, headers, rows);

        result.Should().NotBeNullOrEmpty();
        result.Length.Should().BeGreaterThan(100);
        // XLSX files are ZIP archives (magic bytes PK)
        result[0].Should().Be(0x50); // P
        result[1].Should().Be(0x4B); // K
    }

    [Fact]
    public void GenerateReport_WithEmptyData_ReturnsValidXlsx()
    {
        var result = _sut.GenerateReport(
            "Relatorio Vazio",
            "Eleicao Teste",
            DateTime.UtcNow,
            new Dictionary<string, string>(),
            new List<string>(),
            new List<List<string>>());

        result.Should().NotBeNullOrEmpty();
        result[0].Should().Be(0x50); // PK header
    }

    [Fact]
    public void GenerateReport_WithOnlyHeaders_ReturnsValidXlsx()
    {
        var headers = new List<string> { "Nome", "CPF", "Status" };

        var result = _sut.GenerateReport(
            "Relatorio Sem Dados",
            "Eleicao Teste",
            DateTime.UtcNow,
            new Dictionary<string, string>(),
            headers,
            new List<List<string>>());

        result.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public void GenerateReport_WithManyRows_HandlesLargeDataset()
    {
        var headers = new List<string> { "ID", "Nome", "Email", "Regional", "Apto" };
        var rows = new List<List<string>>();
        for (int i = 0; i < 200; i++)
        {
            rows.Add(new List<string>
            {
                i.ToString(),
                $"Profissional {i}",
                $"prof{i}@cau.org.br",
                i % 3 == 0 ? "SP" : i % 3 == 1 ? "RJ" : "MG",
                i % 5 == 0 ? "Nao" : "Sim"
            });
        }

        var result = _sut.GenerateReport(
            "Lista Completa de Eleitores",
            "Eleicao CAU/SP 2026",
            DateTime.UtcNow,
            new Dictionary<string, string> { ["Total"] = "200" },
            headers, rows);

        result.Should().NotBeNullOrEmpty();
        result.Length.Should().BeGreaterThan(5000);
    }
}
