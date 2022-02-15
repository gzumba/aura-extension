<?php
declare(strict_types=1);
namespace Zumba\Db;

trait PgFormatterTrait
{
    private function formatArraySql(array $array): string
    {
        return '{' . implode(',', array_map(fn ($value) => $this->formatSql($value), $array)) . '}';
    }

    protected static function formatDateTimeSqlValue(\DateTimeInterface $date_time = null): ?string
    {
        if (!$date_time) {
            return null;
        }

        return $date_time->format('Y-m-d H:i:s');
    }

    private function formatSql($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return self::formatDateTimeSqlValue($value);
        }

        if (is_array($value)) {
            return $this->formatArraySql($value);
        }

        if ($value === null) {
            return 'null';
        }

        return (string)$value;
    }

    protected static function explodeArrayValue(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $res = [];

        if (preg_match('/^{(.+)}$/', $value, $matches)) {
            $res = str_getcsv($matches[1]);
        }

        return $res;
    }
}
