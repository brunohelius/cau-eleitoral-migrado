#!/usr/bin/env python3
from __future__ import annotations

import argparse
import dataclasses
import datetime as dt
import json
import re
import subprocess
import time
import unicodedata
from collections import Counter, defaultdict
from pathlib import Path
from typing import Any

ROOT = Path(__file__).resolve().parents[1]
REQ_DOC = ROOT / "docs" / "documentacao-qa.md"
OUT_MD = ROOT / "docs" / "matriz-rastreabilidade-qa.md"
OUT_JSON = ROOT / "docs" / "matriz-rastreabilidade-qa.json"

TEST_GLOBS = [
    "apps/admin/e2e/*.spec.ts",
    "apps/public/e2e/*.spec.ts",
    "apps/admin/src/**/__tests__/*.test.ts",
    "apps/admin/src/**/__tests__/*.spec.ts",
    "apps/public/src/**/__tests__/*.test.ts",
    "apps/public/src/**/__tests__/*.spec.ts",
    "apps/api/CAU.Eleitoral.Tests/*.cs",
]

STOPWORDS = {
    "a",
    "ao",
    "aos",
    "as",
    "com",
    "como",
    "da",
    "das",
    "de",
    "do",
    "dos",
    "e",
    "em",
    "entre",
    "esta",
    "este",
    "ha",
    "na",
    "nas",
    "no",
    "nos",
    "o",
    "os",
    "ou",
    "para",
    "por",
    "se",
    "sem",
    "ser",
    "sua",
    "suas",
    "suo",
    "seu",
    "seus",
    "um",
    "uma",
    "uns",
    "umas",
    "deve",
    "devera",
    "resultado",
    "esperado",
    "sistema",
    "dados",
    "completo",
    "corretamente",
    "pagina",
    "paginas",
    "tela",
    "telas",
    "status",
    "com",
    "sem",
    "via",
    "rota",
    "rotas",
    "modulo",
}

GENERIC_CASE_TOKENS = {
    "visualizar",
    "exibir",
    "exibe",
    "listar",
    "listagem",
    "pagina",
    "paginas",
    "dados",
    "detalhes",
    "detalhe",
    "sistema",
}

ACTION_TOKENS = {
    "login",
    "logout",
    "criar",
    "editar",
    "excluir",
    "deletar",
    "iniciar",
    "encerrar",
    "suspender",
    "cancelar",
    "aprovar",
    "reprovar",
    "votar",
    "apurar",
    "publicar",
    "exportar",
    "filtrar",
    "consultar",
    "validar",
    "registrar",
}

HIGH_SIGNAL_TOKENS = {
    "auth",
    "login",
    "logout",
    "senha",
    "token",
    "refresh",
    "dashboard",
    "eleicao",
    "chapas",
    "chapa",
    "votacao",
    "voto",
    "apuracao",
    "resultado",
    "denuncia",
    "impugnacao",
    "julgamento",
    "usuario",
    "relatorio",
    "auditoria",
    "configuracao",
    "eleitor",
    "candidato",
    "comprovante",
    "protocolo",
    "responsividade",
    "mobile",
    "tablet",
    "desktop",
    "cors",
    "sql",
    "xss",
    "rate",
    "sigilo",
    "calendario",
    "documento",
}

