<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Model;
use Emadadly\LaravelUuid\Uuids;
use Spatie\Activitylog\Traits\LogsActivity;

class Course extends Model
{
    use Uuids;
    use LogsActivity;

    protected $table = 'courses';

    protected $fillable = ['title', 'description', 'type', 'engineer_id', 'color', 'workload', 'ordinance', 'ordinance_year', 'nbr', 'nt', 'created_by', 'grade', 'active'];

    protected static $logAttributes = ['title', 'description', 'type', 'engineer_id', 'color', 'workload', 'ordinance', 'ordinance_year', 'nbr', 'nt', 'created_by', 'grade', 'active'];

    public function teams()
    {
        return $this->hasMany('App\Models\Training\Team', 'course_id');
    }

    public function engineer()
    {
        return $this->belongsTo('App\User', 'engineer_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName == 'updated') {
            return "Curso atualizado";
        } elseif ($eventName == 'deleted') {
            return "Curso removido";
        }

        return "Curso adicionado";
    }
}
