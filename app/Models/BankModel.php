<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BankModel extends Model
{
     use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_banks';
      protected static $logAttributes = ['NAME', 'ACCOUNT_NUMBER'];
    protected $primaryKey="ID";
    protected $fillable=array ('NAME','ACCOUNT_NUMBER');
    
}
