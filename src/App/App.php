<?php

namespace Hilsager\GuestService\App;

use Hilsager\GuestService\DB\Connect;
use Doctrine\DBAL\Connection;
use Hilsager\GuestService\Rest;
use Hilsager\GuestService\RestMessage;
use Symfony\Component\HttpFoundation\Request;

class App
{
    public static App $app;
    private array $config;
    public Connection $db;

    public Request $request;

    public function __construct()
    {
        $this->config = require_once(__DIR__.'/../../config.php');

        $this->db = Connect::getConnectionInstance($this->getDB());
        $this->request = new Request(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );

        self::$app = $this;
    }

    public function run(): void
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setHeader('Access-Control-Allow-Origin', '*');

        try {
            $rest = $this->getRest($this->request->getRequestUri());
            $rest->run();
        } catch (\Exception $e) {
            $this->render404();
        }
    }

    public function setHeader(string $key, string $value): void
    {
        header("$key: $value");
    }

    protected function getRest($requestUri): Rest
    {
        $rules = $this->getRules();

        foreach ($rules as $class => $restUrl) {
            if(preg_match("#^/$restUrl/#", $requestUri)) {
                return new $class();
            }
        }

        throw new \Exception('test');
    }

    protected function getRules(): array
    {
        if(empty($this->config['rules'])) {
            throw new \Exception('Отсутствуют правила генерации url');
        }

        return $this->config['rules'];
    }

    protected function getDB(): array
    {
        if(empty($this->config['db'])) {
            throw new \Exception('Отсутствуют данные БД');
        }

        return $this->config['db'];
    }

    protected function render404()
    {
        $restMessage = new RestMessage([], 'Не удалось ничего найти', 404);
        echo json_encode($restMessage->prepareResponse());
    }
}