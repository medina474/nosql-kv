<?php
declare(strict_types=1);

return [
  [
    'name'          => 'Redis Master',
    'host'          => 'redis-master',
    'port'          => 6379,
    'auth'          => null,
    'timeout'       => 2.5,
    'retryInterval' => 100,
    'databaseMap'   => [],
  ],
  [
    'name'          => 'Redis Replicata',
    'host'          => 'redis-replica',
    'port'          => 6379,
    'auth'          => null,
    'timeout'       => 2.5,
    'retryInterval' => 100,
    'databaseMap'   => [],
  ],
];
