<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class HallModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_hall';
      
    protected $primaryKey="ID";
      public $timestamps = false;
    
    protected $guarded = ['ID'];
    
}
