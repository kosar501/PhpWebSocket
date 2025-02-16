# WebSocket Server PHP Package

## Overview

Real-time messaging platform utilizing WebSocket for bidirectional communication between clients and a ZeroMQ-powered backend for scalable, asynchronous message distribution. This project enables efficient and low-latency communication across multiple connected clients, with ZeroMQ handling high-throughput messaging and WebSocket ensuring real-time updates in a responsive web interface.


**WebSocket Server**: Listens for WebSocket connections and broadcasts messages to connected clients.

---

## Dependencies
* Install ZeroMQ Dependencies
```bash
sudo apt-get update
sudo apt-get install -y libzmq3-dev php7.4-dev pkg-config
cd /etc
sudo git clone https://github.com/mkoppanen/php-zmq.git
cd php-zmq
sudo phpize
sudo ./configure
sudo make
sudo make install
echo "extension=zmq.so" | sudo tee /etc/php/7.4/mods-available/zmq.ini
sudo phpenmod zmq

```
to check you can use ```bash php -m | grep zmq```

## Installation 1:
run this on terminal
```bash
php7.4 path-to-the-composer  require kosar501/phpwebsocket:dev-main --prefer-stable
```
## Installation 2:

### Step 1: Clone the Repository
Clone the repository to your local machine:
```bash
git clone https://github.com/kosar501/PhpWebSocket.git
cd your-project-folder
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

### Dependencies:
you will need `ZMQ` requirements on your server
you can change websocket and ZMQ port from `src/configs`
by default this project will run websocket server on port 8080 and ZMQ on port 5555