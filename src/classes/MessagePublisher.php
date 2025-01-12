<?php

namespace Kosar501\PhpWebSocket\classes;

use ZMQ;
use ZMQContext;

class MessagePublisher
{
    protected $socket;
    protected $context;
    protected $server;

    /**
     * @throws \ZMQSocketException
     */
    public function __construct()
    {

        // ZeroMQ Configurations
        $zmqHost = config('zeromq.host', '127.0.0.1'); // Retrieve from config, fallback to default
        $zmqPort = config('zeromq.port', 5555);        // Retrieve from config, fallback to default

        if (!$zmqHost or !$zmqPort) {
            throw new \InvalidArgumentException("zeromq.php host or port is not defined in the configuration.");
        }
        $this->server = 'tcp://' . $zmqHost . ':' . $zmqPort;

        $this->context = new ZMQContext();
        $this->socket = $this->context->getSocket(ZMQ::SOCKET_PUSH); // Use PUSH socket to send messages
    }

    /**
     * @throws \ZMQSocketException
     */
    public function sendMessage($message)
    {
        $this->connect();
        $this->socket->send($message);
        echo 'message sent.' . PHP_EOL;
        $this->disconnect();
    }

    private function connect()
    {
        try {
            $this->socket->connect($this->server);  // Connect to the same ZeroMQ address
            echo "Connected to ZeroMQ.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function disconnect()
    {
        try {
            // Ensure the socket is properly closed
            $this->socket->disconnect($this->server);
            $this->socket = null;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
