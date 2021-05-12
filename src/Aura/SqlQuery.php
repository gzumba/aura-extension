<?php
declare(strict_types=1);

namespace Zumba\Aura;

use Aura\SqlQuery\QueryInterface;

/**
 * @method string getStatement()
 */
class SqlQuery implements QueryInterface
{
    private string $sql;
    private array $bind_values;

    public function __construct(string $sql, array $bind_values)
    {
        $this->sql = $sql;
        $this->bind_values = $bind_values;
    }

    /**
     *
     * Builds this query object into a string.
     *
     * @return string
     *
     */
    public function __toString()
    {
        return $this->sql;
    }

    /**
     *
     * Returns the prefix to use when quoting identifier names.
     *
     * @return string
     *
     */
    public function getQuoteNamePrefix()
    {
        // TODO: Implement getQuoteNamePrefix() method.
    }

    /**
     *
     * Returns the suffix to use when quoting identifier names.
     *
     * @return string
     *
     */
    public function getQuoteNameSuffix()
    {
        // TODO: Implement getQuoteNameSuffix() method.
    }

    /**
     *
     * Adds values to bind into the query; merges with existing values.
     *
     * @param array $bind_values Values to bind to the query.
     *
     * @return $this
     *
     */
    public function bindValues(array $bind_values)
    {

    }

    /**
     *
     * Binds a single value to the query.
     *
     * @param string $name The placeholder name or number.
     *
     * @param mixed $value The value to bind to the placeholder.
     *
     * @return $this
     *
     */
    public function bindValue($name, $value)
    {
        // TODO: Implement bindValue() method.
    }

    /**
     *
     * Gets the values to bind into the query.
     *
     * @return array
     *
     */
    public function getBindValues()
    {
        return $this->bind_values;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getStatement()
    }
}