<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;



class AcademicCalenderModel extends Model

{

     use LogsActivity;

    //

    /**

     * The database table used by the model.

     *

     * @var string

     */

    protected $table = 'tpoly_academic_settings';

    protected static $logAttributes = ['YEAR', 'SEMESTER','STATUS','ENTER_RESULT','LIAISON','QA','RESULT_BLOCK','QAOPEN','RESULT_DATE'];

    protected $primaryKey="ID";

    protected $guarded=array ('ID');

     public $timestamps = false;

}

