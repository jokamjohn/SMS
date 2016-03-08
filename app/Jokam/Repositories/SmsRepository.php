<?php

namespace Jokam\Repositories;


use App\Inbox;
use App\Job;
use App\Message;
use App\Sms;
use App\Subscription as SubscriptionModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jokam\AfricasTalkingGateway;
use Jokam\AfricasTalkingGatewayException;
use Jokam\Interfaces\SmsInterface;
use Jokam\Subscription;
use Log;

class SmsRepository implements SmsInterface
{
    const JOB = 'job';
    const TRAINING = 'training';
    const SUBSCRIBE = 'sub';
    const UNSUBSCRIBE = 'unsub';

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
     * @var Job
     */
    private $job;
    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * MakeSms constructor.
     * @param AfricasTalkingGateway $gateway
     * @param Inbox $inbox
     * @param Job $job
     * @param Subscription $subscription
     */
    public function __construct(AfricasTalkingGateway $gateway, Inbox $inbox, Job $job, Subscription $subscription)
    {
        $this->gateway = $gateway;
        $this->inbox = $inbox;
        $this->keyword = config('sms.keyword');
        $this->shortCode = config('sms.short_code');
        $this->job = $job;
        $this->subscription = $subscription;
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

    /**Receive a message and send a reply.
     *
     * @param Request $request
     */
    public function receive(Request $request)
    {
        $this->saveMessage($request);

        $this->replyMessage($request);
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

    /**Save the received message to the database.
     *
     * @param Request $request
     */
    private function saveMessage(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $text = $request->get('text');
        $date = $request->get('date');
        $id = $request->get('id');
        $linkId = $request->get('linkId');

        $sms = new Sms();
        $sms->fromNumber = $from;
        $sms->to = $to;
        $sms->message = $text;
        $sms->messageId = $id;
        $sms->date = $date;
        $sms->linkId = $linkId;
        $sms->save();
    }

    /**Look up for the category, construct a message and then send a reply.
     *
     * @param Request $request
     */
    private function replyMessage(Request $request)
    {
        $message = $request->get('text');
        $messageArray = explode(' ', $message);
        $intent = $messageArray[1];
        $recipient = $request->get('from');

        switch (strtolower($intent)) {

            case self::JOB:
                $this->processJobRequest($messageArray, $recipient);
                break;

            case self::TRAINING:
                break;

            case self::SUBSCRIBE:
                $this->saveSubscription($recipient);
                break;

            case self::UNSUBSCRIBE:
                break;

            default:

        }

    }

    /**Reply to the person who wants information about a specific job category.
     *
     * @param $category
     * @param $recipient
     */
    private function replyToJobSender($category, $recipient)
    {
        $jobs = $this->job->where('deadline', '>=', Carbon::today())
            ->where('category', 'like', '%' . $category . '%')
            ->orderBy('id', 'desc')
            ->take(5)->get();

        if (count($jobs) > 0) {
            foreach ($jobs as $job) {
                $message = "Job is $job->jobType, located at $job->location, contact $job->contact on $job->contactName, positions available $job->positions";
                $this->send($recipient, $message);
                Log::debug("msg sent: " . $message);
            }
        } else {
            $message = "Sorry no jobs matching your category, try another category";
            $this->send($recipient,$message);
        }

    }

    /**
     * @param $recipient
     */
    private function saveSubscription($recipient)
    {
        $result = $this->subscription->subscribe($recipient);
        $subscription = new SubscriptionModel();
        $subscription->number = $recipient;
        $subscription->status = $result->status;
        $subscription->description = $result->description;
        $subscription->save();
        Log::debug($recipient . ' is subscribed');
    }

    /**Process the job request.
     *
     * @param $messageArray
     * @param $recipient
     */
    private function processJobRequest($messageArray, $recipient)
    {
        if (isset($messageArray[2])) {
            $category = $messageArray[2];
            $this->replyToJobSender($category, $recipient);
        } else {
            $message = 'Job category missing try sending: ' . "<keyword> job <category>";
            $this->send($recipient, $message);
            Log::debug("no job category message: " . $message);
        }
    }


}