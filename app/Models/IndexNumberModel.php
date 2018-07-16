<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class IndexNumberModel extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'programme_prefix';

    protected $primaryKey="id";
    protected $guarded = ['id'];
    public $timestamps = false;
    public function program(){
        return $this->belongsTo('App\Models\ProgrammeModel', "programme","PROGRAMMECODE");
    }
}
