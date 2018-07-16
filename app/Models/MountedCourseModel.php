<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;





class MountedCourseModel extends Model

{

    use LogsActivity;

    //

    /**

     * The database table used by the model.

     *

     * @var string

     */

    protected $table = 'tpoly_mounted_courses';

     protected static $logAttributes = ['COURSE', 'COURSE_LEVEL','COURSE_CREDIT','PROGRAMME'];

    protected $primaryKey="ID";

    protected $guarded = ['ID'];

    public $timestamps = false;

    public function course(){

        return $this->belongsTo('App\Models\CourseModel', "COURSE_CODE","COURSE_CODE");

    }

    public function courses(){

        return $this->hasMany('App\Models\CourseModel', "COURSE_CODE","COURSE_CODE");

    }

     public function lecturer(){

        return $this->belongsTo('App\Models\WorkerModel', "LECTURER","staffID");

    }

    /**

     * Get the user that owns the course.

     */

    public function user()

    {

        return $this->belongsTo(User::class);

    }

     public function levels(){

        return $this->belongsTo('App\Models\LevelModel', "COURSE_LEVEL","name");

    }

}

