<?php

namespace Zumba\Aura;

use Aura\SqlQuery\Quoter;

class CastAwareQuoter extends Quoter
{
    /**
     * Quotes an identifier name (table, index, etc); ignores empty values and
     * values of '*'.
     *
     * @param string $name the identifier name to quote
     *
     * @return string the quoted identifier name
     *
     * @see quoteName()
     */
    protected function replaceName($name)
    {
        if (strpos($name, ')') !== false) {
            return $name;
        }

        return parent::replaceName($name);
    }
}
