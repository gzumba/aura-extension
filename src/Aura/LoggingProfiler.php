<?php
declare(strict_types=1);
namespace Zumba\Aura;

use Aura\Sql\Profiler\Profiler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class LoggingProfiler extends Profiler
{
    private ?Stopwatch $stopwatch;
    private bool $isPrepared = false;
    private int $cnt = 1;

    public function __construct(?Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function addProfile(
        $duration,
        $function,
        $statement,
        array $bind_values = array()
    ) {

        parent::addProfile($duration, $function, $statement, $bind_values);

        $this->addStopwatchEntry($function);

        if ($function === 'prepare') {
            return null;
        }

        $this->logDebug(
            "[{duration} ms][{function}] {sql}",
            array_merge(['duration' => (int)(1000*$duration), 'function' => $function, 'sql' => $statement], $bind_values)
        );

        return null;
    }

    private function addStopwatchEntry(string $function)
    {
        if (null !== $this->stopwatch) {

            $watch = sprintf("AuraSQL #%d", $this->cnt);

            if ($function === 'prepare') {
                $this->isPrepared = true;
                $this->stopwatch->start($watch, 'Aura');

            } elseif ($this->isPrepared) {
                $this->isPrepared = false;
                $this->stopwatch->stop($watch);
                $this->cnt++;
            }
        }
    }

    public function setLogger($logger): void
    {
        $this->logger = $logger;
    }
}
