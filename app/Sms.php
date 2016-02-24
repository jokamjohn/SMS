<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    /**Table name.
     *
     * @var string
     */
    protected $table = 'sms';

    /**Fillable table fields.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'message', 'messageId', 'linkId', 'date'
    ];


}
