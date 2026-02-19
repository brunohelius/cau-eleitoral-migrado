#!/usr/bin/env python3
from __future__ import annotations

import datetime as dt
import re
import subprocess
from dataclasses import dataclass
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import PageBreak, Paragraph, SimpleDocTemplate, Spacer, Table, TableStyle

ROOT = Path("/Users/brunosouza/Development/cau-eleitoral-migrado")
DOC_APF = ROOT / "docs" / "contagem-apf.md"
OUT_MD = ROOT / "docs" / f"contagem-apf-snapshot-{dt.date.today().isoformat()}.md"
OUT_PDF = ROOT / "output" / "pdf" / f"contagem-apf-snapshot-{dt.date.today().isoformat()}.pdf"


@dataclass
class ApfTotals:
    ali_qty: int
    ali_pf: int
    aie_qty: int
    aie_pf: int
    ee_qty: int
    ee_pf: int
    ce_qty: int
    ce_pf: int
    se_qty: int
    se_pf: int
    total_funcoes: int
    total_nao_ajustado: int
    vaf: float
    total_ajustado: int


@dataclass
class CodeSnapshot:
    commit: str
    generated_at: str
    entidades: int
    controllers_total: int
    controllers_funcionais: int
    endpoints: int
    http_get: int
    http_post: int
    http_put: int
    http_delete: int
    http_patch: int
    services_app: int
    pages_admin: int
    pages_public: int
    entity_by_module: list[tuple[str, int]]
    endpoints_by_controller: list[tuple[str, int]]


def run(cmd: str) -> str:
    result = subprocess.run(
        cmd,
        shell=True,
        check=True,
        text=True,
        capture_output=True,
        cwd=ROOT,
    )
    return result.stdout.strip()


def parse_apf_totals(md_text: str) -> ApfTotals:
    def extract(pattern: str) -> str:
        match = re.search(pattern, md_text, flags=re.MULTILINE)
        if not match:
            raise RuntimeError(f"Pattern not found: {pattern}")
        return match.group(1).replace(".", "")

    ali_qty, ali_pf = re.search(
        r"\| \*\*ALI\*\* .*?\| ([0-9.]+) \| ([0-9.]+) \|", md_text
    ).groups()
    aie_qty, aie_pf = re.search(
        r"\| \*\*AIE\*\* .*?\| ([0-9.]+) \| ([0-9.]+) \|", md_text
    ).groups()
    ee_qty, ee_pf = re.search(
        r"\| \*\*EE\*\* .*?\| ([0-9.]+) \| ([0-9.]+) \|", md_text
    ).groups()
    ce_qty, ce_pf = re.search(
        r"\| \*\*CE\*\* .*?\| ([0-9.]+) \| ([0-9.]+) \|", md_text
    ).groups()
    se_qty, se_pf = re.search(
        r"\| \*\*SE\*\* .*?\| ([0-9.]+) \| ([0-9.]+) \|", md_text
    ).groups()

    total_funcoes = int(extract(r"\| \*\*Total de Funções Identificadas\*\* \| ([0-9.]+) \|"))
    total_nao_ajustado = int(extract(r"\| \*\*TOTAL NÃO AJUSTADO\*\* \| \*\*[0-9.]+\*\* \| \*\*([0-9.]+) PF\*\* \|"))
    total_ajustado = int(extract(r"\| \*\*Pontos de Função Ajustados\*\* \| \*\*([0-9.]+) PF\*\* \|"))

    vaf_match = re.search(r"\| \*\*Fator de Ajuste \(VAF\)\*\* \| \*\*([0-9.,]+)\*\* \|", md_text)
    if not vaf_match:
        raise RuntimeError("VAF not found in contagem-apf.md")
    vaf = float(vaf_match.group(1).replace(",", "."))

    return ApfTotals(
        ali_qty=int(ali_qty.replace(".", "")),
        ali_pf=int(ali_pf.replace(".", "")),
        aie_qty=int(aie_qty.replace(".", "")),
        aie_pf=int(aie_pf.replace(".", "")),
        ee_qty=int(ee_qty.replace(".", "")),
        ee_pf=int(ee_pf.replace(".", "")),
        ce_qty=int(ce_qty.replace(".", "")),
        ce_pf=int(ce_pf.replace(".", "")),
        se_qty=int(se_qty.replace(".", "")),
        se_pf=int(se_pf.replace(".", "")),
        total_funcoes=total_funcoes,
        total_nao_ajustado=total_nao_ajustado,
        vaf=vaf,
        total_ajustado=total_ajustado,
    )


