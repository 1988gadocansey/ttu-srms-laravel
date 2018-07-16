<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OutreachCodeModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outreachCode';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
      
}
