<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class CourseModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_courses';
     protected static $logAttributes = ['COURSE_NAME', 'COURSE_CODE','PROGRAMME','COURSE_LEVEL'];
    protected $primaryKey="ID";
    protected $guarded = ['ID','COURSE_CODE'];
    public $timestamps = false;
    public function programme(){
        return $this->belongsTo('App\Models\ProgrammeModel', "PROGRAMME","PROGRAMMECODE");
    }
     public function programs(){
        return $this->hasMany('App\Models\ProgrammeModel', "PROGRAMMECODE","PROGRAMME");
    }
      public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "COURSE_LEVEL","name");
    }
}