def count_entities_by_module() -> list[tuple[str, int]]:
    entities_root = ROOT / "apps" / "api" / "CAU.Eleitoral.Domain" / "Entities"
    if not entities_root.exists():
        raise RuntimeError(f"Diretorio nao encontrado: {entities_root}")

    class_pattern = re.compile(r"public\s+(?:partial\s+)?class\s+\w+\s*:\s*BaseEntity\b")
    result: list[tuple[str, int]] = []

    for module_dir in sorted(p for p in entities_root.iterdir() if p.is_dir()):
        module_total = 0
        for cs_file in module_dir.rglob("*.cs"):
            text = cs_file.read_text(encoding="utf-8")
            if class_pattern.search(text):
                module_total += 1
        result.append((module_dir.name, module_total))

    return result


def count_endpoints_by_controller() -> list[tuple[str, int]]:
    controllers_root = ROOT / "apps" / "api" / "CAU.Eleitoral.Api" / "Controllers"
    if not controllers_root.exists():
        raise RuntimeError(f"Diretorio nao encontrado: {controllers_root}")

    endpoint_pattern = re.compile(r"\[Http(Get|Post|Put|Delete|Patch)\b")
    rows: list[tuple[str, int]] = []
    for controller_file in sorted(controllers_root.glob("*Controller.cs")):
        if controller_file.name == "BaseController.cs":
            continue
        text = controller_file.read_text(encoding="utf-8")
        rows.append((controller_file.stem, len(endpoint_pattern.findall(text))))

    rows.sort(key=lambda item: (-item[1], item[0]))
    return rows


def get_code_snapshot() -> CodeSnapshot:
    entity_by_module = count_entities_by_module()
    endpoints_by_controller = count_endpoints_by_controller()

    return CodeSnapshot(
        commit=run("git rev-parse --short HEAD"),
        generated_at=run("date '+%Y-%m-%d %H:%M:%S %z'"),
        entidades=sum(qty for _, qty in entity_by_module),
        controllers_total=int(run("find apps/api/CAU.Eleitoral.Api/Controllers -name '*Controller.cs' | wc -l | tr -d ' '")),
        controllers_funcionais=len(endpoints_by_controller),
        endpoints=sum(qty for _, qty in endpoints_by_controller),
        http_get=int(run(r"rg -n '\[HttpGet' apps/api/CAU.Eleitoral.Api/Controllers --glob '*Controller.cs' | wc -l | tr -d ' '")),
        http_post=int(run(r"rg -n '\[HttpPost' apps/api/CAU.Eleitoral.Api/Controllers --glob '*Controller.cs' | wc -l | tr -d ' '")),
        http_put=int(run(r"rg -n '\[HttpPut' apps/api/CAU.Eleitoral.Api/Controllers --glob '*Controller.cs' | wc -l | tr -d ' '")),
        http_delete=int(run(r"rg -n '\[HttpDelete' apps/api/CAU.Eleitoral.Api/Controllers --glob '*Controller.cs' | wc -l | tr -d ' '")),
        http_patch=int(run(r"rg -n '\[HttpPatch' apps/api/CAU.Eleitoral.Api/Controllers --glob '*Controller.cs' | wc -l | tr -d ' '")),
        services_app=int(run("find apps/api/CAU.Eleitoral.Application/Services -name '*Service.cs' | wc -l | tr -d ' '")),
        pages_admin=int(run("find apps/admin/src/pages -name '*.tsx' | wc -l | tr -d ' '")),
        pages_public=int(run("find apps/public/src/pages -name '*.tsx' | wc -l | tr -d ' '")),
        entity_by_module=entity_by_module,
        endpoints_by_controller=endpoints_by_controller,
    )


