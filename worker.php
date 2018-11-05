<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Hybridars\BioSounds\Service\FileService;
use Hybridars\BioSounds\Database\Database;

try {
    $connection = new AMQPStreamConnection('localhost', 5672, 'bioSounds', 'mAsr0xv18');
    $channel = $connection->channel();

    $channel->queue_declare('biosounds_file_upload', false, true, false, false);

    echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

    $config = parse_ini_file('config/config.ini');

    define('ABSOLUTE_DIR', $config['ABSOLUTE_DIR']);
    define('TMP_DIR', $config['TMP_DIR']);

    $callback = function($msg) use ($config) {
        Database::$connection = new \PDO(
            $config['DRIVER'].':host='.$config['HOST'].';dbname='.$config['DATABASE'],
            $config['USER'],
            $config['PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        echo ' [x] Received file id: ' . $msg->body , "\n";
        (new FileService())->process($msg->body);
        Database::$connection = null;
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
