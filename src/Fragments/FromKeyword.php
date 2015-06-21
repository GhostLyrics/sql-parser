<?php

namespace SqlParser\Fragments;

use SqlParser\Fragment;
use SqlParser\Lexer;
use SqlParser\Parser;
use SqlParser\Token;
use SqlParser\TokensList;

/**
 * `FROM` keyword parser.
 */
class FromKeyword extends Fragment
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

        $expr = new FieldFragment();

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

            // No keyword is expected.
            if (($token->type === Token::TYPE_KEYWORD) && ($token->flags & Token::FLAG_KEYWORD_RESERVED)) {
                break;
            }

            if (($token->type === Token::TYPE_OPERATOR) && ($token->value === ',')) {
                $ret[] = $expr;
            } else {
                $expr = FieldFragment::parse($parser, $list, array('skipColumn' => true));
                if ($expr === null) {
                    break;
                }
            }

        }

        // Last iteration was not saved.
        if ($expr !== null) {
            $ret[] = $expr;
        }

        --$list->idx;
        return $ret;
    }
}
