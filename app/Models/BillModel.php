<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class BillModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_bills';
    protected static $logAttributes = ['LEVEL', 'AMOUNT','PROGRAMME'];
    protected $primaryKey="ID";
    protected $guarded = ['ID'];
    public $timestamps = false;
   public function program(){
        return $this->belongsTo('App\Models\ProgrammeModel', "PROGRAMME","PROGRAMMECODE");
    }
       public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "LEVEL","name");
    }
}