DOMAIN_ANCHOR_TOKENS: dict[str, set[str]] = {
    "AUTH": {"auth", "login", "logout", "senha", "token", "refresh", "recuperar", "redefinir"},
    "DASH": {"dashboard"},
    "ELE": {"eleicao", "calendario", "apuracao"},
    "CHA": {"chapa", "membro", "documento", "analise", "deferir", "indeferir"},
    "VOT": {"votacao", "voto", "apuracao", "resultado", "comprovante", "exportar"},
    "DEN": {"denuncia", "protocolo", "admissibilidade"},
    "IMP": {"impugnacao"},
    "JUL": {"julgamento", "sessao", "decisao"},
    "USR": {"usuario", "perfil", "permissao"},
    "REL": {"relatorio", "exportar"},
    "AUD": {"auditoria", "log"},
    "CFG": {"configuracao", "smtp", "seguranca", "notificacao"},
    "PUB-AUTH": {"eleitor", "login", "logout", "codigo", "elegibilidade"},
    "PUB-VOT": {"votacao", "voto", "cedula", "comprovante", "historico", "sessao"},
    "PUB-CAND": {"candidato", "chapa", "documento", "plataforma", "defesa", "recurso", "historico"},
    "PUB-PAG": {"home", "eleicao", "calendario", "documento", "faq", "denuncia"},
    "RESP": {"mobile", "tablet", "desktop", "responsividade"},
    "SEC": {"seguranca", "cors", "sql", "xss", "rate", "sigilo", "token"},
}

TOKEN_CANONICAL: dict[str, str] = {
    "authentication": "auth",
    "autenticacao": "auth",
    "autenticado": "auth",
    "autenticada": "auth",
    "logar": "login",
    "entrar": "login",
    "signin": "login",
    "sign": "login",
    "in": "login",
    "sair": "logout",
    "signout": "logout",
    "password": "senha",
    "senha": "senha",
    "recuperacao": "recuperar",
    "recuperar": "recuperar",
    "forgot": "recuperar",
    "reset": "redefinir",
    "redefinicao": "redefinir",
    "redefinir": "redefinir",
    "refresh": "refresh",
    "dashboard": "dashboard",
    "elections": "eleicao",
    "election": "eleicao",
    "eleicoes": "eleicao",
    "eleicao": "eleicao",
    "slates": "chapa",
    "slate": "chapa",
    "chapas": "chapa",
    "chapa": "chapa",
    "members": "membro",
    "member": "membro",
    "membros": "membro",
    "membro": "membro",
    "voting": "votacao",
    "votacao": "votacao",
    "vote": "voto",
    "votes": "voto",
    "voto": "voto",
    "apurar": "apuracao",
    "apuracao": "apuracao",
    "results": "resultado",
    "resultados": "resultado",
    "resultado": "resultado",
    "complaint": "denuncia",
    "complaints": "denuncia",
    "denuncia": "denuncia",
    "denuncias": "denuncia",
    "impugnacao": "impugnacao",
    "impugnacoes": "impugnacao",
    "julgamento": "julgamento",
    "julgamentos": "julgamento",
    "users": "usuario",
    "user": "usuario",
    "usuario": "usuario",
    "usuarios": "usuario",
    "report": "relatorio",
    "reports": "relatorio",
    "relatorios": "relatorio",
    "relatorio": "relatorio",
    "audit": "auditoria",
    "auditoria": "auditoria",
    "configuracoes": "configuracao",
    "configuracao": "configuracao",
    "settings": "configuracao",
    "elector": "eleitor",
    "voter": "eleitor",
    "eleitor": "eleitor",
    "candidate": "candidato",
    "candidato": "candidato",
    "receipt": "comprovante",
    "comprovante": "comprovante",
    "faq": "faq",
    "home": "home",
    "calendar": "calendario",
    "calendario": "calendario",
    "documents": "documento",
    "documentos": "documento",
    "documento": "documento",
    "mobile": "mobile",
    "tablet": "tablet",
    "desktop": "desktop",
    "security": "seguranca",
    "seguranca": "seguranca",
    "cors": "cors",
    "sql": "sql",
    "xss": "xss",
    "rate": "rate",
    "limiting": "limit",
    "limit": "limit",
    "privacy": "sigilo",
    "sigilo": "sigilo",
    "export": "exportar",
    "exportar": "exportar",
}


@dataclasses.dataclass
class RequirementCase:
    module: str
    case_id: str
    title: str
    domain: str
    expected_result: str
    steps: list[str]


