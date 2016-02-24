<?php
/**
 * Created by PhpStorm.
 * User: jokamjohn
 * Date: 10/16/2015
 * Time: 5:00 PM
 */

namespace Jokam\Interfaces;


interface SmsInterface
{
    /**Method to send a message
     * @param $recipients
     * @param $message
     * @return mixed
     */
    public function send($recipients, $message);

}