<?php

namespace Zumba\Db;

class WhereFilter
{
    public $where;
    public $value;

    public function __construct($where, $value)
    {
        $this->where = $where;
        $this->value = $value;
    }
}
