<?php
/**
 * Created by PhpStorm.
 * User: jokamjohn
 * Date: 10/16/2015
 * Time: 5:00 PM
 */

namespace Jokam\Interfaces;


use Illuminate\Http\Request;

interface SmsInterface
{
    /**Method to send a message
     * @param $recipients
     * @param $message
     * @return mixed
     */
    public function send($recipients, $message);

    /**Receive a message and save it to the database.
     *
     * @param Request $request
     */
    public function receive(Request $request);

    /**Save the sent message in the database.
     *
     * @param Request $request
     */
    public function save(Request $request);

    /**
     * Fetch messages from the account dashboard or your account.
     */
    public function fetch();

}