@dataclasses.dataclass
class TestEvidence:
    file_path: str
    title: str
    suite_kind: str
    domains: set[str]
    tokens: set[str]


@dataclasses.dataclass
class TestMatch:
    file_path: str
    title: str
    score: int
    shared_tokens: list[str]


@dataclasses.dataclass
class CaseCoverage:
    req: RequirementCase
    status: str
    mapped_files: list[str]
    detected_test_count: int
    direct_matches: list[TestMatch]


@dataclasses.dataclass
class CommandResult:
    name: str
    command: str
    cwd: str
    ok: bool
    exit_code: int
    duration_sec: float
    output_tail: list[str]


def normalize_text(value: str) -> str:
    normalized = unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")
    normalized = re.sub(r"([a-z0-9])([A-Z])", r"\1 \2", normalized)
    normalized = normalized.lower()
    normalized = normalized.replace("/", " ").replace("-", " ").replace("_", " ")
    normalized = re.sub(r"[^a-z0-9\s]", " ", normalized)
    normalized = re.sub(r"\s+", " ", normalized).strip()
    return normalized


def canonical_token(token: str) -> str:
    return TOKEN_CANONICAL.get(token, token)


def tokenize(value: str) -> set[str]:
    tokens: set[str] = set()
    normalized = normalize_text(value)
    for raw in normalized.split():
        if not raw or len(raw) < 2:
            continue
        tok = canonical_token(raw)
        if tok in STOPWORDS or len(tok) < 2:
            continue
        tokens.add(tok)
    return tokens


def parse_domain(case_id: str) -> str:
    token = case_id.replace("CT-", "", 1)
    parts = token.split("-")
    if len(parts) >= 3 and parts[0] == "PUB":
        return f"PUB-{parts[1]}"
    return parts[0]


def parse_requirement_cases(path: Path) -> list[RequirementCase]:
    lines = path.read_text(encoding="utf-8").splitlines()
    module_re = re.compile(r"^##\s+\d+\.\s+(.+)$")
    case_re = re.compile(r"^###\s+(CT-[A-Z0-9-]+):\s*(.+)$")
    expected_re = re.compile(r"^- \*\*Resultado Esperado:\*\*\s*(.+)$")
    step_re = re.compile(r"^\d+\.\s+(.+)$")

    cases: list[RequirementCase] = []
    current_module = ""
    current_case: RequirementCase | None = None
    in_steps = False

    def flush_current() -> None:
        nonlocal current_case
        if current_case:
            cases.append(current_case)
            current_case = None

    for raw in lines:
        line = raw.rstrip("\n")
        stripped = line.strip()

        module_match = module_re.match(stripped)
        if module_match:
            flush_current()
            current_module = module_match.group(1).strip()
            in_steps = False
            continue

        case_match = case_re.match(stripped)
        if case_match:
            flush_current()
            case_id = case_match.group(1).strip()
            title = case_match.group(2).strip()
            current_case = RequirementCase(
                module=current_module,
                case_id=case_id,
                title=title,
                domain=parse_domain(case_id),
                expected_result="",
                steps=[],
            )
            in_steps = False
            continue

        if current_case is None:
            continue

        if stripped.startswith("- **Passos:**"):
            in_steps = True
            continue

        if in_steps:
            step_match = step_re.match(stripped)
            if step_match:
                current_case.steps.append(step_match.group(1).strip())
                continue
            if stripped.startswith("- **"):
                in_steps = False

        expected_match = expected_re.match(stripped)
        if expected_match:
            current_case.expected_result = expected_match.group(1).strip()
            continue

    flush_current()
    return cases


def discover_test_files() -> list[Path]:
    files: set[Path] = set()
    for pattern in TEST_GLOBS:
        for hit in ROOT.glob(pattern):
            if hit.is_file():
                files.add(hit)
    return sorted(files)


