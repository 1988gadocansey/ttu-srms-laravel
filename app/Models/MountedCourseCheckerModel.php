<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MountedCourseCheckerModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_mount_course_checker';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    
   public function hod(){
        return $this->belongsTo('App\User', "fund","hod");
    }
        public $timestamps = false;  
}
