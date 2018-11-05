<?php

namespace Hybridars\BioSounds\Service\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitQueueService
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'bioSounds', 'mAsr0xv18');
        $this->channel = $this->connection->channel();
    }

    public function add(int $fileId)
    {
        $this->channel->queue_declare('biosounds_file_upload', false, true, false, false);

        $message = new AMQPMessage(
            $fileId,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, '', 'biosounds_file_upload');
    }

    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }
}