<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class GradeLogModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_grade_log';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
    public function courseMount(){
        return $this->belongsTo('App\Models\MountedCourseModel', "course","ID");
    }
     public function student(){
        return $this->belongsTo('App\Models\StudentModel', "student","ID");
    }
     
}
