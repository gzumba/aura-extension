<?php

namespace Zumba\Aura;

use Aura\SqlQuery\AbstractQuery;
use Aura\SqlQuery\Quoter;

class WhereClause extends AbstractQuery
{
    public function __construct()
    {
        parent::__construct(new Quoter('"', '"'));
    }
    /**
     *
     * Builds this query object into a string.
     *
     * @return string
     */
    protected function build()
    {
        return $this->buildWhere();
    }

    /**
     *
     * Adds a WHERE condition to the query by AND. If the condition has
     * ?-placeholders, additional arguments to the method will be bound to
     * those placeholders sequentially.
     *
     * @param string $cond The WHERE condition.
     * param mixed ...$bind arguments to bind to placeholders
     *
     * @return self
     */
    public function where($cond): self
    {
        $this->addWhere('AND', func_get_args());
        return $this;
    }
}
