<?php

namespace Hilsager\GuestService\Validator;

use Hilsager\GuestService\App\App;
use Symfony\Component\HttpFoundation\Request;

class Validator
{
    protected string $message = '';
    protected string $method;
    protected array $data;
    protected array $propertiesList;

    protected string $tableName;
    public function __construct(
        string $method,
        array $data,
        array $propertiesList,
        string $tableName
    ) {
        $this->method = $method;
        $this->data = $data;
        $this->propertiesList = $propertiesList;
        $this->tableName = $tableName;
    }

    public function validate(): bool
    {
        if(
            $this->method === Request::METHOD_GET
            || $this->method === Request::METHOD_DELETE
        ) {
            return $this->validateData();
        }

        if(empty($this->data)) {
            $this->message = 'Никаких данных не было получено';
            return false;
        }

        if($this->method === Request::METHOD_POST) {
            return $this->validatePost();
        }

        return false;
    }

    protected function validatePost(): bool
    {
        if ($this->getData('id')) {
            return $this->validateData();
        }

        return $this->validateCreate();
    }

    protected function validateData(): bool
    {
        foreach ($this->data as $key => $value) {
            if(!$this->validateByKey($key, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function validateCreate(): bool
    {
        foreach ($this->propertiesList as $key => $properties) {
            if(
                in_array('required', $properties)
                && empty($this->getData($key))
            ) {
                $this->message = "Не установлено значение для $key";
                return false;
            }
        }

        foreach ($this->data as $key => $value) {
            if(!$this->validateByKey($key, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function validateByKey(string $key, $value): bool
    {
        if (empty($this->propertiesList[$key])) {
            return true;
        }

        if(
            !$this->validateByKeyOnInt($key, $value)
            || !$this->validateByKeyOnNotNull($key, $value)
            || !$this->validateByKeyOnMax($key, $value)
            || !$this->validateByKeyOnEmail($key, $value)
            || !$this->validateByKeyOnUnique($key, $value)
        ) {
            return false;
        }

        return true;
    }

    protected function validateByKeyOnInt($key, $value): bool
    {
        if (
            in_array('int', $this->propertiesList[$key])
            && !is_numeric($value)
        ) {
            $this->message = "$key должен быть числом";
            return false;
        }

        return true;
    }

    protected function validateByKeyOnNotNull($key, $value): bool
    {
        if (
            in_array('notNull', $this->propertiesList[$key])
            && empty($value)
        ) {
            $this->message = "$key должен быть заполнен";
            return false;
        }

        return true;
    }

    protected function validateByKeyOnMax($key, $value): bool
    {
        if (
            in_array('max', $this->propertiesList[$key])
            && strlen($value) >= $this->propertiesList[$key]['max']
        ) {
            $max = $this->propertiesList[$key]['max'];
            $this->message = "$key не должен быть длиннее чем $max символов";
            return false;
        }

        return true;
    }

    protected function validateByKeyOnEmail($key, $value): bool
    {
        if (
            in_array('email', $this->propertiesList[$key])
            && !filter_var($value, FILTER_VALIDATE_EMAIL)
        ) {
            $this->message = "$key должен быть корректным адресом электронной почты";
            return false;
        }

        return true;
    }

    protected function validateByKeyOnUnique($key, $value): bool
    {
        if (
            in_array('unique', $this->propertiesList[$key])
            && !$this->isUniqueValue($key, $value)
        ) {
            $this->message = "$key должен быть уникальным";
            return false;
        }

        return true;
    }

    protected function isUniqueValue($key, $value): bool
    {
        $qb = App::$app->db->createQueryBuilder();
        return !(bool)$qb->select(1)
            ->from($this->tableName)
            ->where("$key = :$key")
            ->setParameter($key, $value)
            ->executeQuery()
            ->fetchOne();
    }

    protected function getData(string $key)
    {
        if(empty($this->data[$key])) {
            return null;
        }

        return $this->data[$key];
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}