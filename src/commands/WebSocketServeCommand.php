<?php

namespace Kosar501\PhpWebSocket\commands;

use Kosar501\PhpWebSocket\classes\Pusher;
use Kosar501\PhpWebSocket\classes\WebSocketServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as EventLoopFactory;
use React\ZMQ\Context as ReactZMQContext;
use React\Socket\Server as ReactSocketServer;
use ZMQ;

class WebSocketServeCommand
{
    public static function serve()
    {
        try {
            // PhpWebSocket Configurations
            $host = config('websocket.host', '0.0.0.0'); // Retrieve from config, fallback to default
            $port = config('websocket.port', 8080);      // Retrieve from config, fallback to default

            if (!$host || !$port) {
                throw new \InvalidArgumentException("PhpWebSocket host or port is not defined in the configuration.");
            }


            // Create the shared event loop
            $loop = EventLoopFactory::create();

            // Instantiate the PhpWebSocket handler (Pusher or WebSocketServer)
            $wsHandler =  new WebSocketServer();

            // Create the PhpWebSocket server
            $webSocket = new ReactSocketServer("$host:$port", $loop);
            $server = new IoServer(
                new HttpServer(
                    new WsServer($wsHandler)
                ),
                $webSocket,
                $loop
            );

            echo "PhpWebSocket server running at ws://$host:$port\n";

            // Set up ZeroMQ for message broadcasting
            // ZeroMQ Configurations
            $zmqHost = config('zeromq.host', '127.0.0.1'); // Retrieve from config, fallback to default
            $zmqPort = config('zeromq.port', 5555);        // Retrieve from config, fallback to default

            $zmqEndpoint = "tcp://$zmqHost:$zmqPort";
            $zmqContext = new ReactZMQContext($loop);
            $zmqSocket = $zmqContext->getSocket(ZMQ::SOCKET_PULL);

            // Debug: Check the ZeroMQ binding
            echo "Binding ZeroMQ to $zmqEndpoint\n";
            $zmqSocket->bind($zmqEndpoint);

            echo "ZeroMQ server running at $zmqEndpoint\n";

            // Listen for ZeroMQ messages and use Pusher to broadcast
            $zmqSocket->on('message', function ($message) use ($wsHandler) {
                    $wsHandler->broadcast($message);
            });

            // Run the shared event loop
            $loop->run();
        } catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
    }
}
