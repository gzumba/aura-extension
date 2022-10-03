<?php
declare(strict_types=1);

namespace Aura;

use Zumba\Aura\ExtendedPdoWithExceptions;
use PHPUnit\Framework\TestCase;
use Zumba\Db\Exception\DBException;

class ExtendedPdoWithExceptionsTest extends TestCase
{
    private ExtendedPdoWithExceptions $pdo;

    public function setUp(): void
    {
        parent::setUp();

        $this->pdo = new ExtendedPdoWithExceptions('dummy-dsn');
    }

    public function testFailureToConnectThrowsDBException(): void
    {
        self::expectException(DBException::class);
        $this->pdo->perform('SELECT 1');
    }

    public function testFromPdoWorks(): void
    {
        $inner_pdo = new \PDO('pgsql:dbname=test');

        $pdo = ExtendedPdoWithExceptions::fromPdo($inner_pdo);
        self::assertSame($inner_pdo, $pdo->getPdo());
    }
}
