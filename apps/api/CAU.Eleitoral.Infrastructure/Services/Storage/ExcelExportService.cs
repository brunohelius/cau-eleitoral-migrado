using ClosedXML.Excel;

namespace CAU.Eleitoral.Infrastructure.Services.Storage;

public interface IExcelExportService
{
    byte[] GenerateReport(string titulo, string eleicaoNome, DateTime dataGeracao,
        Dictionary<string, string> summary, List<string> headers, List<List<string>> rows);
}

public class ExcelExportService : IExcelExportService
{
    public byte[] GenerateReport(string titulo, string eleicaoNome, DateTime dataGeracao,
        Dictionary<string, string> summary, List<string> headers, List<List<string>> rows)
    {
        using var workbook = new XLWorkbook();
        var ws = workbook.Worksheets.Add("Relatorio");

        // Title
        ws.Cell(1, 1).Value = "CAU - SISTEMA ELEITORAL";
        ws.Cell(1, 1).Style.Font.Bold = true;
        ws.Cell(1, 1).Style.Font.FontSize = 14;
        ws.Range(1, 1, 1, Math.Max(headers.Count, 2)).Merge();

        ws.Cell(2, 1).Value = titulo;
        ws.Cell(2, 1).Style.Font.Bold = true;
        ws.Cell(2, 1).Style.Font.FontSize = 12;
        ws.Range(2, 1, 2, Math.Max(headers.Count, 2)).Merge();

        ws.Cell(3, 1).Value = $"Eleicao: {eleicaoNome}";
        ws.Cell(3, 1).Style.Font.FontSize = 10;

        ws.Cell(4, 1).Value = $"Gerado em: {dataGeracao:dd/MM/yyyy HH:mm}";
        ws.Cell(4, 1).Style.Font.FontSize = 9;
        ws.Cell(4, 1).Style.Font.FontColor = XLColor.Gray;

        var currentRow = 6;

        // Summary section
        if (summary.Count > 0)
        {
            ws.Cell(currentRow, 1).Value = "RESUMO";
            ws.Cell(currentRow, 1).Style.Font.Bold = true;
            ws.Cell(currentRow, 1).Style.Font.FontSize = 11;
            currentRow++;

            foreach (var kv in summary)
            {
                ws.Cell(currentRow, 1).Value = kv.Key;
                ws.Cell(currentRow, 1).Style.Font.Bold = true;
                ws.Cell(currentRow, 2).Value = kv.Value;
                currentRow++;
            }
            currentRow++;
        }

        // Data table
        if (headers.Count > 0)
        {
            ws.Cell(currentRow, 1).Value = "DADOS";
            ws.Cell(currentRow, 1).Style.Font.Bold = true;
            ws.Cell(currentRow, 1).Style.Font.FontSize = 11;
            currentRow++;

            // Header row
            for (int i = 0; i < headers.Count; i++)
            {
                var cell = ws.Cell(currentRow, i + 1);
                cell.Value = headers[i];
                cell.Style.Font.Bold = true;
                cell.Style.Fill.BackgroundColor = XLColor.DarkBlue;
                cell.Style.Font.FontColor = XLColor.White;
                cell.Style.Alignment.Horizontal = XLAlignmentHorizontalValues.Center;
                cell.Style.Border.OutsideBorder = XLBorderStyleValues.Thin;
            }
            currentRow++;

            // Data rows
            foreach (var row in rows)
            {
                for (int i = 0; i < row.Count && i < headers.Count; i++)
                {
                    var cell = ws.Cell(currentRow, i + 1);
                    cell.Value = row[i];
                    cell.Style.Border.OutsideBorder = XLBorderStyleValues.Thin;
                }
                currentRow++;
            }

            // Auto-fit columns
            ws.Columns().AdjustToContents();
        }

        using var stream = new MemoryStream();
        workbook.SaveAs(stream);
        return stream.ToArray();
    }
}
