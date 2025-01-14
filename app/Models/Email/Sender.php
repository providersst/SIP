<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;
use Emadadly\LaravelUuid\Uuids;

class Sender extends Model
{
    use Uuids;

    protected $table = 'email_sender';
    protected $fillable = ['email_id', 'contact_id'];

    public function contact()
    {
       return $this->belongsTo('App\Models\Email\Contact');
    }
}
