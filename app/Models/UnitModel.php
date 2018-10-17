<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class UnitModel extends Model
{

    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tpoly_units_LA';
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;



}
