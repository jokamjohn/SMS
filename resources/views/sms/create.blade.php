@extends('app')

@section('body')

    {!! Form::open([ 'route' => 'sms.store', 'method' => 'POST' ])!!}

        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

            {!!Form::label('phone', 'Phone Number:')!!}
            {!!Form::text('phoneNumber',null,['class' => 'form-control'])!!}
            {!! $errors->first('number','<span class="help-block">:message</span>')!!}
        </div>

        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

            {!!Form::label('message', 'Message: ')!!}
            {!!Form::textarea('message',null,['class' => 'form-control'])!!}
            {!! $errors->first('message','<span class="help-block">:message</span>')!!}
        </div>

        <div class="form-group">
            {!!Form::submit('send',['class' => 'btn btn-primary'])!!}
        </div>

    {!!Form::close()!!}


    @stop