def infer_domains(file_rel: str, text: str) -> set[str]:
    space = normalize_text(f"{file_rel} {text}")
    domains: set[str] = set()

    def has(*terms: str) -> bool:
        return any(term in space for term in terms)

    is_admin = "/admin/" in f"/{file_rel}/"
    is_public = "/public/" in f"/{file_rel}/"

    if has("auth", "login", "logout", "senha", "token", "refresh", "forgot", "reset"):
        if is_admin:
            domains.add("AUTH")
            domains.add("SEC")
        if is_public:
            domains.add("PUB-AUTH")
            domains.add("PUB-CAND")

    if has("dashboard", "estatistica"):
        domains.add("DASH")

    if has("eleicao", "election", "elections", "calendario", "canedit"):
        domains.add("ELE")
        if is_public:
            domains.add("PUB-PAG")

    if has("chapa", "slate", "membro"):
        domains.add("CHA")
        if is_public:
            domains.add("PUB-PAG")
            domains.add("PUB-CAND")

    if has("votacao", "voting", "voto", "apuracao", "comprovante", "cedula", "ja votou"):
        if is_admin:
            domains.add("VOT")
        if is_public:
            domains.add("PUB-VOT")
            domains.add("PUB-PAG")
        domains.add("SEC")

    if has("denuncia", "protocolo"):
        domains.add("DEN")
        if is_public:
            domains.add("PUB-PAG")
            domains.add("PUB-CAND")

    if has("impugn"):
        domains.add("IMP")

    if has("julgamento", "sessao"):
        domains.add("JUL")

    if has("usuario", "users", "profile", "perfil"):
        if is_admin:
            domains.add("USR")

    if has("relatorio", "report", "export", "pdf", "excel", "csv"):
        domains.add("REL")

    if has("auditoria", "audit", "logs"):
        domains.add("AUD")

    if has("configuracao", "settings", "smtp", "notificacao"):
        domains.add("CFG")

    if has("faq", "home", "calendario", "documento", "public", "publica"):
        if is_public:
            domains.add("PUB-PAG")

    if has("candidato", "candidate"):
        domains.add("PUB-CAND")

    if has("mobile", "tablet", "desktop", "responsive", "responsiv"):
        domains.add("RESP")

    if has("cors", "xss", "sql", "rate", "sigilo", "ownership", "ownership dos dados"):
        domains.add("SEC")

    if "modules-pages.spec.ts" in file_rel:
        domains.update({"AUD", "REL", "JUL", "ELE", "CFG"})
    if "full-system.spec.ts" in file_rel:
        domains.update({"AUTH", "DASH", "ELE", "CHA", "VOT", "SEC"})
    if "voting.spec.ts" in file_rel:
        domains.update({"PUB-PAG", "PUB-VOT", "PUB-AUTH", "PUB-CAND"})

    return domains


def parse_ts_tests(path: Path) -> list[TestEvidence]:
    content = path.read_text(encoding="utf-8")
    test_matches = re.findall(r"\b(?:test|it)\(\s*(['\"])(.*?)\1", content, flags=re.DOTALL)

    rel = str(path.relative_to(ROOT))
    evidences: list[TestEvidence] = []

    for _, title in test_matches:
        title_clean = " ".join(title.strip().split())
        base_text = f"{title_clean} {rel}"
        domains = infer_domains(rel, base_text)
        tokens = tokenize(base_text)
        evidences.append(
            TestEvidence(
                file_path=rel,
                title=title_clean,
                suite_kind="ts",
                domains=domains,
                tokens=tokens,
            )
        )
    return evidences


