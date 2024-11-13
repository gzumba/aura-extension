<?php

declare(strict_types=1);

namespace Tests\Aura;

use Aura\Sql\ExtendedPdo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Zumba\Aura\ExtendedPdoWithExceptions;
use Zumba\Db\Exception\DBException;

#[CoversClass(ExtendedPdoWithExceptions::class)]
class ExtendedPdoWithExceptionsTest extends TestCase
{
    private ExtendedPdo $pdo;

    protected function setUp(): void
    {
        $this->pdo = new ExtendedPdoWithExceptions(
            dsn: 'pgsql:host=localhost',
        );
//        $this->pdo = new ExtendedPdo(
//            'sqlite::memory:'
//        );
    }

    public function testPerformThrowsDBExceptions(): void
    {
        self::expectException(DBException::class);
        $this->pdo->perform('error');
    }
}
