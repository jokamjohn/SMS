<?php
/**
 * Created by PhpStorm.
 * User: jokamjohn
 * Date: 10/16/2015
 * Time: 5:41 PM
 */

namespace Jokam\Facades;


use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }

}