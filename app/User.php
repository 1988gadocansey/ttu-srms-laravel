<?php

namespace App;

 
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $primaryKey="id";
     protected $guarded = ['id'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get all of the tasks for the user.
     */
    public function lecturer()
    {
        return $this->hasMany(\App\Models\AcademicRecordsModel::class);
    }
    public function hasRole($user)
    {
        $query=User::where('id', $user)->first();
        return $query->role;
    }
    public function worker(){
        return $this->belongsTo('App\Models\WorkerModel', "staffID","id");
    }
}
