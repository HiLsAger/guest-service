<?php

namespace Hilsager\GuestService\Api\Guest;

use Hilsager\GuestService\Entity;
use Hilsager\GuestService\Helpers\CountryHelper;

class GuestApi extends Entity
{

    protected string $tableName = 'guest';

    protected array $attributes = [
        'id' => ['int'],
        'name' => ['required', 'string', 'notNull', 'max' => 255],
        'lastname' => ['required', 'string', 'notNull', 'max' => 255],
        'email' => ['email', 'unique', 'notNull', 'max' => 255],
        'phone' => ['required', 'unique', 'phone', 'notNull', 'max' => 20],
        'country' => ['string', 'max' => 255]
    ];

    protected function insertEntity(): bool
    {
        $phone = $this->data['phone'];

        if (
            empty($this->data['country'])
            && $countryCode = CountryHelper::getCountryByPhone($phone)
        ) {
            $this->data['country'] = $countryCode;
        }

        return parent::insertEntity();
    }
}