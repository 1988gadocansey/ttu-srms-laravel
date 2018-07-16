<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class ExamTypeModel extends Model
{
    use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_waec_exam_type';
    protected $guarded = ['ID'];
    public $timestamps = false;
    
}
