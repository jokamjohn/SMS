<?php

namespace Jokam\Repositories;


use Jokam\AfricasTalkingGateway;
use Jokam\AfricasTalkingGatewayException;
use Jokam\Facades\Sms;
use Jokam\Interfaces\SmsInterface;

class MakeSms implements SmsInterface
{
    /**
     * @var AfricasTalkingGateway
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


    /**Method to send a message
     * @param $recipients
     * @param $message
     * @return mixed
     */
    public function sendSms($recipients, $message)
    {
        try
        {
            // Thats it, hit send and we'll take care of the rest.
            $results = $this->gateway->sendMessage($recipients, $message);

            foreach($results as $result) {
                // status is either "Success" or "error message"
                echo " Number: " .$result->number;
                echo " Status: " .$result->status;
                echo " MessageId: " .$result->messageId;
                echo " Cost: "   .$result->cost."\n";
            }
        }
        catch ( AfricasTalkingGatewayException $e )
        {
            echo "Encountered an error while sending: ".$e->getMessage();
        }
    }
}