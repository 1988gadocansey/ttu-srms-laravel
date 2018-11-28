<?php

namespace App\Http\Controllers;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models;
use App\User;
use App\Models\AcademicRecordsModel;
use PhpParser\Node\Expr\AssignOp\Mod;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Excel;

class NabptexController extends Controller
{

    /**
     * Create a new controller instance.
     *

     * @return void
     */
    public function __construct()
    {
        //set_time_limit(36000);
        ini_set('max_input_vars', '90000');
        ini_set('max_execution_time', 180000);
        $this->middleware('auth');


    }
    public function log_query() {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }
    
public function showFileUploadResults(SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Admin' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer' || @\Auth::user()->role=='Registrar'){
            $programme=$sys->getProgramList();
            $course=$sys->getMountedCourseList3();

            return view('courses.downloadResults')->with('programme', $programme)->with('courses',$course)
                ->with('level', $sys->getLevelList())->with('year',$sys->years());
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }


    public function showFileUploadError(SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Admin' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer' || @\Auth::user()->role=='Registrar'){
            $programme=$sys->getProgramList();
            $course=$sys->getMountedCourseList3();

            return view('courses.downloadError')->with('programme', $programme)->with('courses',$course)
                ->with('level', $sys->getLevelList())->with('year',$sys->years());
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }    

public function downloadError(Request $request, SystemController $sys )

    {

        
        set_time_limit(200000000);
        $this->validate($request, [


            'program' => 'required',
           // 'sem' => 'required',
           // 'year' => 'required',
            'year' => 'required',
        ]);

        $arraycc = $sys->getSemYear();
        $yearcc = $arraycc[0]->YEAR;
        //$yearcc = '2017/2018';

        $currentResultsArray=$arraycc[0]->RESULT_DATE;
       // dd($resultb);

        $currentResultsArray1 = explode(',',$currentResultsArray);
        $resultyear = $currentResultsArray1[0];
        $resultsem = $currentResultsArray1[1];

        //$year = $array[0]->YEAR;
        if ($arraycc[0]->YEAR != $resultyear) {
            $yearcc = $resultyear;
        }


        $kojoSense = 0;
        $array = $sys->getSemYear();
        //$sem = $request->input("sem");
        $year = $request->input("year");
        //$level = $request->input("level");
        $program = $request->input("program");
        //dd($program);
        $rsaProgram = $sys->getProgramResult($program);
        //$course = $request->input("course");
        $lecturer = @\Auth::user()->fund;
        $lectname = @\Auth::user()->name;
        $programme2 = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->first();
        $programme = $programme2->PROGRAMME;
        $dpt1 = $programme2->DEPTCODE;
        $dpt2 = \DB::table('tpoly_department')->where('DEPTCODE',$dpt1)->first();
        $dpt3 = $dpt2->DEPARTMENT;
        $fac1 = $dpt2->FACCODE;
        $fac2 = \DB::table('tpoly_faculty')->where('FACCODE',$fac1)->first();
        $fac3 = $fac2->FACULTY;


        $data = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')
            ->orderBy("tpoly_students.STATUS")     
            ->orderBy("tpoly_academic_record.indexno")
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.resit")
            ->select('tpoly_academic_record.id', 'tpoly_academic_record.course', 'tpoly_academic_record.code', 'tpoly_academic_record.credits', 'tpoly_academic_record.student', 'tpoly_academic_record.indexno', 'tpoly_academic_record.total as mm', 'tpoly_academic_record.grade', 'tpoly_academic_record.gpoint', 'tpoly_academic_record.year', 'tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'), 'tpoly_academic_record.yrgp', 'tpoly_academic_record.groups', 'tpoly_academic_record.lecturer', 'tpoly_academic_record.resit', 'tpoly_academic_record.dateRegistered', 'tpoly_academic_record.createdAt', 'tpoly_academic_record.updates', 'tpoly_academic_record.programme', \DB::raw('concat(tpoly_academic_record.code, tpoly_academic_record.indexno)'), 'tpoly_academic_record.total as tt', \DB::raw('concat(tpoly_academic_record.code, tpoly_academic_record.indexno, tpoly_academic_record.resit)'), 'tpoly_academic_record.total as yes', \DB::raw('concat(tpoly_students.INDEXNO, tpoly_students.STATUS)'), 'tpoly_students.STATUS')
            ->get();


            $dataResit = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')
            ->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year) 
            ->where('tpoly_academic_record.resit','no')
            ->where('tpoly_academic_record.grade','F')
            ->where('tpoly_academic_record.total','<',50)    
            ->orderBy("tpoly_academic_record.indexno")
            ->select('tpoly_academic_record.id', \DB::raw('case when tpoly_academic_record.resit = "no" and tpoly_academic_record.grade = "F" and tpoly_academic_record.total < 50 then CONCAT(tpoly_academic_record.indexno,tpoly_academic_record.resit) end as tt'), \DB::raw('GROUP_CONCAT(if((tpoly_academic_record.resit = "no" and tpoly_academic_record.grade = "F" and tpoly_academic_record.total < 50),tpoly_academic_record.code, null)) as yy'), "tpoly_academic_record.indexno")
            ->groupBy('tpoly_academic_record.indexno')
            ->get();

                            

            //$dataMerge = array_merge($data, $dataResit);
