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

class PlanningController extends Controller
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
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Admin' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer' || @\Auth::user()->role=='Registrar' || @\Auth::user()->department=='Planning'){
            $programme=$sys->getProgramList();
            $course=$sys->getMountedCourseList3();

            return view('students.downloadReports')->with('programme', $programme)->with('courses',$course)
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



        public function downloadResults(Request $request, SystemController $sys )

    {

        
        set_time_limit(200000000);
        $this->validate($request, [


            //'program' => 'required',
           // 'sem' => 'required',
           // 'year' => 'required',
           // 'year' => 'required',
        ]);

        $arraycc = $sys->getSemYear();
        $yearcc = $arraycc[0]->YEAR;
        $grad = $arraycc[0]->GRAD;
        //$yearcc = '2017/2018';

        $currentResultsArray=$arraycc[0]->RESULT_DATE;

        $partYear = date('Y');
       // dd($resultb);
        //dd($partYear);

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
        
        //$course = $request->input("course");
        


        $programPopulation = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')->where("tpoly_students.STATUS", 'In school')
            ->groupBy('tpoly_students.PROGRAMMECODE')
            ->groupBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.PROGRAMMECODE')
            ->select('tpoly_programme.PROGRAMME', 'tpoly_students.LEVEL', \DB::raw('count(*)'))
            ->get();

            

        $genderProgram = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')
            ->where("tpoly_students.STATUS", 'In school')
            ->groupBy('tpoly_students.PROGRAMMECODE')
            ->groupBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.PROGRAMMECODE')
            ->select('tpoly_programme.PROGRAMME', 'tpoly_students.LEVEL', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();

            $genderDepartment = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')->join('tpoly_department', 'tpoly_programme.DEPTCODE', '=', 'tpoly_department.DEPTCODE')->join('tpoly_faculty', 'tpoly_department.FACCODE', '=', 'tpoly_faculty.FACCODE')
            ->where("tpoly_students.STATUS", 'In school')
            ->groupBy('tpoly_department.DEPTCODE')
            ->groupBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_faculty.FACCODE')
            ->orderBy('tpoly_students.LEVEL')
            
            ->select('tpoly_department.DEPARTMENT', 'tpoly_students.LEVEL', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();

        $genderFaculty = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')->join('tpoly_department', 'tpoly_programme.DEPTCODE', '=', 'tpoly_department.DEPTCODE')->join('tpoly_faculty', 'tpoly_department.FACCODE', '=', 'tpoly_faculty.FACCODE')
            ->where("tpoly_students.STATUS", 'In school')
            ->groupBy('tpoly_faculty.FACCODE')
            ->groupBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_faculty.FACCODE')
            ->select('tpoly_faculty.FACULTY', 'tpoly_students.LEVEL', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();


            $interNation = Models\StudentModel::where("COUNTRY", "NOT LIKE", "GHANA". "%")
            ->where("STATUS", "In school")
            ->groupBy('COUNTRY')
            ->groupBy('LEVEL')
            ->groupBy('SEX')
            ->orderBy('COUNTRY')
            ->orderBy('LEVEL')
            ->select('COUNTRY', 'LEVEL', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();


            $region = Models\StudentModel::where("STNO", "LIKE", $partYear. "%")
            ->where("STATUS", "In school")
            ->groupBy('REGION')           
            ->orderBy('REGION')
            ->select('REGION', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();

            $genderAdmit = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')->join('tpoly_department', 'tpoly_programme.DEPTCODE', '=', 'tpoly_department.DEPTCODE')->join('tpoly_faculty', 'tpoly_department.FACCODE', '=', 'tpoly_faculty.FACCODE')
            ->where("tpoly_students.STNO", "LIKE", $partYear. "%")
            ->groupBy('tpoly_faculty.FACCODE')
            ->groupBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_faculty.FACCODE')
            ->select('tpoly_faculty.FACULTY', 'tpoly_students.LEVEL', \DB::raw('count(case when tpoly_students.SEX = "MALE" then tpoly_students.ID END) as MALE'), \DB::raw('count(case when tpoly_students.SEX = "FEMALE" then tpoly_students.ID END) as FEMALE'), \DB::raw('count(*) as TOTAL'))
            ->get();


             $bestProgramme = "";//Models\StudentModel:://join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')->join('tpoly_department', 'tpoly_programme.DEPTCODE', '=', 'tpoly_department.DEPTCODE')->join('tpoly_faculty', 'tpoly_department.FACCODE', '=', 'tpoly_faculty.FACCODE')
            // ->where("tpoly_students.GRADUATING_GROUP", $grad)
            // ->where("tpoly_students.STATUS", "Alumni")
            // ->where("tpoly_students.SEX", "!=", "")
            // ->where("tpoly_students.TOTAL_CREDIT_DONE", ">", 75)
            // ->groupBy('tpoly_students.PROGRAMMECODE')
            // ->groupBy('tpoly_students.SEX')
            // ->orderBy('tpoly_faculty.FACULTY')
            // ->orderBy('tpoly_programme.PROGRAMME')
            // ->orderBy('tpoly_students.SEX')
            // ->select('tpoly_faculty.FACULTY', 'tpoly_programme.PROGRAMME', 'tpoly_students.INDEXNO', 'tpoly_students.NAME', 'tpoly_students.SEX', 'tpoly_students.LEVEL', 'tpoly_students.CGPA', \DB::raw('INNER JOIN (SELECT PROGRAMMECODE, SEX, MAX(CGPA) CGPA FROM tpoly_students where GRADUATING_GROUP = $grad and GROUP BY PROGRAMMECODE, sex) f on tpoly_students.PROGRAMMECODE = f.PROGRAMMECODE and tpoly_students.SEX = f.SEX and tpoly_students.CGPA = f.CGPA'))
            // ->get();

        //     SELECT(\DB::raw('d.FACULTY, b.PROGRAMME, a.INDEXNO, a.NAME, a.SEX, a.LEVEL, a.CGPA FROM tpoly_students as a INNER JOIN tpoly_programme as b on a.PROGRAMMECODE = b.PROGRAMMECODE and a.STATUS = "Alumni" and a.GRADUATING_GROUP = "2017/2018"  and a.SEX != "" and a.TOTAL_CREDIT_DONE > "75" INNER JOIN tpoly_department as c on b.DEPTCODE = c.DEPTCODE INNER JOIN tpoly_faculty as d on c.FACCODE = d.FACCODE INNER JOIN (SELECT PROGRAMMECODE, SEX, MAX(CGPA) CGPA FROM tpoly_students where GRADUATING_GROUP = "2017/2018" GROUP BY PROGRAMMECODE, sex) f on a.PROGRAMMECODE = f.PROGRAMMECODE and a.SEX = f.SEX and a.CGPA = f.CGPA group by a.PROGRAMMECODE, a.SEX ORDER BY d.FACULTY, b.PROGRAMME, a.SEX'))

        // ->get();

            
            

        //dd($kojoSensible);

             $insure = Models\StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')
            ->where("tpoly_students.STATUS","In school")
            ->orderBy('tpoly_programme.PROGRAMME')
            ->orderBy('tpoly_students.LEVEL')
            ->orderBy('tpoly_students.INDEXNO')
            ->select('tpoly_programme.PROGRAMME', 'tpoly_students.LEVEL', 'tpoly_students.INDEXNO', 'tpoly_students.NAME', 'tpoly_students.TELEPHONENO', 'tpoly_students.GUARDIAN_NAME','tpoly_students.GUARDIAN_ADDRESS', 'tpoly_students.GUARDIAN_PHONE')
            ->get();

        return Excel::create($partYear, function ($excel) use ($kojoSense, $sys, $programPopulation, $genderProgram, $genderFaculty, $genderDepartment, $interNation, $region, $genderAdmit, $yearcc, $bestProgramme, $insure){

            $excel->getProperties()
   ->setCreator("TTU")
   ->setTitle("TTU STUDENTS REPORTS")
   ->setLastModifiedBy("TPCONNECT")
   ->setDescription('Multiple sheets showing all results')
   ->setSubject("TPCONNECT")
   ->setKeywords('TP, marks, rs, normal')
   ;

            $excel->sheet('prog_pop', function ($sheet) use ($kojoSense, $programPopulation, $yearcc) {
                


                $sheet->fromArray($programPopulation);

                

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' POPULATION BY PROGRAMME for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

              
            });

            $excel->sheet('prog_gen', function ($sheet) use ($kojoSense, $genderProgram, $yearcc) {
                


                $sheet->fromArray($genderProgram);

                

                  $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' GENDER BY PROGRAMME for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

           
//});
            });


            $excel->sheet('dep_gen', function ($sheet) use ($kojoSense, $genderDepartment, $yearcc) {

                $sheet->fromArray($genderDepartment);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' GENDER BY DEPARTMENT for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

           
//});
            });

            $excel->sheet('fac_gen', function ($sheet) use ($kojoSense,$genderFaculty, $yearcc) {
                


                $sheet->fromArray($genderFaculty);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' GENDER BY FACULTY for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });


                $excel->sheet('Inter', function ($sheet) use ($kojoSense,$interNation, $yearcc) {
                


                $sheet->fromArray($interNation);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' INTERNATIONAL STUDENTS for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });


                $excel->sheet('Region', function ($sheet) use ($kojoSense,$region, $yearcc) {
                


                $sheet->fromArray($region);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' POPULATION BY REGION for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });


            $excel->sheet('Admit_gen', function ($sheet) use ($kojoSense,$region, $genderAdmit, $yearcc) {
                


                $sheet->fromArray($genderAdmit);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' ADMISSIONS BY GENDER for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });


            $excel->sheet('Prog_class', function ($sheet) use ($kojoSense,$region, $bestProgramme, $yearcc) {
                


                $sheet->fromArray($bestProgramme);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' BEST STUDENTS BY GENDER for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });

            $excel->sheet('Insure_list', function ($sheet) use ($kojoSense,$region, $insure, $yearcc) {
                


                $sheet->fromArray($insure);

                 $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                 $current_time = \Carbon\Carbon::now()->toDateTimeString();
                            //$sheet->setCellValue('A3',$current_time);
                 $sheet->prependRow(1, array(' '.' '. $current_time
                ));
                $sheet->prependRow(1, array(' '.' INSURANCE LIST for '.$yearcc
                ));
                $sheet->prependRow(1, array(' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));
           
//});
            });

            

           
            





        })->download('xlsx');


    }



}
