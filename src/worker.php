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
        $__SERVER = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => getcwd(),
            'HTTP_HOST' => $request->host(),
            'SERVER_NAME' => $request->host(true),
            'REMOTE_ADDR' => $request->connection->getRemoteIp(),
            'REMOTE_PORT' => $request->connection->getRemotePort(),
            'REQUEST_METHOD' => $request->method(),
            'REQUEST_URI' => $request->uri(),
            'QUERY_STRING' => $request->queryString(),
            'SCRIPT_FILENAME' => getcwd() . '/' . basename($__SERVER['SCRIPT_FILENAME']),
            'REQUEST_TIME' => $__SERVER['REQUEST_TIME'],
            'REQUEST_TIME_FLOAT' => $__SERVER['REQUEST_TIME_FLOAT']
        ];

        foreach ($request->header() as $key => $value) {
            $key = str_replace('-', '_', strtoupper($key));

            if (in_array($key, array_keys($_SERVER))) {
                continue;
            }

            if ( ! in_array($key, ['SERVER_ADDR'])) {
                $key = 'HTTP_' . $key;
            }

            $_SERVER[$key] = $value;
        }
    }
}