<?php

namespace Zumba\Db\Exception;

use Throwable;

class DbUniqueViolation extends DBException
{
    private $constraint;
    private $field;
    private $value;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->setFieldsFrom($message);
    }

    /*
     * Parse the message that is formatted as:
     *     SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "campaign_slug"\n
     *   DETAIL:  Key (slug)=(will-be-duplicated) already exists.
     */
    private function setFieldsFrom(string $message): void
    {
        \Safe\preg_match('|constraint "([^"]+)"[^(]+\\(([^)]+)\\)[^(]+\\(([^)]+)\\)|', $message, $matches);

        if (count($matches) === 4) {
            $this->constraint = $matches[1];
            $this->field = $matches[2];
            $this->value = $matches[3];
        }
    }

    /**
     * @return mixed
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}
