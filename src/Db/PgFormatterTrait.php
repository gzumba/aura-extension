<?php

namespace Zumba\Db;

trait PgFormatterTrait
{
    private function formatArraySql(array $array)
    {
        return '{' . implode(',', array_map([$this, 'formatSql'], $array)) . '}';
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function formatFieldSql($value): ?string
    {
        if ($value instanceof  \DateTimeInterface) {
            return $value->format('c');
        }

        if (is_array($value)) {
            return $this->formatArraySql($value);
        }

        if ($value === null) {
            return null;
        }

        return (string)$value;
    }

    /**
     * Build fields writable to Postgres.
     *
     * @return array
     */
    public function formatSql()
    {
        $data = [];

        foreach ($this->getDbFields() as $field) {
            switch (self::resolveFieldType($field)) {
                case 'json':
                    $data[$field] = $this->formatFieldSql(\GuzzleHttp\json_encode($this->$field));
                    break;
                case 'basic':
                default:
                    $data[$field] = $this->formatFieldSql($this->$field);
                    break;
            }
        }

        return $data;
    }

    public static function createFromDbRow(array $row)
    {
        $fields = [];

        foreach ($row as $key => $value) {
            if ($value === null) {
                continue;
            }
            switch (self::resolveFieldType($key)) {
                case 'json':
                    $fields[$key] = \GuzzleHttp\json_decode($value, true);
                    break;
                case 'basic':
                default:
                    $fields[$key] = $value;
                    break;
            }
        }

        return new static($fields);
    }

    private static function resolveFieldType($field)
    {
        return self::$special_field_types[$field] ?? 'basic';
    }

    abstract public function getDbFields(): array;
}
