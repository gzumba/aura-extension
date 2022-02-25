<?php
declare(strict_types=1);
namespace Zumba\Aura;

use Aura\Sql\Profiler\Profiler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SymfonyProfiler extends Profiler
{
    private ?Stopwatch $stopwatch;
    private int $cnt = 1;
    private string $watch;
    private array $profiles = [];

    public function __construct(?Stopwatch $stopwatch = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->stopwatch = $stopwatch;
        $this->logFormat = "[{duration} ms][{function}] {statement}";
    }

    /**
     *
     * Starts a profile entry.
     *
     * @param string $function The function starting the profile entry.
     *
     * @return void
     */
    public function start(string $function): void
    {
        $this->startStopwatch();
        parent::start($function);
    }


    public function finish(?string $statement = null, array $values = []): void
    {
        $this->stopStopwatch();

        $profile = $this->buildProfile($statement, $values);

        $this->logEntry($profile);
        $this->addProfile($profile);
    }

    private function logEntry(array $profile): void
    {
        $this->logger->log($this->logLevel, $this->logFormat, $profile);

        $this->context = [];
    }

    private function startStopwatch(): void
    {
        if (null !== $this->stopwatch) {
            $this->watch = sprintf("AuraSQL #%d", $this->cnt);
            $this->stopwatch->start($this->watch, 'Aura');
        }
    }

    private function stopStopwatch()
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->stop($this->watch);
            $this->cnt++;
        }
    }

    public function getProfiles(): array
    {
        return $this->profiles;
    }

    public function resetProfiles()
    {
        $this->profiles = [];
    }

    private function addProfile(array $profile)
    {
        $this->profiles[] = $profile;
    }

    private function buildProfile(?string $statement, array $values): array
    {
        $finish = microtime(true);
        $profile = [
            'duration' => (int)(1000 * ($finish - $this->context['start'])),
            'function' => $this->context['function'],
            'statement' => $statement,
            'bind_values' => $values,
        ];
        return $profile;
    }

}
