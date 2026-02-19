#!/usr/bin/env python3
"""
Fix Portuguese diacritics — ULTRA CONSERVATIVE version (v5).
ONLY modifies text between > and < on the same line (JSX text content).
Does NOT touch:
  - import/export lines
  - navigate() calls
  - Property names, type definitions
  - String literals in code
  - Anything that's not pure JSX text content
"""
import re
import os

WORDS = {
    'Eleicoes': 'Eleições', 'eleicoes': 'eleições',
    'Eleicao': 'Eleição', 'eleicao': 'eleição',
    'Impugnacoes': 'Impugnações', 'impugnacoes': 'impugnações',
    'Impugnacao': 'Impugnação', 'impugnacao': 'impugnação',
    'Configuracoes': 'Configurações', 'configuracoes': 'configurações',
    'Configuracao': 'Configuração', 'configuracao': 'configuração',
    'Notificacoes': 'Notificações', 'notificacoes': 'notificações',
    'Notificacao': 'Notificação', 'notificacao': 'notificação',
    'Informacoes': 'Informações', 'informacoes': 'informações',
    'Informacao': 'Informação', 'informacao': 'informação',
    'Inscricoes': 'Inscrições', 'inscricoes': 'inscrições',
    'Inscricao': 'Inscrição', 'inscricao': 'inscrição',
    'Operacoes': 'Operações', 'operacoes': 'operações',
    'Operacao': 'Operação', 'operacao': 'operação',
    'Alteracoes': 'Alterações', 'alteracoes': 'alterações',
    'Alteracao': 'Alteração', 'alteracao': 'alteração',
    'Condicoes': 'Condições', 'condicoes': 'condições',
    'Condicao': 'Condição', 'condicao': 'condição',
    'Permissoes': 'Permissões', 'permissoes': 'permissões',
    'Permissao': 'Permissão', 'permissao': 'permissão',
    'Funcoes': 'Funções', 'funcoes': 'funções',
    'Funcao': 'Função', 'funcao': 'função',
    'Acoes': 'Ações', 'acoes': 'ações',
    'Votacao': 'Votação', 'votacao': 'votação',
    'Apuracao': 'Apuração', 'apuracao': 'apuração',
    'Diplomacao': 'Diplomação', 'diplomacao': 'diplomação',
    'Identificacao': 'Identificação', 'identificacao': 'identificação',
    'Descricao': 'Descrição', 'descricao': 'descrição',
    'Fundamentacao': 'Fundamentação', 'fundamentacao': 'fundamentação',
    'Apresentacao': 'Apresentação', 'apresentacao': 'apresentação',
    'Participacao': 'Participação', 'participacao': 'participação',
    'Visualizacao': 'Visualização', 'visualizacao': 'visualização',
    'Confirmacao': 'Confirmação', 'confirmacao': 'confirmação',
    'Conclusao': 'Conclusão', 'conclusao': 'conclusão',
    'Resolucao': 'Resolução', 'resolucao': 'resolução',
    'Justificacao': 'Justificação', 'justificacao': 'justificação',
    'Exclusao': 'Exclusão', 'exclusao': 'exclusão',
    'Criacao': 'Criação', 'criacao': 'criação',
    'Atualizacao': 'Atualização', 'atualizacao': 'atualização',
    'Administracao': 'Administração', 'administracao': 'administração',
    'Selecao': 'Seleção', 'selecao': 'seleção',
    'Edicao': 'Edição', 'edicao': 'edição',
    'Profissao': 'Profissão', 'profissao': 'profissão',
    'Situacao': 'Situação', 'situacao': 'situação',
    'Conexao': 'Conexão', 'conexao': 'conexão',
    'Protecao': 'Proteção', 'protecao': 'proteção',
    'Recuperacao': 'Recuperação', 'recuperacao': 'recuperação',
    'Comissao': 'Comissão', 'comissao': 'comissão',
    'Sessao': 'Sessão', 'sessao': 'sessão',
    'Denuncias': 'Denúncias', 'denuncias': 'denúncias',
    'Denuncia': 'Denúncia', 'denuncia': 'denúncia',
    'Relatorios': 'Relatórios', 'relatorios': 'relatórios',
    'Relatorio': 'Relatório', 'relatorio': 'relatório',
    'Obrigatorio': 'Obrigatório', 'obrigatorio': 'obrigatório',
    'Historico': 'Histórico', 'historico': 'histórico',
    'Formulario': 'Formulário', 'formulario': 'formulário',
    'Calendario': 'Calendário', 'calendario': 'calendário',
    'Periodo': 'Período', 'periodo': 'período',
    'Minimo': 'Mínimo', 'minimo': 'mínimo',
    'Maximo': 'Máximo', 'maximo': 'máximo',
    'Numero': 'Número', 'numero': 'número',
    'Valido': 'Válido', 'valido': 'válido',
    'Invalido': 'Inválido', 'invalido': 'inválido',
    'invalidas': 'inválidas',
    'Disponivel': 'Disponível', 'disponivel': 'disponível',
    'Disponiveis': 'Disponíveis', 'disponiveis': 'disponíveis',
    'Possivel': 'Possível', 'possivel': 'possível',
    'seguranca': 'segurança', 'Seguranca': 'Segurança',
    'Inicio': 'Início', 'inicio': 'início',
    'Rapidos': 'Rápidos', 'rapidos': 'rápidos',
    'orgaos': 'órgãos', 'Orgaos': 'Órgãos',
    'duvidas': 'dúvidas', 'Duvidas': 'Dúvidas',
    'duvida': 'dúvida', 'Duvida': 'Dúvida',
    'digitos': 'dígitos',
    'Academico': 'Acadêmico', 'academico': 'acadêmico',
    'anonima': 'anônima', 'Anonima': 'Anônima',
    'instrucoes': 'instruções', 'Instrucoes': 'Instruções',
    'Analise': 'Análise', 'analise': 'análise',
    'Conheca': 'Conheça', 'conheca': 'conheça',
    'exerca': 'exerça',
    'atraves': 'através', 'Atraves': 'Através',
    'Areas': 'Áreas', 'areas': 'áreas',
    'notificacoes': 'notificações',
    'comunicacao': 'comunicação',
    'resolucoes': 'resoluções',
    'reunioes': 'reuniões',
    'aplicaveis': 'aplicáveis',
    'cronologica': 'cronológica',
    'termino': 'término',
    'recomendavel': 'recomendável',
    'publico': 'público',
    'secao': 'seção',
    'estao': 'estão',
    'proclamacao': 'proclamação',
}

