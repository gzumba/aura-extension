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

}
