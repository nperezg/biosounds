<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use BioSounds\Service\FileService;

try {
    $config = parse_ini_file('config/config.ini');

    define('ABSOLUTE_DIR', $config['ABSOLUTE_DIR']);
    define('TMP_DIR', $config['TMP_DIR']);
    define('DRIVER', $config['DRIVER']);
    define('HOST', $config['HOST']);
    define('DATABASE', $config['DATABASE']);
    define('USER', $config['USER']);
    define('PASSWORD', $config['PASSWORD']);
    define('QUEUE_NAME', $config['QUEUE_NAME']);
    define('QUEUE_HOST', $config['QUEUE_HOST']);
    define('QUEUE_PORT', $config['QUEUE_PORT']);
    define('QUEUE_USER', $config['QUEUE_USER']);
    define('QUEUE_PASSWORD', $config['QUEUE_PASSWORD']);

    $connection = new AMQPStreamConnection(QUEUE_HOST, QUEUE_PORT, QUEUE_USER, QUEUE_PASSWORD);

    $channel = $connection->channel();
    $channel->queue_declare($config['QUEUE_NAME'], false, true, false, false);

    echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

    $callback = function($msg) use ($config) {
        echo ' [x] Received file id: ' . $msg->body , "\n";
        (new FileService())->process($msg->body);
        sleep(substr_count($msg->body, '.'));
        echo " [x] Done", "\n";
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    };

    $channel->basic_qos(null, 1, null);
    $channel->basic_consume($config['QUEUE_NAME'], '', false, false, false, false, $callback);

    while(count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
} catch (\Exception $exception) {
    error_log($exception->getMessage());
    echo($exception->getMessage());
}