//SELECT `id`, `indexno`, case when resit = 'no' and grade = 'F' and total < 50 then GROUP_CONCAT(code) end FROM `tpoly_academic_record` where `yrgp` = '2017/2018' GROUP BY `indexno`
            $kojoSen2 = count($data)+7; 
            $kojoSense = count($data)+1;

        

        //dd($kojoSensible);

        return Excel::create($year.'_'.$programme, function ($excel) use ($data,$program,$kojoSen2,$yearcc,$year,$programme,$kojoSense,$dpt3,$fac3,$lectname,$dataResit,$sys){

            $excel->getProperties()
   ->setCreator($lectname)
   ->setTitle($year.'_'.$programme)
   ->setLastModifiedBy($lectname)
   ->setDescription('Multiple sheets showing all results')
   ->setSubject($year)
   ->setKeywords('TP, marks, rs, normal')
   ;

            $excel->sheet('TP', function ($sheet) use ($data,$kojoSense,$program,$kojoSen2,$year,$programme,$dpt3,$fac3,$lectname,$dataResit) {
                


                $sheet->fromArray($data);


                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$programme
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$dpt3.' DEPARTMENT'
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$fac3
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

                $sheet->setCellValue('D2','');
                $sheet->setCellValue('D3','');
                $sheet->setCellValue('D4','Year Group :');
                $sheet->setCellValue('D5','');

                $sheet->setCellValue('F2','');
                $sheet->setCellValue('F3',$kojoSen2);
                $sheet->setCellValue('F4',$year);
             
                $sheet->setCellValue('J3','');
                //$sheet->setCellValue('V7',$year);
                $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
           
//});
            });

            $excel->sheet('TP2', function ($sheet) use ($data,$kojoSense,$program,$kojoSen2,$year,$programme,$dpt3,$fac3,$lectname,$dataResit) {
                


                $sheet->fromArray($dataResit);


                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$programme
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$dpt3.' DEPARTMENT'
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$fac3
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

                $sheet->setCellValue('D2','');
                $sheet->setCellValue('D3','');
                $sheet->setCellValue('D4','Year Group :');
                $sheet->setCellValue('D5','');

                $sheet->setCellValue('F2','');
                $sheet->setCellValue('F3',$kojoSen2);
                $sheet->setCellValue('F4',$year);
             
                $sheet->setCellValue('J3','');
                //$sheet->setCellValue('V7',$year);
                $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
           
//});
            });


})->download('xlsx');

        }

        public function downloadResults(Request $request, SystemController $sys )

    {

        
        set_time_limit(200000000);
        $this->validate($request, [


            'program' => 'required',
           // 'sem' => 'required',
           // 'year' => 'required',
            'year' => 'required',
        ]);

        $arraycc = $sys->getSemYear();
        $yearcc = $arraycc[0]->YEAR;
        //$yearcc = '2017/2018';

        $currentResultsArray=$arraycc[0]->RESULT_DATE;
       // dd($resultb);

        $currentResultsArray1 = explode(',',$currentResultsArray);
        $resultyear = $currentResultsArray1[0];
        $resultsem = $currentResultsArray1[1];

        //$year = $array[0]->YEAR;
        if ($arraycc[0]->YEAR != $resultyear) {
            $yearcc = $resultyear;
        }


        $kojoSense = 0;
        $array = $sys->getSemYear();
        //$sem = $request->input("sem");
        $year = $request->input("year");
        //$level = $request->input("level");
        $program = $request->input("program");
        //dd($program);
        $rsaProgram = $sys->getProgramResult($program);
        //$course = $request->input("course");
        $lecturer = @\Auth::user()->fund;
        $lectname = @\Auth::user()->name;
        $programme2 = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->first();
        $programme = $programme2->PROGRAMME;
        $dpt1 = $programme2->DEPTCODE;
        $dpt2 = \DB::table('tpoly_department')->where('DEPTCODE',$dpt1)->first();
        $dpt3 = $dpt2->DEPARTMENT;
        $fac1 = $dpt2->FACCODE;
        $fac2 = \DB::table('tpoly_faculty')->where('FACCODE',$fac1)->first();
        $fac3 = $fac2->FACULTY;


        $data = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')  
            ->orderBy("tpoly_students.STATUS")   
            ->orderBy("tpoly_academic_record.indexno")
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.resit")
            ->select('tpoly_academic_record.id', 'tpoly_academic_record.course', 'tpoly_academic_record.code', 'tpoly_academic_record.credits', 'tpoly_academic_record.student', 'tpoly_academic_record.indexno', 'tpoly_academic_record.total as mm', 'tpoly_academic_record.grade', 'tpoly_academic_record.gpoint', 'tpoly_academic_record.year', 'tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'), 'tpoly_academic_record.yrgp', 'tpoly_academic_record.groups', 'tpoly_academic_record.lecturer', 'tpoly_academic_record.resit', 'tpoly_academic_record.dateRegistered', 'tpoly_academic_record.createdAt', 'tpoly_academic_record.updates', 'tpoly_academic_record.programme', \DB::raw('concat(tpoly_academic_record.code, tpoly_academic_record.indexno)'), 'tpoly_academic_record.total as tt', \DB::raw('concat(tpoly_academic_record.code, tpoly_academic_record.indexno, tpoly_academic_record.resit)'), 'tpoly_academic_record.total as yes', \DB::raw('concat(tpoly_students.INDEXNO, tpoly_students.STATUS)'), 'tpoly_students.STATUS')
            ->get();

            $dataResit = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')
            ->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year) 
            ->where('tpoly_academic_record.resit','no')
            ->where('tpoly_academic_record.grade','F')
            ->where('tpoly_academic_record.total','<',50)    
            ->orderBy("tpoly_academic_record.indexno")
            ->select('tpoly_academic_record.id', \DB::raw('case when tpoly_academic_record.resit = "no" and tpoly_academic_record.grade = "F" and tpoly_academic_record.total < 50 then CONCAT(tpoly_academic_record.indexno,tpoly_academic_record.resit) end as tt'), \DB::raw('GROUP_CONCAT(if((tpoly_academic_record.resit = "no" and tpoly_academic_record.grade = "F" and tpoly_academic_record.total < 50),tpoly_academic_record.code, null)) as yy'), "tpoly_academic_record.indexno")
            ->groupBy('tpoly_academic_record.indexno')
            ->get();


            $kojoSen2 = count($data)+7; 
            $kojoSense = count($data)+1;

        

        //dd($kojoSensible);

        return Excel::create($year.'_'.$programme, function ($excel) use ($data,$dataResit,$program,$kojoSen2,$yearcc,$year,$programme,$kojoSense,$dpt3,$fac3,$lectname, $sys){

            $excel->getProperties()
   ->setCreator($lectname)
   ->setTitle($year.'_'.$programme)
   ->setLastModifiedBy($lectname)
   ->setDescription('Multiple sheets showing all results')
   ->setSubject($year)
   ->setKeywords('TP, marks, rs, normal')
   ;

            $excel->sheet('TP', function ($sheet) use ($data,$kojoSense,$program,$kojoSen2,$year,$programme,$dpt3,$fac3,$lectname) {
                


                $sheet->fromArray($data);

                

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$programme
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$dpt3.' DEPARTMENT'
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$fac3
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

                $sheet->setCellValue('D2','');
                $sheet->setCellValue('D3','');
                $sheet->setCellValue('D4','Year Group :');
                $sheet->setCellValue('D5','');

                $sheet->setCellValue('F2','');
                $sheet->setCellValue('F3',$kojoSen2);
                $sheet->setCellValue('F4',$year);
             
                $sheet->setCellValue('J3','');
                //$sheet->setCellValue('V7',$year);
                $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
           
//});
            });


            $excel->sheet('TP2', function ($sheet) use ($dataResit,$kojoSense,$program,$kojoSen2,$year,$programme,$dpt3,$fac3,$lectname) {
                


                $sheet->fromArray($dataResit);

                

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$programme
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$dpt3.' DEPARTMENT'
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$fac3
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

                $kojoResitCount = count($dataResit);

                $sheet->setCellValue('B2',$kojoResitCount);
                $sheet->setCellValue('D2','');
                $sheet->setCellValue('D3','');
                $sheet->setCellValue('D4','Year Group :');
                $sheet->setCellValue('D5','');

                $sheet->setCellValue('F2','');
                $sheet->setCellValue('F3',$kojoSen2);
                $sheet->setCellValue('F4',$year);
             
                $sheet->setCellValue('J3','');
                //$sheet->setCellValue('V7',$year);
                $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
           
//});
            });


            $data = Models\StudentModel::where("PROGRAMMECODE",$program)
            ->where("GRADUATING_GROUP",$year)
            ->where("STATUS","!=","Admitted")
            ->orderBy("TRAIL")
            ->orderBy("SUP")
            ->orderBy(\DB::raw('FIELD(STATUS, "Alumni", "In school", "Abandoned", "Withdrawn", "Rusticated", "Deffered")'))
            
            //ORDER BY FIELD(priority, "core", "board", "other")
            ->orderBy("INDEXNO")
            ->select('INDEXNO', \DB::raw('concat(SURNAME,", ",FIRSTNAME," ",OTHERNAMES) NAME'))
            ->get();

            $kojoSense = count($data)+1;

                $rsaProgram = $sys->getProgramResult($program);

                //dd($program,$rsaProgram);
            
                //list of raw score format programs
                if ($rsaProgram == 'RSA') {

                $excel->sheet('RSA', function ($sheet) use ($data,$kojoSense,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname) 
                    {

                    //  Raw score format begins here
            
                        $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  4.7,
                        'D'     =>  4.7,
                        'E'     =>  8.7,
                        'F'     =>  7.7,
                        'G'     =>  4.7,
                        'H'     =>  4.7,
                        'I'     =>  8.7,
                        'J'     =>  7.7,
                        'K'     =>  4.7,
                        'L'     =>  4.7,
                        'M'     =>  8.7,
                        'N'     =>  7.7,
                        'O'     =>  4.7,
                        'P'     =>  4.7,
                        'Q'     =>  8.7,
                        'R'     =>  7.7,
                        'S'     =>  4.7,
                        'T'     =>  4.7,
                        'U'     =>  8.7,
                        'V'     =>  7.7,
                        'W'     =>  4.7,
                        'X'     =>  4.7,
                        'Y'     =>  8.7,
                        'Z'     =>  7.7,
                        'AA'     =>  9,
                        'AB'     =>  9,
                        'AC'     =>  9,
                        'AD'     =>  9
                        ));

                        $sheet->prependRow(1, array('prepended', 'prepended', 'CR', 'NC', 'RS', 'RSA', 'CR', 'NC', 'RS', 'RSA', 'CR', 'NC', 'RS', 'RSA', 'CR', 'NC', 'RS', 'RSA', 'CR', 'NC', 'RS', 'RSA', 'CR', 'NC', 'RS', 'RSA', 'CR', 'RSA', 'CPA', 'REMARKS'));

                
                
                        //$sheet->prependRow(1, array('assignment', 'quiz', 'midsem', 'exam', 'total'));

                        $sheet->fromArray($data);
                
                        $kojoCellBeauty = $kojoSense+6;
                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });

                            

                            //=SUMIFS(TP!D1:TP!D25000,TP!F1:TP!F25000,A3,TP!K1:TP!K25000,1,TP!L1:TP!L25000,"100H")
                            //=SUMIFS(TP!G1:TP!G25000,TP!F1:TP!F25000,A3,TP!K1:TP!K25000,1,TP!L1:TP!L25000,"100H")

                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            //$sheet->setCellValue('C'.$k.'','0');
                            //$sheet->setCellValue('D'.$k.'','0');
                            //$sheet->setCellValue('E'.$k.'','0');
                            //$sheet->setCellValue('F'.$k.'','0');
                            $sheet->setCellValue('C'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('D'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('E'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('F'.$k.'','=IFERROR(E'.$k.'/D'.$k.',"")');
                            $sheet->getStyle('F'.$k.'')->getNumberFormat()->setFormatCode('0.00'); 


                            $sheet->setCellValue('G'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('H'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('I'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('J'.$k.'','=IFERROR(I'.$k.'/H'.$k.',"")');
                            $sheet->getStyle('J'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('K'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('L'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('M'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('N'.$k.'','=IFERROR(M'.$k.'/L'.$k.',"")');
                            $sheet->getStyle('N'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('O'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('P'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('Q'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('R'.$k.'','=IFERROR(Q'.$k.'/P'.$k.',"")');
                            $sheet->getStyle('R'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('S'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('T'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('U'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('V'.$k.'','=IFERROR(U'.$k.'/T'.$k.',"")');
                            $sheet->getStyle('V'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('W'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('X'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('Y'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('Z'.$k.'','=IFERROR(Y'.$k.'/X'.$k.',"")');
                            $sheet->getStyle('Z'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AA'.$k.'','=C'.$k.'+G'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k);
                            //=SUM(SUMIF(F3,">0",F3),SUMIF(J3,">0",J3),SUMIF(N3,">0",N3),SUMIF(R3,">0",R3),SUMIF(V3,">0",V3),SUMIF(Z3,">0",Z3))

                            $sheet->setCellValue('AB'.$k.'','=SUM(SUMIF(F'.$k.',">0",F'.$k.'),SUMIF(J'.$k.',">0",J'.$k.'),SUMIF(N'.$k.',">0",N'.$k.'),SUMIF(R'.$k.',">0",R'.$k.'),SUMIF(V'.$k.',">0",V'.$k.'),SUMIF(Z'.$k.',">0",Z'.$k.'))');
                            $sheet->getStyle('AB'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AC'.$k.'','=IFERROR(AB'.$k.'/AA'.$k.',"")');
                            $sheet->getStyle('AC'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            //=IF(AC3>6,"",IF(AC3>=4,"CD",IF(AC3>=3,"CM",IF(AC3>=2,"C",IF(AC3<2,"NC","")))))

                            //$sheet->setCellValue('AD'.$k.'','=IF(AC'.$k.'>6,"",IF(AC'.$k.'>3.994,"CD",IF(AC'.$k.'>2.994,"CM",IF(AC'.$k.'>1.994,"C",IF(AC'.$k.'>=0,"NC","")))))');

                            $sheet->setCellValue('AD'.$k.'','=IFERROR(VLOOKUP($A'.$k.'&"Abandoned",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Deffered",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Rusticated",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Withdrawn",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"no",TP2!$B$8:TP2!$C$'.$kojoSen2.',2,FALSE),IF(AC'.$k.'>6,"",IF(AC'.$k.'>3.994,"CD",IF(AC'.$k.'>2.994,"CM",IF(AC'.$k.'>1.994,"C",IF(AC'.$k.'>=0,"NC",""))))))))))');

                           $sheet->getStyle('AD'.$k.'')->getAlignment()->setWrapText(true);


                        }

                            $cheat = 25+$k;
                            $cheat2 = $cheat + 3;
                            $cheat3 = $cheat2 + 1;

                            $sheet->prependRow(1, array('', '', 'SEMESTER 1', '', '', '', 'SEMESTER 2', '', '', '', 'SEMESTER 3', '', '', '', 'SEMESTER 4', '', '', '', 'SEMESTER 5', '', '', '', 'SEMESTER 6', '', '', '', 'CUMMULATIVE', '', '', ''));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','NABPTEX BROADSHEET');
                            $sheet->setCellValue('N2','RAW SCORE FORMAT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','2');
                            $sheet->setCellValue('F4','');
                            $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');

                            $sheet->cells('A7:AD7', function($cells) {
                            // manipulate the cell
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10',
                                'bold'       =>  true
                            ));

                            });
                
                


                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->mergeCells('A6:B6');
                            $sheet->mergeCells('C6:F6');
                            $sheet->mergeCells('G6:J6');
                            $sheet->mergeCells('K6:N6');
                            $sheet->mergeCells('O6:R6');
                            $sheet->mergeCells('S6:V6');
                            $sheet->mergeCells('W6:Z6');
                            $sheet->mergeCells('AA6:AD6');
                                   

                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('A8'); 

                            $sheet->cells('C6:AD'.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A6:AD'.$kojoCellBeauty.'', 'thin'); 

                            $sheet->cells('J6:J'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('F6:F'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('N6:N'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('R6:R'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('V6:V'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('Z6:Z'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                            $sheet->cells('AD6:AD'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            //$sheet->cell('LEFT(B8,1)', function($celB) {

                                // manipulate the cell
                            //     $celB->setFontWeight('bold');
                                   
                            //});

                            //LEFT(B8,(FIND(",",B8,1)-1))
                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('G6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('K6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('O6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('S6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('W6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            
                            $sheet->cells('A7:AD7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thick','thin');
                                   
                            });

                            $sheet->cells('C5:AD5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });

                     });
            
                }
                else {
                    $excel->sheet('CGPA', function ($sheet) use ($data,$kojoSense,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname) 
                    {
                    //CGPA format begins here
                    $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  2.9,
                        'D'     =>  5.7,
                        'E'     =>  4.5,
                        'F'     =>  2.9,
                        'G'     =>  5.7,
                        'H'     =>  4.5,
                        'I'     =>  4.5,
                        'J'     =>  2.9,
                        'K'     =>  5.7,
                        'L'     =>  4.5,
                        'M'     =>  4.5,
                        'N'     =>  2.9,
                        'O'     =>  5.7,
                        'P'     =>  4.5,
                        'Q'     =>  4.5,
                        'R'     =>  2.9,
                        'S'     =>  5.7,
                        'T'     =>  4.5,
                        'U'     =>  4.5,
                        'V'     =>  2.9,
                        'W'     =>  5.7,
                        'X'     =>  4.5,
                        'Y'     =>  4.0,
                        'Z'     =>  5.7,
                        'AA'     =>  4.7,
                        'AB'     =>  25
                        ));

                        $sheet->prependRow(1, array('prepended', 'prepended', 'CR', 'GP', 'GPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CR', 'GP', 'CGPA', 'REMARKS'));

                
                
                        //$sheet->prependRow(1, array('assignment', 'quiz', 'midsem', 'exam', 'total'));

                        $sheet->fromArray($data);
                
                        $kojoCellBeauty = $kojoSense+6;
                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '9'//,
                            //'bold'       =>  true
                                ));
                            });

                            

                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            //$sheet->setCellValue('C'.$k.'','0');
                            //$sheet->setCellValue('D'.$k.'','0');
                            //$sheet->setCellValue('E'.$k.'','0');
                            //$sheet->setCellValue('F'.$k.'','0');
                            $sheet->setCellValue('C'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('D'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');
                            $sheet->getStyle('D'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            

                            $sheet->setCellValue('E'.$k.'','=IFERROR(D'.$k.'/C'.$k.',"")');
                            $sheet->getStyle('E'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('F'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('G'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');
                            $sheet->getStyle('G'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('H'.$k.'','=IFERROR(G'.$k.'/F'.$k.',"")');
                            $sheet->getStyle('H'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('I'.$k.'','=(G'.$k.'+D'.$k.')/(F'.$k.'+C'.$k.')');
                            $sheet->getStyle('I'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('J'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('K'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');
                            $sheet->getStyle('K'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('L'.$k.'','=IFERROR(K'.$k.'/J'.$k.',"")');
                            $sheet->getStyle('L'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('M'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.')');
                            $sheet->getStyle('M'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            
                            $sheet->setCellValue('N'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('O'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');
                            $sheet->getStyle('O'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('P'.$k.'','=IFERROR(O'.$k.'/N'.$k.',"")');
                            $sheet->getStyle('P'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('Q'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.')');
                            $sheet->getStyle('Q'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('R'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('S'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');
                            $sheet->getStyle('S'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('T'.$k.'','=IFERROR(S'.$k.'/R'.$k.',"")');
                            $sheet->getStyle('T'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('U'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.')');
                            $sheet->getStyle('U'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('V'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('W'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');
                            $sheet->getStyle('W'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('X'.$k.'','=IFERROR(W'.$k.'/V'.$k.',"")');
                            $sheet->getStyle('X'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            //$sheet->setCellValue('Y'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k.')');

                            $sheet->setCellValue('Y'.$k.'','=F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k);

                            $sheet->setCellValue('Z'.$k.'','=G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k);
                            $sheet->getStyle('Z'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AA'.$k.'','=IFERROR(Z'.$k.'/Y'.$k.',"")');
                            $sheet->getStyle('AA'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            

                            $sheet->setCellValue('AB'.$k.'','=IFERROR(VLOOKUP($A'.$k.'&"Abandoned",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Deffered",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Rusticated",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Withdrawn",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"no",TP2!$B$8:TP2!$C$'.$kojoSen2.',2,FALSE),IF(AA'.$k.'>5,"",IF(AA'.$k.'>3.994,"First Class",IF(AA'.$k.'>2.994,"Second Class Upper Division",IF(AA'.$k.'>1.994,"Second Class Lower Division",IF(AA'.$k.'>1.494,"Pass",IF(AA'.$k.'<=1.494,"Fail","")))))))))))');

                            $sheet->getStyle('AB'.$k.'')->getAlignment()->setWrapText(true);



                        }

                       

                            $cheat = 25+$k;
                            $cheat2 = $cheat + 3;
                            $cheat3 = $cheat2 + 1;

                            $sheet->prependRow(1, array('', '', 'SEMESTER 1', '', '', 'SEMESTER 2', '', '', '', 'SEMESTER 3', '', '', '', 'SEMESTER 4', '', '', '', 'SEMESTER 5', '', '', '', 'SEMESTER 6', '', '', 'CUMMULATIVE', '', '', ''));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','NABPTEX BROADSHEET');
                            $sheet->setCellValue('N2','CGPA FORMAT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','2');
                            $sheet->setCellValue('F4','');
                            $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
                            $sheet->cells('A7:AB7', function($cells) {
                            // manipulate the cell
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10',
                                'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->mergeCells('A6:B6');
                            $sheet->mergeCells('C6:E6');
                            $sheet->mergeCells('F6:I6');
                            $sheet->mergeCells('J6:M6');
                            $sheet->mergeCells('N6:Q6');
                            $sheet->mergeCells('R6:U6');
                            $sheet->mergeCells('V6:X6');
                            $sheet->mergeCells('Y6:AB6');
                                   

                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('A8'); 

                            $sheet->cells('C6:AB'.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A6:AB'.$kojoCellBeauty.'', 'thin'); 

                            $sheet->cells('I6:I'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('E6:E'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('M6:M'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('Q6:Q'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('U6:U'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('X6:X'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                            $sheet->cells('AB6:AB'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('F6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('J6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('N6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('R6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('V6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            
                            $sheet->cells('A7:AB7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thick','thin');
                                   
                            });

                            $sheet->cells('C5:AB5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });
                
            //});
            });

            $excel->sheet('NABPTEX', function ($sheet) use ($data,$kojoSense,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname) 
                    {
                    //CGPA format begins here
                    $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  2.9,
                        'D'     =>  5.7,
                        'E'     =>  4.5,
                        'F'     =>  2.9,
                        'G'     =>  5.7,
                        'H'     =>  4.5,
                        'I'     =>  4.5,
                        'J'     =>  2.9,
                        'K'     =>  5.7,
                        'L'     =>  4.5,
                        'M'     =>  4.5,
                        'N'     =>  2.9,
                        'O'     =>  5.7,
                        'P'     =>  4.5,
                        'Q'     =>  4.5,
                        'R'     =>  2.9,
                        'S'     =>  5.7,
                        'T'     =>  4.5,
                        'U'     =>  4.5,
                        'V'     =>  2.9,
                        'W'     =>  5.7,
                        'X'     =>  4.5,
                        'Y'     =>  4.0,
                        'Z'     =>  5.7,
                        'AA'     =>  4.7,
                        'AB'     =>  25
                        ));

                        $sheet->prependRow(1, array('prepended', 'prepended', 'CR', 'GP', 'GPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CR', 'GP', 'CGPA', 'REMARKS'));

                
                
                        //$sheet->prependRow(1, array('assignment', 'quiz', 'midsem', 'exam', 'total'));

                        $sheet->fromArray($data);
                
                        $kojoCellBeauty = $kojoSense+6+8;
                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '9'//,
                            //'bold'       =>  true
                                ));
                            });

                           

                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            //$sheet->setCellValue('C'.$k.'','0');
                            //$sheet->setCellValue('D'.$k.'','0');
                            //$sheet->setCellValue('E'.$k.'','0');
                            //$sheet->setCellValue('F'.$k.'','0');
                            $sheet->setCellValue('C'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('D'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');
                            $sheet->getStyle('D'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            

                            $sheet->setCellValue('E'.$k.'','=IFERROR(D'.$k.'/C'.$k.',"")');
                            $sheet->getStyle('E'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('F'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('G'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');
                            $sheet->getStyle('G'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('H'.$k.'','=IFERROR(G'.$k.'/F'.$k.',"")');
                            $sheet->getStyle('H'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('I'.$k.'','=(G'.$k.'+D'.$k.')/(F'.$k.'+C'.$k.')');
                            $sheet->getStyle('I'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('J'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('K'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');
                            $sheet->getStyle('K'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('L'.$k.'','=IFERROR(K'.$k.'/J'.$k.',"")');
                            $sheet->getStyle('L'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('M'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.')');
                            $sheet->getStyle('M'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            
                            $sheet->setCellValue('N'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('O'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');
                            $sheet->getStyle('O'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('P'.$k.'','=IFERROR(O'.$k.'/N'.$k.',"")');
                            $sheet->getStyle('P'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('Q'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.')');
                            $sheet->getStyle('Q'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('R'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('S'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');
                            $sheet->getStyle('S'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('T'.$k.'','=IFERROR(S'.$k.'/R'.$k.',"")');
                            $sheet->getStyle('T'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('U'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.')');
                            $sheet->getStyle('U'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('V'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('W'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');
                            $sheet->getStyle('W'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('X'.$k.'','=IFERROR(W'.$k.'/V'.$k.',"")');
                            $sheet->getStyle('X'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            //$sheet->setCellValue('Y'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k.')');

                            $sheet->setCellValue('Y'.$k.'','=F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k);

                            $sheet->setCellValue('Z'.$k.'','=G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k);
                            $sheet->getStyle('Z'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AA'.$k.'','=IFERROR(Z'.$k.'/Y'.$k.',"")');
                            $sheet->getStyle('AA'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            

                            $sheet->setCellValue('AB'.$k.'','=IFERROR(VLOOKUP($A'.$k.'&"Abandoned",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Deffered",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Rusticated",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"Withdrawn",TP!$Y$8:TP!$Z$'.$kojoSen2.',2,FALSE),IFERROR(VLOOKUP($A'.$k.'&"no",TP2!$B$8:TP2!$C$'.$kojoSen2.',2,FALSE),IF(AA'.$k.'>5,"",IF(AA'.$k.'>3.994,"First Class",IF(AA'.$k.'>2.994,"Second Class Upper Division",IF(AA'.$k.'>1.994,"Second Class Lower Division",IF(AA'.$k.'>1.494,"Pass",IF(AA'.$k.'<=1.494,"Fail","")))))))))))');

                            $sheet->getStyle('AB'.$k.'')->getAlignment()->setWrapText(true);



                        }

                       

                            $cheat = 25+$k;
                            $cheat2 = $cheat + 3;
                            $cheat3 = $cheat2 + 1;

                            $sheet->prependRow(1, array('INDEX NO', 'NAME', 'SEMESTER 1', '', '', 'SEMESTER 2', '', '', '', 'SEMESTER 3', '', '', '', 'SEMESTER 4', '', '', '', 'SEMESTER 5', '', '', '', 'SEMESTER 6', '', '', 'CUMMULATIVE', '', '', 'REMARKS'));
                            
                           
                            

                            ///////////////
                             
                             
                            
                                //$sheet->setBorder($range, 'none');
                                $sheet->prependRow(1, array('DEPARTMENT: '.$dpt3,'','','','','','','','','','','PROGRAMME: '.$programme,'','','','','','','','','','','','','','','','YEAR 2018'));
                                
                                $sheet->cell('L1', function($cell) {

                            $cell->setAlignment('center');
                            
                            });

                                $sheet->cell('A1:AB1', function($cell) {

                            //$cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                //$sheet->setBorder($range, 'none');
                                $sheet->prependRow(1, array('CUMULATIVE GRADE POINT FOR HIGHER NATIONAL DIPLOMA PROGRAMMES IN THE UNIVERSITY'));
                                $sheet->mergeCells('A1:AB1');
                                $sheet->cell('A1', function($cell) {

                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                //$sheet->setBorder($range, 'none');
                                
                                $sheet->prependRow(1, array('TAKORADI TECHNICAL UNIVERSITY'));
                                $sheet->mergeCells('A1:AB1');
                                $sheet->cell('A1', function($cell) {

                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                //$sheet->setBorder($range, 'none');
                                $sheet->prependRow(1, array('','','','','','','','','','','','','','','','','','','','','','','','','','','','Page 1 of' ));
                                //$sheet->setBorder($range, 'none');              
                                $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            $sheet->prependRow(1, array(''));
                            ///////////////



                            $sheet->cells('A13:AB14', function($cells) {
                            // manipulate the cell
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10',
                                'bold'       =>  true
                            ));

                            });
                


                            for($lisa=1;$lisa<5;$lisa++)
                                {
                               $sheet->mergeCells('A'.$lisa.':AB'.$lisa);
                                $sheet->cells('A'.$lisa.':AB'.$lisa.'', function($celcenter1) {

                                // manipulate the cell
                                $celcenter1->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }
                                                              

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->mergeCells('A5:AB5');

                            $sheet->cell('A5', function($cell) {

                            $cell->setAlignment('center');
                            });


                            $sheet->mergeCells('A13:A14');
                            $sheet->mergeCells('B13:B14');
                            $sheet->mergeCells('C13:E13');
                            $sheet->mergeCells('F13:I13');
                            $sheet->mergeCells('J13:M13');
                            $sheet->mergeCells('N13:Q13');
                            $sheet->mergeCells('R13:U13');
                            $sheet->mergeCells('V13:X13');
                            $sheet->mergeCells('Y13:AA13');
                            $sheet->mergeCells('AB13:AB14');
                                   

                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            $kojoCellBeauty = $kojoCellBeauty - 1;

                            //$sheet->setFreeze('A8'); 

                            $sheet->cells('C13:AB'.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                      
                            }); 

                            $sheet->setBorder('A13:AB'.$kojoCellBeauty.'', 'thin'); 

                            $sheet->cells('I13:I'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('B13:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('E13:E'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('M13:M'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('Q13:Q'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('U13:U'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cells('X13:X'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                            $sheet->cells('AB13:AB'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });

                            $sheet->cells('A13:A'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','medium');
                                   
                            });

                            $sheet->cells('AA13:AA'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                            $sheet->cell('A13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('B13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('C13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('F13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('J13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('N13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('R13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('V13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('Y13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('AB13', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            
                            $sheet->cells('A14:AB14', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','medium','thin');
                                   
                            });

                            $sheet->cells('C12:AB12', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });

                            
                           //dd($f, $k);
//\DB::raw('substr(tpoly_academic_record.level, 1, 3) as level')

            

            //dd($kojoSupplementary);

            $dataNoTrail = Models\StudentModel::
            where("PROGRAMMECODE", $program)
            ->where('GRADUATING_GROUP',$year) 
            ->where('TRAIL','no')
            ->where('SUP','0')
            ->where('STATUS',"!=",'Abandoned')
            ->where('STATUS',"!=",'Rusticated')
            ->where('STATUS',"!=",'Deffered')
            
            ->select('id')
            
            ->get();

            $kojoNoTrail = count($dataNoTrail); 

            $dataSupplementary = Models\StudentModel::
            where("PROGRAMMECODE", $program)
            ->where('GRADUATING_GROUP',$year)
            //->where('SUP','0') 
            ->where('TRAIL','no')
            ->where('STATUS',"!=",'Abandoned')
            ->where('STATUS',"!=",'Rusticated')
            ->where('STATUS',"!=",'Deffered')
            
            ->select('id')
            
            ->get();

            $kojoSupplementary = count($dataSupplementary); 

            $dataTrail = Models\StudentModel::
            where("PROGRAMMECODE", $program)
            ->where('GRADUATING_GROUP',$year) 
            //->where('TRAIL','yes')
            ->where('STATUS',"!=",'Abandoned')
            ->where('STATUS',"!=",'Rusticated')
            ->where('STATUS',"!=",'Deffered')
            
            ->select('id')
            
            ->get();

            $kojoTrail = count($dataTrail);


            $dataAll = Models\StudentModel::
            where("PROGRAMMECODE", $program)
            ->where('GRADUATING_GROUP',$year) 
            //->where('TRAIL','yes')
                        
            ->select('id')
            
            ->get();

            $kojoAll = count($dataAll);
            //dd($kojoGraduate);

                                // $sheet->prependRow(41, array('INDEXNO', 'NAME', 'CR', 'GP', 'GPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CR', 'GP', 'CGPA', 'REMARKS'));
                                
                                // $sheet->prependRow(41, array('', '', 'SEMESTER 1', '', '', 'SEMESTER 2', '', '', '', 'SEMESTER 3', '', '', '', 'SEMESTER 4', '', '', '', 'SEMESTER 5', '', '', '', 'SEMESTER 6', '', '', 'CUMMULATIVE', '', '', ''));
                                // $sheet->prependRow(41, array('','','','','','','','','','','','','','','','','','','','','','','','','','','','Page 2'));
                                // $sheet->prependRow(41, array('','')); 
                            $pageCount = -1;
                            $pageNo = 1;
                            $newHeaderRow = 1;  
                            $newHeaderAdd = 39;
                            $stucCount = 0;
                            $gradCheck = 1;
                            $supCheck = 1;
                            $trailCheck = 1;
                            $allCheck = 1;
                            for ($i=1; $i < $kojoSense; $i+=$newHeaderAdd) 
                                { 
                                    if ($newHeaderAdd == 39) 
                                        {
                                            $stucCount+=25;
                                        } else 
                                        {
                                            $stucCount+=15;
                                        }
                                
                                    $newHeaderRow+=$newHeaderAdd;
                                    $kojoSense+=14;
                            
                                    $pageNo += 1;
                                    $pageCount +=1;

                                    if ($kojoNoTrail > 0) 
                                        {
                                            if ($stucCount >= $kojoNoTrail && $gradCheck == 1) 
                                                {
                                                    $gradCheck = 2;
                                    
                                                    $newHeaderRow = $newHeaderRow - $newHeaderAdd + 14 - ($pageCount * 25) + $kojoNoTrail;
                                       
                                                    $newHeaderAdd = 39;
                                            
                                                }
                                        }

                                    if ($kojoSupplementary > 0 and $kojoSupplementary > $kojoNoTrail) 
                                        {
                                            if ($stucCount >= $kojoSupplementary && $supCheck == 1) 
                                                {
                                                    $supCheck = 2;
                                                    $newHeaderRow = $kojoNoTrail + ($pageCount * 14) + 15;
                                                    $newHeaderAdd = 39;
                                                }
                                        }


                                    if ($kojoTrail > 0 and $kojoTrail > $kojoSupplementary) 
                                        {
                                            if ($stucCount >= $kojoTrail && $trailCheck == 1) 
                                                {
                                                    $trailCheck = 2;
                                                    $newHeaderRow = $kojoSupplementary + ($pageCount * 14) + 15;
                                                    $sheet->setCellValue('AA'.$newHeaderRow.'',$kojoSupplementary);
                                                    $newHeaderAdd = 29;
                                                }
                                        }

                                    if ($kojoAll > 0 and $kojoAll > $kojoTrail) 
                                        {
                                            if ($stucCount >= $kojoAll && $allCheck == 1) 
                                                {
                                                    $allCheck = 2;
                                                    $newHeaderRow = $kojoTrail + ($pageCount * 14) + 15;
                                                    $sheet->setCellValue('AA'.$newHeaderRow.'','All');
                                                    $newHeaderAdd = 39;
                                                }
                                        }

                                        // $newHeaderAdd = 29;
                                    //dd($newHeaderAdd);

                                    $range = "A".$newHeaderRow.":AB".$newHeaderRow;


                                    $sheet->prependRow($newHeaderRow, array('INDEXNO', 'NAME', 'CR', 'GP', 'GPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CGPA', 'CR', 'GP', 'GPA', 'CR', 'GP', 'CGPA', ''));



                                    $sheet->cells('A'.$newHeaderRow.':AB'.$newHeaderRow, function($celB) {

                                    $celB->setBorder('thin','thin','medium','thin');
                                   
                                    });

                                    $sheet->prependRow($newHeaderRow, array('INDEX NO', 'NAME', 'SEMESTER 1', '', '', 'SEMESTER 2', '', '', '', 'SEMESTER 3', '', '', '', 'SEMESTER 4', '', '', '', 'SEMESTER 5', '', '', '', 'SEMESTER 6', '', '', 'CUMMULATIVE', '', '', 'REMARKS'));
                                    $headnex = $newHeaderRow;
                                    $headnext = $headnex + 1;

                                    $sheet->mergeCells('C'.$newHeaderRow.':E'.$newHeaderRow);
                                    $sheet->mergeCells('F'.$newHeaderRow.':I'.$newHeaderRow);
                                    $sheet->mergeCells('J'.$newHeaderRow.':M'.$newHeaderRow);
                                    $sheet->mergeCells('N'.$newHeaderRow.':Q'.$newHeaderRow);
                                    $sheet->mergeCells('R'.$newHeaderRow.':U'.$newHeaderRow);
                                    $sheet->mergeCells('V'.$newHeaderRow.':X'.$newHeaderRow);
                                    $sheet->mergeCells('Y'.$newHeaderRow.':AA'.$newHeaderRow);
                                    $sheet->mergeCells('AB'.$newHeaderRow.':AB'.$headnext);
                                    $sheet->mergeCells('A'.$newHeaderRow.':A'.$headnext);
                                    $sheet->mergeCells('B'.$newHeaderRow.':B'.$headnext);

                                    $sheet->cells('C'.$newHeaderRow.':AB'.$kojoSense.'', function($celcenter) {

                                
                                    $celcenter->setAlignment('center');
                      
                                    }); 

                                    $sheet->cell('A'.$newHeaderRow, function($celB) {

                                
                                    $celB->setBorder('medium','medium','medium','medium');
                                   
                                    });

                                    $sheet->cell('B'.$newHeaderRow, function($celB) {

                                
                                    $celB->setBorder('medium','medium','medium','medium');
                                   
                                    });

                                    $sheet->cell('C'.$newHeaderRow, function($celB) {

                                
                                    $celB->setBorder('medium','medium','medium','medium');
                                   
                                    });
                                    $sheet->cell('F'.$newHeaderRow, function($celB) {

                               
                                    $celB->setBorder('medium','medium','medium','medium');
                                   
                                    });
                                    $sheet->cell('J'.$newHeaderRow, function($celB) {

                                
                                    $celB->setBorder('medium','medium','medium','medium');
                                   
                                    });
                                    $sheet->cell('N'.$newHeaderRow, function($celB) {

                                
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('R'.$newHeaderRow, function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                            $sheet->cell('V'.$newHeaderRow, function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });

                            $sheet->cell('Y'.$newHeaderRow, function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });

                            $sheet->cell('AB'.$newHeaderRow, function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('medium','medium','medium','medium');
                                   
                            });
                                
                            $headnex = $newHeaderRow;
                                $headnext = $headnex + 1;

                                $sheet->cell('A'.$newHeaderRow.':AB'.$headnext, function($cell) {

                            //$cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(10);
                            });
                            
                                //$sheet->setBorder($range, 'none');
                                $sheet->prependRow($newHeaderRow, array('DEPARTMENT: '.$dpt3,'','','','','','','','','','','PROGRAMME: '.$programme,'','','','','','','','','','','','','','','','YEAR 2018'));
                                

                                $sheet->cell('A'.$newHeaderRow.':AB'.$newHeaderRow, function($cell) {

                            //$cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                $sheet->setBorder($range, 'none');
                                $sheet->prependRow($newHeaderRow, array('CUMULATIVE GRADE POINT FOR HIGHER NATIONAL DIPLOMA PROGRAMMES IN THE UNIVERSITY'));
                                $sheet->mergeCells('A'.$newHeaderRow.':AB'.$newHeaderRow);
                                $sheet->cell('A'.$newHeaderRow, function($cell) {

                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                $sheet->setBorder($range, 'none');
                                
                                $sheet->prependRow($newHeaderRow, array('TAKORADI TECHNICAL UNIVERSITY'));
                                $sheet->mergeCells('A'.$newHeaderRow.':AB'.$newHeaderRow);
                                $sheet->cell('A'.$newHeaderRow, function($cell) {

                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(12);
                            });
                                $sheet->setBorder($range, 'none');
                                $sheet->prependRow($newHeaderRow, array('','','','','','','','','','','','','','','','','','','','','','','','','','','','Page '.$pageNo.' of' ));
                                $sheet->setBorder($range, 'none');
                           
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array(' External Examiner____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                           
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array(' Pro Vice Chancellor____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array(' Head of Department____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                            $sheet->setBorder($range, 'none');
                
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');
                                
                                
                            
                            //$newHeaderRowCGPA=$newHeaderRow + 2;
                            //}
                             }

                             $pageNo = $pageNo - 1;
                             $newHeaderRow = $kojoAll + ($pageNo * 14) + 15;
                             $range = "A".$newHeaderRow.":AB".$newHeaderRow;
                             $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array(' External Examiner____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                           
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array(' Pro Vice Chancellor____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');

                            $sheet->prependRow($newHeaderRow, array(' Head of Department____________________________','','','','','','','','','','','Signature_______________________','','','','','','','','','','','Date_______________________'

                            ));
                            $sheet->setBorder($range, 'none');
                
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');
                            $sheet->prependRow($newHeaderRow, array('',''));
                            $sheet->setBorder($range, 'none');


                           
            });
            
}
        //$arraycc = $sys->getSemYear();
        //$yearcc = $arraycc[0]->YEAR;
//dd($celB);
#selected mounted courses for the academic year
$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();
//dd($program);
               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }
#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            //->where('tpoly_academic_record.grade','!=','E')
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.code")
            ->groupBY("tpoly_academic_record.code")
            ->select('tpoly_academic_record.code','tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'))
            ->get();
            //dd($year);
            #variable holding course codes
            @$fuck = '';
            @$fuckr1 = '';
            @$fuckr2 = '';
            @$fuckr3 = '';
            @$fuckr4 = '';
            @$fuckr5 = '';
            @$fuckr6 = '';
            @$fuckr7 = '';
            @$fuckr8 = '';
            @$kuck1 = '';
            @$kuck2 = '';
            @$kuck3 = '';
            @$kuck4 = '';
            @$kuck5 = '';
            @$kuck6 = '';
            @$kuck7 = '';
            @$kuck8 = '';

            $rsaProgram = $sys->getProgramResult($program);
if ($rsaProgram == 'RSA' ) {
            #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcountr1 = 1;
            @$fuckcountr2 = 1;
            @$fuckcountr3 = 1;
            @$fuckcountr4 = 1;
            @$fuckcountr5 = 1;
            @$fuckcountr6 = 1;
            @$fuckcountr7 = 1;
            @$fuckcountr8 = 1;
            //dd($fuck);
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            //@$fuckcount++;
            //@$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                @$fuckcountr1++;
                @$course=$row['code'];
                #checker for sem 1, level 100
                $preyear = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
                $fuckr1 = $fuckr1.','.$course;
                 @$kuck1 = ',PA';
                //dd($fuckr1);
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
               @$fuckcountr2++;
                @$course=$row['code'];
                //dd($course2);
                #checker for sem 2, level 100
                 $preyear = 2;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
                //$fuck = $fuck.','.$course;
                $fuckr2 = $fuckr2.','.$course;
                @$kuck2 = ',PA';
                //dd($fuckr2);
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                @$fuckcountr3++;
                @$course=$row['code'];
                #checker for sem 1, level 200
                 $preyear = 3;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
                $fuckr3 = $fuckr3.','.$course;
                @$kuck3 = ',PA';
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                @$fuckcountr4++;
                @$course=$row['code'];
                 $preyear = 4;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
                $fuckr4 = $fuckr4.','.$course;
                @$kuck4 = ',PA';
            }
            if ($semcourse == '1300') {
                @$fuckcountr5++;
                @$course=$row['code'];
                $preyear = 5;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
                $fuckr5 = $fuckr5.','.$course;
                @$kuck5 = ',PA';
            }
            if ($semcourse == '2300') {
                @$fuckcountr6++;
                @$course=$row['code'];
                $preyear = 6;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
                $fuckr6 = $fuckr6.','.$course;
                @$kuck6 = ',PA';
            }
            if ($semcourse == '1400') {
                @$fuckcountr7++;
                @$course=$row['code'];
                $preyear = 7;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
                $fuckr7 = $fuckr7.','.$course;
                @$kuck7 = ',PA';
            }
            if ($semcourse == '2400') {
                @$fuckcountr8++;
                $preyear = 8;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
                $fuckr8 = $fuckr8.','.$course;
                @$kuck8 = ',PA';
            }
            

            //$fuck = $fuck.','.$course;

            
        }

        if (@$fuckcountr1 == 1) {
            @$fuckcountr1 = 0;
        }
        if (@$fuckcountr2 == 1) {
            @$fuckcountr2 = 0;
        }
        if (@$fuckcountr3 == 1) {
            @$fuckcountr3 = 0;
        }
        if (@$fuckcountr4 == 1) {
            @$fuckcountr4 = 0;
        }
        if (@$fuckcountr5 == 1) {
            @$fuckcountr5 = 0;
        }
        if (@$fuckcountr6 == 1) {
            @$fuckcountr6 = 0;
        }
        if (@$fuckcountr7 == 1) {
            @$fuckcountr7 = 0;
        }
        if (@$fuckcountr8 == 1) {
            @$fuckcountr8 = 0;
        }


        for ($esi = 0; $esi <1; $esi++) {
                        @$fucksem1++;
                        @$fucksem11++;
                        @$fucksem2++;
                        @$fucksem21++;
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;

                        
                    }
                    
        for ($esi2 = 0; $esi2 <1; $esi2++) {
                        @$fucksem2++;
                        @$fucksem21++;
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;

                    }
                    
                        
        for ($esi3 = 0; $esi3 <1; $esi3++) {
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi4 = 0; $esi4 <1; $esi4++) {
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi5 = 0; $esi5 <1; $esi5++) {
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi6 = 0; $esi6 <1; $esi6++) {
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi7 = 0; $esi7 <1; $esi7++) {
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi8 = 0; $esi8 <1; $esi8++) {
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi9 = 0; $esi9 <1; $esi9++) {
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
      
                        
                    

        @$fuckcount = @$fuckcountr1+@$fuckcountr2+@$fuckcountr3+@$fuckcountr4+@$fuckcountr5+@$fuckcountr6+@$fuckcountr7+@$fuckcountr8+1;
        $fuck = @$fuckr1.@$kuck1.@$fuckr2.@$kuck2.@$fuckr3.@$kuck3.@$fuckr4.@$kuck4.@$fuckr5.@$kuck5.@$fuckr6.@$kuck6.@$fuckr7.@$kuck7.@$fuckr8.@$kuck8.',CPA';
        //dd(@$fuck);
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            $kojoSense = count($data)+1;

            #excel for headers
            @$excel->sheet('MACS', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fuckcountr1,$fuckcountr2,$fuckcountr3,$fuckcountr4,$fuckcountr5,$fuckcountr6,$fuckcountr7,$fuckcountr8,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  7,
                        'D'     =>  7,
                        'E'     =>  7
                        ));


                        #add course codes
                        $sheet->prependRow(1, $explode_fuck);
                        
                        #add stuents
                        $sheet->fromArray($data);
                        
                        #no of rows, +6 for headers
                        $kojoCellBeauty = $kojoSense+6;

                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });
                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $fuckcou = 0;
                            $alpha_last = 'B';
                            $GP = 'F';
                            $GPC = 'C';
                            $fourNine = '1';
                            
                        for ($alpha='C'; $fuckcou < $fuckcount; $alpha++) { 
                            $fuckcou++;
                            $fourNine++;

                            $value = $sheet->getCell(''.$alpha.'1')->getValue();

                            if ($fuckcou > $fuckcountr1 and $fuckcountr1 != 0) {
                                $GP = 'J';
                                $GPC = 'G';
                                
                            }
                            if ($fuckcou > $fuckcountr2 + $fuckcountr1  and $fuckcountr2 != 0) {
                                $GP = 'N';
                                $GPC = 'K';
                                
                            }
                            if ($fuckcou > $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr3 != 0) {
                                $GP = 'R';
                                $GPC = 'O';
                                
                            }
                            if ($fuckcou > $fuckcountr4 + $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr4 != 0) {
                                $GP = 'V';
                                $GPC = 'S';
                            }
                            if ($fuckcou > $fuckcountr5 + $fuckcountr4 + $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr5 != 0) {
                                $GP = 'Z';
                                $GPC = 'W';
                            }
                            //dd($fuckcou, $fuckcountr1, $fuckcountr2, $fuckcountr3, $fuckcountr4, $fuckcountr5);
                            #last cell
                            $alpha_last++;
                            //dd($alpha);
                            $sheet->setWidth(array(
                        ''.$alpha.''    =>  8,
                        
                        ));
                            #selects cells vertically, $kojosen2 the last row in the TP sheet
                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            $k_fuck = $k + 2;
                            $k_RSA = $k + 6;
                            
                            
                            
                            //$sheet->getStyle('AC'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            
                            $sheet->setCellValue(''.$alpha.$k.'','=IF('.$alpha.'$7="PA",RSA!$'.$GP.$k_RSA.'/RSA!$'.$GPC.$k_RSA.',IF('.$alpha.'$7="CPA",RSA!$AC'.$k_RSA.',IFERROR(VLOOKUP('.$alpha.'$7&$A'.$k.',TP!$U$8:TP!$V$'.$kojoSen2.',2,FALSE),"")))');

                            
                            
                            if ($value == 'PA' || $value == 'CPA') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            }
                            //$colIndex = $sheet->columnIndexFromString('B2')->getColumn();
                            //dd($colIndex);

/*to be visited later
getCalculatedValue
                            $value = $sheet->getCellByColumnAndRow($fourNine, $k)->getValue();
                            dd($value);

                            $sheet->cell('J3', function($cell) {

                               //$cell->setFontColor('#edeff6');
                            });
*/
                            //=IF(AO$7="PA",RSA!$Z8/RSA!$W8,IF(AO$7="CPA","K",VLOOKUP(AO$7&$A8,TP!$U$8:TP!$V$205,2,FALSE)))
                           
                            
                        }
                        }
                        //dd($fucksem1, $fucksem2, $fucksem3, $fucksem4, $fucksem5, $fucksem6, $fucksem7, $fucksem8);
                            #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck;
                            @$k_fuck2 = $k_fuck;
                            @$k_fuck3 = $k_fuck;
                            @$k_fuck4 = $k_fuck;
                            @$k_fuck5 = $k_fuck;
                            @$k_fuck6 = $k_fuck;
                            @$k_fuck7 = $k_fuck;
                            @$k_fuck8 = $k_fuck;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            $g7 = 0;
                            $g8 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1500) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2500 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and !empty($fucksem41) and !is_numeric($fucksem41)) {
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and !empty($fucksem51) and !is_numeric($fucksem51)) {  
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        if ($e == 1400 and !empty($fucksem61) and !is_numeric($fucksem61)) {   
                            $k_fuck7++;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7.'',$b.' - ('.$g.') - '.$a); 
                            $g7 = $g7 + $g; 
                        }
                        if ($e == 2400 and !empty($fucksem71) and !is_numeric($fucksem71)) {  
                            $k_fuck8++;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8.'',$b.' - ('.$g.') - '.$a); 
                            $g8 = $g8 + $g; 
                        }
                        
                }

                            
                            $k_fuck1d = $k_fuck1 + 2;
                            $k_fuck1 = $k_fuck1 - $k_fuck;
                            @$sheet->setCellValue('C'.$k_fuck1d.'','Courses = '.$k_fuck1.' // Credit Hours = '.$g1);

                        if ($preyear > 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                            $k_fuck2 = $k_fuck2 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2d.'','Courses = '.$k_fuck2.' // Credit Hours = '.$g2); 
                             
                         }
                        if ($preyear > 2) {
                            $k_fuck3d = $k_fuck3 + 2;
                            $k_fuck3 = $k_fuck3 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3d.'','Courses = '.$k_fuck3.' // Credit Hours = '.$g3);
                              
                         }
                         if ($preyear > 3) {
                            $k_fuck4d = $k_fuck4 + 2;
                            $k_fuck4 = $k_fuck4 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4d.'','Courses = '.$k_fuck4.' // Credit Hours = '.$g4);
                              
                         }
                         if ($preyear > 4) {
                            $k_fuck5d = $k_fuck5 + 2;
                            $k_fuck5 = $k_fuck5 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5d.'','Courses = '.$k_fuck5.' // Credit Hours = '.$g5);
                              
                         }
                         if ($preyear > 5) {    
                            $k_fuck6d = $k_fuck6 + 2;
                            $k_fuck6 = $k_fuck6 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6d.'','Courses = '.$k_fuck6.' // Credit Hours = '.$g6); 
                             
                        }
                        if ($preyear > 6) {    
                            $k_fuck7d = $k_fuck7 + 2;
                            $k_fuck7 = $k_fuck7 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7d.'','Courses = '.$k_fuck7.' // Credit Hours = '.$g7); 
                             
                        }
                        if ($preyear > 7) {    
                            $k_fuck8d = $k_fuck8 + 2;
                            $k_fuck8 = $k_fuck8 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8d.'','Courses = '.$k_fuck8.' // Credit Hours = '.$g8); 
                             
                        }
                            

                            $lastb = $k_fuck1d + 10;

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','ACADEMIC BROADSHEET');
                            $sheet->setCellValue('N2','ADMINISTRATOR FORMAT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');
                            $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');

                            $sheet->cells('A7:'.$alpha.$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            @$sheet->mergeCells('A6:B6');
                           @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            $board1 = 'C6';
                            $board2 = $fucksem1;
                            if (!empty($fucksem11) and $preyear > 1) {
                              @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');
                               $board1 = $fucksem11;
                               $board2 = $fucksem2;  
                             } 
                            if (!empty($fucksem21) and $preyear > 2) {
                            @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                                $board1 = $fucksem21;
                                $board2 = $fucksem3;
                            } 
                            if (!empty($fucksem31) and  $preyear > 3) {
                            @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                                $board1 = $fucksem31;
                                $board2 = $fucksem4;
                            } 
                            if (!empty($fucksem41) and $preyear > 4) {
                            @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                            $board1 = $fucksem41;
                                $board2 = $fucksem5;
                            } 
                            if (!empty($fucksem51) and $preyear > 5) {
                            @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                            $board1 = $fucksem51;
                                $board2 = $fucksem6;
                            }
                            if (!empty($fucksem61) and $preyear > 6) {
                            @$sheet->mergeCells(''.$fucksem61.'6:'.$fucksem7.'6');
                            $board1 = $fucksem61;
                                $board2 = $fucksem7;
                            } 
                            if (!empty($fucksem71) and $preyear > 7) {
                            @$sheet->mergeCells(''.$fucksem71.'6:'.$fucksem8.'6');
                            $board1 = $fucksem71;
                                $board2 = $fucksem8;
                            }  
                            
                            //$sheet->mergeCells('K6:N6');
                            @$sheet->setCellValue('C6','Sem 1');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Sem 2');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Sem 3');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Sem 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Sem 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Sem 6');
                            } 
                            if ($preyear > 6) {
                            @$sheet->setCellValue(''.$fucksem61.'6','Sem 7');
                            } 
                            if ($preyear > 7) {
                            @$sheet->setCellValue(''.$fucksem71.'6','Sem 8');
                            } 
                           
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C8'); 

                            $sheet->cells('C6:'.$alpha.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A6:'.$alpha_last.$kojoCellBeauty.'', 'thin'); 

                                   
                            
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });

                            if (!empty($fucksem1)) { 
                            $sheet->cells(''.$fucksem1.'6:'.$fucksem1.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                                }
                                if ($preyear > 1) { 
                            $sheet->cells(''.$fucksem2.'6:'.$fucksem2.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 2) { 
                            $sheet->cells(''.$fucksem3.'6:'.$fucksem3.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 3) { 
                            $sheet->cells(''.$fucksem4.'6:'.$fucksem4.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 4) { 
                            $sheet->cells(''.$fucksem5.'6:'.$fucksem5.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                        }

                        if ($preyear > 5) { 
                            $sheet->cells(''.$fucksem6.'6:'.$fucksem6.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }
                         if ($preyear > 6) { 
                            $sheet->cells(''.$fucksem7.'6:'.$fucksem7.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }
                         if ($preyear > 7) { 
                            $sheet->cells(''.$fucksem8.'6:'.$fucksem8.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }

                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                            if ($preyear > 2) {
                            $sheet->cell(''.$fucksem21.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 3) {
                            $sheet->cell(''.$fucksem31.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 4) {
                            $sheet->cell(''.$fucksem41.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 5) {
                            $sheet->cell(''.$fucksem51.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                        if ($preyear > 6) {
                            $sheet->cell(''.$fucksem61.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                        if ($preyear > 7) {
                            $sheet->cell(''.$fucksem71.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                            
                            
                            $sheet->cells('A7:'.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thick','thin');
                                   
                            });

                            $sheet->cell(''.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thick','thin');
                                   
                            });

                            $sheet->cells('C5:'.$alpha_last.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });
             
                
                
            //});
            });

        //}
}
else {
     #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcountr1 = 1;
            @$fuckcountr2 = 2;
            @$fuckcountr3 = 2;
            @$fuckcountr4 = 2;
            @$fuckcountr5 = 2;
            @$fuckcountr6 = 2;
            @$fuckcountr7 = 2;
            @$fuckcountr8 = 2;
            //dd($fuck);
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            //@$fuckcount++;
            //@$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                @$fuckcountr1++;
                @$course=$row['code'];
                #checker for sem 1, level 100
                $preyear = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
                $fuckr1 = $fuckr1.','.$course;
                 @$kuck1 = ',GPA';
                //dd($fuckr1);
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
               @$fuckcountr2++;
                @$course=$row['code'];
                //dd($course2);
                #checker for sem 2, level 100
                 $preyear = 2;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
                //$fuck = $fuck.','.$course;
                $fuckr2 = $fuckr2.','.$course;
                @$kuck2 = ',GPA,CGPA';
                //dd($fuckr2);
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                @$fuckcountr3++;
                @$course=$row['code'];
                #checker for sem 1, level 200
                 $preyear = 3;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
                $fuckr3 = $fuckr3.','.$course;
                @$kuck3 = ',GPA,CGPA';
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                @$fuckcountr4++;
                @$course=$row['code'];
                 $preyear = 4;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
                $fuckr4 = $fuckr4.','.$course;
                @$kuck4 = ',GPA,CGPA';
            }
            if ($semcourse == '1300') {
                @$fuckcountr5++;
                @$course=$row['code'];
                $preyear = 5;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
                $fuckr5 = $fuckr5.','.$course;
                @$kuck5 = ',GPA,CGPA';
            }
            if ($semcourse == '2300') {
                @$fuckcountr6++;
                @$course=$row['code'];
                $preyear = 6;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
                $fuckr6 = $fuckr6.','.$course;
                @$kuck6 = ',GPA,CGPA';
            }
            if ($semcourse == '1400') {
                @$fuckcountr7++;
                @$course=$row['code'];
                $preyear = 7;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
                $fuckr7 = $fuckr7.','.$course;
                @$kuck7 = ',GPA,CGPA';
            }
            if ($semcourse == '2400') {
                @$fuckcountr8++;
                $preyear = 8;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
                $fuckr8 = $fuckr8.','.$course;
                @$kuck8 = ',GPA,CGPA';
            }
            

            //$fuck = $fuck.','.$course;

            
        }

        if (@$fuckcountr1 == 1) {
            @$fuckcountr1 = 0;
        }
        if (@$fuckcountr2 == 2) {
            @$fuckcountr2 = 0;
        }
        if (@$fuckcountr3 == 2) {
            @$fuckcountr3 = 0;
        }
        if (@$fuckcountr4 == 2) {
            @$fuckcountr4 = 0;
        }
        if (@$fuckcountr5 == 2) {
            @$fuckcountr5 = 0;
        }
        if (@$fuckcountr6 == 2) {
            @$fuckcountr6 = 0;
        }
        if (@$fuckcountr7 == 2) {
            @$fuckcountr7 = 0;
        }
        if (@$fuckcountr8 == 2) {
            @$fuckcountr8 = 0;
        }


        for ($esi = 0; $esi <1; $esi++) {
                        @$fucksem1++;
                        @$fucksem11++;
                        @$fucksem2++;
                        @$fucksem21++;
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;

                        
                    }
                    
        for ($esi2 = 0; $esi2 <2; $esi2++) {
                        @$fucksem2++;
                        @$fucksem21++;
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;

                    }
                    
                        
        for ($esi3 = 0; $esi3 <2; $esi3++) {
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi4 = 0; $esi4 <2; $esi4++) {
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi5 = 0; $esi5 <2; $esi5++) {
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi6 = 0; $esi6 <2; $esi6++) {
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi7 = 0; $esi7 <2; $esi7++) {
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi8 = 0; $esi8 <2; $esi8++) {
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi9 = 0; $esi9 <2; $esi9++) {
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
      
                        
                    

        @$fuckcount = @$fuckcountr1+@$fuckcountr2+@$fuckcountr3+@$fuckcountr4+@$fuckcountr5+@$fuckcountr6+@$fuckcountr7+@$fuckcountr8;
        $fuck = @$fuckr1.@$kuck1.@$fuckr2.@$kuck2.@$fuckr3.@$kuck3.@$fuckr4.@$kuck4.@$fuckr5.@$kuck5.@$fuckr6.@$kuck6.@$fuckr7.@$kuck7.@$fuckr8.@$kuck8;
        //dd(@$fuck);
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            $kojoSense = count($data)+1;

            #excel for headers
            @$excel->sheet('MACS', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fuckcountr1,$fuckcountr2,$fuckcountr3,$fuckcountr4,$fuckcountr5,$fuckcountr6,$fuckcountr7,$fuckcountr8,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  7,
                        'D'     =>  7,
                        'E'     =>  7
                        ));


                        #add course codes
                        $sheet->prependRow(1, $explode_fuck);
                        
                        #add stuents
                        $sheet->fromArray($data);
                        
                        #no of rows, +6 for headers
                        $kojoCellBeauty = $kojoSense+6;

                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });
                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $fuckcou = 0;
                            $alpha_last = 'B';
                            $GP = 'E';
                            $CGP = 'E';
                        for ($alpha='C'; $fuckcou < $fuckcount; $alpha++) { 
                            $fuckcou++;

                            $value = $sheet->getCell(''.$alpha.'1')->getValue();

                            if ($fuckcou > $fuckcountr1 and $fuckcountr1 != 0) {
                                $GP = 'H';
                                $CGP = 'I';
                            }
                            if ($fuckcou > $fuckcountr2 + $fuckcountr1  and $fuckcountr2 != 0) {
                                $GP = 'L';
                                $CGP = 'M';
                            }
                            if ($fuckcou > $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr3 != 0) {
                                $GP = 'P';
                                $CGP = 'Q';
                            }
                            if ($fuckcou > $fuckcountr4 + $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr4 != 0) {
                                $GP = 'T';
                                $CGP = 'U';
                            }
                            if ($fuckcou > $fuckcountr5 + $fuckcountr4 + $fuckcountr3 + $fuckcountr2 + $fuckcountr1 and $fuckcountr5 != 0) {
                                $GP = 'X';
                                $CGP = 'AA';
                            }
                            //dd($fuckcou, $fuckcountr1, $fuckcountr2, $fuckcountr3, $fuckcountr4, $fuckcountr5);
                            #last cell
                            $alpha_last++;
                            //dd($alpha);
                            $sheet->setWidth(array(
                        ''.$alpha.''    =>  8,
                        
                        ));
                            #selects cells vertically, $kojosen2 the last row in the TP sheet
                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            $k_fuck = $k + 2;
                            $k_RSA = $k + 6;
                            
                            
                            //IFERROR(VLOOKUP('.$alpha.'$7&$A'.$k.',TP!$U$8:TP!$V$'.$kojoSen2.',2,FALSE),"")))');
                            
                            
                            $sheet->setCellValue(''.$alpha.$k.'','=IF(OR('.$alpha.'$7="GPA",'.$alpha.'$7="CGPA"),(IF('.$alpha.'$7="GPA",CGPA!$'.$GP.$k_RSA.',CGPA!$'.$CGP.$k_RSA.')),IFERROR(VLOOKUP('.$alpha.'$7&$A'.$k.',TP!$U$8:TP!$V$'.$kojoSen2.',2,FALSE),""))');
                            

                            if ($value == 'GPA' || $value == 'CGPA') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            }
                            
                        }
                        }

                        
                        //dd($fucksem1, $fucksem2, $fucksem3, $fucksem4, $fucksem5, $fucksem6, $fucksem7, $fucksem8);
                            #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck;
                            @$k_fuck2 = $k_fuck;
                            @$k_fuck3 = $k_fuck;
                            @$k_fuck4 = $k_fuck;
                            @$k_fuck5 = $k_fuck;
                            @$k_fuck6 = $k_fuck;
                            @$k_fuck7 = $k_fuck;
                            @$k_fuck8 = $k_fuck;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            $g7 = 0;
                            $g8 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1500) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2500 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                             $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                           //dd($fucksem21,$fucksem31);
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and !empty($fucksem41) and !is_numeric($fucksem41)) {
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and !empty($fucksem51) and !is_numeric($fucksem51)) {    
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        if ($e == 1400 and !empty($fucksem61) and !is_numeric($fucksem61)) {    
                            $k_fuck7++;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7.'',$b.' - ('.$g.') - '.$a); 
                            $g7 = $g7 + $g; 
                        }
                        if ($e == 2400 and !empty($fucksem71) and !is_numeric($fucksem71)) {    
                            $k_fuck8++;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8.'',$b.' - ('.$g.') - '.$a); 
                            $g8 = $g8 + $g; 
                        }
                        
                }

                            
                            $k_fuck1d = $k_fuck1 + 2;
                            $k_fuck1 = $k_fuck1 - $k_fuck;
                            @$sheet->setCellValue('C'.$k_fuck1d.'','Courses = '.$k_fuck1.' // Credit Hours = '.$g1);

                        if ($preyear > 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                            $k_fuck2 = $k_fuck2 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2d.'','Courses = '.$k_fuck2.' // Credit Hours = '.$g2); 
                             
                         }
                        if ($preyear > 2) {
                            $k_fuck3d = $k_fuck3 + 2;
                            $k_fuck3 = $k_fuck3 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3d.'','Courses = '.$k_fuck3.' // Credit Hours = '.$g3);
                              
                         }
                         if ($preyear > 3) {
                            $k_fuck4d = $k_fuck4 + 2;
                            $k_fuck4 = $k_fuck4 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4d.'','Courses = '.$k_fuck4.' // Credit Hours = '.$g4);
                              
                         }
                         if ($preyear > 4) {
                            $k_fuck5d = $k_fuck5 + 2;
                            $k_fuck5 = $k_fuck5 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5d.'','Courses = '.$k_fuck5.' // Credit Hours = '.$g5);
                              
                         }
                         if ($preyear > 5) {    
                            $k_fuck6d = $k_fuck6 + 2;
                            $k_fuck6 = $k_fuck6 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6d.'','Courses = '.$k_fuck6.' // Credit Hours = '.$g6); 
                             
                        }
                        if ($preyear > 6) {    
                            $k_fuck7d = $k_fuck7 + 2;
                            $k_fuck7 = $k_fuck7 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7d.'','Courses = '.$k_fuck7.' // Credit Hours = '.$g7); 
                             
                        }
                        if ($preyear > 7) {    
                            $k_fuck8d = $k_fuck8 + 2;
                            $k_fuck8 = $k_fuck8 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8d.'','Courses = '.$k_fuck8.' // Credit Hours = '.$g8); 
                             
                        }
                            

                            $lastb = $k_fuck1d + 10;

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','ACADEMIC BROADSHEET');
                            $sheet->setCellValue('N2','ADMINISTRATOR FORMAT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');

                            $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');
                            $sheet->cells('A7:'.$alpha.$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            @$sheet->mergeCells('A6:B6');
                           @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            $board1 = 'C6';
                            $board2 = $fucksem1;
                            if (!empty($fucksem11) and $preyear > 1) {
                              @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');
                               $board1 = $fucksem11;
                               $board2 = $fucksem2;  
                             } 
                            if (!empty($fucksem21) and $preyear > 2) {
                            @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                                $board1 = $fucksem21;
                                $board2 = $fucksem3;
                            } 
                            if (!empty($fucksem31) and  $preyear > 3) {
                            @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                                $board1 = $fucksem31;
                                $board2 = $fucksem4;
                            } 
                            if (!empty($fucksem41) and $preyear > 4) {
                            @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                            $board1 = $fucksem41;
                                $board2 = $fucksem5;
                            } 
                            if (!empty($fucksem51) and $preyear > 5) {
                            @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                            $board1 = $fucksem51;
                                $board2 = $fucksem6;
                            }
                            if (!empty($fucksem61) and $preyear > 6) {
                            @$sheet->mergeCells(''.$fucksem61.'6:'.$fucksem7.'6');
                            $board1 = $fucksem61;
                                $board2 = $fucksem7;
                            } 
                            if (!empty($fucksem71) and $preyear > 7) {
                            @$sheet->mergeCells(''.$fucksem71.'6:'.$fucksem8.'6');
                            $board1 = $fucksem71;
                                $board2 = $fucksem8;
                            }  
                            
                            //$sheet->mergeCells('K6:N6');
                            @$sheet->setCellValue('C6','Sem 1');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Sem 2');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Sem 3');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Sem 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Sem 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Sem 6');
                            } 
                            if ($preyear > 6) {
                            @$sheet->setCellValue(''.$fucksem61.'6','Sem 7');
                            } 
                            if ($preyear > 7) {
                            @$sheet->setCellValue(''.$fucksem71.'6','Sem 8');
                            } 
                           
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C8'); 

                            $sheet->cells('C6:'.$alpha.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A6:'.$alpha_last.$kojoCellBeauty.'', 'thin'); 

                                   
                            
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });

                            if (!empty($fucksem1)) { 
                            $sheet->cells(''.$fucksem1.'6:'.$fucksem1.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                                }
                                if ($preyear > 1) { 
                            $sheet->cells(''.$fucksem2.'6:'.$fucksem2.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 2) { 
                            $sheet->cells(''.$fucksem3.'6:'.$fucksem3.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 3) { 
                            $sheet->cells(''.$fucksem4.'6:'.$fucksem4.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($preyear > 4) { 
                            $sheet->cells(''.$fucksem5.'6:'.$fucksem5.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                        }

                        if ($preyear > 5) { 
                            $sheet->cells(''.$fucksem6.'6:'.$fucksem6.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }
                         if ($preyear > 6) { 
                            $sheet->cells(''.$fucksem7.'6:'.$fucksem7.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }
                         if ($preyear > 7) { 
                            $sheet->cells(''.$fucksem8.'6:'.$fucksem8.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }

                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                            if ($preyear > 2) {
                            $sheet->cell(''.$fucksem21.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 3) {
                            $sheet->cell(''.$fucksem31.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 4) {
                            $sheet->cell(''.$fucksem41.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($preyear > 5) {
                            $sheet->cell(''.$fucksem51.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                        if ($preyear > 6) {
                            $sheet->cell(''.$fucksem61.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                        if ($preyear > 7) {
                            $sheet->cell(''.$fucksem71.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                            
                            
                            $sheet->cells('A7:'.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thick','thin');
                                   
                            });

                            $sheet->cell(''.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thick','thin');
                                   
                            });

                            $sheet->cells('C5:'.$alpha_last.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });
             
                
                
            //});
            });  
}

$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();

               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }
#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')
            ->where('tpoly_academic_record.resit','=','yes')
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.code")
            ->groupBY("tpoly_academic_record.code")
            ->select('tpoly_academic_record.code','tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'))
            ->get();

//dd($year);
            #variable holding course codes
            @$fuck = '';
            #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcount = 0;
            //dd($fuck);
            $checkResit1 = 0;
            $checkResit2 = 0;
            $checkResit3 = 0;
            $checkResit4 = 0;
            $checkResit5 = 0;
            $checkResit6 = 0;
            $checkResit7 = 0;
            $checkResit8 = 0;
            
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            @$fuckcount++;
            @$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                #checker for sem 1, level 100
                $checkResit1 = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
            }
            #check if there was resit for the above semester
            if ($checkResit1 == 0) {
                #checker for sem 1, level 100
                $checkResit1 = 2;
                @$fucksem2 = @$fucksem1;
                @$fucksem21 = @$fucksem11;
                
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
                #checker for sem 2, level 100
                 $checkResit2 = 1;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
            }
            if ($checkResit2 == 0) {
                #checker for sem 1, level 100
                $checkResit2 = 2;
                @$fucksem3 = @$fucksem2;
                @$fucksem31 = @$fucksem21;
                
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                #checker for sem 1, level 200
                 $checkResit3 = 1;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
            }
            if ($checkResit3 == 0) {
                #checker for sem 1, level 100
                $checkResit3 = 2;
                @$fucksem4 = @$fucksem3;
                @$fucksem41 = @$fucksem31;
                
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                $checkResit4 = 1;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
            }
            if ($checkResit4 == 0) {
                #checker for sem 1, level 100
                $checkResit4 = 2;
                @$fucksem5 = @$fucksem4;
                @$fucksem51 = @$fucksem41;
                
            }
            if ($semcourse == '1300') {
                $checkResit5 = 1;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
            }
            if ($checkResit5 == 0) {
                #checker for sem 1, level 100
                $checkResit5 = 2;
                @$fucksem6 = @$fucksem5;
                @$fucksem61 = @$fucksem51;
                
            }
            if ($semcourse == '2300') {
                $checkResit6 = 1;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
            }
            if ($checkResit6 == 0) {
                #checker for sem 1, level 100
                $checkResit6 = 2;
                @$fucksem7 = @$fucksem6;
                @$fucksem71 = @$fucksem61;
                
            }
            if ($semcourse == '1400') {
                $checkResit7 = 1;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
            }
            if ($checkResit7 == 0) {
                #checker for sem 1, level 100
                $checkResit7 = 2;
                @$fucksem8 = @$fucksem7;
                @$fucksem81 = @$fucksem71;
                
            }
            if ($semcourse == '2400') {
                $checkResit8 = 1;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
            }
            if ($checkResit8 == 0) {
                #checker for sem 1, level 100
                $checkResit8 = 2;
                @$fucksem9 = @$fucksem8;
                @$fucksem91 = @$fucksem81;
                
            }
            
            $fuck = $fuck.','.$course;
            
        }
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            $kojoSense = count($data)+1;
//dd($kojoSense);
            #excel for headers
            @$excel->sheet('RESIT', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $checkResit1,$checkResit2,$checkResit3,$checkResit4,$checkResit5,$checkResit6,$checkResit7,$checkResit8) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  7,
                        'D'     =>  7,
                        'E'     =>  7
                        ));


                        #add course codes
                        $sheet->prependRow(1, $explode_fuck);
                        
                        #add stuents
                        $sheet->fromArray($data);
                        
                        #no of rows, +6 for headers
                        $kojoCellBeauty = $kojoSense+6;

                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });
                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $fuckcou = 0;
                            $alpha_last = 'B';
                        for ($alpha='C'; $fuckcou < $fuckcount; $alpha++) { 
                            $fuckcou++;

                            #last cell
                            $alpha_last++;
                            //dd($alpha);
                            $sheet->setWidth(array(
                        ''.$alpha.''    =>  8,
                        
                        ));
                            #selects cells vertically, $kojosen2 the last row in the TP sheet
                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            $k_fuck = $k + 2;
                            
                            $sheet->setCellValue(''.$alpha.$k.'','=IFERROR(VLOOKUP('.$alpha.'$7&$A'.$k.'&"yes",TP!$W$8:TP!$X$'.$kojoSen2.',2,FALSE),"")');

                           
                        }
                        }
                        
                            #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck;
                            @$k_fuck2 = $k_fuck;
                            @$k_fuck3 = $k_fuck;
                            @$k_fuck4 = $k_fuck;
                            @$k_fuck5 = $k_fuck;
                            @$k_fuck6 = $k_fuck;
                            @$k_fuck7 = $k_fuck;
                            @$k_fuck8 = $k_fuck;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            $g7 = 0;
                            $g8 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1500 and $checkResit1 == 1) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2500 and $checkResit2 == 1) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and $checkResit3 == 1) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and $checkResit4 == 1) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100 and $checkResit1 == 1) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and $checkResit2 == 1) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and $checkResit3 == 1) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and $checkResit4 == 1) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and $checkResit5 == 1) {
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and $checkResit6 == 1) {    
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        if ($e == 1400 and $checkResit7 == 1) {    
                            $k_fuck7++;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7.'',$b.' - ('.$g.') - '.$a); 
                            $g7 = $g7 + $g; 
                        }
                        if ($e == 2400 and $checkResit8 == 1) {    
                            $k_fuck8++;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8.'',$b.' - ('.$g.') - '.$a); 
                            $g8 = $g8 + $g; 
                        }
                        
                }
                            $lastb = 1;
                            //dd($lastb);
                            if ($checkResit1 == 1) {
                            $k_fuck1d = $k_fuck1 + 2;
                            $k_fuck1 = $k_fuck1 - $k_fuck;
                            @$sheet->setCellValue('C'.$k_fuck1d.'','Courses = '.$k_fuck1.' // Credit Hours = '.$g1);
                            $lastb = $k_fuck1d + 10;
                             }

                            if ($checkResit2 == 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                            $k_fuck2 = $k_fuck2 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2d.'','Courses = '.$k_fuck2.' // Credit Hours = '.$g2); 
                             $lastb = $k_fuck2d + 10;
                         }
                        if ($checkResit3 == 1) {
                            $k_fuck3d = $k_fuck3 + 2;
                            $k_fuck3 = $k_fuck3 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3d.'','Courses = '.$k_fuck3.' // Credit Hours = '.$g3);
                            $lastb = $k_fuck3d + 10;  
                         }
                         if ($checkResit4 == 1) {
                            $k_fuck4d = $k_fuck4 + 2;
                            $k_fuck4 = $k_fuck4 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4d.'','Courses = '.$k_fuck4.' // Credit Hours = '.$g4);
                             $lastb = $k_fuck4d + 10; 
                         }
                         if ($checkResit5 == 1) {
                            $k_fuck5d = $k_fuck5 + 2;
                            $k_fuck5 = $k_fuck5 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5d.'','Courses = '.$k_fuck5.' // Credit Hours = '.$g5);
                            $lastb = $k_fuck5d + 10;  
                         }
                         if ($checkResit6 == 1) {    
                            $k_fuck6d = $k_fuck6 + 2;
                            $k_fuck6 = $k_fuck6 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6d.'','Courses = '.$k_fuck6.' // Credit Hours = '.$g6); 
                            $lastb = $k_fuck6d + 10; 
                        }
                        if ($checkResit7 == 1) {    
                            $k_fuck7d = $k_fuck7 + 2;
                            $k_fuck7 = $k_fuck7 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7d.'','Courses = '.$k_fuck7.' // Credit Hours = '.$g7); 
                            $lastb = $k_fuck7d + 10; 
                        }
                        if ($checkResit8 == 1) {    
                            $k_fuck8d = $k_fuck8 + 2;
                            $k_fuck8 = $k_fuck8 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem71.$k_fuck8d.'','Courses = '.$k_fuck8.' // Credit Hours = '.$g8); 
                            $lastb = $k_fuck8d + 10; 
                        }
                            

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));

                            
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','ACADEMIC BROADSHEET');
                            $sheet->setCellValue('N2','RESIT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');
                            $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            $sheet->setCellValue('A2',$current_time);
                            $sheet->setCellValue('A1','Downloaded Time');

                            @$sheet->cells('A7:'.@$alpha.@$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            @$cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });
                            //dd($kojoCellBeauty);

                            @$sheet->mergeCells('A6:B6');
                            if ($checkResit1 == 1) {
                                //dd($checkResit1);
                            @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            }
                           //  if (!empty($fucksem11) and $checkResit2 == 1) {
                           //      //dd($fucksem11);
                           //    @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');   
                           //   } 
                           //  if (!empty($fucksem21) and $checkResit3 == 1 and $fucksem3 != $fucksem2) {
                           //      dd($fucksem21);
                           //  @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                           //  } 
                           //  if (!empty($fucksem31) and  $checkResit4 == 1 and $fucksem4 != $fucksem3) {
                           //     // dd($fucksem31, $fucksem3, $fucksem4, $fucksem5);
                           //  @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                           //  } 
                           //  if (!empty($fucksem41) and $checkResit5 == 1 and $fucksem5 != $fucksem4) {
                           //     // dd($fucksem41);
                           //  @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                           //  } 
                           // if (!empty($fucksem51) and $checkResit6 == 1 and $fucksem6 != $fucksem5) {
                           //  //dd($fucksem51);
                           //  @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                           //  }
                           //   if (!empty($fucksem61) and $checkResit7 == 1 and $fucksem7 != $fucksem6) {
                           //      //dd($fucksem61);
                           //  @$sheet->mergeCells(''.$fucksem61.'6:'.$fucksem7.'6');
                           //  $board1 = $fucksem61;
                           //      $board2 = $fucksem7;
                           //  } 
                           //  if (!empty($fucksem71) and $checkResit8 == 1 and $fucksem7 != $fucksem8) {
                           //  @$sheet->mergeCells(''.$fucksem71.'6:'.$fucksem8.'6');
                           //  $board1 = $fucksem71;
                           //      $board2 = $fucksem8;
                           //  }   
                           
                            //$sheet->mergeCells('K6:N6');
                            if ($checkResit1 == 1) {
                            @$sheet->setCellValue('C6','Sem 1');
                            }
                             if ($checkResit2 == 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Sem 2');
                            } 
                            if ($checkResit3 == 1) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Sem 3');
                            } 
                            if ($checkResit4 == 1) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Sem 4');
                            } 
                            if ($checkResit5 == 1) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Sem 5');
                            } 
                            if ($checkResit6 == 1) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Sem 6');
                            } 
                            if ($checkResit7 == 1) {
                            @$sheet->setCellValue(''.$fucksem61.'6','Sem 7');
                            } 
                            if ($checkResit8 == 1) {
                            @$sheet->setCellValue(''.$fucksem71.'6','Sem 8');
                            } 
                           
                                            
                           $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C8'); 

                            $sheet->cells('C6:'.$alpha.$kojoCellBeauty.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A6:'.$alpha_last.$kojoCellBeauty.'', 'thin'); 

                                   
                            
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });

                            if ($checkResit1 == 1) { 
                            $sheet->cells(''.$fucksem1.'6:'.$fucksem1.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                                }
                                if ($checkResit2 == 1) { 
                            $sheet->cells(''.$fucksem2.'6:'.$fucksem2.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($checkResit3 == 1) { 
                            $sheet->cells(''.$fucksem3.'6:'.$fucksem3.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($checkResit4 == 1) { 
                            $sheet->cells(''.$fucksem4.'6:'.$fucksem4.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                        }
                        if ($checkResit5 == 1) { 
                            $sheet->cells(''.$fucksem5.'6:'.$fucksem5.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            }); 
                        }

                        if ($checkResit6 == 1) { 
                            $sheet->cells(''.$fucksem6.'6:'.$fucksem6.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }

                         if ($checkResit7 == 1) { 
                            $sheet->cells(''.$fucksem7.'6:'.$fucksem7.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }
                         if ($checkResit8 == 1) { 
                            $sheet->cells(''.$fucksem8.'6:'.$fucksem8.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','medium','thin','');
                                   
                            });
                         }

                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            if ($checkResit2 == 1) {
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                            }
                            if ($checkResit3 == 1) {
                            $sheet->cell(''.$fucksem21.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($checkResit4 == 1) {
                            $sheet->cell(''.$fucksem31.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($checkResit5 == 1) {
                            $sheet->cell(''.$fucksem41.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($checkResit6 == 1) {
                            $sheet->cell(''.$fucksem51.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }
                        if ($checkResit7 == 1) {
                            $sheet->cell(''.$fucksem61.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        } 
                        if ($checkResit8 == 1) {
                            $sheet->cell(''.$fucksem71.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','medium');
                                   
                            });
                        }  
                            
                            
                            $sheet->cells('A7:'.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thick','thin');
                                   
                            });

                            $sheet->cell(''.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thick','thin');
                                   
                            });

                          /*  $sheet->cells('C5:'.$alpha_last.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });
            */
                
                
            //});
            });
//dd($yearcc);

$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->join('users','tpoly_mounted_courses.LECTURER', '=', 'users.fund')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'users.name', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();
//dd($yearcc);
               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }
#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.code")
            ->groupBY("tpoly_academic_record.code")
            ->select('tpoly_academic_record.code','tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'))
            ->get();
            #variable holding course codes
            @$fuck = '';
            #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcount = 0;
            //dd($fuck);
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            @$fuckcount++;
            @$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                #checker for sem 1, level 100
                $preyear = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
                #checker for sem 2, level 100
                 $preyear = 2;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                #checker for sem 1, level 200
                 $preyear = 3;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                 $preyear = 4;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
            }
            if ($semcourse == '1300') {
                $preyear = 5;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
            }
            if ($semcourse == '2300') {
                $preyear = 6;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
            }
            if ($semcourse == '1400') {
                $preyear = 7;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
            }
            if ($semcourse == '2400') {
                $preyear = 8;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
            }
            
            $fuck = $fuck.','.$course;
            
        }
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            

            #excel for headers
            @$excel->sheet('COURSES', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  40,
                        'B'     =>  10,
                        'C'     =>  5,
                        'D'     =>  35
                        ));

                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $fuckcou = 0;
                            $alpha_last = 'B';
                        
                        
                            #margin to display course codes for semesters
                            @$k_fuck1 = 1;
                            @$k_fuck2 = 1;
                            @$k_fuck3 = 1;
                            @$k_fuck4 = 1;
                            @$k_fuck5 = 1;
                            @$k_fuck6 = 1;
                            @$k_fuck7 = 1;
                            @$k_fuck8 = 1;

                            @$k_fuck1g = 0;
                            @$k_fuck2g = 0;
                            @$k_fuck3g = 0;
                            @$k_fuck4g = 0;
                            @$k_fuck5g = 0;
                            @$k_fuck6g = 0;
                            @$k_fuck7g = 0;
                            @$k_fuck8g = 0;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            $g7 = 0;
                            $g8 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         $h= $value->name;
                         //dd($h);
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1100 || $e == 1500) {
                            @$sheet->setCellValue('A1',' '.' '.'SEMESTER 1');
                            $k_fuck1++;
                            $k_fuck1g++;
                            $k_fuckplus1 = $k_fuck1+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck1.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck1.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck1.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck1.'',' '.' '.$h);
                            $g1 = $g1 + $g; 
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck1g.' // Credit Hours = '.$g1);
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 2');
                            $k_fuck2 = $k_fuckplus5;
                            $kojoSense = $k_fuck2 + 10;
                            
                         }
                          if ($e == 2100 || $e == 2500) {
                            $k_fuck2++;
                            $k_fuck2g++;
                            $k_fuckplus1 = $k_fuck2+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck2.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck2.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck2.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck2.'',' '.' '.$h);
                            $g2 = $g2 + $g;
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck2g.' // Credit Hours = '.$g2);
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 3');
                            $k_fuck3 = $k_fuckplus5; 
                            $kojoSense = $k_fuck3 + 10;
                            
                         }
                        if ($e == 1200 || $e == 1600) {
                            $k_fuck3++;
                            $k_fuck3g++;
                            $k_fuckplus1 = $k_fuck3+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck3.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck3.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck3.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck3.'',' '.' '.$h);
                            $g3 = $g3 + $g; 
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck3g.' // Credit Hours = '.$g3);
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 4');
                            $k_fuck4 = $k_fuckplus5; 
                            $kojoSense = $k_fuck4 + 10; 
                            
                         }
                         if ($e == 2200 || $e == 2600) {
                            $k_fuck4++;
                            $k_fuck4g++;
                            $k_fuckplus1 = $k_fuck4+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck4.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck4.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck4.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck4.'',' '.' '.$h);
                            $g4 = $g4 + $g;
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck4g.' // Credit Hours = '.$g4);  
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 5');
                            $k_fuck5 = $k_fuckplus5;
                            $kojoSense = $k_fuck5 + 10; 
                            
                         }
                         if ($e == 1300) {
                            $k_fuck5++;
                            $k_fuck5g++;
                            $k_fuckplus1 = $k_fuck5+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck5.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck5.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck5.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck5.'',' '.' '.$h);
                            $g5 = $g5 + $g;
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck5g.' // Credit Hours = '.$g5); 
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 6'); 
                            $k_fuck6 = $k_fuckplus5;
                            $kojoSense = $k_fuck6 + 10; 
                            
                         }
                         if ($e == 2300) {    
                            $k_fuck6++;
                            $k_fuck6g++;
                            $k_fuckplus1 = $k_fuck6+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck6.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck6.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck6.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck6.'',' '.' '.$h);
                            $g6 = $g6 + $g; 
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck6g.' // Credit Hours = '.$g6);
                            @$sheet->setCellValue('A'.$k_fuckplus3.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus4.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus5.'',' '.' '.'SEMESTER 7');
                            $k_fuck7 = $k_fuckplus2;  
                            $kojoSense = $k_fuck7 + 10;
                            
                        }
                        if ($e == 1400) {    
                            $k_fuck7++;
                            $k_fuck7g++;
                            $k_fuckplus1 = $k_fuck7+1;
                            $k_fuckplus2 = $k_fuckplus1+1;
                            $k_fuckplus3 = $k_fuckplus2+1;
                            $k_fuckplus4 = $k_fuckplus3+1;
                            $k_fuckplus5 = $k_fuckplus4+1;
                            @$sheet->setCellValue('A'.$k_fuck7.'',' '.' '.$a);
                            @$sheet->setCellValue('B'.$k_fuck7.'',' '.' '.$b);
                            @$sheet->setCellValue('C'.$k_fuck7.'',$g);
                            @$sheet->setCellValue('D'.$k_fuck7.'',' '.' '.$h);
                            $g7 = $g7 + $g; 
                            @$sheet->setCellValue('A'.$k_fuckplus1.'',' '.' '.'');
                            @$sheet->setCellValue('A'.$k_fuckplus2.'',' '.' '.'Courses = '.$k_fuck7g.' // Credit Hours = '.$g7);
                            $k_fuck8 = $k_fuckplus2 + 1; 
                            //dd($k_fuck7 + 8); 
                            $kojoSense = $k_fuck8 + 10;
                            
                        }
                        
                }
                            
                            
                            $sheet->cells('A1:D'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });

                            $k_fuck1d = $k_fuck1 + 2;
                           
                            if ($preyear > 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                           
                         }
                        if ($preyear > 2) {
                            $k_fuck3d = $k_fuck3 + 2;
                           
                         }
                         if ($preyear > 3) {
                            $k_fuck4d = $k_fuck4 + 2;
                           
                              
                         }
                         if ($preyear > 4) {
                            $k_fuck5d = $k_fuck5 + 2;
                           
                              
                         }
                         if ($preyear > 5) {                            
                            $k_fuck6d = $k_fuck6 + 2;
                          
                        }
                        if ($preyear > 6) {                            
                            $k_fuck7d = $k_fuck7 + 2;
                          
                        }
                        if ($preyear > 7) {                            
                            $k_fuck8d = $k_fuck8 + 2;
                          
                        }
                            

                            $lastb = $k_fuck1d + 10;

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));

                            
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array(' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->cells('A7:D'.$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<6;$lisa++)
                                {
                                $sheet->mergeCells('A'.$lisa.':D'.$lisa);
                               
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('A1:D6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            @$sheet->setCellValue('A6',' '.' '.'COURSE');
                            @$sheet->setCellValue('B6',' '.' '.'CODE');
                            @$sheet->setCellValue('C6',' '.' '.'CR');
                            @$sheet->setCellValue('D6',' '.' '.'LECTURER');

                            
                           
                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            $sheet->cells('C8:C'.$kojoSense.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 
                
            //});
            });

//if(@\Auth::user()->department=='Tptop'|| @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){




    if ($rsaProgram != 'RSA' ) {

$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();

               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }

        $currentResult=$sys->getSemYear();
        $currentResultsArray=$currentResult[0]->RESULT_DATE;
       // dd($resultb);

        $currentResultsArray1 = explode(',',$currentResultsArray);
        $resultyear = $currentResultsArray1[0];
        $resultsem = $currentResultsArray1[1];

        //dd($resultyear,$resultsem);

#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')
            //->where('year','!=',$resultyear)
            //->where('sem','!=',$resultsem)
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.code")
            ->groupBY("tpoly_academic_record.code")
            ->select('tpoly_academic_record.code','tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'))
            ->get();

            @$fuck = '';
            @$fuckr1 = '';
            @$fuckr2 = '';
            @$fuckr3 = '';
            @$fuckr4 = '';
            @$fuckr5 = '';
            @$fuckr6 = '';
            @$fuckr7 = '';
            @$fuckr8 = '';
            @$kuck1 = '';
            @$kuck2 = '';
            @$kuck3 = '';
            @$kuck4 = '';
            @$kuck5 = '';
            @$kuck6 = '';
            @$kuck7 = '';
            @$kuck8 = '';
            #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcountr1 = 1;
            @$fuckcountr2 = 2;
            @$fuckcountr3 = 2;
            @$fuckcountr4 = 2;
            @$fuckcountr5 = 2;
            @$fuckcountr6 = 2;
            @$fuckcountr7 = 2;
            @$fuckcountr8 = 2;
            //dd($fuck);
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            //@$fuckcount++;
            //@$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                @$fuckcountr1++;
                @$course=$row['code'];
                #checker for sem 1, level 100
                $preyear = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
                $fuckr1 = $fuckr1.','.$course;
                 @$kuck1 = ',';
                //dd($fuckr1);
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
               @$fuckcountr2++;
                @$course=$row['code'];
                //dd($course2);
                #checker for sem 2, level 100
                 $preyear = 2;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
                //$fuck = $fuck.','.$course;
                $fuckr2 = $fuckr2.','.$course;
                @$kuck2 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck2 = ',';
                }
                //dd($fuckr2);
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                @$fuckcountr3++;
                @$course=$row['code'];
                #checker for sem 1, level 200
                 $preyear = 3;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
                $fuckr3 = $fuckr3.','.$course;
                @$kuck3 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck3 = ',';
                }
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                @$fuckcountr4++;
                @$course=$row['code'];
                 $preyear = 4;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
                $fuckr4 = $fuckr4.','.$course;
                @$kuck4 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck4 = ',';
                }
            }
            if ($semcourse == '1300') {
                @$fuckcountr5++;
                @$course=$row['code'];
                $preyear = 5;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
                $fuckr5 = $fuckr5.','.$course;
                @$kuck5 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck5 = ',';
                }
            }
            if ($semcourse == '2300') {
                @$fuckcountr6++;
                @$course=$row['code'];
                $preyear = 6;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
                $fuckr6 = $fuckr6.','.$course;
                @$kuck6 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck6 = ',';
                }
            }
            if ($semcourse == '1400') {
                @$fuckcountr7++;
                @$course=$row['code'];
                $preyear = 7;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
                $fuckr7 = $fuckr7.','.$course;
                @$kuck7 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck7 = ',';
                }
            }
            if ($semcourse == '2400') {
                @$fuckcountr8++;
                $preyear = 8;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
                $fuckr8 = $fuckr8.','.$course;
                @$kuck8 = ',1,';
                if ($rsaProgram == 'RSA') {
                   @$kuck8 = ',';
                }
            }
            

            //$fuck = $fuck.','.$course;

            
        }

        if (@$fuckcountr1 == 1) {
            @$fuckcountr1 = 0;
        }
        if (@$fuckcountr2 == 2) {
            @$fuckcountr2 = 0;
        }
        if (@$fuckcountr3 == 2) {
            @$fuckcountr3 = 0;
        }
        if (@$fuckcountr4 == 2) {
            @$fuckcountr4 = 0;
        }
        if (@$fuckcountr5 == 2) {
            @$fuckcountr5 = 0;
        }
        if (@$fuckcountr6 == 2) {
            @$fuckcountr6 = 0;
        }
        if (@$fuckcountr7 == 2) {
            @$fuckcountr7 = 0;
        }
        if (@$fuckcountr8 == 2) {
            @$fuckcountr8 = 0;
        }


        for ($esi = 0; $esi <1; $esi++) {
                        //@$fucksem1++;
                        @$fucksem11++;
                        //@$fucksem2++;
                        @$fucksem21++;
                        //@$fucksem3++;
                        @$fucksem31++;
                        //@$fucksem4++;
                        @$fucksem41++;
                        //@$fucksem5++;
                        @$fucksem51++;
                        //@$fucksem6++;
                        @$fucksem61++;
                        //@$fucksem7++;
                        @$fucksem71++;
                        //@$fucksem8++;
                        @$fucksem81++;
                        //@$fucksem9++;
                        @$fucksem91++;

                        
                    }
                    
        for ($esi2 = 0; $esi2 <2; $esi2++) {
                        @$fucksem2++;
                        @$fucksem21++;
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;

                    }
                    
                        
        for ($esi3 = 0; $esi3 <2; $esi3++) {
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi4 = 0; $esi4 <2; $esi4++) {
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi5 = 0; $esi5 <2; $esi5++) {
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi6 = 0; $esi6 <2; $esi6++) {
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi7 = 0; $esi7 <2; $esi7++) {
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi8 = 0; $esi8 <2; $esi8++) {
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi9 = 0; $esi9 <2; $esi9++) {
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
      
                        
                    

        @$fuckcount = @$fuckcountr1+@$fuckcountr2+@$fuckcountr3+@$fuckcountr4+@$fuckcountr5+@$fuckcountr6+@$fuckcountr7+@$fuckcountr8;
        $fuck = @$fuckr1.@$kuck1.@$fuckr2.@$kuck2.@$fuckr3.@$kuck3.@$fuckr4.@$kuck4.@$fuckr5.@$kuck5.@$fuckr6.@$kuck6.@$fuckr7.@$kuck7.@$fuckr8.@$kuck8;
        //dd(@$fuck);
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            $kojoSense = count($data)+1;

            $rsaProgram = $sys->getProgramResult($program);

            //if ($rsaProgram != 'RSA' ) {
            #excel for headers
            @$excel->sheet('STATS', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fuckcountr1,$fuckcountr2,$fuckcountr3,$fuckcountr4,$fuckcountr5,$fuckcountr6,$fuckcountr7,$fuckcountr8,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  1,
                        'B'     =>  6,
                        'C'     =>  8,
                        'D'     =>  8,
                        'E'     =>  8
                        ));


                        #add course codes
                        $sheet->prependRow(1, $explode_fuck);
                        
                        #add stuents
                        //$sheet->fromArray($data);
                        @$sheet->setCellValue('B2',' '.' '.' A+');
                        @$sheet->setCellValue('B3',' '.' '.' A');
                        @$sheet->setCellValue('B4',' '.' '.' B+');
                        @$sheet->setCellValue('B5',' '.' '.' B');
                        @$sheet->setCellValue('B6',' '.' '.' C+');
                        @$sheet->setCellValue('B7',' '.' '.' C');
                        @$sheet->setCellValue('B8',' '.' '.' D+');
                        @$sheet->setCellValue('B9',' '.' '.' D');
                        @$sheet->setCellValue('B10',' '.' '.' F');
                        @$sheet->setCellValue('B12',' '.'SUM');
                        @$sheet->setCellValue('B13',' '.'MAX');
                        @$sheet->setCellValue('B14',' '.'MIN');
                        @$sheet->setCellValue('B15',' '.'AVG');
                        //@$sheet->setCellValue('B11','A+');
                        
                        #no of rows, +6 for headers
                        $kojoCellBeauty = 21;

                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });
                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $valueColumn = 2;
                            $fuckcou = 0;
                            $alpha_last = 'B';
                            $GP = 'E';
                            $CGP = 'E';
                            $B8 = 8;
                            $B16 = 10;
                        for ($alpha='C'; $fuckcou < $fuckcount; $alpha++) { 
                            $fuckcou++;
                            $valueColumn++;
                            //getCellByColumnAndRow(1, 8)->getCalculatedValue();

                            $value = $sheet->getCell(''.$alpha.'1')->getValue();

                            
                            //dd($value);
                            //$kwhyCode = $k + 6;//MACS!.$alpha.$k
                            // just commented $sheet->setCellValue(''.$alpha.'1','=MACS!'.$alpha.'7');

                            #last cell
                            $alpha_last++;
                            //dd($alpha);
                            $sheet->setWidth(array(
                        ''.$alpha.''    =>  8,
                        
                        ));
                            #selects cells vertically, $kojosen2 the last row in the TP sheet
                        for($k=2;$k<$B16+1;$k++)
                            {
                            $k_fuck = $k + 2;
                            $k_RSA = $k + 6;

                            $kwhy = $k + 6;
                            $kwhykojoSense = $kojoSense + 6;//MACS!.$alpha.$k

                           // =COUNTIF(MACS!C8:MACS!C80,">79.994")
                            if ($k == 2) {
                                $gradeMarkLower = 84.994;
                                $gradeMarkUpper = 100.00;
                            } elseif ($k == 3) {
                                $gradeMarkLower = 79.994;
                                $gradeMarkUpper = 84.994;
                            } elseif ($k == 4) {
                                $gradeMarkLower = 74.994;
                                $gradeMarkUpper = 79.994;
                            } elseif ($k == 5) {
                                $gradeMarkLower = 69.994;
                                $gradeMarkUpper = 74.994;
                            } elseif ($k == 6) {
                                $gradeMarkLower = 64.994;
                                $gradeMarkUpper = 69.994;
                            } elseif ($k == 7) {
                                $gradeMarkLower = 59.994;
                                $gradeMarkUpper = 64.994;
                            } elseif ($k == 8) {
                                $gradeMarkLower = 54.994;
                                $gradeMarkUpper = 59.994;
                            } elseif ($k == 9) {
                                $gradeMarkLower = 49.994;
                                $gradeMarkUpper = 54.994;
                            } elseif ($k == 10) {
                                $gradeMarkLower = 4.994;
                                $gradeMarkUpper = 49.994;
                            } 
                            
                            //dd($kwhy,$kwhykojoSense, '=COUNTIF(MACS!'.$alpha.$kwhy.':MACS!'.$alpha.$kwhykojoSense.',">79.994")');

                           // =COUNTIFS(MACS!C8:MACS!C13,">79.994",MACS!C8:MACS!C13,"<100.001")
                                                    
                            $sheet->setCellValue(''.$alpha.$k.'','=COUNTIFS(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.',">'.$gradeMarkLower.'",MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.',"<='.$gradeMarkUpper.'")');

                              if ($value == '1') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                                $gapColor.$valueColumn = $alpha;
                                //dd($valueColumn);
                                $sheet->setWidth(array(
                                ''.$alpha.''    =>  0,
                        
                                 ));
                                
                                $sheet->cell(''.$alpha.$k.'', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'bold'       => true,
                                    'bold'       =>  true
                                    ));

                                    });

                                $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');

                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            });
                            }

                            if ($value == '' || $value == '') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                                $gapColor.$valueColumn = $alpha;
                                //dd($valueColumn);
                                $sheet->setWidth(array(
                                ''.$alpha.''    =>  4,
                        
                                 ));
                                
                                $sheet->cell(''.$alpha.$k.'', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'bold'       => true,
                                    'bold'       =>  true
                                    ));

                                    });

                                $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');

                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            });
                            }

                        /*    $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); */
                        }
                        $sheet->setCellValue(''.$alpha.'12','=SUM('.$alpha.'2:'.$alpha.'10)');
                        $sheet->setCellValue(''.$alpha.'13','=MAX(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.')');
                        $sheet->setCellValue(''.$alpha.'14','=MIN(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.')');
                        $sheet->setCellValue(''.$alpha.'15','=(SUM(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.'))/'.$alpha.'12');
                        $sheet->getStyle(''.$alpha.'15')->getNumberFormat()->setFormatCode('0.00');

                        //=(SUM(MACS!C8:MACS!C13))/C18
                        }

                        $statsClass = $alpha++;
                        //$statsClass = $statsClass++;
                        $sheet->setCellValue(''.$statsClass.'3','Award');
                        $sheet->setCellValue(''.$statsClass.'4',' '.' '.'1st Class : ');
                        $sheet->setCellValue(''.$statsClass.'5','2nd Upper : ');
                        $sheet->setCellValue(''.$statsClass.'6','2nd Lower : ');
                        $sheet->setCellValue(''.$statsClass.'7','Pass : ');
                        $sheet->setCellValue(''.$statsClass.'8','Fail : ');

                        $statsClassAlign = $statsClass;

                        $statsClassCount = ++$statsClass;
                        $sheet->setCellValue(''.$statsClassCount.'3','No');
                        $sheet->setCellValue(''.$statsClassCount.'4','=COUNTIFS(CGPA!AA8:CGPA!AA'.$kwhykojoSense.',">3.994",CGPA!AA8:CGPA!AA'.$kwhykojoSense.',"<5.001")');
                        $sheet->setCellValue(''.$statsClassCount.'5','=COUNTIFS(CGPA!AA8:CGPA!AA'.$kwhykojoSense.',">2.994",CGPA!AA8:CGPA!AA'.$kwhykojoSense.',"<3.995")');
                        $sheet->setCellValue(''.$statsClassCount.'6','=COUNTIFS(CGPA!AA8:CGPA!AA'.$kwhykojoSense.',">1.994",CGPA!AA8:CGPA!AA'.$kwhykojoSense.',"<2.995")');
                        $sheet->setCellValue(''.$statsClassCount.'7','=COUNTIFS(CGPA!AA8:CGPA!AA'.$kwhykojoSense.',">1.494",CGPA!AA8:CGPA!AA'.$kwhykojoSense.',"<1.995")');
                        $sheet->setCellValue(''.$statsClassCount.'8','=COUNTIFS(CGPA!AA8:CGPA!AA'.$kwhykojoSense.',">0",CGPA!AA8:CGPA!AA'.$kwhykojoSense.',"<1.495")');
                        

                        
                       // =COUNTIFS(CGPA!AA8:CGPA!AA15,">3.994",CGPA!AA8:CGPA!AA15,"<5.001")
                            #margin to display course codes for semesters
                              #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck + 5;
                            @$k_fuck2 = $k_fuck + 5;
                            @$k_fuck3 = $k_fuck + 5;
                            @$k_fuck4 = $k_fuck + 5;
                            @$k_fuck5 = $k_fuck + 5;
                            @$k_fuck6 = $k_fuck + 5;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1500) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2500 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and !empty($fucksem41) and !is_numeric($fucksem41)) {
                            //dd($fucksem41);
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and !empty($fucksem51) and !is_numeric($fucksem51)) {  
                            //dd($fucksem51);  
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        
                }

                            
                            $k_fuck1d = $k_fuck1 + 2;
                            $k_fuck1 = $k_fuck1 - $k_fuck;
                            @$sheet->setCellValue('C'.$k_fuck1d.'','Courses = '.$k_fuck1.' // Credit Hours = '.$g1);

                            if ($preyear > 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                            $k_fuck2 = $k_fuck2 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2d.'','Courses = '.$k_fuck2.' // Credit Hours = '.$g2); 
                             
                         }
                        if ($preyear > 2) {
                            $k_fuck3d = $k_fuck3 + 2;
                            $k_fuck3 = $k_fuck3 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3d.'','Courses = '.$k_fuck3.' // Credit Hours = '.$g3);
                              
                         }
                         if ($preyear > 3) {
                            $k_fuck4d = $k_fuck4 + 2;
                            $k_fuck4 = $k_fuck4 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4d.'','Courses = '.$k_fuck4.' // Credit Hours = '.$g4);
                              
                         }
                         if ($preyear > 4) {
                            $k_fuck5d = $k_fuck5 + 2;
                            $k_fuck5 = $k_fuck5 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5d.'','Courses = '.$k_fuck5.' // Credit Hours = '.$g5);
                              
                         }
                         if ($preyear > 5) {    
                            $k_fuck6d = $k_fuck6 + 2;
                            $k_fuck6 = $k_fuck6 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6d.'','Courses = '.$k_fuck6.' // Credit Hours = '.$g6); 
                             
                        }
                            

                            $lastb = $k_fuck1d + 10;

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','ACADEMIC BROADSHEET');
                            $sheet->setCellValue('N2','STATISTICS');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');
                            //$current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A2',$current_time);
                            //$sheet->setCellValue('A1','Downloaded Time');

                            $sheet->cells('A7:'.$alpha.$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<5;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            @$sheet->mergeCells('A6:B6');
                            @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            if (!empty($fucksem11) and $preyear > 1) {
                              @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');   
                             } 
                            if (!empty($fucksem21) and $preyear > 2) {
                            @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                            } 
                            if (!empty($fucksem31) and  $preyear > 3) {
                            @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                            } 
                            if (!empty($fucksem41) and $preyear > 4) {
                            @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                            } 
                            if (!empty($fucksem51) and $preyear > 5) {
                            @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                            } 
                           
                             //$sheet->mergeCells('K6:N6');
                            @$sheet->setCellValue('C6','Sem 1');
                            $sheet->setBorder('C6', 'thin');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Sem 2');
                            $sheet->setBorder(''.$fucksem11.'6', 'thin');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Sem 3');
                            $sheet->setBorder(''.$fucksem21.'6', 'thin');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Sem 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Sem 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Sem 6');
                            } 
                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            
                            $sheet->cells('B8:'.$alpha_last.'8', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B10:'.$alpha_last.'10', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B12:'.$alpha_last.'12', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B14:'.$alpha_last.'14', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B16:'.$alpha_last.'16', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B18:'.$alpha_last.'18', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#efefef');
                            });

                            $sheet->cells('B20:'.$alpha_last.'20', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            //$sheet->mergeCells('A17:'.$alpha_last.'17');

                          // dd($valueColumn);
                            $valueColumn = 0;

                            

                            $sheet->setFreeze('C1'); 

                            $sheet->cells('C6:'.$alpha.'21', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A7:'.$alpha_last.$kojoCellBeauty.'', 'thin'); 

                                   
                            
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','');
                                   
                            });

                            if (!empty($fucksem1)) { 
                            $sheet->cells(''.$fucksem1.'6:'.$fucksem1.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                                }
                                if ($preyear > 1) { 
                            $sheet->cells(''.$fucksem2.'6:'.$fucksem2.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 2) { 
                            $sheet->cells(''.$fucksem3.'6:'.$fucksem3.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 3) { 
                            $sheet->cells(''.$fucksem4.'6:'.$fucksem4.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 4) { 
                            $sheet->cells(''.$fucksem5.'6:'.$fucksem5.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            }); 
                        }

                        if ($preyear > 5) { 
                            $sheet->cells(''.$fucksem6.'6:'.$fucksem6.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                         }

                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                            if ($preyear > 2) {
                            $sheet->cell(''.$fucksem21.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 3) {
                            $sheet->cell(''.$fucksem31.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 4) {
                            $sheet->cell(''.$fucksem41.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 5) {
                            $sheet->cell(''.$fucksem51.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        } 
                            
                            
                            $sheet->cells('A7:'.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','none','thin');
                                   
                            });

                            $sheet->cell(''.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thick','thin');
                                   
                            });

                            $sheet->cells('C5:'.$alpha_last.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });

                            for($valueColumn1=C;$valueColumn<$fuckcount;$valueColumn1++)
                                {
                                    
                                    $valueColumn++;

                                $valueBlank = $sheet->getCell(''.$valueColumn1.'7')->getValue();
                                //dd($valueColumn1, $valueColumn, $fuckcount, $valueBlank);
                                if ($valueBlank == '') {
                                    //dd($valueColumn1, $valueBlank);
                                $sheet->cells(''.$valueColumn1.'8:'.$valueColumn1.'21', function($cells) {
                                     //dd($valueColumn1);
                                $cells->setBackground('#ffffff');
                                
                               // $cells->setFontColor('#edeff6');
                                });
                                $sheet->mergeCells(''.$valueColumn1.'6:'.$valueColumn1.'21');
                                $sheet->cell(''.$valueColumn1.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','none','none');
                                   
                            });
                                $sheet->cell(''.$valueColumn1.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','none','none','thin');
                                   
                            });
                                }
                            }
                           
            //});               
                            //$statsClassAlign = $statsClass--;
                            $sheet->cells(''.$statsClassAlign.'10:'.$statsClassAlign.'14', function($cells) {

                            $cells->setAlignment('right');
                            });

                            $sheet->cells(''.$statsClassCount.'9:'.$statsClassCount.'14', function($cells) {

                            $cells->setAlignment('left');
                            });

                            $sheet->setWidth(array(
                            ''.$statsClassAlign.''    =>  11,
                        
                            ));

                            $sheet->setWidth(array(
                            ''.$statsClassCount.''    =>  5,
                        
                            ));

                            //dd($statsClass,$statsClassAlign,$statsClassCount);
            });

}

$rsaProgram = $sys->getProgramResult($program);
 if ($rsaProgram == 'RSA') {

$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();

               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }

        $currentResult=$sys->getSemYear();
        $currentResultsArray=$currentResult[0]->RESULT_DATE;
       // dd($resultb);

        $currentResultsArray1 = explode(',',$currentResultsArray);
        $resultyear = $currentResultsArray1[0];
        $resultsem = $currentResultsArray1[1];

        //dd($resultyear,$resultsem);

#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')->where("tpoly_students.PROGRAMMECODE", $program)
            ->where('tpoly_students.GRADUATING_GROUP',$year)
            ->where('tpoly_academic_record.grade','!=','E')
            //->where('year','!=',$resultyear)
            //->where('sem','!=',$resultsem)
            ->orderBy("tpoly_academic_record.level")
            ->orderBy("tpoly_academic_record.sem")
            ->orderBy("tpoly_academic_record.code")
            ->groupBY("tpoly_academic_record.code")
            ->select('tpoly_academic_record.code','tpoly_academic_record.sem', \DB::raw('substr(tpoly_academic_record.level, 1, 3) as level'))
            ->get();

            @$fuck = '';
            @$fuckr1 = '';
            @$fuckr2 = '';
            @$fuckr3 = '';
            @$fuckr4 = '';
            @$fuckr5 = '';
            @$fuckr6 = '';
            @$fuckr7 = '';
            @$fuckr8 = '';
            @$kuck1 = '';
            @$kuck2 = '';
            @$kuck3 = '';
            @$kuck4 = '';
            @$kuck5 = '';
            @$kuck6 = '';
            @$kuck7 = '';
            @$kuck8 = '';
            #last cell of current semesters course codes
            @$fucksem1 = 'B';
            #first cell of next semesters course codes
            @$fucksem11 = 'C';
            #No of cells for codes in a semester
            @$fuckcountr1 = 1;
            @$fuckcountr2 = 1;
            @$fuckcountr3 = 1;
            @$fuckcountr4 = 1;
            @$fuckcountr5 = 1;
            @$fuckcountr6 = 1;
            @$fuckcountr7 = 1;
            @$fuckcountr8 = 1;
            //dd($fuck);
            $courseArray=array();
        foreach($datafuck as $row){
            #count no of codes
            //@$fuckcount++;
            //@$course=$row['code'];
            $semcourse=($row['sem']).($row['level']);
            #counters for 1st sem level 100
            if ($semcourse == '1100' || $semcourse == '1500') {
                @$fuckcountr1++;
                @$course=$row['code'];
                #checker for sem 1, level 100
                $preyear = 1;
                #last cell of sem 1
                @$fucksem1++;
                @$fucksem2 = @$fucksem1;
                #1st cell of sem 2
                @$fucksem11++;
                @$fucksem21 = @$fucksem11;
                $fuckr1 = $fuckr1.','.$course;
                 @$kuck1 = ',';
                //dd($fuckr1);
            }
            #counters of 2nd sem level 100
            if ($semcourse == '2100' || $semcourse == '2500') {
               @$fuckcountr2++;
                @$course=$row['code'];
                //dd($course2);
                #checker for sem 2, level 100
                 $preyear = 2;
                 #last cell of sem 2
                @$fucksem2++;
                @$fucksem3 = @$fucksem2;
                #1st cell of sem 3
                @$fucksem21++;
                @$fucksem31 = @$fucksem21;
                //$fuck = $fuck.','.$course;
                $fuckr2 = $fuckr2.','.$course;
                @$kuck2 = ',';
                
                //dd($fuckr2);
            }
            if ($semcourse == '1200' || $semcourse == '1600') {
                @$fuckcountr3++;
                @$course=$row['code'];
                #checker for sem 1, level 200
                 $preyear = 3;
                @$fucksem3++;
                @$fucksem4 = @$fucksem3;
                @$fucksem31++;
                @$fucksem41 = @$fucksem31;
                $fuckr3 = $fuckr3.','.$course;
                @$kuck3 = ',';
               
            }
            if ($semcourse == '2200' || $semcourse == '2600') {
                @$fuckcountr4++;
                @$course=$row['code'];
                 $preyear = 4;
                @$fucksem4++;
                @$fucksem5 = @$fucksem4;
                @$fucksem41++;
                @$fucksem51 = @$fucksem41;
                $fuckr4 = $fuckr4.','.$course;
                @$kuck4 = ',';
                
            }
            if ($semcourse == '1300') {
                @$fuckcountr5++;
                @$course=$row['code'];
                $preyear = 5;
                @$fucksem5++;
                @$fucksem6 = @$fucksem5;
                @$fucksem51++;
                @$fucksem61 = @$fucksem51;
                $fuckr5 = $fuckr5.','.$course;
                @$kuck5 = ',';
                
            }
            if ($semcourse == '2300') {
                @$fuckcountr6++;
                @$course=$row['code'];
                $preyear = 6;
                @$fucksem6++;
                @$fucksem7 = @$fucksem6;
                @$fucksem61++;
                @$fucksem71 = @$fucksem61;
                $fuckr6 = $fuckr6.','.$course;
                @$kuck6 = ',';
               
            }
            if ($semcourse == '1400') {
                @$fuckcountr7++;
                @$course=$row['code'];
                $preyear = 7;
                @$fucksem7++;
                @$fucksem8 = @$fucksem7;
                @$fucksem71++;
                @$fucksem81 = @$fucksem71;
                $fuckr7 = $fuckr7.','.$course;
                @$kuck7 = ',';
                
            }
            if ($semcourse == '2400') {
                @$fuckcountr8++;
                $preyear = 8;
                @$fucksem8++;
                @$fucksem9 = @$fucksem8;
                @$fucksem81++;
                @$fucksem91 = @$fucksem81;
                $fuckr8 = $fuckr8.','.$course;
                @$kuck8 = ',';
                
            }
            

            //$fuck = $fuck.','.$course;

            
        }

        if (@$fuckcountr1 == 1) {
            @$fuckcountr1 = 0;
        }
        if (@$fuckcountr2 == 1) {
            @$fuckcountr2 = 0;
        }
        if (@$fuckcountr3 == 1) {
            @$fuckcountr3 = 0;
        }
        if (@$fuckcountr4 == 1) {
            @$fuckcountr4 = 0;
        }
        if (@$fuckcountr5 == 1) {
            @$fuckcountr5 = 0;
        }
        if (@$fuckcountr6 == 1) {
            @$fuckcountr6 = 0;
        }
        if (@$fuckcountr7 == 1) {
            @$fuckcountr7 = 0;
        }
        if (@$fuckcountr8 == 1) {
            @$fuckcountr8 = 0;
        }


        for ($esi = 0; $esi <1; $esi++) {
                        
                        @$fucksem11++;
                        @$fucksem2++;
                        @$fucksem31++;
                        @$fucksem4++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem71++;
                        @$fucksem81++;
                        @$fucksem91++;

                        
                    }
                    
        for ($esi2 = 0; $esi2 <2; $esi2++) {
                        @$fucksem21++;
                        @$fucksem61++;
                        @$fucksem71++;
                        @$fucksem81++;
                        @$fucksem91++;

                    }
                    
                        
        for ($esi3 = 0; $esi3 <2; $esi3++) {
                        @$fucksem3++;
                        @$fucksem31++;
                        @$fucksem41++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi4 = 0; $esi4 <2; $esi4++) {
                        @$fucksem4++;
                        @$fucksem41++;
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi5 = 0; $esi5 <2; $esi5++) {
                        @$fucksem5++;
                        @$fucksem51++;
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi6 = 0; $esi6 <2; $esi6++) {
                        @$fucksem6++;
                        @$fucksem61++;
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi7 = 0; $esi7 <2; $esi7++) {
                        @$fucksem7++;
                        @$fucksem71++;
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi8 = 0; $esi8 <2; $esi8++) {
                        @$fucksem8++;
                        @$fucksem81++;
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
        for ($esi9 = 0; $esi9 <2; $esi9++) {
                        @$fucksem9++;
                        @$fucksem91++;
                        
                    }
      
                        
                    

        @$fuckcount = @$fuckcountr1+@$fuckcountr2+@$fuckcountr3+@$fuckcountr4+@$fuckcountr5+@$fuckcountr6+@$fuckcountr7+@$fuckcountr8;
        $fuck = @$fuckr1.@$kuck1.@$fuckr2.@$kuck2.@$fuckr3.@$kuck3.@$fuckr4.@$kuck4.@$fuckr5.@$kuck5.@$fuckr6.@$kuck6.@$fuckr7.@$kuck7.@$fuckr8.@$kuck8;
        //dd(@$fuck);
            #course codes as string
            $fuck = ',,'.(substr($fuck, 1));
            
            #course codes as array
        $explode_fuck = array_map('strval', explode(',',$fuck));
        
            #no of students, +1 is added for header
            $kojoSense = count($data)+1;

            $rsaProgram = $sys->getProgramResult($program);

            //if ($rsaProgram != 'RSA' ) {
            #excel for headers
            @$excel->sheet('STATS', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fuckcountr1,$fuckcountr2,$fuckcountr3,$fuckcountr4,$fuckcountr5,$fuckcountr6,$fuckcountr7,$fuckcountr8,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  1,
                        'B'     =>  6,
                        'C'     =>  8,
                        'D'     =>  8,
                        'E'     =>  8
                        ));


                        #add course codes
                        $sheet->prependRow(1, $explode_fuck);
                        
                        #add stuents
                        //$sheet->fromArray($data);
                        @$sheet->setCellValue('B1',' '.' '.' >=');
                        @$sheet->setCellValue('B2',' '.' '.' 85');
                        @$sheet->setCellValue('B3',' '.' '.' 80');
                        @$sheet->setCellValue('B4',' '.' '.' 75');
                        @$sheet->setCellValue('B5',' '.' '.' 70');
                        @$sheet->setCellValue('B6',' '.' '.' 65');
                        @$sheet->setCellValue('B7',' '.' '.' 60');
                        @$sheet->setCellValue('B8',' '.' '.' 55');
                        @$sheet->setCellValue('B9',' '.' '.' 50');
                        @$sheet->setCellValue('B10',' '.' '.' F');
                        @$sheet->setCellValue('B12',' '.'SUM');
                        @$sheet->setCellValue('B13',' '.'MAX');
                        @$sheet->setCellValue('B14',' '.'MIN');
                        @$sheet->setCellValue('B15',' '.'AVG');
                        //@$sheet->setCellValue('B11','A+');
                        
                        #no of rows, +6 for headers
                        $kojoCellBeauty = 21;

                        $sheet->cells('A1:AD'.$kojoSense.'', function($cells) 
                            {

                            // manipulate the cell
                            ////$cell->setAlignment('center');
                                $cells->setFont(array(
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });
                          
                            #selects cells horizontally, $fuckcount is no of course codes
                            $valueColumn = 2;
                            $fuckcou = 0;
                            $alpha_last = 'B';
                            $GP = 'E';
                            $CGP = 'E';
                            $B8 = 8;
                            $B16 = 10;
                        for ($alpha='C'; $fuckcou < $fuckcount; $alpha++) { 
                            $fuckcou++;
                            $valueColumn++;
                            //getCellByColumnAndRow(1, 8)->getCalculatedValue();

                            $value = $sheet->getCell(''.$alpha.'1')->getValue();

                            
                            //dd($value);
                            //$kwhyCode = $k + 6;//MACS!.$alpha.$k
                            // just commented $sheet->setCellValue(''.$alpha.'1','=MACS!'.$alpha.'7');

                            #last cell
                            $alpha_last++;
                            //dd($alpha);
                            $sheet->setWidth(array(
                        ''.$alpha.''    =>  8,
                        
                        ));
                            #selects cells vertically, $kojosen2 the last row in the TP sheet
                        for($k=2;$k<$B16+1;$k++)
                            {
                            $k_fuck = $k + 2;
                            $k_RSA = $k + 6;

                            $kwhy = $k + 6;
                            $kwhykojoSense = $kojoSense + 6;//MACS!.$alpha.$k

                           // =COUNTIF(MACS!C8:MACS!C80,">79.994")
                            if ($k == 2) {
                                $gradeMarkLower = 84.994;
                                $gradeMarkUpper = 100.00;
                            } elseif ($k == 3) {
                                $gradeMarkLower = 79.994;
                                $gradeMarkUpper = 84.994;
                            } elseif ($k == 4) {
                                $gradeMarkLower = 74.994;
                                $gradeMarkUpper = 79.994;
                            } elseif ($k == 5) {
                                $gradeMarkLower = 69.994;
                                $gradeMarkUpper = 74.994;
                            } elseif ($k == 6) {
                                $gradeMarkLower = 64.994;
                                $gradeMarkUpper = 69.994;
                            } elseif ($k == 7) {
                                $gradeMarkLower = 59.994;
                                $gradeMarkUpper = 64.994;
                            } elseif ($k == 8) {
                                $gradeMarkLower = 54.994;
                                $gradeMarkUpper = 59.994;
                            } elseif ($k == 9) {
                                $gradeMarkLower = 49.994;
                                $gradeMarkUpper = 54.994;
                            } elseif ($k == 10) {
                                $gradeMarkLower = 4.994;
                                $gradeMarkUpper = 49.994;
                            } 
                            
                            //dd($kwhy,$kwhykojoSense, '=COUNTIF(MACS!'.$alpha.$kwhy.':MACS!'.$alpha.$kwhykojoSense.',">79.994")');

                           // =COUNTIFS(MACS!C8:MACS!C13,">79.994",MACS!C8:MACS!C13,"<100.001")
                                                    
                            $sheet->setCellValue(''.$alpha.$k.'','=COUNTIFS(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.',">'.$gradeMarkLower.'",MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.',"<='.$gradeMarkUpper.'")');


                            if ($value == '') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                                $gapColor.$valueColumn = $alpha;
                                //dd($valueColumn);
                                $sheet->setWidth(array(
                                ''.$alpha.''    =>  4,
                        
                                 ));
                                
                                $sheet->cell(''.$alpha.$k.'', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'bold'       => true,
                                    'bold'       =>  true
                                    ));

                                    });

                                $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');

                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            });
                            }

                        /*    $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); */
                        }
                        $sheet->setCellValue(''.$alpha.'12','=SUM('.$alpha.'2:'.$alpha.'10)');
                        $sheet->setCellValue(''.$alpha.'13','=MAX(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.')');
                        $sheet->setCellValue(''.$alpha.'14','=MIN(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.')');
                        $sheet->setCellValue(''.$alpha.'15','=(SUM(MACS!'.$alpha.$B8.':MACS!'.$alpha.$kwhykojoSense.'))/'.$alpha.'12');
                        $sheet->getStyle(''.$alpha.'15')->getNumberFormat()->setFormatCode('0.00');

                        //=(SUM(MACS!C8:MACS!C13))/C18
                        }
                        
                        $statsClass = $alpha++;
                        //$statsClass = $statsClass++;
                        $sheet->setCellValue(''.$statsClass.'3',' '.' '.' '.' '.'Competency');
                        $sheet->setCellValue(''.$statsClass.'4','CD : ');
                        $sheet->setCellValue(''.$statsClass.'5','CM : ');
                        $sheet->setCellValue(''.$statsClass.'6','C : ');
                        $sheet->setCellValue(''.$statsClass.'7','NC : ');
                        //$sheet->setCellValue(''.$statsClass.'6','Fail : ');

                         $statsClassAlign = $statsClass;

                        $statsClassCount = ++$statsClass;
                        //$sheet->setCellValue(''.$statsClassCount.'3','No');
                        $sheet->setCellValue(''.$statsClassCount.'4','=COUNTIFS(RSA!AC8:RSA!AC'.$kwhykojoSense.',">3.994",RSA!AC8:RSA!AC'.$kwhykojoSense.',"<6.001")');
                        $sheet->setCellValue(''.$statsClassCount.'5','=COUNTIFS(RSA!AC8:RSA!AC'.$kwhykojoSense.',">2.994",RSA!AC8:RSA!AC'.$kwhykojoSense.',"<3.995")');
                        $sheet->setCellValue(''.$statsClassCount.'6','=COUNTIFS(RSA!AC8:RSA!AC'.$kwhykojoSense.',">1.994",RSA!AC8:RSA!AC'.$kwhykojoSense.',"<2.995")');
                        $sheet->setCellValue(''.$statsClassCount.'7','=COUNTIFS(RSA!AC8:RSA!AC'.$kwhykojoSense.',">0",RSA!AC8:RSA!AC'.$kwhykojoSense.',"<1.995")');
                        
                            #margin to display course codes for semesters
                              #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck + 5;
                            @$k_fuck2 = $k_fuck + 5;
                            @$k_fuck3 = $k_fuck + 5;
                            @$k_fuck4 = $k_fuck + 5;
                            @$k_fuck5 = $k_fuck + 5;
                            @$k_fuck6 = $k_fuck + 5;

                            $g1 = 0;
                            $g2 = 0;
                            $g3 = 0;
                            $g4 = 0;
                            $g5 = 0;
                            $g6 = 0;
                            
                         foreach ($courseMACS1 as $key => $value) {
               //   # code...
                         $a = $value->course->COURSE_NAME;
                         $b= $value->course->COURSE_CODE;
                         $c= $value->COURSE_SEMESTER;
                         $d= $value->COURSE_LEVEL;
                         $g= $value->COURSE_CREDIT;
                         #all level types, 100H, 100BTT ia all made 100
                         $d= substr($d, 0,3);
                         $e = $c.$d;
                         if ($e == 1500) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2500 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11) and !is_numeric($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and !empty($fucksem21) and !is_numeric($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and !empty($fucksem31) and !is_numeric($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and !empty($fucksem41) and !is_numeric($fucksem41)) {
                            //dd($fucksem41);
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and !empty($fucksem51) and !is_numeric($fucksem51)) {  
                            //dd($fucksem51);  
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        
                }

                            
                            $k_fuck1d = $k_fuck1 + 2;
                            $k_fuck1 = $k_fuck1 - $k_fuck;
                            @$sheet->setCellValue('C'.$k_fuck1d.'','Courses = '.$k_fuck1.' // Credit Hours = '.$g1);

                            if ($preyear > 1) {
                            $k_fuck2d = $k_fuck2 + 2;
                            $k_fuck2 = $k_fuck2 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2d.'','Courses = '.$k_fuck2.' // Credit Hours = '.$g2); 
                             
                         }
                        if ($preyear > 2) {
                            $k_fuck3d = $k_fuck3 + 2;
                            $k_fuck3 = $k_fuck3 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3d.'','Courses = '.$k_fuck3.' // Credit Hours = '.$g3);
                              
                         }
                         if ($preyear > 3) {
                            $k_fuck4d = $k_fuck4 + 2;
                            $k_fuck4 = $k_fuck4 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4d.'','Courses = '.$k_fuck4.' // Credit Hours = '.$g4);
                              
                         }
                         if ($preyear > 4) {
                            $k_fuck5d = $k_fuck5 + 2;
                            $k_fuck5 = $k_fuck5 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5d.'','Courses = '.$k_fuck5.' // Credit Hours = '.$g5);
                              
                         }
                         if ($preyear > 5) {    
                            $k_fuck6d = $k_fuck6 + 2;
                            $k_fuck6 = $k_fuck6 - $k_fuck;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6d.'','Courses = '.$k_fuck6.' // Credit Hours = '.$g6); 
                             
                        }
                            

                            $lastb = $k_fuck1d + 10;

                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array(' '.' '.' '.''
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$programme
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$dpt3.' DEPARTMENT'
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' '.$fac3
                            ));
                            $sheet->prependRow(1, array('','',' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                            ));

                            $sheet->setCellValue('N1','ACADEMIC BROADSHEET');
                            $sheet->setCellValue('N2','STATISTICS');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');
                            //$current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A2',$current_time);
                            //$sheet->setCellValue('A1','Downloaded Time');

                            $sheet->cells('A7:'.$alpha.$lastb.'', function($cells) {
                           
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10'//,
                                //'bold'       =>  true
                            ));

                            });
                
                

                            for($lisa=1;$lisa<5;$lisa++)
                                {
                                $sheet->mergeCells('C'.$lisa.':L'.$lisa);
                                $sheet->mergeCells('N'.$lisa.':R'.$lisa);
                                //$sheet->mergeCells('F'.$lisa.':G'.$lisa);
                                //$sheet->mergeCells('J3:K3');
                                
                                //$sheet->cell('A'.$lisa, function($cell) {
                                $sheet->cells('A1:Z5', function($cells) {

                                // manipulate the cell
                                 ////$cell->setAlignment('center');
                                    $cells->setFont(array(
                                    'size'       => '12',
                                    'bold'       =>  true
                                    ));

                                    });
                                }

                            $sheet->cells('C1:J6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            $sheet->cells('N1:R6', function($cells) {

                            $cells->setBackground('#ffffff');
                            });

                            @$sheet->mergeCells('A6:B6');
                            @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            if (!empty($fucksem11) and $preyear > 1) {
                              @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');   
                             } 
                            if (!empty($fucksem21) and $preyear > 2) {
                            @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                            } 
                            if (!empty($fucksem31) and  $preyear > 3) {
                            @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                            } 
                            if (!empty($fucksem41) and $preyear > 4) {
                            @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                            } 
                            if (!empty($fucksem51) and $preyear > 5) {
                            @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                            } 
                           
                             //$sheet->mergeCells('K6:N6');
                            @$sheet->setCellValue('C6','Sem 1');
                            $sheet->setBorder('C6', 'thin');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Sem 2');
                            $sheet->setBorder(''.$fucksem11.'6', 'thin');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Sem 3');
                            $sheet->setBorder(''.$fucksem21.'6', 'thin');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Sem 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Sem 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Sem 6');
                            } 
                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            
                            $sheet->cells('B8:'.$alpha_last.'8', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B10:'.$alpha_last.'10', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B12:'.$alpha_last.'12', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });
                            
                            $sheet->cells('B14:'.$alpha_last.'14', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B16:'.$alpha_last.'16', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            $sheet->cells('B18:'.$alpha_last.'18', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#efefef');
                            });

                            $sheet->cells('B20:'.$alpha_last.'20', function($cells) {
                            $cells->setBackground('#dce6f1');
                            //$cells->setFontColor('#edeff6');
                            });

                            //$sheet->mergeCells('A17:'.$alpha_last.'17');

                          // dd($valueColumn);
                            $valueColumn = 0;

                            

                            $sheet->setFreeze('C1'); 

                            $sheet->cells('C6:'.$alpha.'21', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 

                            $sheet->setBorder('A7:'.$alpha_last.$kojoCellBeauty.'', 'thin'); 

                                   
                            
                            $sheet->cells('B6:B'.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','');
                                   
                            });

                            if (!empty($fucksem1)) { 
                            $sheet->cells(''.$fucksem1.'6:'.$fucksem1.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                                }
                                if ($preyear > 1) { 
                            $sheet->cells(''.$fucksem2.'6:'.$fucksem2.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 2) { 
                            $sheet->cells(''.$fucksem3.'6:'.$fucksem3.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 3) { 
                            $sheet->cells(''.$fucksem4.'6:'.$fucksem4.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 4) { 
                            $sheet->cells(''.$fucksem5.'6:'.$fucksem5.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            }); 
                        }

                        if ($preyear > 5) { 
                            $sheet->cells(''.$fucksem6.'6:'.$fucksem6.$kojoCellBeauty.'', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','thin','thin','thin');
                                   
                            });
                         }

                            $sheet->cell('A6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thin','thin');
                                   
                            });
                            $sheet->cell('C6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                            if ($preyear > 2) {
                            $sheet->cell(''.$fucksem21.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 3) {
                            $sheet->cell(''.$fucksem31.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 4) {
                            $sheet->cell(''.$fucksem41.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        }
                        if ($preyear > 5) {
                            $sheet->cell(''.$fucksem51.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','thin','thin');
                                   
                            });
                        } 
                            
                            
                            $sheet->cells('A7:'.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','thin','none','thin');
                                   
                            });

                            $sheet->cell(''.$alpha_last.'7', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('thin','medium','thick','thin');
                                   
                            });

                            $sheet->cells('C5:'.$alpha_last.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','medium','none');
                                   
                            });

                            for($valueColumn1=C;$valueColumn<$fuckcount;$valueColumn1++)
                                {
                                    
                                    $valueColumn++;

                                $valueBlank = $sheet->getCell(''.$valueColumn1.'7')->getValue();
                                //dd($valueColumn1, $valueColumn, $fuckcount, $valueBlank);
                                if ($valueBlank == '') {
                                    //dd($valueColumn1, $valueBlank);
                                $sheet->cells(''.$valueColumn1.'8:'.$valueColumn1.'21', function($cells) {
                                     //dd($valueColumn1);
                                $cells->setBackground('#ffffff');
                                
                               // $cells->setFontColor('#edeff6');
                                });
                                $sheet->mergeCells(''.$valueColumn1.'6:'.$valueColumn1.'21');
                                $sheet->cell(''.$valueColumn1.'5', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('none','none','none','none');
                                   
                            });
                                $sheet->cell(''.$valueColumn1.'6', function($celB) {

                                // manipulate the cell
                                 $celB->setBorder('','none','none','thin');
                                   
                            });
                                }
                            }
            
                                
                            $sheet->cells(''.$statsClassAlign.'10:'.$statsClassAlign.'14', function($cells) {

                            $cells->setAlignment('right');
                            });

                            $sheet->cells(''.$statsClassCount.'10:'.$statsClassCount.'14', function($cells) {

                            $cells->setAlignment('left');
                            });

                            $sheet->setWidth(array(
                            ''.$statsClassAlign.''    =>  10,
                        
                            ));

                            $sheet->setWidth(array(
                            ''.$statsClassCount.''    =>  5,
                        
                            ));
            //});
            });



}

        })->download('xlsx');


    }



}
