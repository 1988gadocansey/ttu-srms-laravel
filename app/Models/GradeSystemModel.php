<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GradeSystemModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_grade_system';
    protected static $logAttributes = ['type', 'grade'];
    protected $primaryKey="id";
    protected $guarded = ['id','COURSE_CODE'];
    public $timestamps = false;
    
     
}
