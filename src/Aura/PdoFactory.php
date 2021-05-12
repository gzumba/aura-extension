<?php
declare(strict_types=1);
namespace Zumba\Aura;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ProfilerInterface;

class PdoFactory
{
    private ?ExtendedPdoWithExceptions $pdo = null;
    private ?ProfilerInterface $profiler;
    private string $db_host;
    private string $db_port;
    private string $db_name;
    private string $username;
    private string $password;

    public function __construct(string $db_host, string $db_port, string $db_name, ProfilerInterface $profiler = null)
    {
        $this->profiler = $profiler;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        $this->db_name = $db_name;
        $this->username = $this->password = 'unset';
    }

    public function setCredentials(string $username, string $password): void
    {
        $this->username = $username;
        $this->password = $password;
        if ($this->pdo) {
            $this->pdo->setUsername($username);
            $this->pdo->setPassword($password);
        }
    }

    public function getPdo(): ExtendedPdo
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        $dsn = sprintf(
          "pgsql:host=%s;port=%s;dbname=%s",
          $this->db_host,
          $this->db_port,
          $this->db_name
        );

        $options = [
            \PDO::ATTR_PERSISTENT => true,
        ];

        $this->pdo = new ExtendedPdoWithExceptions($dsn, $this->username, $this->password, $options, []);

        if ($this->profiler) {
            $this->pdo->setProfiler($this->profiler);
        }

        return $this->pdo;
    }
}
