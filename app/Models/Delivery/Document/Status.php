<?php

namespace App\Models\Delivery\Document;

use Illuminate\Database\Eloquent\Model;
use Emadadly\LaravelUuid\Uuids;
use Spatie\Activitylog\Traits\LogsActivity;

class Status extends Model
{
    use Uuids;

    protected $table = 'delivery_document_statuses';

    protected $fillable = ['name', 'active'];

    protected static $logAttributes = ['name', 'active'];
}
