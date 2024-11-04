<?php

namespace Hilsager\GuestService;

use Doctrine\DBAL\Connection;
use Hilsager\GuestService\App\App;
use Hilsager\GuestService\Validator\Validator;

class Entity
{
    protected string $tableName;

    protected array $attributes;

    protected array $data;

    protected Connection $db;

    protected Validator $validator;

    public function __construct($data = [])
    {
        $this->db = App::$app->db;
        $this->data = $data ?? [];
        $this->validator = new Validator(
            App::$app->request->getMethod(),
            $data,
            $this->attributes,
            $this->tableName
        );
    }

    public function validate(): bool
    {
        return $this->validator->validate();
    }

    public function getEntity(): array
    {
        $qb = $this->db->createQueryBuilder();

        $qb->select('*')
            ->from($this->tableName);

        if (!empty($this->data)) {
            foreach ($this->data as $key => $value) {
                $qb->where("$key = :$key")
                ->setParameter($key, $value);
            }
        }

        return $qb->executeQuery()
            ->fetchAllAssociative();
    }

    public function insertOrUpdateEntity(int $id = 0): bool
    {
        return $id
            ? $this->updateEntity($id)
            : $this->insertEntity();
    }

    protected function insertEntity(): bool
    {
        return (bool)$this->db->insert(
            $this->tableName,
            $this->data,
        );
    }

    protected function updateEntity(int $id): bool
    {
        return (bool)$this->db->update(
            $this->tableName,
            $this->data,
            ['id' => $id]
        );
    }

    public function deleteEntity(int $id): bool
    {
        return (bool)$this->db->delete($this->tableName, ['id' => $id]);
    }

    public function getMessage(): string
    {
        return $this->validator->getMessage();
    }
}