PHRASES = {
    'Area do': 'Área do', 'area do': 'área do',
    'Esta area': 'Esta área', 'esta area': 'esta área',
    'sua area': 'sua área',
    'Nao e ': 'Não é ', 'nao e ': 'não é ',
    'Nao foi': 'Não foi', 'nao foi': 'não foi',
    'Nao ha': 'Não há', 'nao ha': 'não há',
    'nao encontr': 'não encontr', 'Nao encontr': 'Não encontr',
    'nao realizou': 'não realizou',
    'nao possui': 'não possui',
    'nao pode': 'não pode',
    'nao sera': 'não será',
    'Nao existem': 'Não existem',
    'nao existem': 'não existem',
    'nao esta ': 'não está ',
    'Nao esta ': 'Não está ',
    'O que e ': 'O que é ',
    ' e a plataforma': ' é a plataforma',
    ' e protegida': ' é protegida',
    ' e exclusiva': ' é exclusiva',
    ' e obrig': ' é obrig',
    ' sao ': ' são ',
    'Links Rapidos': 'Links Rápidos',
    'Ainda nao ': 'Ainda não ',
    'Voce ': 'Você ', 'voce ': 'você ',
    'tambem ': 'também ', 'Tambem ': 'Também ',
    ' sera ': ' será ',
    ' ja ': ' já ', ' Ja ': ' Já ',
    ' ate ': ' até ', ' Ate ': ' Até ',
    ' apos ': ' após ', ' Apos ': ' Após ',
    'Faca login': 'Faça login', 'faca login': 'faça login',
    'esta em ': 'está em ',
    'esta apta': 'está apta',
}

_sorted_words = sorted(WORDS.keys(), key=len, reverse=True)
_word_pattern = re.compile(r'\b(' + '|'.join(re.escape(w) for w in _sorted_words) + r')\b')

def apply_fixes(text):
    text = _word_pattern.sub(lambda m: WORDS.get(m.group(0), m.group(0)), text)
    for old, new in PHRASES.items():
        text = text.replace(old, new)
    return text


def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        original = f.read()

    lines = original.split('\n')
    fixed_lines = []

    for line in lines:
        stripped = line.strip()
        # Skip import/export/navigate lines
        if re.match(r'^\s*(import|export)\b', stripped):
            fixed_lines.append(line)
            continue
        if 'navigate(' in line or 'navigate(`' in line:
            fixed_lines.append(line)
            continue

        # ONLY fix text between > and < on the same line
        result = re.sub(
            r'(>)([^<>{]+)(<)',
            lambda m: m.group(1) + apply_fixes(m.group(2)) + m.group(3),
            line
        )

        fixed_lines.append(result)

    fixed = '\n'.join(fixed_lines)
    if fixed != original:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(fixed)
        return True
    return False


def main():
    project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    apps_dir = os.path.join(project_root, 'apps')

    tsx_files = []
    for root, dirs, files in os.walk(apps_dir):
        dirs[:] = [d for d in dirs if d != 'node_modules']
        for f in files:
            if f.endswith('.tsx'):
                tsx_files.append(os.path.join(root, f))

    modified = 0
    for filepath in sorted(tsx_files):
        rel = os.path.relpath(filepath, project_root)
        if process_file(filepath):
            print(f"  ✏️  {rel}")
            modified += 1

    print(f"\n✅ Fixed accents in {modified} files (out of {len(tsx_files)} scanned)")


if __name__ == '__main__':
    main()
