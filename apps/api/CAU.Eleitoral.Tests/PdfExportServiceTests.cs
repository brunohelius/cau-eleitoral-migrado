using CAU.Eleitoral.Infrastructure.Services.Pdf;
using FluentAssertions;
using QuestPDF.Infrastructure;

namespace CAU.Eleitoral.Tests;

public class PdfExportServiceTests
{
    private readonly PdfExportService _sut;

    public PdfExportServiceTests()
    {
        QuestPDF.Settings.License = LicenseType.Community;
        _sut = new PdfExportService();
    }

    [Fact]
    public void GenerateReport_WithSummaryAndTable_ReturnsValidPdf()
    {
        var data = new ReportData
        {
            Summary = new Dictionary<string, string>
            {
                ["Total Eleitores"] = "1000",
                ["Total Votos"] = "800",
                ["Participacao"] = "80%"
            },
            TableHeaders = new List<string> { "Chapa", "Numero", "Votos", "Percentual" },
            TableRows = new List<List<string>>
            {
                new() { "Chapa Inovacao", "10", "400", "50%" },
                new() { "Chapa Tradicao", "20", "300", "37.5%" },
                new() { "Chapa Excelencia", "30", "100", "12.5%" }
            }
        };

        var result = _sut.GenerateReport(
            "Relatorio de Participacao",
            "Eleicao CAU/SP 2026",
            new DateTime(2026, 2, 23, 12, 0, 0),
            data);

        result.Should().NotBeNullOrEmpty();
        result.Length.Should().BeGreaterThan(100);
        // PDF magic bytes: %PDF
        result[0].Should().Be(0x25); // %
        result[1].Should().Be(0x50); // P
        result[2].Should().Be(0x44); // D
        result[3].Should().Be(0x46); // F
    }

    [Fact]
    public void GenerateReport_WithEmptyData_ReturnsValidPdf()
    {
        var data = new ReportData();

        var result = _sut.GenerateReport(
            "Relatorio Vazio",
            "Eleicao Teste",
            DateTime.UtcNow,
            data);

        result.Should().NotBeNullOrEmpty();
        result[0].Should().Be(0x25); // %PDF
    }

    [Fact]
    public void GenerateReport_WithOnlySummary_ReturnsValidPdf()
    {
        var data = new ReportData
        {
            Summary = new Dictionary<string, string>
            {
                ["Total"] = "100",
                ["Ativos"] = "80"
            }
        };

        var result = _sut.GenerateReport(
            "Resumo",
            "Eleicao CAU/BR 2026",
            DateTime.UtcNow,
            data);

        result.Should().NotBeNullOrEmpty();
        result.Length.Should().BeGreaterThan(100);
    }

    [Fact]
    public void GenerateReport_WithManyRows_ReturnsValidPdf()
    {
        var rows = new List<List<string>>();
        for (int i = 0; i < 100; i++)
        {
            rows.Add(new List<string> { $"Eleitor {i}", $"CPF-{i:D11}", i % 2 == 0 ? "Sim" : "Nao" });
        }

        var data = new ReportData
        {
            TableHeaders = new List<string> { "Nome", "CPF", "Votou" },
            TableRows = rows
        };

        var result = _sut.GenerateReport(
            "Lista de Eleitores",
            "Eleicao CAU/SP 2026",
            DateTime.UtcNow,
            data);

        result.Should().NotBeNullOrEmpty();
        result.Length.Should().BeGreaterThan(1000); // Multi-page PDF should be larger
    }
}
