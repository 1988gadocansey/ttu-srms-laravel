<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class QAquestionModel extends Model
{
    use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'qa_questions';
    protected static $logAttributes = ['indexno',  'lecturer' ];
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function courseDetails(){
        return $this->belongsTo('App\Models\MountedCourseModel', "course","ID");
    }
    public function lecturerDetails(){
        return $this->belongsTo('App\Models\WorkerModel', "lecturer","staffID");
    }
    public function studentDetials(){
        return $this->belongsTo('App\Models\StudentModel', "indexno","INDEXNO");
    }
}
