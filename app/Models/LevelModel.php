<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LevelModel extends Model
{
    
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_levels';
    
    protected $primaryKey="id";
    protected $fillable=array ('name');
    
}
