<?php
require_once __DIR__ . '/../bootstrap.php';

use Kosar501\PhpWebSocket\classes\MessagePublisher;


try {
    $client = new MessagePublisher();

    // Prepare the message as an associative array
    $message = json_encode(['topic' => 'news', 'content' => 'This is a test message']);
    $client->sendMessage($message);

} catch (ZMQSocketException $e) {
    echo $e->getMessage();
}
