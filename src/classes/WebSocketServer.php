<?php

namespace Kosar501\PhpWebSocket\classes;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class WebSocketServer implements MessageComponentInterface
{
    protected $clients;


    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";

        // Start a separate thread to handle ZeroMQ messages
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message from {$from->resourceId}";

    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * @param $message
     */
    public function broadcast($message)
    {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}