def split_identifier(identifier: str) -> str:
    text = identifier.replace("_", " ")
    text = re.sub(r"([a-z0-9])([A-Z])", r"\1 \2", text)
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def parse_cs_tests(path: Path) -> list[TestEvidence]:
    content = path.read_text(encoding="utf-8")
    rel = str(path.relative_to(ROOT))

    display_names = re.findall(
        r"DisplayName\s*=\s*\"([^\"]+)\"",
        content,
        flags=re.DOTALL,
    )
    method_names = re.findall(
        r"public\s+(?:async\s+)?(?:Task|void)\s+([A-Za-z0-9_]+)\s*\(",
        content,
    )

    titles = [dn.strip() for dn in display_names if dn.strip()]
    titles.extend(split_identifier(mn) for mn in method_names if mn.strip())

    evidences: list[TestEvidence] = []
    for title in titles:
        text = f"{title} {rel}"
        domains = infer_domains(rel, text)
        tokens = tokenize(text)
        evidences.append(
            TestEvidence(
                file_path=rel,
                title=title,
                suite_kind="xunit",
                domains=domains,
                tokens=tokens,
            )
        )
    return evidences


def collect_test_inventory() -> list[TestEvidence]:
    evidence: list[TestEvidence] = []
    for file_path in discover_test_files():
        if file_path.suffix in {".ts", ".tsx"}:
            evidence.extend(parse_ts_tests(file_path))
        elif file_path.suffix == ".cs":
            evidence.extend(parse_cs_tests(file_path))
    return evidence


def case_tokens(case: RequirementCase) -> set[str]:
    base = tokenize(" ".join([case.title, case.expected_result, " ".join(case.steps)]))
    trimmed = {tok for tok in base if tok not in GENERIC_CASE_TOKENS}
    return trimmed or base


def score_match(req_tokens: set[str], test_tokens: set[str]) -> tuple[int, list[str]]:
    shared = sorted(req_tokens & test_tokens)
    if not shared:
        return 0, []
    score = 0
    for token in shared:
        score += 2 if token in HIGH_SIGNAL_TOKENS else 1
        if token in ACTION_TOKENS:
            score += 1
    return score, shared


def has_anchor_match(domain: str, shared_tokens: list[str]) -> bool:
    anchors = DOMAIN_ANCHOR_TOKENS.get(domain)
    if not anchors:
        return True
    return bool(set(shared_tokens) & anchors)


def classify_case(req: RequirementCase, tests: list[TestEvidence], max_evidence: int) -> CaseCoverage:
    mapped_tests = [t for t in tests if req.domain in t.domains]
    mapped_files = sorted({t.file_path for t in mapped_tests})

    req_tokens = case_tokens(req)
    matches: list[TestMatch] = []
    for test in mapped_tests:
        score, shared = score_match(req_tokens, test.tokens)
        if score <= 0:
            continue
        matches.append(
            TestMatch(
                file_path=test.file_path,
                title=test.title,
                score=score,
                shared_tokens=shared[:6],
            )
        )

    matches.sort(key=lambda m: (m.score, len(m.shared_tokens)), reverse=True)
    top_matches = matches[:max_evidence]

    direct = [m for m in top_matches if m.score >= 3 and has_anchor_match(req.domain, m.shared_tokens)]

    if not mapped_tests:
        status = "Sem cobertura automatizada"
    elif direct:
        status = "Coberto automatizado"
    else:
        status = "Cobertura parcial"

    return CaseCoverage(
        req=req,
        status=status,
        mapped_files=mapped_files,
        detected_test_count=len(mapped_tests),
        direct_matches=direct if direct else top_matches,
    )


def run_shell(name: str, command: str, cwd: Path) -> CommandResult:
    start = time.monotonic()
    proc = subprocess.run(
        ["bash", "-lc", command],
        cwd=cwd,
        capture_output=True,
        text=True,
        check=False,
    )
    duration = time.monotonic() - start
    output = (proc.stdout or "") + "\n" + (proc.stderr or "")
    lines = [ln.rstrip() for ln in output.splitlines() if ln.strip()]
    return CommandResult(
        name=name,
        command=command,
        cwd=str(cwd.relative_to(ROOT)) if cwd != ROOT else ".",
        ok=proc.returncode == 0,
        exit_code=proc.returncode,
        duration_sec=round(duration, 2),
        output_tail=lines[-20:],
    )


