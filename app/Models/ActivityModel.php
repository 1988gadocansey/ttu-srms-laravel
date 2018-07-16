<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ActivityModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_log';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
      
     public function user(){
        return $this->belongsTo('App\User', "causer_id","id");
    }
      
     
}
