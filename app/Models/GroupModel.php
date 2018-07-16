<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class GroupModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_groups';
     protected static $logAttributes = ['name', 'program','level'];
    protected $primaryKey="id";
    protected $guarded = ['id','program'];
    public $timestamps = false;
    public function programme(){
        return $this->belongsTo('App\Models\ProgrammeModel', "program","PROGRAMMECODE");
    }
     public function programs(){
        return $this->hasMany('App\Models\ProgrammeModel', "program","PROGRAMMECODE");
    }
    public function tutor(){
        return $this->belongsTo('App\Models\WorkerModel', "lecturer","staffID");
    }
     public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "level","name");
    }
     
}
