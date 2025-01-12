# WebSocket Server PHP Package

## Overview

This project provides a simple WebSocket server implementation in PHP, 
designed to interact with Redis for message queuing. 
It consists of a WebSocket server that communicates with clients and a Redis consumer 
that processes messages from a Redis queue.

**WebSocket Server**: Listens for WebSocket connections and broadcasts messages to connected clients.

---

## Installation

### Step 1: Clone the Repository
Clone the repository to your local machine:
```bash
git clone https://your-repository-url.git
cd your-project-folder
```

### Step 2: Install Dependencies
```bash
composer install
```
### Step 2: Install Dependencies
```bash
composer install
```
## Running the Server
There are two main components in the system: the WebSocket server and the Redis queue consumer.
You can run both components as separate processes, and they will be managed using Supervisor.

### 1: Running WebSocket Server Along With ZMQ

#### 1.1: Manually (for development or testing)
```bash
php server.php
```
#### 1.2: Using Supervisor (Recommended for production)
```ini
[program:websocket-server]
command=php /path/to/your/project/server.php
autostart=true
autorestart=true
stderr_logfile=/var/log/websocket_server.err.log
stdout_logfile=/var/log/websocket_server.out.log
```
After adding this configuration, update Supervisor:
```bash
supervisorctl start websocket-server
```
Start the WebSocket server:
```bash
supervisorctl start websocket-server
```



If you encounter issues, restart the processes via Supervisor:
```bash
supervisorctl restart websocket-server
```

## How to Use
You can use the Client class to send messages to the WebSocket server through Redis. This class sends messages to 
the Redis queue that the consumer will process.

### Server Side:
#### Example:
```php
    $client = new MessagePublisher();

    // Prepare the message as an associative array
    $message = json_encode(['topic' => 'news', 'content' => 'This is a test message']);
    $client->sendMessage($message);
```

### On the Client Side (Web Browser):
#### Example:
You can check examples folder 
```javascript
const socket = new WebSocket('ws://127.0.0.1:5555');

socket.onmessage = function(event) {
    const message = JSON.parse(event.data);
    console.log('Received message:', message);
    // Now you can access the message properties like message.action, message.username, etc.
};
```