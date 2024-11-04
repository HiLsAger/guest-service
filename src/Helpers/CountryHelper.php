<?php

namespace Hilsager\GuestService\Helpers;

use libphonenumber\PhoneNumberUtil;

class CountryHelper
{

   public static function getCountryByPhone(string $phoneNumber): string | bool
   {
       $phoneUtil = PhoneNumberUtil::getInstance();

       try {
           $numberProto = $phoneUtil->parse($phoneNumber, null);
           $countryCode = $numberProto->getCountryCode();
           $regionCode = $phoneUtil->getRegionCodeForCountryCode($countryCode);
           return $regionCode;
       } catch (\Exception $e) {
            return false;
       }
   }
}