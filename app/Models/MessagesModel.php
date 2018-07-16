<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MessagesModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_sms';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
     public function student(){
        return $this->belongsTo('App\Models\StudentModel', "receipient","INDEXNO");
    }
     public function sender(){
        return $this->belongsTo('App\User', "sender","id");
    }
      
     
}
