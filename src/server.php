<?php


use Kosar501\PhpWebSocket\commands\WebSocketServeCommand;

require_once __DIR__ . '/bootstrap.php';

// Start the PhpWebSocket server
WebSocketServeCommand::serve();
