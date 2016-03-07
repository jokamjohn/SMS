<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'postDate',
        'deadline',
        'deleted_at'
    ];
    /**Fill-able fields.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'location',
        'duties',
        'workType',
        'qualifications',
        'deadline',
        'postDate',
        'positions',
        'jobType',
        'salary',
        'applicationProcedures',
        'contact',
        'contactName'
    ];

}
