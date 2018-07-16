<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class WorkerModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_workers';
    
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
    
     
}