def run_quality_commands() -> list[CommandResult]:
    commands: list[tuple[str, str, Path]] = [
        (
            "api-unit",
            "dotnet test apps/api/CAU.Eleitoral.Tests/CAU.Eleitoral.Tests.csproj --nologo",
            ROOT,
        ),
        (
            "admin-unit",
            "pnpm exec vitest run",
            ROOT / "apps/admin",
        ),
        (
            "public-unit",
            "pnpm exec vitest run",
            ROOT / "apps/public",
        ),
        (
            "admin-build",
            "pnpm --filter @cau-eleitoral/admin build",
            ROOT,
        ),
        (
            "public-build",
            "pnpm --filter @cau-eleitoral/public build",
            ROOT,
        ),
        (
            "admin-e2e",
            "pkill -f \"dotnet run --urls http://localhost:7779\" || true; "
            "pkill -f \"vite.*7777\" || true; "
            "pnpm --filter @cau-eleitoral/admin e2e",
            ROOT,
        ),
        (
            "public-e2e",
            "pkill -f \"dotnet run --urls http://localhost:7779\" || true; "
            "pkill -f \"vite.*7778\" || true; "
            "pnpm --filter @cau-eleitoral/public e2e",
            ROOT,
        ),
    ]

    results: list[CommandResult] = []
    for name, command, cwd in commands:
        results.append(run_shell(name=name, command=command, cwd=cwd))
    return results


def build_json_payload(
    coverages: list[CaseCoverage],
    tests: list[TestEvidence],
    command_results: list[CommandResult],
) -> dict[str, Any]:
    status_counts = Counter(c.status for c in coverages)

    domain_summary: dict[str, dict[str, Any]] = {}
    grouped_by_domain: dict[str, list[CaseCoverage]] = defaultdict(list)
    for coverage in coverages:
        grouped_by_domain[coverage.req.domain].append(coverage)

    for domain in sorted(grouped_by_domain):
        domain_cases = grouped_by_domain[domain]
        domain_tests = [t for t in tests if domain in t.domains]
        domain_summary[domain] = {
            "total_cases": len(domain_cases),
            "covered": sum(1 for c in domain_cases if c.status == "Coberto automatizado"),
            "partial": sum(1 for c in domain_cases if c.status == "Cobertura parcial"),
            "uncovered": sum(1 for c in domain_cases if c.status == "Sem cobertura automatizada"),
            "mapped_files": sorted({t.file_path for t in domain_tests}),
            "detected_tests": len(domain_tests),
        }

    module_summary: dict[str, dict[str, int]] = {}
    grouped_by_module: dict[str, list[CaseCoverage]] = defaultdict(list)
    for coverage in coverages:
        grouped_by_module[coverage.req.module].append(coverage)
    for module in sorted(grouped_by_module):
        module_cases = grouped_by_module[module]
        module_summary[module] = {
            "total_cases": len(module_cases),
            "covered": sum(1 for c in module_cases if c.status == "Coberto automatizado"),
            "partial": sum(1 for c in module_cases if c.status == "Cobertura parcial"),
            "uncovered": sum(1 for c in module_cases if c.status == "Sem cobertura automatizada"),
        }

    tests_by_file = Counter(t.file_path for t in tests)
    cases_payload = []
    for coverage in coverages:
        cases_payload.append(
            {
                "case_id": coverage.req.case_id,
                "title": coverage.req.title,
                "module": coverage.req.module,
                "domain": coverage.req.domain,
                "expected_result": coverage.req.expected_result,
                "status": coverage.status,
                "mapped_files": coverage.mapped_files,
                "detected_test_count": coverage.detected_test_count,
                "direct_matches": [
                    {
                        "file_path": match.file_path,
                        "title": match.title,
                        "score": match.score,
                        "shared_tokens": match.shared_tokens,
                    }
                    for match in coverage.direct_matches
                ],
            }
        )

    return {
        "generated_at": dt.datetime.now(dt.UTC).isoformat(),
        "source_requirements": str(REQ_DOC.relative_to(ROOT)),
        "summary": {
            "total_cases": len(coverages),
            "covered": status_counts.get("Coberto automatizado", 0),
            "partial": status_counts.get("Cobertura parcial", 0),
            "uncovered": status_counts.get("Sem cobertura automatizada", 0),
        },
        "domain_summary": domain_summary,
        "module_summary": module_summary,
        "test_inventory": {
            "total_tests_detected": len(tests),
            "files": dict(sorted(tests_by_file.items())),
        },
        "command_results": [dataclasses.asdict(r) for r in command_results],
        "cases": cases_payload,
    }


