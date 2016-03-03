<?php

namespace Jokam\Repositories;


use App\Inbox;
use App\Message;
use App\Sms;
use Illuminate\Http\Request;
use Jokam\AfricasTalkingGateway;
use Jokam\AfricasTalkingGatewayException;
use Jokam\Interfaces\SmsInterface;
use Log;

class SmsRepository implements SmsInterface
{
    private $lastReceivedId;
    /**
     * @var AfricasTalkingGateway
     *
     */
    private $gateway;
    /**
     * @var Inbox
     */
    private $inbox;

    /**Short code.
     *
     * @var
     */
    private $shortCode;

    /**Keyword.
     *
     * @var
     */
    private $keyword;

    /**
     * MakeSms constructor.
     * @param AfricasTalkingGateway $gateway
     * @param Inbox $inbox
     */
    public function __construct(AfricasTalkingGateway $gateway, Inbox $inbox)
    {
        $this->gateway = $gateway;
        $this->inbox = $inbox;
        $this->keyword = config('sms.keyword');
        $this->shortCode = config('sms.short_code');
    }

    /**Send a message
     *
     * @param $recipients
     * @param $message
     * @return mixed
     */
    public function send($recipients, $message)
    {
        try {
            $results = $this->gateway->sendMessage($recipients, $message);

            foreach ($results as $result) {
                // status is either "Success" or "error message"
                echo " Number: " . $result->number;
                echo " Status: " . $result->status;
                echo " MessageId: " . $result->messageId;
                echo " Cost: " . $result->cost . "\n";
            }
        } catch (AfricasTalkingGatewayException $e) {
            echo "Encountered an error while sending: " . $e->getMessage();
        }
    }

    /**Receive a message and save it to the database.
     *
     * @param Request $request
     */
    public function receive(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $text = $request->get('text');
        $date = $request->get('date');
        $id = $request->get('id');
        $linkId = $request->get('linkId');
        Log::debug('new sms received');

        $sms = new Sms();
        $sms->fromNumber = $from;
        $sms->to = $to;
        $sms->message = $text;
        $sms->messageId = $id;
        $sms->date = $date;
        $sms->linkId = $linkId;
        $sms->save();

        Log::debug('new sms saved');
    }

    /**Save the sent message in the database.
     *
     * @param Request $request
     */
    public function save(Request $request)
    {
        $sms = new Message();
        $sms->phoneNumber = $request->get('phoneNumber');
        $sms->message = $request->get('message');
        $sms->save();
    }

    /**
     * Fetch messages from the account dashboard or your account.
     */
    public function fetch()
    {
        try {
            $this->lastReceivedId();
            $results = $this->gateway->fetchMessages($this->lastReceivedId);

            foreach ($results as $result) {
                $this->inboxMessage($result);
            }
        } catch (AfricasTalkingGatewayException $e) {
            echo "Encountered an error: " . $e->getMessage();
        }
    }

    /**Get the last received message Id.
     *
     */
    private function lastReceivedId()
    {
        if (Inbox::count() > 0) {
            $this->lastReceivedId = $this->inbox->all()->last()->lastReceivedId;
        } else {
            $this->lastReceivedId = 0;
        }
    }

    /**Save the received message to the database.
     *
     * @param $result
     */
    private function inboxMessage($result)
    {
        $inbox = new Inbox();
        $inbox->fromNumber = $result->from;
        $inbox->to = $result->to;
        $inbox->message = $result->text;
        $inbox->date = $result->date;
        $inbox->linkId = $result->linkId;
        $inbox->lastReceivedId = $result->id;
        $inbox->save();
    }
}