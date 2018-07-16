<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class FormModel extends Model
{
      //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_forms';
       protected $primaryKey="ID";
    protected $guarded = ['ID'];
    public $timestamps = false;
    public function applicants(){
        return $this->hasMany('App\Models\ApplicantModel', "FORM_NO","APPLICATION_NUMBER");
    }
     
     
}