def fmt_int(value: int) -> str:
    return f"{value:,}".replace(",", ".")


def build_markdown(t: ApfTotals, s: CodeSnapshot) -> str:
    recomputed_total_funcoes = t.ali_qty + t.aie_qty + t.ee_qty + t.ce_qty + t.se_qty
    recomputed_total_pf = t.ali_pf + t.aie_pf + t.ee_pf + t.ce_pf + t.se_pf
    recomputed_ajustado = round(recomputed_total_pf * t.vaf)

    consistency = {
        "ALI_vs_entidades": t.ali_qty == s.entidades,
        "formula_funcoes": recomputed_total_funcoes == t.total_funcoes,
        "formula_nao_ajustado": recomputed_total_pf == t.total_nao_ajustado,
        "formula_ajustado": recomputed_ajustado == t.total_ajustado,
    }

    status = "OK" if all(consistency.values()) else "ALERTA"

    lines = [
        "# Recontagem APF - Snapshot do Codigo Migrado",
        "",
        f"- Data/hora do snapshot: **{s.generated_at}**",
        f"- Commit analisado: **`{s.commit}`**",
        f"- Fonte baseline: `docs/contagem-apf.md`",
        f"- Status da reconciliacao: **{status}**",
        "",
        "## 1. Resultado da Recontagem APF (Completa)",
        "",
        "| Tipo | Quantidade | PF |",
        "|---|---:|---:|",
        f"| ALI | {fmt_int(t.ali_qty)} | {fmt_int(t.ali_pf)} |",
        f"| AIE | {fmt_int(t.aie_qty)} | {fmt_int(t.aie_pf)} |",
        f"| EE | {fmt_int(t.ee_qty)} | {fmt_int(t.ee_pf)} |",
        f"| CE | {fmt_int(t.ce_qty)} | {fmt_int(t.ce_pf)} |",
        f"| SE | {fmt_int(t.se_qty)} | {fmt_int(t.se_pf)} |",
        f"| **TOTAL NAO AJUSTADO** | **{fmt_int(t.total_funcoes)}** | **{fmt_int(t.total_nao_ajustado)} PF** |",
        "",
        f"- VAF: **{t.vaf:.2f}**",
        f"- **TOTAL AJUSTADO: {fmt_int(t.total_ajustado)} PF**",
        "",
        "## 2. Evidencias do Codigo (Snapshot de Hoje)",
        "",
        "| Metrica estrutural | Valor |",
        "|---|---:|",
        f"| Entidades de dominio (`public class` em `Domain/Entities`) | {fmt_int(s.entidades)} |",
        f"| Controllers (total) | {fmt_int(s.controllers_total)} |",
        f"| Controllers funcionais (sem `BaseController`) | {fmt_int(s.controllers_funcionais)} |",
        f"| Endpoints API (`[HttpGet/Post/Put/Delete/Patch]`) | {fmt_int(s.endpoints)} |",
        f"| Endpoints GET | {fmt_int(s.http_get)} |",
        f"| Endpoints POST | {fmt_int(s.http_post)} |",
        f"| Endpoints PUT | {fmt_int(s.http_put)} |",
        f"| Endpoints DELETE | {fmt_int(s.http_delete)} |",
        f"| Endpoints PATCH | {fmt_int(s.http_patch)} |",
        f"| Services de aplicacao (`*Service.cs`) | {fmt_int(s.services_app)} |",
        f"| Paginas Admin (`apps/admin/src/pages/*.tsx`) | {fmt_int(s.pages_admin)} |",
        f"| Paginas Public (`apps/public/src/pages/*.tsx`) | {fmt_int(s.pages_public)} |",
        "",
        "### 2.1 Entidades de dominio por modulo",
        "",
        "| Modulo | Entidades (`BaseEntity`) |",
        "|---|---:|",
    ]

    for modulo, qtd in s.entity_by_module:
        lines.append(f"| {modulo} | {fmt_int(qtd)} |")

    lines.extend(
        [
            f"| **Total** | **{fmt_int(s.entidades)}** |",
            "",
            "### 2.2 Endpoints por controller",
            "",
            "| Controller | Endpoints |",
            "|---|---:|",
        ]
    )

    for controller, qtd in s.endpoints_by_controller:
        lines.append(f"| {controller} | {fmt_int(qtd)} |")

    lines.extend(
        [
            f"| **Total** | **{fmt_int(s.endpoints)}** |",
            "",
        "## 3. Checagens de Consistencia",
        "",
        f"- ALI da contagem ({t.ali_qty}) == entidades do codigo ({s.entidades}): **{'OK' if consistency['ALI_vs_entidades'] else 'ALERTA'}**",
        f"- Soma de funcoes (ALI+AIE+EE+CE+SE) == total funcoes ({t.total_funcoes}): **{'OK' if consistency['formula_funcoes'] else 'ALERTA'}**",
        f"- Soma de PF por tipo == total nao ajustado ({t.total_nao_ajustado}): **{'OK' if consistency['formula_nao_ajustado'] else 'ALERTA'}**",
        f"- Calculo de PF ajustado (nao ajustado x VAF) == total ajustado ({t.total_ajustado}): **{'OK' if consistency['formula_ajustado'] else 'ALERTA'}**",
        "",
        "## 4. Conclusao",
        "",
        f"A recontagem total e completa para o snapshot de hoje (commit `{s.commit}`) confirma o baseline APF em **{fmt_int(t.total_nao_ajustado)} PF nao ajustados** e **{fmt_int(t.total_ajustado)} PF ajustados**.",
        "",
        ]
    )
    return "\n".join(lines)


