<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProgrammeModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_programme';
    
    protected $primaryKey="ID";
    protected $guarded = ['ID','PROGRAMMECODE'];
    public $timestamps = false;
    public function departments(){
        return $this->hasMany('App\Models\DepartmentModel', "DEPTCODE","DEPTCODE");
    }
    public function gradeSystem(){
        return $this->belongsTo('App\Models\GradeSystemModel', "GRADING_SYSTEM","type");
    }
    public function students() {
    return $this->belongsTo('App\Models\StudentModel', "PROGRAMMECODE","PROGRAMMECODE");
  
    }
     public function department(){
        return $this->belongsTo('App\Models\DepartmentModel', "DEPTCODE","DEPTCODE");
    }
     
}
