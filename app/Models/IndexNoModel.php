<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class IndexNoModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_indexno';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
    
     
}
