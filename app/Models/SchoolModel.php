<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SchoolModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_faculty';
    protected $primaryKey = "ID";
 public function banks(){
        return $this->belongsTo('App\Models\BankModel', "BANK","ID");
    }
}
