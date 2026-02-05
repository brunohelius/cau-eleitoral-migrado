#!/bin/bash

# Claude Code - Execução Direta com Bypass de Permissões
# Executa o Claude diretamente sem usar venv com memória otimizada

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     🚀 CLAUDE - Execução com Bypass - 4GB Memory           ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Configurar NODE_OPTIONS para memória heap otimizada
# 4GB é suficiente para o Claude Code CLI
echo -e "${YELLOW}🔧 Configurando memória Node.js (4GB)...${NC}"
export NODE_OPTIONS="--max-old-space-size=4096"
echo -e "${GREEN}✅ Memória heap configurada: 4GB${NC}"
echo ""

# Executa Claude com bypass de permissões
echo -e "${GREEN}🚀 Iniciando Claude com bypass de permissões...${NC}"
echo ""

# Se houver argumentos, passa para o Claude
if [ $# -gt 0 ]; then
    claude --dangerously-skip-permissions "$@"
else
    claude --dangerously-skip-permissions
fi