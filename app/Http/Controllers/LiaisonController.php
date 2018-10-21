<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;

use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

        $data=Models\UnitModel::paginate();

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
                    $data->name = $units[$i];

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

}
