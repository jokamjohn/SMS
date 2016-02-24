<?php

namespace Jokam\Repositories;


use App\Message;
use App\Sms;
use Illuminate\Http\Request;
use Jokam\AfricasTalkingGateway;
use Jokam\AfricasTalkingGatewayException;
use Jokam\Interfaces\SmsInterface;
use Log;

class SmsRepository implements SmsInterface
{
    /**
     * @var AfricasTalkingGateway
     *
     */
    private $gateway;

    /**
     * MakeSms constructor.
     * @param AfricasTalkingGateway $gateway
     */
    public function __construct(AfricasTalkingGateway $gateway)
    {
        $this->gateway = $gateway;
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
//        $linkId = $request->get('linkId'); //This works for onDemand subscription products
        Log::debug('new sms received');

        $sms = new Sms();

        $sms->from = $from;
        $sms->to = $to;
        $sms->message = $text;
        $sms->messageId = $id;
        $sms->date = $date;
//        $sms->linkId = $linkId;

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
}