def build_markdown(payload: dict[str, Any]) -> str:
    summary = payload["summary"]
    total = summary["total_cases"]
    covered = summary["covered"]
    partial = summary["partial"]
    uncovered = summary["uncovered"]
    covered_ratio = ((covered / total) * 100.0) if total else 0.0
    partial_ratio = ((partial / total) * 100.0) if total else 0.0
    uncovered_ratio = ((uncovered / total) * 100.0) if total else 0.0

    lines: list[str] = []
    lines.append("# Matriz de Rastreabilidade QA (Requisitos x Testes)")
    lines.append("")
    lines.append(f"- Data de geracao: {dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    lines.append(f"- Fonte de requisitos: `{payload['source_requirements']}`")
    lines.append(f"- Testes detectados no snapshot: {payload['test_inventory']['total_tests_detected']}")
    lines.append("")
    lines.append("## 1. Resumo Geral")
    lines.append("")
    lines.append("| Indicador | Valor |")
    lines.append("|---|---:|")
    lines.append(f"| Total de casos CT-* | {total} |")
    lines.append(f"| Coberto automatizado | {covered} ({covered_ratio:.1f}%) |")
    lines.append(f"| Cobertura parcial | {partial} ({partial_ratio:.1f}%) |")
    lines.append(f"| Sem cobertura automatizada | {uncovered} ({uncovered_ratio:.1f}%) |")

    command_results = payload.get("command_results", [])
    if command_results:
        lines.append("")
        lines.append("## 2. Execucao das Suites")
        lines.append("")
        lines.append("| Suite | Status | Duracao (s) | Exit |")
        lines.append("|---|---|---:|---:|")
        for result in command_results:
            status = "OK" if result["ok"] else "FALHA"
            lines.append(
                f"| `{result['name']}` | {status} | {result['duration_sec']:.2f} | {result['exit_code']} |"
            )

    lines.append("")
    lines.append("## 3. Cobertura por Dominio")
    lines.append("")
    lines.append("| Dominio | CTs | Coberto | Parcial | Sem cobertura | Evidencias (tests) |")
    lines.append("|---|---:|---:|---:|---:|---:|")
    for domain, data in sorted(payload["domain_summary"].items()):
        lines.append(
            f"| `{domain}` | {data['total_cases']} | {data['covered']} | {data['partial']} | {data['uncovered']} | {data['detected_tests']} |"
        )

    lines.append("")
    lines.append("## 4. Lacunas Prioritarias")
    lines.append("")
    uncovered_cases = [case for case in payload["cases"] if case["status"] == "Sem cobertura automatizada"]
    partial_cases = [case for case in payload["cases"] if case["status"] == "Cobertura parcial"]
    if not uncovered_cases and not partial_cases:
        lines.append("Nenhuma lacuna de cobertura foi encontrada neste snapshot.")
    else:
        if uncovered_cases:
            lines.append("### Sem cobertura automatizada")
            for case in uncovered_cases:
                lines.append(f"- `{case['case_id']}` ({case['module']}): {case['title']}")
            lines.append("")
        if partial_cases:
            lines.append("### Cobertura parcial")
            for case in partial_cases:
                lines.append(f"- `{case['case_id']}` ({case['module']}): {case['title']}")

    lines.append("")
    lines.append("## 5. Matriz Detalhada")
    lines.append("")
    lines.append("| Caso | Modulo | Dominio | Status | Evidencia direta |")
    lines.append("|---|---|---|---|---|")
    for case in payload["cases"]:
        if case["direct_matches"]:
            best = case["direct_matches"][0]
            evid = f"`{best['file_path']}` :: {best['title']} (score={best['score']})"
        elif case["mapped_files"]:
            evid = ", ".join(f"`{f}`" for f in case["mapped_files"][:2])
        else:
            evid = "-"
        lines.append(
            f"| `{case['case_id']}` | {case['module']} | `{case['domain']}` | {case['status']} | {evid} |"
        )

    lines.append("")
    lines.append("## 6. Criterio de Classificacao")
    lines.append("")
    lines.append("- Coberto automatizado: existe pelo menos uma evidencia direta (matching textual score >= 3).")
    lines.append("- Cobertura parcial: ha automacao no dominio, mas sem evidencia direta forte para o caso.")
    lines.append("- Sem cobertura automatizada: nenhum teste foi detectado para o dominio do caso.")
    lines.append("- O matching textual usa titulo do caso, resultado esperado, passos e titulos reais dos testes.")

    return "\n".join(lines) + "\n"


def main() -> int:
    parser = argparse.ArgumentParser(description="Generate QA requirement traceability matrix.")
    parser.add_argument("--run-tests", action="store_true", help="Run local test/build suites before report.")
    parser.add_argument("--strict", action="store_true", help="Fail when any case is partial or uncovered.")
    parser.add_argument(
        "--fail-on-uncovered",
        type=int,
        default=None,
        help="Fail if uncovered cases are greater than this threshold.",
    )
    parser.add_argument(
        "--fail-on-partial",
        type=int,
        default=None,
        help="Fail if partial cases are greater than this threshold.",
    )
    parser.add_argument("--max-evidence", type=int, default=3, help="Max direct evidence entries per case.")
    parser.add_argument("--output-md", type=Path, default=OUT_MD)
    parser.add_argument("--output-json", type=Path, default=OUT_JSON)
    args = parser.parse_args()

    if not REQ_DOC.exists():
        raise FileNotFoundError(f"Requirements source not found: {REQ_DOC}")

    requirements = parse_requirement_cases(REQ_DOC)
    tests = collect_test_inventory()
    coverages = [classify_case(req=req, tests=tests, max_evidence=args.max_evidence) for req in requirements]

    command_results: list[CommandResult] = []
    if args.run_tests:
        command_results = run_quality_commands()

    payload = build_json_payload(coverages=coverages, tests=tests, command_results=command_results)
    args.output_json.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    args.output_md.write_text(build_markdown(payload), encoding="utf-8")

    summary = payload["summary"]
    print(f"Generated: {args.output_md}")
    print(f"Generated: {args.output_json}")
    print(
        "Summary:",
        f"{summary['total_cases']} cases,",
        f"{summary['covered']} covered,",
        f"{summary['partial']} partial,",
        f"{summary['uncovered']} uncovered.",
    )

    if command_results and any(not result.ok for result in command_results):
        print("Status: failing because one or more local suites failed.")
        return 2

    uncovered = int(summary["uncovered"])
    partial = int(summary["partial"])
    if args.strict and (uncovered > 0 or partial > 0):
        print("Status: failing due to strict mode (partial/uncovered cases present).")
        return 5
    if args.fail_on_uncovered is not None and uncovered > args.fail_on_uncovered:
        print(
            f"Status: failing because uncovered ({uncovered}) > threshold ({args.fail_on_uncovered})."
        )
        return 3
    if args.fail_on_partial is not None and partial > args.fail_on_partial:
        print(
            f"Status: failing because partial ({partial}) > threshold ({args.fail_on_partial})."
        )
        return 4

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
