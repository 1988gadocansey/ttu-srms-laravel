<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;

use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AssumptionController extends Controller
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





    /**
     * Display a list of all of the user's task.
     *
     * @param  Request $request
     * @return Response
     */

    public function index(Request $request, SystemController $sys) {



        $data =Models\AssumptionDutyModel::query();




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

        if ($request->has('zone') && trim($request->input('zone')) != "") {
            $data->where("company_subzone", $request->input("zone", ""));
        }


        if ($request->has('search') && trim($request->input('search')) != "" && trim($request->input('by')) != "") {
            // dd($request);
            $data->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%")
            ;
        }


        $records = $data->orderBy('year')->orderBy('date_duty')->paginate(200);


        $request->flashExcept("_token");

        \Session::put('LA', $records);
        //dd($records);
        return view('assumption.index')->with("records", $records)
            ->with('year', $sys->years())


            ->with('level', $sys->getLevelList())
            ->with('zones', $this->getZones())

            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());

    }

    public function getZones() {


        $zones = \DB::table('liaison_zones')->groupBy("zones")
            ->lists('zones', 'id');
        return $zones;


    }


}
