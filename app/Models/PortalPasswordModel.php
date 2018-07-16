<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class PortalPasswordModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_log_portal';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
      public function levels(){
        return $this->belongsTo('App\Models\LevelModel', "level","name");
    }
     
}
