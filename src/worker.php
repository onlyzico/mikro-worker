<?php

namespace MikroWorker;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class Worker
{
    /**
     * @var \Workerman\Connection\TcpConnection
     */
    protected $connection;

    /**
     * @param \Workerman\Connection\TcpConnection $connection
     * @param \Workerman\Protocols\Http\Request $request
     *
     * @return null
     */
    public function onMessage(TcpConnection $connection, Request $request)
    {
        $this->connection = $connection;

        $this->setServerVars($request);
        $this->response();

        return null;
    }

    /**
     * @return void
     */
    public function response()
    {
        $this->connection->send('Hello world.');
    }

    /**
     * @return void
     */
    public function setServerVars(Request $request)
    {
        $_SERVER = [
            'DOCUMENT_ROOT' => getcwd(),
            'HTTP_HOST' => $request->host(),
            'SERVER_NAME' => $request->host(true),
            'REMOTE_ADDR' => $request->connection->getRemoteIp(),
            'REMOTE_PORT' => $request->connection->getRemotePort(),
            'REQUEST_METHOD' => $request->method(),
            'REQUEST_URI' => $request->uri(),
            'QUERY_STRING' => $request->queryString(),
            'SCRIPT_FILENAME' => getcwd() . '/' . $_SERVER['SCRIPT_FILENAME']
        ];
    }
}