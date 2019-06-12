<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;

use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Excel;
class LiaisonController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }

    public function log_query()
    {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }

    public function createZones(Request $request, SystemController $sys){

        if($request->method()=="GET"){

            $region=$sys->getRegions();

            return view ("liaison.create")->with("regions",$region);
        }

        else {


            \DB::beginTransaction();
            try {




                $total = count($request->input('zones'));
                $zone= $request->input('zones');
                $sub = $request->input('sub');


                for ($i = 0; $i < $total; $i++) {
                    $data = new Models\ZonesModel();
                    $data->zones = $zone[$i];
                    $data->sub_zone = $sub[$i];

                    $data->save();
                    \DB::commit();

                }
                return redirect()->back()->with("success", "<span style='font-weight:bold;font-size:13px;'> Zones successfully created </span> ");


            } catch (\Exception $e) {
                \DB::rollback();
            }
        }

    }


    public function zones(Request $request, SystemController $sys)
    {

        $data=Models\ZonesModel::paginate();

        return view("liaison.zones")->with("data",$data);

    }

    public function units(Request $request, SystemController $sys)
    {

        $data=Models\AddressModel::paginate();

        return view("liaison.unit")->with("data",$data);

    }


    public function createUnit(Request $request, SystemController $sys){

        if($request->method()=="GET"){



            return view ("liaison.createUnit");
        }

        else {


            \DB::beginTransaction();
            try {




                $total = count($request->input('names'));
                $units= $request->input('names');


                for ($i = 0; $i < $total; $i++) {
                    $data = new Models\AddressModel();
                    $data->addresses = $units[$i];

                    $data->save();
                    \DB::commit();

                }
                return redirect()->back()->with("success", "<span style='font-weight:bold;font-size:13px;'>Authority(s) successfully created </span> ");


            } catch (\Exception $e) {
                \DB::rollback();
            }
        }

    }



        /**
     * Display a list of all of the user's task.
     *
     * @param  Request $request
     * @return Response
     */

    public function index(Request $request, SystemController $sys) {



             $data =Models\LiaisonModel::query();




        if ($request->has('program') && trim($request->input('program')) != "") {
            $data->whereHas('studentDetials', function($q)use ($request) {
                $q->whereHas('programme', function($q)use ($request) {
                    $q->whereIn('PROGRAMMECODE', [$request->input('program')]);
                });
            });
        }


        if ($request->has('department') && trim($request->input('department')) != "") {
            $data->whereHas('studentDetials', function($q)use ($request) {
                $q->whereHas('programme', function($q )use ($request) {
                    $q->whereHas('departments', function($q)use ($request) {
                        $q->whereIn('DEPTCODE', [$request->input('department')]);
                    });
                });
            }) ;
        }

        if ($request->has('type') && trim($request->input('type')) != "") {
            $data->whereHas('programme', function($q)use ($request) {

                $q->where('TYPE', [$request->input('type')]);

            });
        }

        if ($request->has('school') && trim($request->input('school')) != "") {
            $data->whereHas('programme', function($q)use ($request) {
                $q->whereHas('departments', function($q)use ($request) {

                    $q->whereHas('school', function($q)use ($request) {
                        $q->whereIn('FACCODE', [$request->input('school')]);
                    });
                });
            });
        }






        if ($request->has('level') && trim($request->input('level')) != "") {
            $data->where("level", $request->input("level", ""));
        }

        if ($request->has('year') && trim($request->input('year')) != "") {
            $data->where("year", $request->input("year", ""));
        }

        if ($request->has('as') && trim($request->input('as')) != "") {
            $data->where("status", $request->input("as", ""));
        }


        if ($request->has('search') && trim($request->input('search')) != "" && trim($request->input('by')) != "") {
            // dd($request);
            $data->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%")
            ;
        }


        $records = $data->orderBy('year')->orderBy('terms')->paginate(200);


        $request->flashExcept("_token");

        \Session::put('LA', $records);
        //dd($records);
        return view('liaison.index')->with("records", $records)
            ->with('year', $sys->years())


            ->with('level', $sys->getLevelList())

            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());

    }

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     */
    public function destroyAddress(Request $request)
    {
         Models\AddressModel::where('id', $request->input("id"))->delete();






            return redirect()->back()->with("success", "<span style='font-weight:bold;font-size:13px;'>Authority(s) successfully deleted </span> ");


    }
    public function destroyZones(Request $request)
    {
        Models\ZonesModel::where('id', $request->input("id"))->delete();






            return redirect()->back()->with("success", "<span style='font-weight:bold;font-size:13px;'>Zone successfully deleted </span> ");


    }
    public function getZone() {


        $zone = \DB::table('liaison_zones')
            ->lists('sub_zone', 'id');
        return $zone;


    }
    public function getZoneName($id) {


        $zone = \DB::table('liaison_zones')
            ->where('id', $id)->first();
        return $zone->zones."(".$zone->sub_zone.")";


    }
    public function bulkPrint(Request $request, SystemController $sys)
    {

        if($request->method()=="GET"){


            $yearList=$sys->years();
            $program=$sys->getProgramList();
            $zone=$this->getZone();

            return view ("liaison.bulkPrintForm")->with("years", $yearList)->with("program",$program)
                ->with("levels",$sys->getLevelList())->with("zone",$zone);
        }

        else {

            set_time_limit(200000000);
            $query = \Session::get('assumption');


            $arraycc = $sys->getSemYear();
            $yearcc = $arraycc[0]->YEAR;

            $level=$request->input("level");

            $year=$request->input("year");
            $zone=$request->input("zone");

            $zoneName=$this->getZoneName($zone);

            $kojoSense = 0;
            $array = $sys->getSemYear();

            $query =Models\AssumptionDutyModel::join('tpoly_students', 'tpoly_students.INDEXNO', '=', 'liaison_assumption_duty.indexno')
                ->join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')
                ->where("tpoly_students.STATUS","In school");

                  if ($request->has('zone') && trim($request->input('zone')) != "") {
                      $query->where("liaison_assumption_duty.company_subzone", $zone);
                  }


                     if ($request->has('level') && trim($request->input('level')) != "") {
                         $query->where("liaison_assumption_duty.level", $level);
                     }

                    if ($request->has('from_date') && $request->has('to_date') ) {

                        $query->whereBetween(\DB::raw('liaison_assumption_duty.created_at'), array($request->input('from_date'), $request->input('to_date')));

                    }

                    if ($request->has('year') && trim($request->input('year')) != "") {
                        $query->where("liaison_assumption_duty.year", $request->input("year", ""));
                    }
                    if ($request->has('program') && trim($request->input('program')) != "") {
                        $query->whereHas('studentDetials', function($q)use ($request) {
                            $q->whereHas('programme', function($q)use ($request) {
                                $q->whereIn('tpoly_students.PROGRAMMECODE', [$request->input('program')]);
                            });
                        });
                    }
            $program=$request->input('zone');
            $data=$query->orderBy('tpoly_students.NAME')
                ->orderBy('tpoly_students.LEVEL')
                ->orderBy('tpoly_students.INDEXNO')
                ->select('tpoly_students.INDEXNO', 'tpoly_students.NAME', 'tpoly_programme.PROGRAMME', 'tpoly_students.TELEPHONENO', 'liaison_assumption_duty.company_name','liaison_assumption_duty.company_phone', 'liaison_assumption_duty.company_town','liaison_assumption_duty.company_exact_location')
                
                ->groupBy('tpoly_students.INDEXNO')
                ->groupBy('liaison_assumption_duty.company_name')
                ->get();



            return Excel::create($program."_".$yearcc, function ($excel) use ($kojoSense, $sys, $yearcc, $data,$zoneName,$program){

                $excel->getProperties()
                    ->setCreator("TTU")
                    ->setTitle("TTU ASSUMPTION OF DUTY REPORTS")
                    ->setLastModifiedBy("INDUSTRIAL LIAISON OFFICE")
                    ->setDescription('Multiple sheets showing all results')
                    ->setSubject("LIAISON OFFICE")
                    ->setKeywords('TP, marks, rs, normal')
                ;



                $excel->sheet($program, function ($sheet) use ($kojoSense, $sys, $yearcc, $data,$zoneName,$program) {



                    $sheet->fromArray($data);

                    $sheet->prependRow(1, array(' '.' '.' '.''
                    ));
                    $current_time = \Carbon\Carbon::now()->toDateTimeString();
                    //$sheet->setCellValue('A3',$current_time);
                    $sheet->prependRow(1, array(' '.' '. $current_time
                    ));
                    $sheet->prependRow(1, array('  '. $zoneName . ' For ' .$yearcc . ' Academic Year '
                    ));

                    $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                    ));
                    $sheet->mergeCells('A1:B1');
                    $sheet->mergeCells('A2:B2');
                    $sheet->mergeCells('A3:B3');

                    $sheet->setColumnFormat(array(
                        'E6' => 'dd-mm-yyyy'
                    ));
//});
                });










            })->download('xlsx');




        }

    }

}
