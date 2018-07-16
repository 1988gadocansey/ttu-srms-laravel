<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OutreachModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outreach';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
     
      public function program(){
        return $this->belongsTo('App\Models\ProgrammeModel', "programme","PROGRAMMECODE");
    }
     public function admitedProgram(){
        return $this->belongsTo('App\Models\ProgrammeModel', "programmeAdmitted","PROGRAMMECODE");
    }
}
