<?php

namespace Zumba\Db;

use Psr\Log\LoggerAwareInterface;
use Zumba\Log\LoggingTrait;

class PGWrapper implements LoggerAwareInterface
{
    use LoggingTrait;

    /**
     * @param string $pg_conn_str
     * @param string|null $connect_type
     * @return resource|false
     */
    public function pconnect($pg_conn_str, ?string $connect_type = null)
    {
        if ($connect_type) {
            return pg_pconnect($pg_conn_str, $connect_type);
        }

        return pg_pconnect($pg_conn_str);
    }

    public function query($string)
    {
        $res = @pg_query($string);

        if ($res === false) {
            $this->logErr("Could not execute query ({reason}) {sql}", ['sql' => $string, 'reason' => pg_last_error()]);
        }

        return $res;
    }

    public function prepare($string, $sql)
    {
        $this->logDebug("Preparing {sql}", ['sql' => $sql]);

        return pg_prepare($string, $sql);
    }

    /**
     * @param string $statement_name
     * @param array $params
     * @return resource|false
     */
    public function execute($statement_name, $params)
    {
        $res = pg_execute($statement_name, $params);

        if ($res === false) {
            $this->logErr("Could not execute query ({reason})", ['reason' => pg_last_error()]);
        }

        return $res;
    }

    public function put_line($data)
    {
        return pg_put_line($data);
    }

    public function end_copy()
    {
        $res = @pg_end_copy();

        if ($res === false) {
            $this->logErr("Could not end the copy ({reason}) {sql}", ['reason' => pg_last_error()]);
        }

        return $res;
    }

    public function affected_rows($res)
    {
        return pg_affected_rows($res);
    }

    public function pg_fetch_all_columns($resource, $column = 0)
    {
        return pg_fetch_all_columns($resource, $column);
    }
}
