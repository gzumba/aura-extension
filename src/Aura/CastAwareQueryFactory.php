<?php

namespace Zumba\Aura;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\Quoter;

class CastAwareQueryFactory extends QueryFactory
{
    /**
     * Returns the Quoter object for queries; creates one if needed.
     *
     * @return Quoter
     */
    protected function getQuoter()
    {
        if (!$this->quoter) {
            $this->quoter = new CastAwareQuoter(
                $this->quote_name_prefix,
                $this->quote_name_suffix
            );
        }

        return $this->quoter;
    }
}
