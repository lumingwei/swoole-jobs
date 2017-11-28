<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

date_default_timezone_set('Asia/Shanghai');

require __DIR__ . '/../vendor/autoload.php';

use Kcloze\Jobs\Jobs;
use Kcloze\Jobs\Logs;
use Kcloze\Jobs\Process;
use Kcloze\Jobs\Queue\RedisTopicQueue;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
//$redis->auth('xxx');
//$redis->select(1); //尽量不要和缓存使用同一个 db, 方便管理
$redisTopicQueue = new RedisTopicQueue($redis);

$logPath = __DIR__ . '/../log'; // 日志路径
$log     = new Logs($logPath);

$jobConfig = [
    'topics'   => ['MyJob', 'MyJob2'], // topics, 默认值 []
];
$jobs = new Jobs($redisTopicQueue, $log, $jobConfig);

//启动
$process       = new Process();
$processConfig = [
    'pidPath'      => $logPath,
    'workerNum'    => 5, // 工作进程数, 默认值 5
    'processName'  => ':swooleTopicQueue', // 设置进程名, 方便管理, 默认值 swooleTopicQueue
];
$process->start($jobs, $processConfig);
