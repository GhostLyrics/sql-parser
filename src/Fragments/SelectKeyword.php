<?php

namespace SqlParser\Fragments;

use SqlParser\Fragment;
use SqlParser\Lexer;
use SqlParser\Parser;
use SqlParser\Token;
use SqlParser\TokensList;

/**
 * `SELECT` keyword parser.
 */
class SelectKeyword extends Fragment
{

    /**
     * @param Parser $parser The parser that serves as context.
     * @param TokensList $list The list of tokens that are being parsed.
     * @param array $options Parameters for parsing.
     *
     * @return FieldFragment[]
     */
    public static function parse(Parser $parser, TokensList $list, array $options = array())
    {
        $ret = array();

        $expr = null;

        for (; $list->idx < $list->count; ++$list->idx) {
            /** @var Token Token parsed at this moment. */
            $token = $list->tokens[$list->idx];

            // End of statement.
            if ($token->type === Token::TYPE_DELIMITER) {
                break;
            }

            // Skipping whitespaces and comments.
            if (($token->type === Token::TYPE_WHITESPACE) || ($token->type === Token::TYPE_COMMENT)) {
                continue;
            }

            if (($token->type === Token::TYPE_KEYWORD) && ($token->flags & Token::FLAG_KEYWORD_RESERVED)) {
                // No keyword is expected.
                break;
            }

            if (($token->type === Token::TYPE_OPERATOR) && ($token->value === ',')) {
                $ret[] = $expr;
            } else {
                $expr = FieldFragment::parse($parser, $list);
                if ($expr === null) {
                    break;
                }
            }

        }

        // Last iteration was not processed.
        if ($expr !== null) {
            $ret[] = $expr;
        }

        --$list->idx;
        return $ret;
    }
}
