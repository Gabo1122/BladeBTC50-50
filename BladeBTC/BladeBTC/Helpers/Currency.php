<?php


namespace BladeBTC\Helpers;


class Currency
{

    public static function GetBTCValueFromCurrency($currency_value, $from_currency = 'USD')
    {
       $value = file_get_contents("https://blockchain.info/tobtc?currency=$from_currency&value=$currency_value");

       return $value;
    }


}