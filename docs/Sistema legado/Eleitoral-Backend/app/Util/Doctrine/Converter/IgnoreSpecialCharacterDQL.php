<?php
/*
 * IgnoreSpecialCharacterDQL.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Util\Doctrine\Converter;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;

/**
 * Classe converter DQL responsável por ignorar caracteres especiais na execução de consultas.
 *
 * @author Squadra Tecnologia S/A.
 */
class IgnoreSpecialCharacterDQL extends FunctionNode
{

    /**
     *
     * @var SingleValuedPathExpression | Literal | ParenthesisExpression
     *      | FunctionsReturningNumerics | AggregateExpression | FunctionsReturningStrings
     *      | FunctionsReturningDatetime | IdentificationVariable | ResultVariable
     *      | InputParameter | CaseExpression
     */
    private $valueExpression;

    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\ORM\Query\AST\Functions\FunctionNode::getSql()
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return \sprintf("UNACCENT(%s)", $this->valueExpression->dispatch($sqlWalker));
    }

    /**
     *
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\Query\QueryException
     * @see \Doctrine\ORM\Query\AST\Functions\FunctionNode::parse()
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->valueExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
