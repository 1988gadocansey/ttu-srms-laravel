<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class ZonesModel extends Model
{

    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'liaison_zones';
    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function regionDetails(){
        return $this->belongsTo('App\Models\RegionsModel', "region","id");
    }

}
