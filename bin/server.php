<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Socket;

$port = isset($_SERVER['CHAT_SERVER_PORT']) ? $_SERVER['CHAT_SERVER_PORT'] : 3120;
$bindAddr = isset($_SERVER['CHAT_BIND_ADDR']) ? $_SERVER['CHAT_BIND_ADDR'] : '127.0.0.1';

$server = IoServer::factory(new HttpServer(new WsServer(new Socket())),$port);

printf("Chat server running on %s:%s.\n--\n", $bindAddr, $port);

$server->run();
