#!/bin/bash

# Step 1: Update System
sudo yum update -y

# Step 2: Install Required Development Tools
sudo yum groupinstall "Development Tools" -y
sudo yum install gcc gcc-c++ make autoconf php-pear php-devel git -y

# Step 3: Install ZeroMQ 4.3.1
cd /usr/local/src
curl -LO https://github.com/zeromq/libzmq/releases/download/v4.3.1/zeromq-4.3.1.tar.gz
tar -xvzf zeromq-4.3.1.tar.gz
cd zeromq-4.3.1
./configure
make
sudo make install
sudo ldconfig

# Step 4: Install the ZMQ PHP Extension (from GitHub)
cd /usr/local/src
git clone https://github.com/mkoppanen/php-zmq.git
cd php-zmq
phpize
./configure
make
sudo make install

# Step 5: Enable ZMQ in PHP
PHP_INI=$(php --ini | grep "Loaded Configuration File" | awk '{print $4}')
echo "extension=zmq.so" | sudo tee -a $PHP_INI

# Step 6: Restart Web Server
# For Apache
if systemctl is-active --quiet httpd; then
    sudo systemctl restart httpd
fi

# For Nginx with PHP-FPM
if systemctl is-active --quiet php-fpm; then
    sudo systemctl restart php-fpm
    sudo systemctl restart nginx
fi

# Step 7: Verify Installation
php -m | grep zmq

# Display Success Message
echo "ZMQ PHP extension installed and enabled successfully!"
