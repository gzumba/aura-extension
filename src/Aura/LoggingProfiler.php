<?php

namespace Zumba\Aura;

use Aura\Sql\Profiler;
use Zumba\Log\LoggingI;
use Zumba\Log\LoggingTrait;

class LoggingProfiler extends Profiler implements LoggingI
{
    use LoggingTrait;

    public function addProfile(
        $duration,
        $function,
        $statement,
        array $bind_values = array()
    ) {

        parent::addProfile($duration, $function, $statement, $bind_values);

        if ($function === 'prepare') {
            return null;
        }

        $this->logDebug(
            "[{duration} ms][{function}] {sql}",
            array_merge(['duration' => (int)(1000*$duration), 'function' => $function, 'sql' => $statement], $bind_values)
        );

        return null;
    }
}
