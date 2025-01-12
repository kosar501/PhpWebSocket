<?php

namespace Kosar501\PhpWebSocket\classes;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\Topic;

/**
 * Pusher is a real-time messaging service that allows you to send messages to clients on specific channels.
 * Each channel is like a topic, and clients can subscribe to these channels to receive updates/messages.
 */
class Pusher implements MessageComponentInterface
{
    protected $subscribedTopics = array();

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection: " . $conn->resourceId . "\n";  // Log connection ID
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * This method is called when a message is received from a client
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true); // Decode the incoming JSON message

        // Handle subscription request
        if (isset($data['event']) && $data['event'] == 'subscribe' && isset($data['data']['topic'])) {
            $topicName = $data['data']['topic'];

            // Create or return the topic object
            $this->getTopic($topicName);

            // Subscribe the client to the topic
            //$this->subscribedTopics[$from->resourceId] = $topic;

            // Confirm the subscription back to the client
            $from->send(json_encode([
                'event' => 'subscribed',
                'data' => ['topic' => $topicName]
            ]));
            echo "Client subscribed to topic: " . $topicName . "\n";
        }

        // Handle unsubscription request
//        if (isset($data['event']) && $data['event'] == 'unsubscribe' && isset($data['data']['topic'])) {
//            $topicName = $data['data']['topic'];
//
//            // Remove the client from the subscribed topics list
//            if (isset($this->subscribedTopics[$from->resourceId])) {
//                unset($this->subscribedTopics[$from->resourceId]);
//            }
//
//            // Send confirmation that the client has unsubscribed
//            $from->send(json_encode([
//                'event' => 'unsubscribed',
//                'data' => ['topic' => $topicName]
//            ]));
//            echo "Client unsubscribed from topic: " . $topicName . "\n";
//        }

        // Broadcast message logic (if needed)
        if (isset($data['event']) && $data['event'] == 'broadcast' && isset($data['data']['message'])) {
            $message = $data['data']['message'];

            // Broadcast the message to all clients subscribed to the same topic
            foreach ($this->subscribedTopics as $client) {
                $client->broadcast($message); // Send the message to the topic
            }

            echo "Broadcasting message to topic: " . $topicName . "\n";
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        echo "Connection {$conn->resourceId} has disconnected\n";

    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Connection {$conn->resourceId} has disconnected\n" . $e->getMessage() . "\n";

    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onBlogEntry($entry)
    {
        // Decode the incoming entry
        $entryData = json_decode($entry, true);

        // Check for any errors in decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Failed to decode JSON: $entry\n";
            return;
        }

        // Ensure the topic exists in the subscribed topics
        if (!array_key_exists($entryData['topic'], $this->subscribedTopics)) {
            echo "No subscribers for topic: " . $entryData['topic'] . "\n";
            return;
        }

        // Get the topic
        $topic = $this->subscribedTopics[$entryData['topic']];

        // Log the broadcast attempt
        echo "Broadcasting entry to topic: " . json_encode($this->subscribedTopics) . "\n";
        echo "Broadcasting entry: " . json_encode($entryData) . "\n";

        // Send the data to all subscribed clients
        $topic->broadcast($entryData);

    }


    /**
     * @param $topicName
     * Method to get a topic by name (you can customize this to handle topics)
     */
    private function getTopic($topicName)
    {
        // Ensure this creates or retrieves a topic for the specified name
        if (!isset($this->subscribedTopics[$topicName])) {
            $this->subscribedTopics[$topicName] = new Topic($topicName); // Create a new Topic object if not exists
        }
        return $this->subscribedTopics[$topicName];
    }

}