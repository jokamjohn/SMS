@extends('app')

@section('body')

    {!! Form::open([ 'route' => 'subscription.store', 'method' => 'POST' ])!!}

    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

        {!!Form::label('from', 'from')!!}
        {!!Form::text('from',null,['class' => 'form-control'])!!}
        {!! $errors->first('from','<span class="help-block">:message</span>')!!}
    </div>

    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

        {!!Form::label('to', 'to')!!}
        {!!Form::text('to',null,['class' => 'form-control'])!!}
        {!! $errors->first('to','<span class="help-block">:message</span>')!!}
    </div>


    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

        {!!Form::label('text', 'Message: ')!!}
        {!!Form::textarea('text',null,['class' => 'form-control'])!!}
        {!! $errors->first('text','<span class="help-block">:message</span>')!!}
    </div>

    <div class="form-group">
        {!!Form::submit('send',['class' => 'btn btn-primary'])!!}
    </div>

    {!!Form::close()!!}


@stop