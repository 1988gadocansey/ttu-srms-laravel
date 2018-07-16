<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicRecordsModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_academic_record';
    protected static $logAttributes = ['course', 'student','quiz1','quiz2','quiz3','midSem1','exam','grade','sem','year'];
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function courseMount(){
        return $this->belongsTo('App\Models\MountedCourseModel', "code","COURSE_CODE");
    }
     public function student(){
        return $this->belongsTo('App\Models\StudentModel', "indexno","INDEXNO");
    }
      protected $casts = [
        'lecturer' => 'int',
    ];

    /**
     * Get the user that owns the grades.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function academic(){
        return $this->belongsTo('App\Models\StudentModel', "indexno","INDEXNO");
    }
      public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "LEVEL","name");
    }
}
