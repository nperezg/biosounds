<?php

namespace BioSounds\Service\Queue;

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

    /**
     * RabbitQueueService constructor.
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(getenv('QUEUE_HOST'), getenv('QUEUE_PORT'), QUEUE_USER, QUEUE_PASSWORD);
        $this->channel = $this->connection->channel();
    }

    /**
     * @param int $fileId
     */
    public function add(int $fileId)
    {
        $this->channel->queue_declare(QUEUE_NAME, false, true, false, false);

        $message = new AMQPMessage(
            $fileId,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, '', QUEUE_NAME);
    }

    /**
     * @throws \Exception
     */
    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
