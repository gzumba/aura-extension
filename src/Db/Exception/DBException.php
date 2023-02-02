<?php
declare(strict_types=1);
namespace Zumba\Db\Exception;

use Throwable;
use function Symfony\Component\String\u;

class DBException extends \RuntimeException
{

    /**
     * @var int|string
     */
    private $original_code;
    protected ?string $query_string;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, (int)$code, $previous);
        $this->original_code = $code;
    }

    /**
     * Factory for Postgresql errors
     * @param \PDOException $pdoException
     * @return DBException
     */
    public static function createFromPdoException(\PDOException $pdoException): DBException
    {
        switch ($pdoException->getCode()) {
            case '23505':
                return new DbUniqueViolation($pdoException->getMessage(), $pdoException->getCode(), $pdoException);
            case '23502':
                return new DbNotNullViolation($pdoException->getMessage(), $pdoException->getCode(), $pdoException);
            case '23503':
                return new DbForeignKeyViolation($pdoException->getMessage(), $pdoException->getCode(), $pdoException);
        }

        // NOTE: We check for '08' and '8' as the codes starting with '08' are a bit annoying to handle as ints
        if (u($pdoException->getCode())->startsWith(['57', '08', '8'])) {
            return new DbConnectionFailure($pdoException->getMessage(), $pdoException->getCode(), $pdoException);
        }

        return new self($pdoException->getMessage(), $pdoException->getCode(), $pdoException);
    }

    public function setQueryString(?string $query_string): DBException
    {
        $this->query_string = $query_string;

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->query_string;
    }

}
