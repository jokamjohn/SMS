<?php
/**
 * Created by PhpStorm.
 * User: jokamjohn
 * Date: 2/24/2016
 * Time: 12:19 PM
 */

namespace Jokam;


class Subscription
{
    /**AfricasTalking short code.
     *
     * @var mixed
     */
    private $shortCode;

    /**AfricasTalkingKeyword.
     *
     * @var mixed
     */
    private $keyword;

    /**
     * @var AfricasTalkingGateway
     */
    private $gateway;

    /**
     * Subscribe constructor.
     * @param AfricasTalkingGateway $gateway
     */
    public function __construct(AfricasTalkingGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->keyword = config('sms.keyword');
        $this->shortCode = config('sms.short_code');
    }

    /**Subscribe a number to a short code keyword.
     *
     * Only status Success signifies the subscription was successfully
     *
     * @param $phoneNumber
     * @return string
     */
    public function subscribe($phoneNumber)
    {
        try {
            return $this->gateway->createSubscription($phoneNumber, $this->shortCode, $this->keyword);

        } catch (AfricasTalkingGatewayException $e) {

            return $e->getMessage();
        }
    }

    /**Unsubscribe a user from the short code and keyword.
     *
     * @param $phoneNumber
     * @return string
     */
    public function unsubscribe($phoneNumber)
    {
        try {
            $result = $this->gateway->deleteSubscription($phoneNumber, $this->shortCode, $this->keyword);

            $message = 'status: ' . $result->status . ' description: ' . $result->description;

            return $message;

        } catch (AfricasTalkingGatewayException $e) {

            return $e->getMessage();
        }
    }

    /** The gateway will return 100 subscription numbers at a time back to you, starting with
     * what you currently believe is the lastReceivedId. Specify 0 for the first
     * time you access the gateway, and the ID of the last message we sent you
     * on subsequent results
     *
     * @return string
     */
    public function subscriptions()
    {
        \Log::debug('begin fetch');
        try {

            $lastReceivedId = 0;
            \Log::debug('start fetch');
            do {

                $results = $this->gateway->fetchPremiumSubscriptions($this->shortCode, $this->keyword, $lastReceivedId);
                foreach ($results as $result) {

                    echo "\n";
                    $lastReceivedId = $result->id;

                    $message = " From: " . $result->phoneNumber . " id: " . $result->id;
                    \Log::debug('message fetch');
                    return $message;
                }
//                return $results;

            } while (count($results) > 0);

            \Log::debug('done fetch');
            //TODO Be sure to save lastReceivedId here for next time.

        } catch (AfricasTalkingGatewayException $e) {

            return "Encountered an error: " . $e->getMessage();
        }
    }

}