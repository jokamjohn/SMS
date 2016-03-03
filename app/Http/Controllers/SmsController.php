<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Jokam\AfricasTalkingGateway;
use Jokam\Interfaces\SmsInterface;
use Log;

class SmsController extends Controller
{

    /**
     * @var SmsInterface
     */
    private $sms;
    /**
     * @var AfricasTalkingGateway
     */
    private $gateway;

    /**
     * SmsController constructor.
     * @param SmsInterface $sms
     * @param AfricasTalkingGateway $gateway
     */
    public function __construct(SmsInterface $sms,AfricasTalkingGateway $gateway)
    {
        $this->sms = $sms;
        $this->gateway = $gateway;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->sms->fetch();

        return "done";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $phoneNumber = $request['phoneNumber'];

        $message = $request['message'];

        $this->sms->send($phoneNumber, $message);

        $this->sms->save($request);

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
