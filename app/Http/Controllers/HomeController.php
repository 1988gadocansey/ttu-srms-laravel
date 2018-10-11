<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Http\Response;
use App\Models\MessagesModel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\User;
use App\Models\StudentChart;


class HomeController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('auth');
        ini_set('max_execution_time', 180000);  
       
        $user=@\Auth::user()->id;
        $date=new \Datetime();
        @User::where("id", $user)->update(array("last_login"=>$date));
      
        
    }
    public function chartjs()
    {
        $viewer = User::select(\DB::raw("SUM(id) as count"))
            ->orderBy("created_at")
            ->groupBy(\DB::raw("year(created_at)"))
            ->get()->toArray();
        $viewer = array_column($viewer, 'count');

        $click = User::select(\DB::raw("SUM(id) as count"))
            ->orderBy("created_at")
            ->groupBy(\DB::raw("year(created_at)"))
            ->get()->toArray();
        $click = array_column($click, 'count');


        return view('graphs.try')
            ->with('viewer',json_encode($viewer,JSON_NUMERIC_CHECK))
            ->with('click',json_encode($click,JSON_NUMERIC_CHECK));
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request,SystemController $sys)
    {   if(@\Auth::user()->department=="Admissions"){
            //$sys->getZenith();
            return redirect("applicants/view");
         }

         if(@\Auth::user()->password=='$2y$10$O1CDHaNMOUhhipT9ftmpyew2IXwIdxBNvUPzJCSDh1V2VS0KlPgx6'){
            //$sys->getZenith();
            return view("users.updateProfile");
         }

         if(@\Auth::user()->password=='$2y$10$O1CDHaNMOUhhipT9ftmpyew2IXwIdxBNvUPzJCSDh1V2VS0KlPgx6'){
            //$sys->getZenith();
            return redirect("change_password");
         }

        if(@\Auth::user()->phone==""){
            return view("users.updateProfile");
        }
        else{
           
            //ini_set('max_execution_time', 50000);
         // $sys->getZenith();
           // $sys->generateIndexNumbers();
           /* $dataGenerator=Models\StudentModel::where("LEVEL","100H")->orWhere("LEVEL","100NTT")
                ->orWhere("LEVEL","100BTT")->orWhere("LEVEL","500")->get();
            foreach($dataGenerator as $row){
                $index=$sys->assignIndex($row->PROGRAMMECODE);
                Models\StudentModel::where("STNO",$row->STNO)->update(array("INDEXNO"=>$index));
                Models\PortalPasswordModel::where("username",$row->STNO)->update(array("username"=>$index));
            }*/

 


        $lastVisit=\Carbon\Carbon::createFromTimeStamp(strtotime(@\Auth::user()->last_login))->diffForHumans();

        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;

        $studentDetail=Models\StudentModel::query()->where('STATUS','In school')->sum("BILL_OWING");
        $total=@Models\StudentModel::query()->where('STATUS','In school')->count("ID");

        $totalRegistered =Models\StudentModel::query()->where('REGISTERED','1')->where('STATUS','In school')
                     ->count();
                      
              // $totalRegistered =count($totalRegistered );

               /*
                Gad--- i added the academic year and sem to reduce query weight
               */
       $registered= @Models\AcademicRecordsModel::query()->where('lecturer',@\Auth::user()->fund)
       ->where('sem',$sem)->where('year',$year)
       ->count("id");

        $totalOwing=@$sys->formatMoney($studentDetail);
        //Payment details
        $totalPaid=Models\FeePaymentModel::query()->where('YEAR',$year)->where('FEE_TYPE','School Fees')->sum("AMOUNT");

        $paid=@$sys->formatMoney($totalPaid);
 
        // statistics
         $totalProgram=@Models\StudentModel::query()->where('SYSUPDATE','1')->groupBy("LEVEL")->get();

$stClass=@Models\StudentModel::query()->where('GRADUATING_GROUP','2017/2018')->where('LEVEL','300H')->where('CGPA','>',3.994)->count();

$ndClassU=@Models\StudentModel::query()->where('GRADUATING_GROUP','2017/2018')->where('LEVEL','300H')->where('CGPA','>',2.994)->where('CGPA','<=',3.994)->count();
$ndClassL=@Models\StudentModel::query()->where('GRADUATING_GROUP','2017/2018')->where('LEVEL','300H')->where('CGPA','>',1.994)->where('CGPA','<=',2.994)->count();
$pass=@Models\StudentModel::query()->where('GRADUATING_GROUP','2017/2018')->where('LEVEL','300H')->where('CGPA','>',1.494)->where('CGPA','<=',1.994)->count();
$fail=@Models\StudentModel::query()->where('GRADUATING_GROUP','2017/2018')->where('LEVEL','300H')->where('CGPA','<=',1.494)->count();


$prgramClass=[];
$prgram1stClass=[];
$prgram2ndUClass=[];
$prgram2ndLClass=[];
$prgramPassClass=[];
$prgramFailClass=[];

//$programCount =0;
$newI = 2017;
$programCount =\DB::table('tpoly_students')->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', "LIKE", "" ."H". "%")->distinct('PROGRAMMECODE')->count('PROGRAMMECODE');

for($programCount1= 0; $programCount1 < $programCount; $programCount1++){
    
         }
//$prgramClas=@Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', "LIKE", "" ."H". "%")->distinct('PROGRAMMECODE')->select('PROGRAMMECODE')->get();
    $prgramClas=@Models\ProgrammeModel::join('tpoly_students', 'tpoly_programme.PROGRAMMECODE', '=', 'tpoly_students.PROGRAMMECODE')->where("tpoly_programme.TYPE","HND")->where('tpoly_students.GRADUATING_GROUP', "LIKE", "" .$newI. "%")->distinct('tpoly_students.PROGRAMMECODE')->select('tpoly_programme.PROGRAMMECODE', 'tpoly_programme.PROGRAMME')->get();
             foreach ($prgramClas as $key => $value) {
              array_push($prgramClass, $value['PROGRAMME']);
//dd($prgramClass);
              $prgramClasTotal = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->count();

              $prgram1stClas = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->where('CGPA','>',3.994)->count();
              $prgram1stClassTotal = round(($prgram1stClas/$prgramClasTotal)*100,2);
                array_push($prgram1stClass, $prgram1stClassTotal);

                //dd($prgram1stClas, $prgramClasTotal, $prgram1stClassTotal, $value['PROGRAMMECODE']);

             $prgram2ndUClas = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->where('CGPA','>',2.994)->where('CGPA','<=',3.994)->count();
             $prgram2ndUClassTotal = round(($prgram2ndUClas/$prgramClasTotal)*100,2);
                array_push($prgram2ndUClass, $prgram2ndUClassTotal);

             $prgram2ndLClas = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->where('CGPA','>',1.994)->where('CGPA','<=',2.994)->count();
             $prgram2ndLClassTotal = round(($prgram2ndLClas/$prgramClasTotal)*100,2);
                array_push($prgram2ndLClass, $prgram2ndLClassTotal);

             $prgramPassClas = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->where('CGPA','>',1.494)->where('CGPA','<=',1.994)->count();
             $prgramPassClassTotal = round(($prgramPassClas/$prgramClasTotal)*100,2);
                array_push($prgramPassClass, $prgramPassClassTotal);

             $prgramFailClas = @Models\StudentModel::query()->where('GRADUATING_GROUP', "LIKE", "" .$newI. "%")->where('PROGRAMMECODE', $value['PROGRAMMECODE'])->where('CGPA','<=',1.494)->count();
             $prgramFailClassTotal = round(($prgramFailClas/$prgramClasTotal)*100,2);
                array_push($prgramFailClass, $prgramFailClassTotal);

          }
             //array_push($prgramClass, $prgramClas);
//dd($prgramClass);

$eachYearTotal=[];
         $years = [];
         $totalYearTotal = [];
         $totalMalePerYear = [];
         $totalFemalePerYear = [];

         $yearData = [];
         // $data = Student::all('INDEXNO');
         // foreach ($data as $key => $value) {
         //     echo $value['STNO']."<br>";
         // }
         // for($stdData =2014; $stdData<=date('Y'); $stdData++){
         //    echo ($data[$stdData])."<br>";
         // }
         $newI = 2017;

         $MaleFresh1 = @Models\StudentChart::where('SEX', 'Male')
                 ->where('STNO', "LIKE", "" .'2018'. "%")
                 ->where('STATUS', 'In school')
                 ->count();
             //array_push($totalMalePerYear, $Male);

             $FemaleFresh1 = @Models\StudentChart::where('SEX', 'Female')
                 ->where('STNO', "LIKE", '2018'. "%")
                 ->where('STATUS', 'In school')
                 ->count();

             $MaleFresh = ($MaleFresh1 / ($MaleFresh1 + $FemaleFresh1))*100;
              $FemaleFresh = round(($FemaleFresh1 / ($MaleFresh1 + $FemaleFresh1))*100,2);

              $MaleFinal1 = @Models\StudentChart::where('SEX', 'Male')
                 ->where('GRADUATING_GROUP', "LIKE", "" ."2017". "%")
                 ->where('STATUS','Alumni')
                 ->count();
             //array_push($totalMalePerYear, $Male);

             $FemaleFinal1 = @Models\StudentChart::where('SEX', 'Female')
                 ->where('GRADUATING_GROUP', "LIKE", "" ."2017". "%")
                 ->where('STATUS','Alumni')
                 ->count();

             $MaleFinal = ($MaleFinal1 / ($MaleFinal1 + $FemaleFinal1))*100;
              $FemaleFinal = round(($FemaleFinal1 / ($MaleFinal1 + $FemaleFinal1))*100,2);
             //array_push($totalFemalePerYear, $Female);
         for($i= $newI; $i <= $newI +4; $i++){
             array_push($years,$i);
$iPlusOne = $i + 1;
             $yrgp = $i.'/'.$iPlusOne;
             // substr($i, 0,2);
            // ".$var."
             

             

             $Male = @Models\StudentChart::where('SEX', 'Male')
                 ->where('GRADUATING_GROUP', "LIKE", "" .$i. "%")->count();
             array_push($totalMalePerYear, $Male);

             $Female = @Models\StudentChart::where('SEX', 'Female')
                 ->where('GRADUATING_GROUP', "LIKE", $i. "%")->count();
             array_push($totalFemalePerYear, $Female);

             array_push($yearData, $yrgp);

             $eachYearTotal = $Male + $Female;
             array_push($totalYearTotal, $eachYearTotal);

         }

//dd($totalFemalePerYear);
         //$TotalStudent = Student::whereBetween('yearAdmitted', [$years[0], $years[count($years )-1]])->count();

            $nt100 = @Models\StudentChart::where('LEVEL', '100NT')
                 ->where('STATUS', "In school")->count();
                

              $nt200 = @Models\StudentChart::where('LEVEL', '200NT')
                 ->where('STATUS', "In school")->count();

         $hnd100 = @Models\StudentChart::where('LEVEL', '100H')
                 ->where('STATUS', "In school")->count();

             $hnd200 = @Models\StudentChart::where('LEVEL', '200H')
                 ->where('STATUS', "In school")->count();

             $hnd300 = @Models\StudentChart::where('LEVEL', '300H')
                 ->where('STATUS', "In school")->count();

                
                  $btt100 = @Models\StudentChart::where('LEVEL', '100BTT')
                 ->where('STATUS', "In school")->count();

                 $btt200 = @Models\StudentChart::where('LEVEL', '200BTT')
                 ->where('STATUS', "In school")->count();

                 $bt100 = @Models\StudentChart::where('LEVEL', '100BT')
                 ->where('STATUS', "In school")->count();
                 $bt200 = @Models\StudentChart::where('LEVEL', '200BT')
                 ->where('STATUS', "In school")->count();
                 $bt300 = @Models\StudentChart::where('LEVEL', '300BT')
                 ->where('STATUS', "In school")->count();
                 $bt400 = @Models\StudentChart::where('LEVEL', '400BT')
                 ->where('STATUS', "In school")->count();
                  $mt100 = @Models\StudentChart::where('LEVEL', '500MT')
                 ->where('STATUS', "In school")->count();
                 $mt200 = @Models\StudentChart::where('LEVEL', '600MT')
                 ->where('STATUS', "In school")->count();

                 $admitPreviousHnd =  @Models\StudentChart::where('LEVEL', '200H')
                 ->where('STATUS', "In school")->count();

                 $admitPreviousBtt = @Models\StudentChart::where('LEVEL', '200BTT')
                 ->where('STATUS', "In school")->count();

                 $admitPreviousNt =  @Models\StudentChart::where('LEVEL', '200NT')
                 ->where('STATUS', "In school")->count();

                 $admitPreviousBt = @Models\StudentChart::where('LEVEL', '200BT')
                 ->where('STATUS', "In school")->count();

                $admitPreviousMt =  @Models\StudentChart::where('LEVEL', '600MT')
                 ->where('STATUS', "In school")->count();

                $admitCurrentHnd =  @Models\StudentChart::where('LEVEL', '100H')
                 ->where('STATUS', "In school")->count();

                 $admitCurrentBtt = @Models\StudentChart::where('LEVEL', '100BTT')
                 ->where('STATUS', "In school")->count();

                 $admitCurrentNt =  @Models\StudentChart::where('LEVEL', '100NT')
                 ->where('STATUS', "In school")->count();

                 $admitCurrentBt = @Models\StudentChart::where('LEVEL', '100BT')
                 ->where('STATUS', "In school")->count();

                $admitCurrentMt =  @Models\StudentChart::where('LEVEL', '500MT')
                 ->where('STATUS', "In school")->count();

                 $previousTotal = $admitPreviousMt + $admitPreviousNt + $admitPreviousHnd + $admitPreviousBt + $admitPreviousBtt;
                 $currentTotal = $admitCurrentMt + $admitCurrentNt + $admitCurrentHnd + $admitCurrentBt + $admitCurrentBtt; 




        return view('dashboard')->with('paid', $paid)
                                ->with('owing', $totalOwing)
                                  ->with('register', $registered)
                                  ->with('total', $total)
                                  ->with('totalRegistered', $totalRegistered)
                                  ->with('data', $totalProgram)
                                ->with('sem', $sem)
                                ->with('year', $year)
                                ->with('lastVisit', $lastVisit)
                                ->with('totalYearTotal', $totalYearTotal)
                                ->with('totalMalePerYear', $totalMalePerYear)
                                ->with('totalFemalePerYear', $totalFemalePerYear)
                                ->with('yearData', $yearData)
                                ->with('stClass', $stClass)
                                ->with('ndClassU', $ndClassU)
                                ->with('ndClassL', $ndClassL)
                                ->with('pass', $pass)
                                ->with('fail', $fail)
                                ->with('nt100', $nt100)
                                ->with('nt200', $nt200)
                                ->with('hnd100', $hnd100)
                                ->with('hnd200', $hnd200)
                                ->with('hnd300', $hnd300)
                                ->with('btt100', $btt100)
                                ->with('btt200', $btt200)
                                ->with('bt100', $bt100)
                                ->with('bt200', $bt200)
                                ->with('bt300', $bt300)
                                ->with('bt400', $bt400)
                                 ->with('mt100', $mt100)
                                ->with('mt200', $mt200)
                                ->with('admitPreviousHnd',$admitPreviousHnd)
                                ->with('admitPreviousBtt',$admitPreviousBtt)
                                ->with('admitPreviousBt',$admitPreviousBt)
                                ->with('admitPreviousNt',$admitPreviousNt)
                                ->with('admitPreviousMt',$admitPreviousMt)
                                ->with('admitCurrentHnd',$admitCurrentHnd)
                                ->with('admitCurrentBtt',$admitCurrentBtt)
                                ->with('admitCurrentBt',$admitCurrentBt)
                                ->with('admitCurrentNt',$admitCurrentNt)
                                ->with('admitCurrentMt',$admitCurrentMt)
                                ->with('currentTotal',$currentTotal)
                                ->with('previousTotal',$previousTotal) 
                                ->with('prgramClass',$prgramClass)
                                ->with('prgram1stClass',$prgram1stClass)
                                ->with('prgram2ndUClass',$prgram2ndUClass)                             
                                ->with('prgram2ndLClass',$prgram2ndLClass)
                                ->with('prgramPassClass',$prgramPassClass)
                                ->with('prgramFailClass',$prgramFailClass)
                                ->with('MaleFresh',$MaleFresh)
                                ->with('FemaleFresh',$FemaleFresh)
                                ->with('MaleFinal',$MaleFinal)
                                ->with('FemaleFinal',$FemaleFinal)
                                ->with('MaleFresh1',$MaleFresh1)
                                ->with('FemaleFresh1',$FemaleFresh1)
                                ->with('MaleFinal1',$MaleFinal1)
                                ->with('FemaleFinal1',$FemaleFinal1)
                                ;



        }

    }
    public function accountStatement(Request $request, SystemController $sys) {
        $student=@\Auth::user()->username;


        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;

        $studentDetail=Models\StudentModel::query()->where('STATUS','In school')->where('INDEXNO',$student)->first();


        $outstandingBill=@$sys->formatMoney($studentDetail->BILL_OWING);
        $SemesterBill=@$sys->formatMoney($studentDetail->BILLS);
          //Payment details
        $paymentDetail=  Models\FeePaymentModel::query()->where('INDEXNO',$student)->orderBy('LEVEL','DESC')->orderBy('YEAR','DESC')->paginate(100);
        return view("students.account_statement")->with("transaction", $paymentDetail)
                ->with('balance', $outstandingBill)
                ->with('semesterBill', $paymentDetail);
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function buildChart(Request $request)
    {
         $viewer = User::select(\DB::raw("SUM(id) as count"))
        ->orderBy("created_at")
        ->groupBy(\DB::raw("year(created_at)"))
        ->get()->toArray();
    $viewer = array_column($viewer, 'count');

    $click = User::select(\DB::raw("SUM(id) as count"))
        ->orderBy("created_at")
        ->groupBy(\DB::raw("year(created_at)"))
        ->get()->toArray();
    $click = array_column($click, 'count');

    return view('graph')
            ->with('viewer',json_encode($viewer,JSON_NUMERIC_CHECK))
            ->with('click',json_encode($click,JSON_NUMERIC_CHECK));
    }
    public function getLaraChart()
    {
        $lava = new Lavacharts; // See note below for Laravel

        $popularity = $lava->DataTable();
        $data = \App\Models\CountryUser::select("name as 0","total_users as 1")->get()->toArray();

        $popularity->addStringColumn('Country')
                   ->addNumberColumn('Popularity')
                   ->addRows($data);

        $lava->GeoChart('Popularity', $popularity);

        return view('graph',compact('lava'));
    }
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }
}
