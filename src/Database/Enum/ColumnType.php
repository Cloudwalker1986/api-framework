<?php

declare(strict_types=1);

namespace ApiCore\Database\Enum;

enum ColumnType:string
{
    case CHAR = 'char';
    case BINARY = 'binary';
    case VARBINARY = 'varbinary';
    case VARCHAR = 'varchar';
    case TINYINT = 'tinyint';
    case SMALLINT = 'smallint';
    case MEDIUMINT = 'mediumInt';
    case INT = 'int';
    case BIGINT = 'bigint';
    case DECIMAL = 'decimal';
    case ENUM = 'enum';
    case BLOB = 'blob';

    case DATETIME = 'datetime';

    public static function getDecimal(): string
    {
        return self::DECIMAL->value;
    }
}
