<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['process_id', 'url', 'post', 'headers', 'status', 'message'];

}
