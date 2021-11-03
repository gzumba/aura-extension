<?php
declare(strict_types=1);
namespace Zumba\Db;

use Zumba\Aura\ExtendedPdoWithExceptions;

class ConnectionFactory
{
    private array $configs = [];
    /** @var array|ExtendedPdoWithExceptions[] */
    private array $connections = [];

	public static function createFromIniFile(?string $ini_file = null): ConnectionFactory
	{
		$ini_file ??= sprintf("%s/.pg_service.conf", getenv('HOME'));

		$ini = parse_ini_file($ini_file, true);

		return self::createFromServiceConfig($ini);
	}

    public static function createFromServiceConfig(array $ini): ConnectionFactory
    {
        $cf = new self();
        foreach ($ini as $name => $config) {
            $cf->configs[$name] = $config;
        }

        return $cf;
    }

    public function getPdoFor(string $site): ExtendedPdoWithExceptions
    {
        if (!array_key_exists($site, $this->configs)) {
            throw new \DomainException("Site {$site} does not exist, available sites: " . implode(', ', array_keys($this->configs)));
        }

        return $this->connections[$site] ??=
            $this->buildConnection($this->configs[$site]);
    }

    private function buildConnection(array $config): ExtendedPdoWithExceptions
    {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s",
            $config['host'],
            $config['port'] ?? '5432',
            $config['dbname']
        );

        $options = [
            \PDO::ATTR_PERSISTENT => true,
        ];

        return new ExtendedPdoWithExceptions($dsn, $config['user'], $config['password'], $options, []);
    }
}