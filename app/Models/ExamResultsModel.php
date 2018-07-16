<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class ExamResultsModel extends Model
{
    use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_exams';
     protected static $logAttributes = ['APPLICATION_NUMBER', 'SUBJECT','GRADE','SITTING','EXAM_TYPE','INDEX_NO'];
    protected $primaryKey="ID";
    protected $guarded = ['ID'];
    public $timestamps = false;
    public function applicant(){
        return $this->hasMany('App\Models\ApplicantModel', "APPLICATION_NUMBER","APPLICATION_NUMBER");
    }
     public function subject(){
        return $this->belongsTo('App\Models\SubjectModel', "SUBJECT","ID");
    }
     
}
