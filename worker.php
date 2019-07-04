<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use BioSounds\Service\FileService;

try {
    $connection = new AMQPStreamConnection('localhost', 5672, 'bioSounds', 'mAsr0xv18');
    $channel = $connection->channel();

    $channel->queue_declare('biosounds_file_upload', false, true, false, false);

    echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

    $config = parse_ini_file('config/config.ini');

    define('ABSOLUTE_DIR', $config['ABSOLUTE_DIR']);
    define('TMP_DIR', $config['TMP_DIR']);
    define('DRIVER', $config['DRIVER']);
    define('HOST', $config['HOST']);
    define('DATABASE', $config['DATABASE']);
    define('USER', $config['USER']);
    define('PASSWORD', $config['PASSWORD']);

    $callback = function($msg) use ($config) {
        echo ' [x] Received file id: ' . $msg->body , "\n";
        (new FileService())->process($msg->body);
        sleep(substr_count($msg->body, '.'));
        echo " [x] Done", "\n";
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    };

    $channel->basic_qos(null, 1, null);
    $channel->basic_consume('biosounds_file_upload', '', false, false, false, false, $callback);

    while(count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
} catch (\Exception $exception) {
    error_log($exception->getMessage());
    echo($exception->getMessage());
}
