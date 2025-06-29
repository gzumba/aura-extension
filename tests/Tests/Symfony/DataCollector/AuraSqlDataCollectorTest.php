<?php

declare(strict_types=1);

namespace Tests\Symfony\DataCollector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Zumba\Aura\SymfonyProfiler;
use Zumba\Symfony\DataCollector\AuraSqlDataCollector;

#[CoversClass(AuraSqlDataCollector::class)]
class AuraSqlDataCollectorTest extends TestCase
{
    private AuraSqlDataCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $stopwatch = new Stopwatch();
        $profiler = new SymfonyProfiler($stopwatch);
        $this->collector = new AuraSqlDataCollector($profiler);
    }

    public function testEmptyCollect()
    {
        $request = new Request();
        $response = new Response();
        $this->collector->collect($request, $response);

        self::assertCount(0, $this->collector->getProfiles());
    }
}
