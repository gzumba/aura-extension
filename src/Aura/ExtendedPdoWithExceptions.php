<?php
declare(strict_types=1);
namespace Zumba\Aura;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ProfilerInterface;
use Zumba\Db\Exception\DBException;

class ExtendedPdoWithExceptions extends ExtendedPdo
{
    public function __construct(
        $dsn,
        $username = null,
        $password = null,
        array $options = [],
        array $attributes = [],
        ProfilerInterface $profiler = null
    ) {
        $options[\PDO::ATTR_PERSISTENT] = true;

        parent::__construct($dsn, $username, $password, $options, $attributes);
        if ($profiler) {
            $this->setProfiler($profiler);
        }
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function perform($statement, array $values = [])
    {
        try {
            return parent::perform($statement, $values);
        } catch (\PDOException $e) {
            $db_exception = DBException::createFromPdoException($e);
            $db_exception->setQueryString((string)$statement);
            throw $db_exception;
        }
    }
}
