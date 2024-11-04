<?php

namespace Hilsager\GuestService;

use Hilsager\GuestService\App\App;
use Symfony\Component\HttpFoundation\Request;


class Rest
{
    protected string $restName = '';

    protected array $entities = [];

    protected function getEntity(string $requestUri, array $content): Entity
    {
        if(!preg_match("#^/$this->restName/([^/]+)#", $requestUri, $matches)) {
            throw new Exception('Не удалось найти сущность');
        }

        if (empty($entity = $this->entities[$matches[1]])) {
            throw new Exception('Не удалось найти сущность');
        }

        return new $entity($content);
    }

    public function run(): void
    {
        $request = App::$app->request;

        $content = json_decode($request->getContent(), true) ?? [];

        $entity = $this->getEntity($request->getRequestUri(), $content);

        $restMessage = new RestMessage();
        if(!$entity->validate()) {
            $restMessage = new RestMessage([], $entity->getMessage(), 400);
        } else {
            switch ($request->getMethod()) {
                case Request::METHOD_GET:
                    $restMessage = new RestMessage($entity->getEntity());
                    break;
                case Request::METHOD_POST:
                    $restMessage = new RestMessage($entity->insertOrUpdateEntity($content['id'] ?? 0));
                    break;
                case Request::METHOD_DELETE:
                    $restMessage = new RestMessage($entity->deleteEntity($content['id'] ?? null));
                    break;
            }
        }

        echo json_encode($restMessage->prepareResponse());
    }
}