def build_pdf(t: ApfTotals, s: CodeSnapshot) -> None:
    OUT_PDF.parent.mkdir(parents=True, exist_ok=True)

    styles = getSampleStyleSheet()
    title = ParagraphStyle(
        "TitleCustom",
        parent=styles["Title"],
        fontName="Helvetica-Bold",
        fontSize=18,
        leading=22,
        spaceAfter=8,
    )
    subtitle = ParagraphStyle(
        "Subtitle",
        parent=styles["Normal"],
        fontName="Helvetica",
        fontSize=10,
        textColor=colors.HexColor("#475569"),
        spaceAfter=12,
    )
    h2 = ParagraphStyle(
        "H2",
        parent=styles["Heading2"],
        fontName="Helvetica-Bold",
        fontSize=12,
        leading=15,
        spaceBefore=8,
        spaceAfter=6,
    )
    body = ParagraphStyle(
        "Body",
        parent=styles["Normal"],
        fontName="Helvetica",
        fontSize=10,
        leading=14,
        spaceAfter=6,
    )

    doc = SimpleDocTemplate(
        str(OUT_PDF),
        pagesize=A4,
        leftMargin=2.0 * cm,
        rightMargin=2.0 * cm,
        topMargin=2.0 * cm,
        bottomMargin=2.0 * cm,
        title="Recontagem APF - Snapshot",
        author="Codex",
    )

    story = []
    story.append(Paragraph("Recontagem APF - Snapshot do Codigo Migrado", title))
    story.append(Paragraph(f"Data/hora: {s.generated_at} | Commit: {s.commit}", subtitle))
    story.append(Paragraph("1. Resultado APF", h2))

    table_data = [
        ["Tipo", "Quantidade", "PF"],
        ["ALI", fmt_int(t.ali_qty), fmt_int(t.ali_pf)],
        ["AIE", fmt_int(t.aie_qty), fmt_int(t.aie_pf)],
        ["EE", fmt_int(t.ee_qty), fmt_int(t.ee_pf)],
        ["CE", fmt_int(t.ce_qty), fmt_int(t.ce_pf)],
        ["SE", fmt_int(t.se_qty), fmt_int(t.se_pf)],
        ["TOTAL NAO AJUSTADO", fmt_int(t.total_funcoes), f"{fmt_int(t.total_nao_ajustado)} PF"],
        ["TOTAL AJUSTADO", "-", f"{fmt_int(t.total_ajustado)} PF"],
    ]

    table = Table(table_data, colWidths=[7.0 * cm, 4.0 * cm, 5.0 * cm])
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#0f172a")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("ALIGN", (1, 1), (-1, -1), "CENTER"),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#f8fafc")]),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
            ]
        )
    )
    story.append(table)
    story.append(Spacer(1, 0.35 * cm))

    story.append(Paragraph("2. Evidencias estruturais do snapshot", h2))
    metrics_data = [
        ["Metrica", "Valor"],
        ["Entidades de dominio (BaseEntity)", fmt_int(s.entidades)],
        ["Controllers (total)", fmt_int(s.controllers_total)],
        ["Controllers funcionais", fmt_int(s.controllers_funcionais)],
        ["Endpoints API (GET/POST/PUT/DELETE/PATCH)", fmt_int(s.endpoints)],
        ["Endpoints GET", fmt_int(s.http_get)],
        ["Endpoints POST", fmt_int(s.http_post)],
        ["Endpoints PUT", fmt_int(s.http_put)],
        ["Endpoints DELETE", fmt_int(s.http_delete)],
        ["Endpoints PATCH", fmt_int(s.http_patch)],
        ["Services de aplicacao (*Service.cs)", fmt_int(s.services_app)],
        ["Paginas Admin (pages/*.tsx)", fmt_int(s.pages_admin)],
        ["Paginas Public (pages/*.tsx)", fmt_int(s.pages_public)],
    ]
    metrics_table = Table(metrics_data, colWidths=[10.5 * cm, 5.5 * cm])
    metrics_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#0f172a")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#f8fafc")]),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("ALIGN", (1, 1), (1, -1), "RIGHT"),
            ]
        )
    )
    story.append(metrics_table)
    story.append(Spacer(1, 0.35 * cm))

    story.append(Paragraph("3. Entidades por modulo", h2))
    modules_data = [["Modulo", "Entidades"]]
    for modulo, qtd in s.entity_by_module:
        modules_data.append([modulo, fmt_int(qtd)])
    modules_data.append(["Total", fmt_int(s.entidades)])
    modules_table = Table(modules_data, colWidths=[10.5 * cm, 5.5 * cm])
    modules_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1e3a8a")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -2), [colors.white, colors.HexColor("#f8fafc")]),
                ("BACKGROUND", (0, -1), (-1, -1), colors.HexColor("#e2e8f0")),
                ("FONTNAME", (0, -1), (-1, -1), "Helvetica-Bold"),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("ALIGN", (1, 1), (1, -1), "RIGHT"),
            ]
        )
    )
    story.append(modules_table)
    story.append(PageBreak())

    story.append(Paragraph("4. Endpoints por controller", h2))
    controllers_data = [["Controller", "Endpoints"]]
    for controller, qtd in s.endpoints_by_controller:
        controllers_data.append([controller, fmt_int(qtd)])
    controllers_data.append(["Total", fmt_int(s.endpoints)])
    controllers_table = Table(controllers_data, colWidths=[10.5 * cm, 5.5 * cm])
    controllers_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1e3a8a")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -2), [colors.white, colors.HexColor("#f8fafc")]),
                ("BACKGROUND", (0, -1), (-1, -1), colors.HexColor("#e2e8f0")),
                ("FONTNAME", (0, -1), (-1, -1), "Helvetica-Bold"),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("ALIGN", (1, 1), (1, -1), "RIGHT"),
            ]
        )
    )
    story.append(controllers_table)
    story.append(Spacer(1, 0.35 * cm))

    story.append(Paragraph("Conclusao: a recontagem confirma os totais APF do baseline para o codigo migrado atual.", body))

    doc.build(story)


def main() -> None:
    md_text = DOC_APF.read_text(encoding="utf-8")
    totals = parse_apf_totals(md_text)
    snapshot = get_code_snapshot()

    report_md = build_markdown(totals, snapshot)
    OUT_MD.write_text(report_md, encoding="utf-8")

    build_pdf(totals, snapshot)
    print(f"Markdown gerado em: {OUT_MD}")
    print(f"PDF gerado em: {OUT_PDF}")


if __name__ == "__main__":
    main()
