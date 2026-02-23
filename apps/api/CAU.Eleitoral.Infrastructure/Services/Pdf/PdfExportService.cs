using QuestPDF.Fluent;
using QuestPDF.Helpers;
using QuestPDF.Infrastructure;

namespace CAU.Eleitoral.Infrastructure.Services.Pdf;

public interface IPdfExportService
{
    byte[] GenerateReport(string titulo, string eleicaoNome, DateTime dataGeracao, ReportData data);
}

public record ReportData
{
    public Dictionary<string, string> Summary { get; init; } = new();
    public List<string> TableHeaders { get; init; } = new();
    public List<List<string>> TableRows { get; init; } = new();
}

public class PdfExportService : IPdfExportService
{
    public byte[] GenerateReport(string titulo, string eleicaoNome, DateTime dataGeracao, ReportData data)
    {
        var document = Document.Create(container =>
        {
            container.Page(page =>
            {
                page.Size(PageSizes.A4);
                page.MarginHorizontal(40);
                page.MarginVertical(30);

                page.Header().Column(col =>
                {
                    col.Item().Row(row =>
                    {
                        row.RelativeItem().Column(c =>
                        {
                            c.Item().Text("CAU - CONSELHO DE ARQUITETURA E URBANISMO")
                                .Bold().FontSize(12).FontColor(Colors.Blue.Darken3);
                            c.Item().Text("SISTEMA ELEITORAL")
                                .FontSize(10).FontColor(Colors.Grey.Darken1);
                        });
                    });

                    col.Item().PaddingTop(10).LineHorizontal(2).LineColor(Colors.Blue.Darken3);

                    col.Item().PaddingTop(8).Text(titulo)
                        .Bold().FontSize(16).FontColor(Colors.Grey.Darken3);

                    col.Item().PaddingTop(4).Text($"Eleicao: {eleicaoNome}")
                        .FontSize(10).FontColor(Colors.Grey.Darken1);

                    col.Item().PaddingTop(2).Text($"Gerado em: {dataGeracao:dd/MM/yyyy HH:mm}")
                        .FontSize(9).FontColor(Colors.Grey.Medium);

                    col.Item().PaddingTop(8).PaddingBottom(4);
                });

                page.Content().Column(col =>
                {
                    // Summary section
                    if (data.Summary.Count > 0)
                    {
                        col.Item().PaddingBottom(8).Text("RESUMO").Bold().FontSize(11).FontColor(Colors.Blue.Darken2);

                        col.Item().PaddingBottom(12).Table(table =>
                        {
                            table.ColumnsDefinition(columns =>
                            {
                                columns.RelativeColumn(1);
                                columns.RelativeColumn(2);
                            });

                            foreach (var kv in data.Summary)
                            {
                                table.Cell().Border(0.5f).BorderColor(Colors.Grey.Lighten2)
                                    .Padding(6).Text(kv.Key).FontSize(9).Bold();
                                table.Cell().Border(0.5f).BorderColor(Colors.Grey.Lighten2)
                                    .Padding(6).Text(kv.Value).FontSize(9);
                            }
                        });
                    }

                    // Data table
                    if (data.TableHeaders.Count > 0 && data.TableRows.Count > 0)
                    {
                        col.Item().PaddingBottom(8).Text("DADOS").Bold().FontSize(11).FontColor(Colors.Blue.Darken2);

                        col.Item().Table(table =>
                        {
                            table.ColumnsDefinition(columns =>
                            {
                                foreach (var _ in data.TableHeaders)
                                    columns.RelativeColumn();
                            });

                            // Header row
                            foreach (var header in data.TableHeaders)
                            {
                                table.Cell().Background(Colors.Blue.Darken3)
                                    .Padding(6).Text(header).FontSize(9).Bold().FontColor(Colors.White);
                            }

                            // Data rows
                            var rowIndex = 0;
                            foreach (var row in data.TableRows)
                            {
                                var bgColor = rowIndex % 2 == 0 ? Colors.White : Colors.Grey.Lighten4;
                                foreach (var cell in row)
                                {
                                    table.Cell().Background(bgColor)
                                        .Border(0.5f).BorderColor(Colors.Grey.Lighten2)
                                        .Padding(5).Text(cell).FontSize(8);
                                }
                                rowIndex++;
                            }
                        });
                    }

                    if (data.TableHeaders.Count == 0 && data.Summary.Count == 0)
                    {
                        col.Item().PaddingTop(20).AlignCenter()
                            .Text("Nenhum dado disponivel para este relatorio.")
                            .FontSize(11).FontColor(Colors.Grey.Medium);
                    }
                });

                page.Footer().AlignCenter().Text(text =>
                {
                    text.Span("CAU Sistema Eleitoral - ").FontSize(8).FontColor(Colors.Grey.Medium);
                    text.Span("Pagina ").FontSize(8).FontColor(Colors.Grey.Medium);
                    text.CurrentPageNumber().FontSize(8).FontColor(Colors.Grey.Medium);
                    text.Span(" de ").FontSize(8).FontColor(Colors.Grey.Medium);
                    text.TotalPages().FontSize(8).FontColor(Colors.Grey.Medium);
                });
            });
        });

        return document.GeneratePdf();
    }
}
