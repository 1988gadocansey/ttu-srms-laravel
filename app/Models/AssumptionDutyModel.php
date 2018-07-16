<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class AssumptionDutyModel extends Model
{
    use LogsActivity;
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'liaison_assumption_duty';
    protected static $logAttributes = ['indexno', 'company_name','company_phone','company_location' ];
    protected $primaryKey="id";
    protected $guarded = ['id'];


    public function zoneDetails(){
        return $this->belongsTo('App\Models\ZonesModel', "company_subzone","id");
    }
    public function addressDetails(){
        return $this->belongsTo('App\Models\AddressModel', "company_address_to","id");
    }
    public function studentDetials(){
        return $this->belongsTo('App\Models\StudentModel', "indexno","INDEXNO");
    }
}
