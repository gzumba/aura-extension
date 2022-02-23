<?php
declare(strict_types=1);
namespace Zumba\Aura;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\Parser\ParserInterface;
use Aura\Sql\Profiler\ProfilerInterface;
use Zumba\Aura\Parser\PgsqlParser;
use Zumba\Db\Exception\DBException;

class ExtendedPdoWithExceptions extends ExtendedPdo
{
    public function __construct(
        string $dsn,
        $username = null,
        $password = null,
        array $options = [],
        array $attributes = [],
        ProfilerInterface $profiler = null
    ) {
        $options[\PDO::ATTR_PERSISTENT] = true;

        parent::__construct($dsn, $username, $password, $options, $attributes, $profiler);
    }

    public static function fromPdo(\PDO $pdo): self
    {
        $p = new self('');

        $p->pdo  = $pdo;

        return $p;
    }

    protected function newParser(string $driver): ParserInterface
    {
        return new PgsqlParser();
    }

    /**
     * @param string|\Stringable $statement
     * @throws \Aura\Sql\Exception\CannotBindValue
     * @throws DBException
     */
    public function perform($statement, array $values = []): \PDOStatement
    {
        try {
            return parent::perform((string) $statement, $values);
        } catch (\PDOException $e) {
            $db_exception = DBException::createFromPdoException($e);
            $db_exception->setQueryString((string)$statement);
            throw $db_exception;
        }
    }

    /**
     * @param string|\Stringable $statement
     */
    public function fetchValue($statement, array $values = [])
    {
        return parent::fetchValue((string) $statement, $values);
    }

    /**
     * @param string|\Stringable $statement
     */
    public function fetchCol($statement, array $values = []): array
    {
        return parent::fetchCol((string) $statement, $values);
    }

    public function fetchPairs($statement, array $values = []): array
    {
        return parent::fetchPairs((string) $statement, $values);
    }

    public function fetchAll($statement, array $values = []): array
    {
        return parent::fetchAll((string) $statement, $values);
    }

    public function yieldAll($statement, array $values = []): \Generator
    {
        return parent::yieldAll((string) $statement, $values);
    }


}
