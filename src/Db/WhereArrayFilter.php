<?php

namespace Zumba\Db;

use DusanKasan\Knapsack\Collection;

class WhereArrayFilter extends WhereFilter
{
    public $array_value;

    public function __construct(string $where, array $value)
    {
        sort($value);
        $this->array_value = $value;
        $value_string = $this->buildValueString($value);

        parent::__construct($where, sprintf("{%s}", $value_string));
    }

    private function buildValueString(array $value): string
    {
        return Collection::from($value)
            ->map(
                function ($val) {
                    if (is_string($val)) {
                        return sprintf('"%s"', str_replace(['\\', '"'], ['\\\\', '\"'], $val));
                    }

                    return $val;
                }
            )
            ->interpose(',')
            ->toString()
        ;
    }
}
