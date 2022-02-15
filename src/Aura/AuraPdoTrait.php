<?php
namespace Zumba\Aura;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\Pgsql\Delete;
use Aura\SqlQuery\Pgsql\Insert;
use Aura\SqlQuery\Pgsql\Select;
use Aura\SqlQuery\Pgsql\Update;
use Aura\SqlQuery\QueryFactory;
use Zumba\Db\PgFormatterTrait;

trait AuraPdoTrait
{
    use PgFormatterTrait;
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

    protected function initAuraPdo(?ExtendedPdo $pdo = null, QueryFactory $queryFactory = null): void
    {
        $this->pdo = $pdo;

        if (!$queryFactory) {
            $queryFactory = new CastAwareQueryFactory('pgsql');
        }

        $this->queryFactory = $queryFactory;
    }

    protected function newSelect(): Select
    {
        return $this->queryFactory->newSelect();
    }

    protected function newUpdate(): Update
    {
        return $this->queryFactory->newUpdate();
    }

    protected function newInsert(): Insert
    {
        return $this->queryFactory->newInsert();
    }

    protected function newDelete(): Delete
    {
        return $this->queryFactory->newDelete();
    }

    protected static function prefixedFields(string $prefix, array $fields): array
    {
        return array_map(function ($entry) use ($prefix) {

            return "{$prefix}.{$entry}";
        }, $fields);
    }

}
