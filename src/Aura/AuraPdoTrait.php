<?php
namespace Zumba\Aura;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

trait AuraPdoTrait
{
    protected ?ExtendedPdo $pdo;
    protected QueryFactory $queryFactory;

    /**
     * @return ExtendedPdo
     */
    public function getPdo(): ExtendedPdo
    {
        return $this->pdo;
    }

    public function setPdo(ExtendedPdo $pdo): void
    {
        $this->pdo = $pdo;
    }

    protected function initAuraPdo(?ExtendedPdo $pdo = null, QueryFactory $queryFactory = null)
    {
        $this->pdo = $pdo;

        if (!$queryFactory) {
            $queryFactory = new QueryFactory('pgsql');
        }

        $this->queryFactory = $queryFactory;
    }

    /**
     * @return \Aura\SqlQuery\Pgsql\Select
     */
    protected function newSelect()
    {
        return $this->queryFactory->newSelect();
    }

    /**
     * @return \Aura\SqlQuery\Pgsql\Update
     */
    protected function newUpdate()
    {
        return $this->queryFactory->newUpdate();
    }

    /**
     * @return \Aura\SqlQuery\Pgsql\Insert
     */
    protected function newInsert()
    {
        return $this->queryFactory->newInsert();
    }

    /**
     * @return \Aura\SqlQuery\Pgsql\Delete
     */
    protected function newDelete()
    {
        return $this->queryFactory->newDelete();
    }

    /**
     * @param string $prefix
     * @param array $fields
     * @return array
     */
    protected static function prefixedFields($prefix, array $fields)
    {
        return array_map(function ($entry) use ($prefix) {

            return "{$prefix}.{$entry}";
        }, $fields);
    }

    protected static function explodeArrayValue($value)
    {
        if ($value === null) {
            return null;
        }

        $res = [];

        if (preg_match('/^{(.+)}$/', $value, $matches)) {
            $res = str_getcsv($matches[1]);
        }

        return $res;
    }

    protected static function implodeArrayValue(?array $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return sprintf("{%s}", implode(',', $value));
    }

    protected static function formatDateTimeSqlValue(\DateTimeInterface $date_time = null)
    {
        if (!$date_time) {
            return null;
        }

        return $date_time->format('Y-m-d H:i:s');
    }

}
