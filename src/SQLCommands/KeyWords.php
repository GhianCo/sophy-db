<?php

namespace SophyDB\SQLCommands;

final class Keywords {

    const SELECT = 0x01;
    const FIELDS = 0x02;
    const ALL = 0x03;
    const DISTINCT = 0x04;
    const DISTINCTROW = 0x05;
    const HIGH_PRIORITY = 0x06;
    const STRAIGHT_JOIN = 0x07;
    const FROM = 0x08;
    const JOIN = 0x09;
    const WHERE = 0x10;
    const GROUP_BY = 0x12;
    const HAVING = 0x13;
    const ORDER_BY = 0x14;
    const LIMIT = 0x15;
    const OFFSET = 0x16;
    const UNION = 0x17;

    private static $keywords = [
        'SELECT'        => self::SELECT,
        'FIELDS'        => self::FIELDS,
        'ALL'           => self::ALL,
        'DISTINCT'      => self::DISTINCT,
        'DISTINCTROW'   => self::DISTINCTROW,
        'HIGH_PRIORITY' => self::HIGH_PRIORITY,
        'STRAIGHT_JOIN' => self::STRAIGHT_JOIN,
        'FROM'          => self::FROM,
        'JOIN'          => self::JOIN,
        'WHERE'         => self::WHERE,
        'GROUP_BY'      => self::GROUP_BY,
        'HAVING'        => self::HAVING,
        'ORDER_BY'      => self::ORDER_BY,
        'LIMIT'         => self::LIMIT,
        'OFFSET'        => self::OFFSET,
        'UNION'         => self::UNION,
    ];

    public static function get($key = null) {
        if ($key === null) {
            return self::$keywords;
        } else {
            return self::$keywords[$key];
        }
    }
}