<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
    /**Name of the table.
     *
     * @var string
     */
    protected $table = "inbox";

    /**Fill-able columns.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'message', 'lastReceivedId', 'linkId', 'date'
    ];
}
