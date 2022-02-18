<?php
namespace Zumba\Aura\Parser;

class PgsqlParser extends \Aura\Sql\Parser\PgsqlParser
{
    /**
     *
     * Split the query string on these regexes.
     *
     * @var array
     *
     */
    protected $split = [
        // single-quoted string
        "'(?:[^'\\\\]|\\\\'?)*'",
        // double-quoted string
        '"(?:[^"\\\\]|\\\\"?)*"',
        // double-dollar string (empty dollar-tag)
        '\$\$(?:[^\$]?)*\$\$',
        // dollar-tag string -- DOES NOT match tags properly
        '\$[^\$]+\$.*\$[^\$]+\$',
    ];

    /**
     *
     * Skip query parts matching this regex.
     *
     * @var string
     *
     */
    protected $skip = '/^(\'|\"|\$|\:[^a-zA-Z_])/um';

    protected function prepareNamedPlaceholder(string $sub): string
    {
        if (strpos($sub, '::') === 0) {
            return $sub;
        }

        return parent::prepareNamedPlaceholder($sub);
    }

}