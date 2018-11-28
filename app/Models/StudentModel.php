<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class StudentModel extends Model
{
    use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_students';
     protected static $logAttributes = ['PROGRAMMECODE', 'INDEXNO','NAME','YEAR','BILLS','BILL_OWING'];
    protected $primaryKey="ID";
    protected $guarded = ['ID'];
     protected $fillable = array('LEVEL','YEAR','PROGRAMMECODE', 'INDEXNO','NAME','ADDRESS','GENDER','TELEPHONENO','FIRSTNAME','SURNAME');
    public $timestamps = false;
    public function programme(){
        return $this->hasMany('App\Models\ProgrammeModel', "PROGRAMMECODE","PROGRAMMECODE");
    }
    public function program(){
        return $this->belongsTo('App\Models\ProgrammeModel', "PROGRAMMECODE","PROGRAMMECODE");
    }
     public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "LEVEL","name");
    }
    public function academic(){
        return $this->hasMany('App\Models\AcademicRecordsModel', "indexno","INDEXNO");
    }
     
}
