<?php declare(strict_types=1);

/*
 * This file is part of EventStore.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\eventstore;

use PHPUnit\Framework\TestCase;

class ProvideQueries
{
    private static $comparator;
    private static $class;

    public static function setComparator(string $comparator): void
    {
        self::$comparator = $comparator;
    }

    public static function setClass(string $class): void
    {
        self::$class = $class;
    }

    protected static function initialize(): void
    {
    }

    public static function provideQueries(): array
    {
        static::initialize();

        return [
            'correlation id' => [
                'SELECT topic, event FROM events WHERE correlationId=:correlationId',

                new self::$class(
                    null,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    null
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                }
            ],

            'correlation id + limit' => [
                'SELECT topic, event FROM events WHERE correlationId=:correlationId LIMIT 7',

                new self::$class(
                    7,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    null
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                }
            ],

            'correlation id + topics' => [
                'SELECT topic, event FROM events WHERE correlationId=:correlationId AND topic IN (:topic1,:topic2)',

                new self::$class(
                    null,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    null,
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'correlation id + topics + limit' => [
                'SELECT topic, event FROM events WHERE correlationId=:correlationId AND topic IN (:topic1,:topic2) LIMIT 7',

                new self::$class(
                    7,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    null,
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'position' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE id%s(SELECT id FROM events WHERE eventId=:eventId)',
                    self::$comparator
                ),

                new self::$class(
                    null,
                    null,
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertEventId($spy, $test);
                }
            ],

            'position + limit' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE id%s(SELECT id FROM events WHERE eventId=:eventId) LIMIT 8',
                    self::$comparator
                ),

                new self::$class(
                    8,
                    null,
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertEventId($spy, $test);
                }
            ],

            'position + correlation id' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE correlationId=:correlationId AND id%s(SELECT id FROM events WHERE eventId=:eventId)',
                    self::$comparator
                ),

                new self::$class(
                    null,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertEventId($spy, $test);
                }
            ],

            'position + correlation id + limit' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE correlationId=:correlationId AND id%s(SELECT id FROM events WHERE eventId=:eventId) LIMIT 9',
                    self::$comparator
                ),

                new self::$class(
                    9,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertEventId($spy, $test);
                }
            ],

            'position + correlation id + topics' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE correlationId=:correlationId AND id%s(SELECT id FROM events WHERE eventId=:eventId) AND topic IN (:topic1,:topic2)',
                    self::$comparator
                ),

                new self::$class(
                    null,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertEventId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'position + correlation id + topics + limit' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE correlationId=:correlationId AND id%s(SELECT id FROM events WHERE eventId=:eventId) AND topic IN (:topic1,:topic2) LIMIT 2',
                    self::$comparator
                ),

                new self::$class(
                    2,
                    TestCorrelationId::from('b83262fc-8927-4e4d-ba26-4d5726a8ccd2'),
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertCorrelationId($spy, $test);
                    $test->assertEventId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'position + topics' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE id%s(SELECT id FROM events WHERE eventId=:eventId) AND topic IN (:topic1,:topic2)',
                    self::$comparator
                ),

                new self::$class(
                    null,
                    null,
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertEventId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'position + topics + limit' => [
                sprintf(
                    'SELECT topic, event FROM events WHERE id%s(SELECT id FROM events WHERE eventId=:eventId) AND topic IN (:topic1,:topic2) LIMIT 11',
                    self::$comparator
                ),

                new self::$class(
                    11,
                    null,
                    EventId::from('52749ba8-0b87-4aa7-9416-5ccb860cc8a6'),
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertEventId($spy, $test);
                    $test->assertTopics($spy, $test);
                }
            ],

            'topics' => [
                'SELECT topic, event FROM events WHERE topic IN (:topic1,:topic2)',

                new self::$class(
                    null,
                    null,
                    null,
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertTopics($spy, $test);
                }
            ],

            'topics + limit' => [
                'SELECT topic, event FROM events WHERE topic IN (:topic1,:topic2) LIMIT 13',

                new self::$class(
                    13,
                    null,
                    null,
                    'topic-1',
                    'topic-2'
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {
                    $test->assertTopics($spy, $test);
                }
            ],

            'limit' => [
                'SELECT topic, event FROM events LIMIT 12',

                new self::$class(
                    12,
                    null,
                    null
                ),

                function(SQLite3StmtSpy $spy, TestCase $test) {}
            ],

        ];
    }
}
