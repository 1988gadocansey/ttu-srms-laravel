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

class CourseController extends Controller
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
    public function printCards(Request $request,SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){
            if ($request->isMethod("get")) {
                $program=$sys->getProgramList();

                return view('courses.cardview')
                    ->with('program',$program)->with('year',$sys->years())->with('level', $sys->getLevelList());
            }
            else{

                $program = $request->input('program');

                $level = $request->input('level');

                // $query = Models\AcademicRecordsModel::where("code", $course)->where('year',$year)->where('sem',$semester)->where('level',$level)->paginate(100);

                // $query = Models\StudentModel::where("PROGRAMMECODE",$program)->where("LEVEL",$level)->where("STATUS","In School")->get();
// dd($mark);

                $url = url('/printCards/'.$program.'/program/'.$level.'/level');

                $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                $request->session()->flash("success",
                    "    $print_window");
                return redirect("/print/cards");



            }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function processCards(Request $request,$program ,$level) {
        //$year=\Session::get('year');
        $query = Models\StudentModel::where("PROGRAMMECODE",$program)->where("LEVEL",$level)->where("STATUS","In School")->get();

        return view('courses.printCard')->with('mark', $query)->with("program",$program)
            ->with("level", $level)
            ;


    }
    public function checkMountedCourses(Request $request,SystemController $sys) {
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        if ($request->isMethod("get")) {
            $hod = \Auth::user()->fund;

            $courses = Models\MountedCourseModel::where('COURSE', '!=', '')->where("COURSE_SEMESTER",$sem)->where("COURSE_YEAR",$year)->whereHas('courses', function($q) {
                $q->whereHas('programs', function($q) {
                    $q->whereIn('DEPTCODE', array(@\Auth::user()->department));
                });
            })->paginate(100);
            return view('courses.hodChecker')->with('data', $courses) ;

        } else{
            $hod=\Auth::user()->fund;
            $checked=1;
            $check= Models\MountedCourseCheckerModel::where("hod",$hod)->where("sem",$sem)
                ->where("year",$year)->first();
            if($check==""){
                $checker=new Models\MountedCourseCheckerModel();
                $checker->hod=$hod;
                $checker->sem=$sem;
                $checker->year=$year;
                $checker->checked=$checked;
                $checker->save();
            }
            else{
                Models\MountedCourseCheckerModel::where("hod",$hod)->where("sem",$sem)
                    ->where("year",$year)->update(array("checked"=>$checked));
            }
            return   redirect('mounted_view')->with("success", " <span style='font-weight:bold;font-size:13px;'>Courses has been verified for registration. Thanks!</span> ");

        }

    }
    public function processCourseUploads(Request $request,SystemController $sys) {


        set_time_limit(36000);




        $valid_exts = array('csv','xls','xlsx'); // valid extensions
        $file = $request->file('file');
        $name = time() . '-' . $file->getClientOriginalName();
        if (!empty($file)) {

            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, $valid_exts)) {
                // Moves file to folder on server
                // $file->move($destination, $name);

                $path = $request->file('file')->getRealPath();
                $data = Excel::load($path, function($reader) {

                })->get();
                $total=count($data);

                if(!empty($data) && $data->count()){

                    $user = \Auth::user()->id;
                    foreach($data as $value=>$row)
                    {
                        $code=$row->course_code;
                        $program=$row->programme;
                        $credit=$row->course_credit;
                        $name=  strtoupper($row->course_name);
                        $year=$row->course_level;
                        $semester=$row->course_semester;

                        $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
                        if (in_array($program, $programme)) {

                            $testQuery=Models\CourseModel::where('COURSE_CODE', $code)->first();

                            if(empty($testQuery)){


                                $course = new Models\CourseModel();
                                $course->COURSE_CODE = $code;
                                $course->COURSE_NAME = $name;
                                $course->COURSE_CREDIT = $credit;
                                $course->PROGRAMME = $program;
                                $course->COURSE_SEMESTER = $semester;
                                $course->COURSE_LEVEL = $year;

                                $course->USER = $user;
                                $course->save();
                                \DB::commit();
                            }
                            else{

                                Models\CourseModel::where('COURSE_CODE', $code)->update(array("COURSE_LEVEL" =>@$year, "COURSE_SEMESTER" => $semester, "PROGRAMME" => $program,  "COURSE_CREDIT" =>$credit,"COURSE_NAME"=>$name,"USER"=>$user ));
                                \DB::commit();
                            }
                        }
                        else{
                            redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");

                        }





                    }
                }
            } else {
                return redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");

            }
        } else {
            return redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload an excel file!</span> ");

        }


        return redirect('/courses')->with("success", " <span style='font-weight:bold;font-size:13px;'>$total Courses uploaded successfully</span> ");

    }
    public function processMountedUpload(Request $request,SystemController $sys) {

        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support' || @\Auth::user()->role == 'Admin'|| @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {



            set_time_limit(36000);

            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
            $file = $request->file('file');
            $name = time() . '-' . $file->getClientOriginalName();
            if (!empty($file)) {

                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, $valid_exts)) {
                    // Moves file to folder on server
                    // $file->move($destination, $name);

                    $path = $request->file('file')->getRealPath();
                    $data = Excel::load($path, function($reader) {

                    })->get();
                    $total = count($data);

                    if (!empty($data) && $data->count()) {

                        $user = \Auth::user()->id;
                        $courseError=array();
                        $programError=array();
                        foreach ($data as $value => $row) {

                            $code = $row->code;
                            $program = $row->program;
                            $courseID=$sys->getCourseByCode2($code,$program);
                            $credit = $row->credit;
                            $type = $row->type;
                            $level = $row->level;
                            $name = $row->course;
                            $year = $row->year;
                            $semester = $row->semester;
                            $searchCourse = $sys->courseSearchByCode();
                            $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
                            if (in_array($program, $programme)) {
                                if (in_array($code, $searchCourse)) {
                                    $testQuery = Models\MountedCourseModel::where('COURSE', $courseID)->where("COURSE_YEAR",$year)
                                        ->where("COURSE_SEMESTER",$semester)
                                        ->first();

                                    if (empty($testQuery)) {


                                        $course = new Models\MountedCourseModel();
                                        $course->COURSE = $courseID;
                                        $course->COURSE_CODE = $code;
                                        $course->COURSE_CREDIT = $credit;
                                        $course->PROGRAMME = $program;
                                        $course->COURSE_SEMESTER = $semester;
                                        $course->COURSE_LEVEL = $level;
                                        $course->COURSE_TYPE = $type;
                                        $course->COURSE_YEAR = $year;
                                        $course->MOUNTED_BY = $user;
                                        $course->save();
                                        \DB::commit();
                                    } else {

                                        Models\MountedCourseModel::where('COURSE', $courseID)->update(array("COURSE_CODE" =>$code,"COURSE_LEVEL" =>$level, "COURSE_SEMESTER" => $semester, "PROGRAMME" => $program, "COURSE_CREDIT" => $credit, "COURSE_TYPE" => $type, "COURSE_YEAR" => $year,"MOUNTED_BY" => $user));
                                        \DB::commit();
                                    }
                                } else {
                                    array_push($courseError, $name." ".$code);
                                    //  redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize courses.please try again!</span> ");
                                    //  dd($courseError);
                                    continue;
                                }
                            } else {
                                array_push($programError, $sys->getProgram($program));
                                continue;
                                // redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");
                            }
                        }
                        if(!empty($programError) || !empty($courseError)){
                            return     redirect('/upload/mounted')->with("errorP",$programError)
                                ->with("errorC",$courseError);

                        }
                    }
                } else {
                    return redirect('/upload/mounted')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
                }
            } else {
                return redirect('/upload/mounted')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload an excel file!</span> ");
            }
            return redirect('/mounted_view')->with("success", " <span style='font-weight:bold;font-size:13px;'>$total Courses mounted successfully</span> ");

        }
        else {

            return redirect("/dashboard");
        }






    }

    public function processCourseUpload(Request $request,SystemController $sys) {


        set_time_limit(36000);

        $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
        $file = $request->file('file');
        $name = time() . '-' . $file->getClientOriginalName();
        if (!empty($file)) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, $valid_exts)) {
                // Moves file to folder on server
                // $file->move($destination, $name);
                $path = $request->file('file')->getRealPath();
                $data = Excel::load($path, function($reader) {

                })->get();
                $total = count($data);
                if (!empty($data) && $data->count()) {
                    $user = \Auth::user()->id;
                    $courseError=array();
                    $programError=array();
                    foreach ($data as $value => $row) {

                        $code = $row->code;
                        $program = $row->program;
                        $courseID=$sys->getCourseByCode2($code,$program);
                        $credit = $row->credit;
                        $type = $row->type;
                        $level = $row->level;
                        $name = $row->course;
                        $year = $row->year;
                        $semester = $row->semester;
                        $searchCourse = $sys->courseSearchByCode();
                        $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
                        if (in_array($program, $programme)) {
                            if (in_array($code, $searchCourse)) {
                                $testQuery = Models\MountedCourseModel::where('COURSE', $courseID)->where("COURSE_YEAR",$year)
                                    ->where("COURSE_SEMESTER",$semester)
                                    ->first();
                                if (empty($testQuery)) {
                                    $course = new Models\MountedCourseModel();
                                    $course->COURSE = $courseID;
                                    $course->COURSE_CODE = $code;
                                    $course->COURSE_CREDIT = $credit;
                                    $course->PROGRAMME = $program;
                                    $course->COURSE_SEMESTER = $semester;
                                    $course->COURSE_LEVEL = $level;
                                    $course->COURSE_TYPE = $type;
                                    $course->COURSE_YEAR = $year;
                                    $course->MOUNTED_BY = $user;
                                    $course->save();
                                    \DB::commit();
                                } else {
                                    Models\MountedCourseModel::where('COURSE', $courseID)->update(array("COURSE_CODE" =>$code,"COURSE_LEVEL" =>$level, "COURSE_SEMESTER" => $semester, "PROGRAMME" => $program, "COURSE_CREDIT" => $credit, "COURSE_TYPE" => $type, "COURSE_YEAR" => $year,"MOUNTED_BY" => $user));
                                    \DB::commit();
                                }
                            } else {
                                array_push($courseError, $name." ".$code);
                                //  redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize courses.please try again!</span> ");
                                //  dd($courseError);
                                continue;
                            }
                        } else {
                            array_push($programError, $sys->getProgram($program));
                            continue;
                            // redirect('/upload/courses')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");
                        }
                    }
                    if(!empty($programError) || !empty($courseError)){
                        return     redirect('/upload/mounted')->with("errorP",$programError)
                            ->with("errorC",$courseError);

                    }
                }
            } else {
                return redirect('/upload/mounted')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
            }
        } else {
            return redirect('/upload/mounted')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload an excel file!</span> ");
        }



        return redirect('/mounted_view')->with("success", " <span style='font-weight:bold;font-size:13px;'>$total Courses mounted successfully</span> ");

    }
    public function uploadMounted(Request $request,SystemController $sys){

        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support' || @\Auth::user()->role == 'Admin'|| @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {



            return view('courses.uploadMounted');




        } else {

            return redirect("/dashboard");
        }
    }
    public function processUpdateMounted(SystemController $sys,Request $request) {
        \DB::beginTransaction();
        try {

            $upper = $request->input('upper');

            $sem = $request->input('semester');
            $credit = $request->input('credit');
            $level = $request->input('level');
            $type = $request->input('type');
            $course = $request->input('course');

            $lecturer = $request->input('lecturer');

            $key = $request->input('key');
            for ($i = 0; $i < $upper; $i++) {
                $courseArr = $course[$i];

                $levelArr = $level[$i];
                $semArr = $sem[$i];
                $creditArr = $credit[$i];
                $typeArr = $type[$i];
                $keyArr = $key[$i];
                $lecturerArr = $lecturer[$i];

                Models\MountedCourseModel::where("ID", $key)
                    ->update(array("COURSE_CREDIT" => $creditArr,   "COURSE_SEMESTER" => $semArr, "COURSE_TYPE" => $typeArr, "COURSE_LEVEL" => $levelArr, "LECTURER" => $lecturerArr));

                \DB::commit();
                Models\AcademicRecordsModel::where("course", $key)->update(array("credits" => $creditArr,   "sem" => $semArr, "level" => $levelArr, "lecturer" => $lecturerArr));

            }
            return redirect("/mounted_view")->with("success","Mounted courses updated successfully");
        }
        catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function updateMounted(SystemController $sys,Request $request, $id) {
        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support'|| @\Auth::user()->role == 'Admin'|| @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {


            $lecturers=$sys->getLectureList_All();
            $yearList=$sys->years();
            $program=$sys->getProgramList();
            $course=$sys->getCourseList();
            $user=@\Auth::user()->fund;
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            /*$query=  @Models\MountedCourseModel::query()
                ->where("COURSE_YEAR",$year)
                ->where("ID",$id)
                ->where("COURSE_SEMESTER",$sem)->paginate(20);*/
            $query=  @Models\MountedCourseModel::query()

                ->where("ID",$id)->paginate(20);


            return view('courses.editMounted')->with("data",@$query)
                ->with("lecturer",$lecturers)
                ->with("program",$program)
                ->with("course",$course)
                ->with("ID",$id)
                ->with('level', $sys->getLevelList())
                ->with("years",$yearList);



        }
        else{
            return  redirect()->back();
        }


    }
    public function gadoo(SystemController $sys,Request $request){
        if ($request->isMethod("get")) {



            $programme = $sys->getProgramList();
            $course = $sys->getCourseList();

            return view('courses.legacyGrades')->with('level', $sys->getLevelList())->with('program', $programme)->with('level', $sys->getLevelList())
                ->with('course', $course)->with('year', $sys->years());


        }
        else{
            $destination = "public/upload";

            $handle = fopen($_FILES['file']['tmp_name'], "r");

            move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
            $data = array(); // data array


            $row = 0;
            $columns = [];
            $rows = [];
            // first get the headers into an array
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE AND $row==0) {
                $columns = $data;
                $row++;

                // while get the columns start reading the rows too
                while ($file_line = fgetcsv($handle,1000,",","'")){

                    $totalRecords = count($file_line);

                    for ($c = 0; $c <$totalRecords; $c++) {
                        $col[$c] = $file_line[$c];
                    }
                    // we dont need the index no column so we remove it using unset which is the
                    // first element of the header array
                    unset($columns[0]);

                    $aggie = 1;
                    //dd($columns);
                    foreach ($columns as    $name){

                        $course=$name;
                        $exam=$col[$aggie];
                        $total=$col[$aggie];
                        $indexNo=$col[0];
                        $studentDb= $indexNo  ;
                        $studentID= $sys->getStudentIDfromIndexno($indexNo);

                        $courseName=$sys->getCourseCodeByIDArray2($course);

                        $displayCourse=$courseName[0]->COURSE_NAME;
                        $displayCode=$courseName[0]->COURSE_CODE;
                        $studentSearch = $sys->studentSearchByIndexNo($programme); // check if the students in the file tally
                        /*check if the students in the file tally with  students records
                         * this is done so that users don't upload results of students that
                         * are not in the system
                         */

                        //if (@in_array($studentDb, $studentSearch)) {


                        $total= round($exam,2);
                        $programmeDetail=$sys->getCourseProgramme2($course);

                        $program=$sys->getProgramArray($programmeDetail);
                        $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                        $grade = @$gradeArray[0]->grade;
                        $credit=$sys->getCreditHour($name,$semester,$level,$studentProgram); // get credit hour of a course


                        $gradePoint = @$gradeArray[0]->value;
                        $test=Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("year",$year)->get()->toArray();
                        if(empty($test)){
                            $record = new Models\AcademicRecordsModel();
                            $record->indexno = $indexNo;
                            $record->code = $course;
                            $record->sem = $semester;
                            $record->year = $year;
                            $record->credits = $credit;
                            $record->student= $studentID;
                            $record->level = $level;

                            $record->exam = $exam;
                            $record->total = $total;

                            $record->grade = $grade;
                            $record->gpoint =round(( $credit*$gradePoint),2);
                            $record->save();

                            $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));
                            \DB::commit();

                        }
                        $aggie++;
                    }
                }
            }
        }
    }
    public function processResit(SystemController $sys,Request $request) {

        $this->validate($request, [

            'file' => 'required',

            'sem' => 'required',
            'year' => 'required',

            'program' => 'required',
            'level' => 'required',
        ]);
        $valid_exts = array('csv'); // valid extensions
        $file = $request->file('file');
        $path = $request->file('file')->getRealPath();

        $ext = strtolower($file->getClientOriginalExtension());
        $semester = $request->input('sem');
        $year = $request->input('year');

        $arraycc = $sys->getSemYear();
        $yearcc = $arraycc[0]->YEAR;

        $programme = $request->input('program');
        $level = $request->input('level');
        //$credit=$request->input('credit');
        if (in_array($ext, $valid_exts)) {
            $destination = "public/upload";

            $handle = fopen($_FILES['file']['tmp_name'], "r");

            // move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
            $data = array(); // data array


            $row = 0;
            $columns = [];
            $rows = [];
            // first get the headers into an array
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE AND $row==0) {
                $columns = $data;
                $row++;

                // while get the columns start reading the rows too
                while ($file_line = fgetcsv($handle,1000,",","'")){

                    $totalRecords = count($file_line);

                    for ($c = 0; $c <$totalRecords; $c++) {
                        $col[$c] = $file_line[$c];
                    }
                    // we dont need the index no column so we remove it using unset which is the
                    // first element of the header array
                    unset($columns[0]);

                    $aggie = 1;
                    // dd($columns);
                    foreach ($columns as    $name){

//                                $gad=new Models\GadModel();
//                                 $gad->indexno=$col[0];
//                                 $gad->course=$name;
//                                  $gad->grade=$col[$aggie];
//                                  $gad->save();



                        $course=$name;
                        $exam=$col[$aggie];
                        $total=$col[$aggie];
                        $indexNo=$col[0];
                        $studentDb= $indexNo  ;
                        $studentID= $sys->getStudentIDfromIndexno($indexNo);
                        $studentProgram= $programme; //$sys->getStudentprogramfromIndexno($indexNo);
                        $studentYearGroup= $sys->getStudentyeargroupfromIndexno($indexNo);

                        $courseid3=@$sys->getCourseMountedInfo2($course,$semester,$level,$yearcc,$programme);
                        $courseid2= $courseid3[0]->ID;


                        $courseName=@$sys->getCourseCodeByIDArray2($course);

                        $displayCourse=@$courseName[0]->COURSE_NAME;
                        $displayCode=@$courseName[0]->COURSE_CODE;
                        $studentSearch = @$sys->studentSearchByIndexNo($programme); // check if the students in the file tally
                        /*check if the students in the file tally with  students records
                         * this is done so that users don't upload results of students that
                         * are not in the system
                         */



                        $total= round($exam,2);
                        $programmeDetail=$sys->getCourseProgramme2($course);

                        $program=$sys->getProgramArray($programmeDetail);
                        $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                        $grade = @$gradeArray[0]->grade;

                        $credit=$courseid3[0]->COURSE_CREDIT; // get credit hour of a course

                        $gradePoint = @$gradeArray[0]->value;

                        $testfail=Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("grade","!=","F")->where("grade","!=","E")->get()->toArray();

                        $test=Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("grade","!=","F")->where("resit","yes")->get()->toArray();

                        //dd($testfail, $test);

                        if(count($testfail)==0){
                        if(count($test)==0){
                            if($total>0 || $total!=""){
                                $record = new Models\AcademicRecordsModel();
                                $record->indexno = $indexNo;
                                $record->code = $course;
                                $record->sem = $semester;
                                $record->year = $year;
                                $record->credits = $credit;
                                $record->student= $studentID;
                                $record->level = $level;
                                $record->course = $courseid2;
                                $record->programme = $studentProgram;
                                $record->yrgp = $studentYearGroup;
                                $record->exam = $exam;
                                $record->total = $total;
                                $record->resit = "yes";
                                $record->grade = $grade;
                                $record->gpoint =round(( $credit*$gradePoint),2);
                                $record->save();

                                //$checkF=Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("grade","F")->get()->toArray();
                                   // if(count($checkF)==0){
                                    //    $recordf = new Models\AcademicRecordsModel();
                                    //    $recordf->indexno = $indexNo;
                                    //    $recordf->code = $course;
                                    //    $recordf->sem = $semester;
                                    //    $recordf->year = $year;
                                    //    $recordf->credits = $credit;
                                    //    $recordf->student= $studentID;
                                    //    $recordf->level = $level;
                                    //    $recordf->course = $courseid2;
                                    //    $recordf->programme = $studentProgram;
                                    //    $recordf->yrgp = $studentYearGroup;
                                    //    $recordf->exam = "40";
                                    //    $recordf->total = "40";
                                    //    $recordf->resit = "done";
                                    //    $recordf->grade = "F";
                                    //    $recordf->gpoint =round((0),2);
                                    //    $recordf->save();
                                    //    }

                                    $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));
                                    if($total > 49){
                                    Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("grade","F")->where("resit","!=","yes")->update(array("resit"=>"done","resit"=>"done"));
                                     }
                                 \DB::commit();
                            }
                        }
                         }






                        $aggie++;
                    }


                }
            }
        }



        else{
            return redirect('upload/resit')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload only CSV   file!</span> ");
        }
        return redirect('/dashboard')->with("success",  " <span style='font-weight:bold;font-size:13px;'> Marks  successfully uploaded !</span> ");


    }
    public function uploadResit(SystemController $sys,Request $request){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Registrar' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){

            if ($request->isMethod("get")) {



                $programme = $sys->getProgramList();
                // $course = $sys->getCourseList();

                return view('courses.uploadResit')->with('level', $sys->getLevelList())->with('program', $programme)->with('level', $sys->getLevelList())
                    ->with('year', $sys->years());


            }

        }


        else{
            return redirect("/dashboard");
        }
    }
    public function processsUploadLegacy(SystemController $sys,Request $request) {
if(@\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){
        
        $this->validate($request, [

            'file' => 'required',

            'sem' => 'required',
            'year' => 'required',

            'program' => 'required',
            'level' => 'required',
        ]);
        $valid_exts = array('csv'); // valid extensions
        $file = $request->file('file');
        $path = $request->file('file')->getRealPath();

        $ext = strtolower($file->getClientOriginalExtension());
        $semester = $request->input('sem');
        $year = $request->input('year');
        $arraycc = $sys->getSemYear();
        $yearcc = $arraycc[0]->YEAR;

        $programme = $request->input('program');
        //dd($programme);
        $level = $request->input('level');
        //$credit=$request->input('credit');
        if (in_array($ext, $valid_exts)) {
            $destination = "public/upload";

            $handle = fopen($_FILES['file']['tmp_name'], "r");

            //move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
            $data = array(); // data array


            $row = 0;
            $columns = [];
            $rows = [];
            // first get the headers into an array
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE AND $row==0) {
                $columns = $data;
                $row++;

                // while get the columns start reading the rows too
                while ($file_line = fgetcsv($handle,1000,",","'")){

                    $totalRecords = count($file_line);

                    for ($c = 0; $c <$totalRecords; $c++) {
                        $col[$c] = $file_line[$c];
                    }
                    // we dont need the index no column so we remove it using unset which is the
                    // first element of the header array
                    unset($columns[0]);

                    $aggie = 1;
                    //dd($columns);
                    foreach ($columns as    $name){

//                                $gad=new Models\GadModel();
//                                 $gad->indexno=$col[0];
//                                 $gad->course=$name;
//                                  $gad->grade=$col[$aggie];
//                                  $gad->save();



                        $course=$name;
                        $exam=$col[$aggie];
                        $total=$col[$aggie];
                        $indexNo=$col[0];
                        $studentDb= $indexNo  ;
                        $studentID= $sys->getStudentIDfromIndexno($indexNo);
                        $studentProgram= $programme; //$sys->getStudentprogramfromIndexno($indexNo);
                        $studentYearGroup= $sys->getStudentyeargroupfromIndexno($indexNo);


                        $courseName=@$sys->getCourseCodeByIDArray2($course);

                        $courseid3=@$sys->getCourseMountedInfo2($course,$semester,$level,$yearcc,$programme);
                        //dd($course,$semester,$level,$yearcc,$programme);
                        $courseid2= $courseid3[0]->ID;
                         //dd($courseid2);
                        $displayCourse=@$courseName[0]->COURSE_NAME;
                        $displayCode=@$courseName[0]->COURSE_CODE;
                        //////$studentSearch = @$sys->studentSearchByIndexNo($programme); // check if the students in the file tally
                        /*check if the students in the file tally with  students records
                         * this is done so that users don't upload results of students that
                         * are not in the system
                         */

//                                     if (@in_array($studentDb, $studentSearch)) {
//
//
                        $total= round($exam,2);
                        //$programmeDetail=$sys->getCourseProgramme2($course);
                        $programmeDetail=$studentProgram;

                        $program=$sys->getProgramArray($programmeDetail);
                        $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                        $grade = @$gradeArray[0]->grade;
                        $credit=$courseid3[0]->COURSE_CREDIT;//$sys->getMountedCreditHour($name,$semester,$level,$studentProgram);
                        //dd($studentProgram); // get credit hour of a course
                        

                        $gradePoint = @$gradeArray[0]->value;
                        $test=@Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("resit","!=","yes")->get()->toArray();
                        if(count($test)==0){
                            $record = new Models\AcademicRecordsModel();
                            $record->indexno = $indexNo;
                            $record->code = $course;
                            $record->course = $courseid2;
                            $record->sem = $semester;
                            $record->year = $year;
                            $record->credits = $credit;
                            $record->student= $studentID;
                            $record->level = $level;
                            $record->programme = $studentProgram;
                            $record->yrgp = $studentYearGroup;
                            $record->exam = $exam;
                            $record->total = $total;
                            $record->resit = "no";
                            $record->grade = $grade;
                            $record->gpoint =round(( $credit*$gradePoint),2);
                            $record->save();

                            $cgpa= number_format(@(( $credit*$gradePoint)/$credit), 3, '.', ',');
                            $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class,"STATUS"=>'In school'));
                            \DB::commit();

                        }
                        else{

                            Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("code",$course)->where("resit","!=","yes")->update(
                                array(
                                    "indexno" =>$indexNo,
                                    "code"=>$course,
                                    "sem" =>$semester,
                                    "year"=>$year,
                                    "credits"=>$credit,
                                    "student"=>$studentID,
                                    "level"=>$level,
                                    "course"=>$courseid2,
                                    "exam" =>$exam,
                                    "total"=> $total,
                                    "programme" =>$studentProgram,
                                    "yrgp"=> $studentYearGroup,
                                    "resit"=> "no",

                                    "grade" => $grade,
                                    "gpoint" =>round(( $credit*$gradePoint),2),
                                )

                            );
                           $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class,"STATUS"=>'In school'));

                             \DB::commit();
                        }


//dd($newCgpa);


//                               }
//                              else{
//                                  // continue;
//                                     return redirect('upload/legacy')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognized students for $programme .Please upload only  students for  $programme!</span> ");
//                             
//                                 
//                            } 
                        $aggie++;
                    }


                }
            }
        }



        else{
            return redirect('legacy')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload only CSV   file!</span> ");
        }
        return redirect('legacy')->with("success",  " <span style='font-size:13px;'> ".$year." || ".$level." || sem ".$semester." || "."marks  successfully uploaded !</span> ");

        }
    }

    public function uploadGad2(SystemController $sys,Request $request){
        if(@\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){




            $programme = $sys->getProgramList();
            // $course = $sys->getCourseList();

            return view('courses.legacyGrades')->with('level', $sys->getLevelList())->with('program', $programme)->with('level', $sys->getLevelList())
                ->with('year', $sys->years());




        }
        else{
            return redirect("/dashboard");
        }
    }





    public function uploadCourse(SystemController $sys,Request $request){

        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support'||@\Auth::user()->role == 'Support'|| @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {



            return view('courses.uploadCourse');





        }


        else{

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');

        }


    }

    public function registrationInfo(SystemController $sys,Request $request){

        if ( @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin'  || @\Auth::user()->department == 'Rector' || @\Auth::user()->department == 'Finance' || @\Auth::user()->department == 'Tpmid' || @\Auth::user()->department=='Tptop') {
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            if ($request->isMethod("get")) {
                $data=  Models\StudentModel::where("REGISTERED",1)->select("PROGRAMMECODE","LEVEL")->orderBy("PROGRAMMECODE")->orderBy("LEVEL")->groupBy("PROGRAMMECODE")->paginate(500);
                return view('courses.registrationReport')
                    ->with('program', $sys->getProgramList())
                    ->with('year', $sys->years())
                    ->with("data",$data)
                    ->with("sem",$sem)
                    ->with("years",$year);
            } else {

            }
        }
        else{
            return redirect("/dashboard");
        }
    }
    // recover deleted grades
    public function gradeRecovery(SystemController $sys,Request $request){

        if ( @\Auth::user()->role== 'Lecturer' || @\Auth::user()->role== 'HOD' ||  @\Auth::user()->fund== '755991'||  @\Auth::user()->fund== '1201610' || @\Auth::user()->fund== '701088') {

            if ($request->isMethod("get")) {

                return view('courses.recoverGrades')->with('year', $sys->years())
                    ->with("course",$sys->getMountedCourseList())
                    ->with("level",$sys->getLevelList())
                    ->with('program', $sys->getProgramList());


            }

        }
        elseif( @\Auth::user()->department== 'Tpmid' || @\Auth::user()->department== 'Tptop' ){
            return view('courses.recoverGrades')->with('year', $sys->years())
                ->with("course",$sys->getCourseList())
                ->with("level",$sys->getLevelList())
                ->with('program', $sys->getProgramList());

        }
        else{
            return redirect("/dashboard");
        }
    }
    public function ProcessGradeRecovery(SystemController $sys,Request $request){

        $this->validate($request, [

            'level'=>'required',
            'program'=>'required',
            'semester'=>'required',
            'year'=>'required',

        ]);

        $level=$request->input("level");
        $course=$request->input("course");
        $program=$request->input("program");
        $year=$request->input("year");
        $semester=$request->input("semester");

        if($course=="type course name here"){
            $query= Models\DeletedGradesModel::where("level",$level)->where("sem",$semester)
                ->where("year",$year)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                }) ;
        }
        else{
            $query= Models\DeletedGradesModel::where("level",$level)->where("sem",$semester)
                ->where("year",$year)->where("code",$course)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                }) ;

        }
        $data=$query->get();

        foreach($data as $row){
            $result=new Models\AcademicRecordsModel();
            $result->course=$row->course;
            $result->code=$row->code;
            $result->student=$row->student;
            $result->indexno=$row->indexno;
            $result->credits=$row->credits;
            $result->quiz1=$row->quiz1;
            $result->quiz2=$row->quiz2;
            $result->quiz3=$row->quiz3;
            $result->midSem1=$row->midSem1;
            $result->exam=$row->exam;
            $result->total=$row->total;
            $result->grade=$row->grade;
            $result->gpoint=$row->gpoint;
            $result->year=$row->year;
            $result->sem=$row->sem;
            $result->level=$row->level;
            $result->yrgp=$row->yrgp;
            $result->groups=$row->groups;
            $result->lecturer=$row->lecturer;
            $result->resit=$row->resit;
            $result->dateRegistered=$row->dateRegistered;
            $result->createdAt=$row->createdAt;
            $result->updates=$row->updates;
            $result->save();


        }

        $query->delete();






    }

    public function gradeModification(SystemController $sys,Request $request){

        if ( @\Auth::user()->role== 'Lecturer' || @\Auth::user()->role== 'HOD' || @\Auth::user()->fund== '701088') {

            if ($request->isMethod("get")) {

                return view('courses.deleteGrades')->with('year', $sys->years())
                    ->with("course",$sys->getMountedCourseList())
                    ->with("level",$sys->getLevelList())
                    ->with('program', $sys->getProgramList());


            }

        }
        elseif(@\Auth::user()->role=='Admin' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' ||  @\Auth::user()->department== 'Tpmid' || @\Auth::user()->department== 'Tptop'){
            return view('courses.deleteGrades')->with('year', $sys->years())
                ->with("course",$sys->getMountedCourseList())
                ->with("level",$sys->getLevelList())
                ->with('program', $sys->getProgramList());

        }
        else{
            return redirect("/dashboard");
        }
    }

    public function ProcessGradeModification(SystemController $sys,Request $request){

        //dd($request);
        $this->validate($request, [

            'level'=>'required',
            'program'=>'required',
            'semester'=>'required',

            'year'=>'required',

        ]);

        $level=$request->input("level");
        $course=$request->input("course");
        $program=$request->input("program");
        $year=$request->input("year");
        $indexno=$request->input("indexno");
        $semester=$request->input("semester");

        if($course==""){
            $query= Models\AcademicRecordsModel::where("level",$level)->where("sem",$semester)
                ->where("year",$year)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                }) ;
        }
        elseif($course==""){
            $query= Models\AcademicRecordsModel::where("level",$level)->where("sem",$semester)
                ->where("year",$year)->where("code",$course)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                }) ;
            $query->delete();
        }
        elseif($indexno!=""){
            $query= Models\AcademicRecordsModel::where("level",$level)->where("sem",$semester)->where("indexno",$indexno)
                ->where("year",$year)->where("code",$course)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                }) ;
            $query->delete();


        }
        // $data=$query->get();

//                 foreach($data as $row){
//                        $result=new Models\DeletedGradesModel();
//                        $result->course=$row->course;
//                        $result->code=$row->code;
//                        $result->student=$row->student;
//                        $result->indexno=$row->indexno;
//                        $result->credits=$row->credits;
//                        $result->quiz1=$row->quiz1;
//                        $result->quiz2=$row->quiz2;
//                        $result->quiz3=$row->quiz3;
//                        $result->midSem1=$row->midSem1;
//                        $result->exam=$row->exam;
//                        $result->total=$row->total;
//                        $result->grade=$row->grade;
//                        $result->gpoint=$row->gpoint;
//                        $result->year=$row->year;
//                        $result->sem=$row->sem;
//                        $result->level=$row->level;
//                        $result->yrgp=$row->yrgp;
//                        $result->groups=$row->groups;
//                        $result->lecturer=$row->lecturer;
//                        $result->resit=$row->resit;
//                        $result->dateRegistered=$row->dateRegistered;
//                       $result->createdAt=$row->createdAt;
//                      $result->updates=$row->updates;
//                        $result->save();
//                       
//                            
//                        }





        return redirect()->back()->with("success","Grades deleted successfully");
    }


    public function transcript(SystemController $sys,Request $request){

        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support' || @\Auth::user()->role == 'Lecturer' || @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top' || @\Auth::user()->department == 'Rector' || @\Auth::user()->department == 'Tpmid' || @\Auth::user()->department == 'Tptop' || @\Auth::user()->role == 'Admin') {

            if ($request->isMethod("get")) {

                return view('courses.showTranscript');

            }
            else{

                $student=  explode(',',$request->input('q'));
                $student=$student[0];



                $sql=Models\StudentModel::where("INDEXNO",$student)->first();


                if($sql->PAID<=0.85*$sql->BILLS &&  @\Auth::user()->department != 'Tptop'){
                return redirect("/dashboard")->with("error","Check fee payment");
                }
                //    return redirect("/transcript")->with("error","<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
                // }
                // else{

                $array=$sys->getSemYear();
                $sem=$array[0]->SEMESTER;
                $year=$array[0]->YEAR;


                $data=$this->transcriptHeader($sql, $sys)  ;
                $record=$this->generateTranscript($sql->ID,$sys);
                return view("courses.transcript")->with('grade',$record)->with("student",$data);




                //}
            
            //else{
            	
            //}

        }

    }
}
    public function transcriptHeader($student, SystemController $sys) {
        ?>
        <div class="md-card" style="overflow-x: auto;" >

            <div   class="uk-grid" data-uk-grid-margin>

                <table  border="0" valign="top" cellspacing="0" align="left">
                    <tr>
                        <td>
                            <table width="826px" style="margin-left:18px" height="133">
                                <tr>
                        
                                    <td class="uk-text-danger uk-text-left" colspan="3"><blinks>Use Mozilla Firefox or Google Chrome. Contact your HOD or call 0246091283 / 0505284060 for any assistance. </blinks>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align='left'> <img src="<?php echo url('public/assets/img/academic.jpg')?>" style='width: 826px;height: auto;margin-bottom: 10px;'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold"style="padding-right: px;">INDEX NUMBER
                                    </td> 
                                    <td style=""><?php echo $student->INDEXNO;?>
                                    </td>
                                    <td rowspan="5" width="145" align="right">&nbsp;
                                        <img style="width:130px;height: auto;margin-left: 8px;"
                                            <?php
                                                $pic = $student->INDEXNO;
                                            ?>
                                            src='<?php echo url("public/albums/students/$pic.JPG")?>' alt="  Affix student picture here"    />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold" style="">NAME
                                    </td> 
                                    <td><?php echo strtoupper($student->TITLE .' '.  $student->NAME)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold"style="">GENDER</td> 
                                    <td><?php echo strtoupper($student->SEX)?></td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold">PROGRAMME</td> 
                                    <td><?php echo strtoupper($student->program->PROGRAMME)?></td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold" style="">DATE OF ADMISSION</td> 
                                    <td><?php echo strtoupper($student->DATE_ADMITTED)?></td>
                                </tr>
                                <tr>
                                    <td class="uk-text-bold" style="">DATE OF BIRTH</td> 
                                    <td><?PHP echo  $student->DATEOFBIRTH ; ?></td>
                                </tr>
                                <tr>
                                    <td class="uk-text-left" colspan="3">&nbsp;<br/>For HND only. &nbsp;&nbsp;Grade &nbsp;= &nbsp;Value, &nbsp;&nbsp;&nbsp;A+ &nbsp;= &nbsp;5.0, &nbsp;&nbsp;&nbsp;A &nbsp;= &nbsp;4.5, &nbsp;&nbsp;&nbsp;B+ &nbsp;= &nbsp;4.0, &nbsp;&nbsp;&nbsp;B &nbsp;= &nbsp;3.5, &nbsp;&nbsp;&nbsp;C+ &nbsp;= &nbsp;3, &nbsp;&nbsp;&nbsp;C &nbsp;= &nbsp;2.5, &nbsp;&nbsp;&nbsp;D+ &nbsp;= &nbsp;2, &nbsp;&nbsp;&nbsp;D &nbsp;= &nbsp;1.5, &nbsp;&nbsp;&nbsp;F &nbsp;= &nbsp;0, &nbsp;&nbsp;&nbsp;red asterisk means resit
                                    </td>
                                </tr>
                                <tr>
                                    <td class="uk-text-left" colspan="3">&nbsp;
                                    </td>
                                </tr>
                            </table>
            
        
        

        <?php

    }
    public function generateTranscript($sql,  SystemController $sys){
        $programObject=Models\StudentModel::where('ID',$sql)->select("PROGRAMMECODE")->get();
        $program=$programObject[0]->PROGRAMMECODE;

        $records=  Models\AcademicRecordsModel::where([['student','=',$sql],['grade','!=', 'E'],['grade','!=', 'IC'],['grade','!=', 'NC']])->groupBy("year")->groupBy("level")->orderBy("level")->get();



        ?>


        <table width='700px' style="text-align:left; margin-top:-2px; font-size: 16px" height="90" class=""  border="0">
            <tr>

                <td  style=" " align="left">
                    <?php
                    $gpoint=0.0;
                    $totcredit=0;
                    $totgpoint=0.0;
                    $gcredit=0;
                    $b=0.0;
                    $a=0;
                    foreach ($records as $row){
                        for($i=1;$i<3;$i++){
                            $query=  Models\AcademicRecordsModel::where("student",$sql)->where("year",$row->year)->where("sem",$i)->orderby("code")->orderby("resit")->get()->toArray();


                            if(count($query)>0){


                                echo "<div class='uk-text-bold' align='left' style='margin-left:18px'>YEAR : ".$row->year.", ";
                                echo " LEVEL :  " .$row->level.", ";
                                echo " SEMESTER : ".$i." <hr/></div>";

                                ?>

                                <div class="uk-overflow-container">
                                <table style="margin-left:18px"  border="0" width='826px'  class="uk-table uk-table-striped">
                                    <thead >
                                    <tr class="uk-text-bold" style="background-color:#1A337E;color:white;">
                                        <td  width="86">CODE</td>
                                        <td  width="458">COURSE</td>
                                        <td align='center' width="48">CR</td>
                                        <td align='center' width="49">GD</td>
                                        <td align='center' width="70">GP</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    //$program=$student->program->PROGRAMME;

                                    foreach ($query as $rs){


                                    if($rs['grade']!="IC" and $rs['grade']!="E" and $rs['grade']!="NC"){

                                    ?>
                                    <tr>
                                        <td <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>> <?php $object=$sys->getCourseByCodeProgramObject($rs['code'],$program); echo @$object[0]->COURSE_CODE; ?></td>
                                        <td <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>> <?php
                                            if($rs['resit']=="yes"){
                                                echo "<span style='color:red'>* </span>".@$object[0]->COURSE_NAME."<span style='color:red'> *</span>";}else{echo @$object[0]->COURSE_NAME;}?> </td>

                                        <td align='center' <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>><?php  @$gcredit+=@$rs['credits'];   $totcredit+=@$rs['credits'];@$a+=$totcredit; if($rs['credits']){ echo $rs['credits'];} else{echo "IC";};?></td>

                                        <td align='center' <?php // if($rs['grade']=="E" || $rs['grade']=="F"){ echo "style='display:none'";}?>><?php  if($rs['grade']){ echo @$rs['grade'];} else{echo "IC";}?></td>


                                        <td align='center' <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>>
                                            <?php   @$gpoint+=@$rs['gpoint']; @$totgpoint+=@$rs['gpoint'];@$b+=@$totgpoint;if($rs['gpoint']){ echo $rs['gpoint'];} else{echo "0";}  ?></td>



                                        <?php
                                        }
                                        }?>
                                    </tr>
                                    <tr>

                                        <td>&nbsp</td>

                                        <td class="uk-text-bold"><span>GPA</span> <?php echo  number_format(@($gpoint/$gcredit), 3, '.', ',');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>

                                        <td class="uk-text-bold" align='center'><?php echo $gcredit; ?></td>
                                        <td >&nbsp;</td>
                                        <td class="uk-text-bold" align='center'><?php echo $gpoint; ?>&nbsp;</td>
                                    </tr>
                                    <tr>

                                        <td>&nbsp</td>

                                        <td class="uk-text-bold"><span>CGPA</span> <?php echo  number_format(@($totgpoint/$totcredit), 3, '.', ',');
                                        if ($totgpoint/$totcredit > 3.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;First Class';}
                                        elseif($totgpoint/$totcredit > 2.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Second Upper';}
                                        elseif($totgpoint/$totcredit > 1.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Second lower';}
                                        elseif($totgpoint/$totcredit >1.494) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Pass';}
                                        else {echo '&nbsp;&nbsp;&nbsp;&nbsp;Fail';}?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>

                                        <td class="uk-text-bold" align='center'><?php echo   $totcredit; ?></td>
                                        <td >&nbsp;</td>
                                        <td class="uk-text-bold" align='center'><?php echo $totgpoint;   $b="";$a=""; ?>&nbsp;</td>
                                    </tr>

                                    </tbody>

                                    <?php
                                    $gpoint=0.0;
                                    $gcredit=0;
                                    ?>
                                </table>
                            <?php }else{
                                echo "<p class='uk-text-danger'>No results to display</p>";
                                ?><?php }?>
                            <p>&nbsp;</p>
                            </div><?php }  }

                    ?>


            </tr>

        </table>
    </td>
        </tr>

        </table>
        </div></div>

    <?php }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request,SystemController $sys)
    {
        if($request->user()->isSupperAdmin  || @\Auth::user()->department=="top" || @\Auth::user()->role=="Admin" || @\Auth::user()->department=="Rector" || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop"){

            $courses= Models\CourseModel::query() ;
        }
        elseif(@\Auth::user()->role=="HOD" || @\Auth::user()->role=="Support" || @\Auth::user()->role=="Registrar") {
            $courses = Models\CourseModel::where('PROGRAMME', '!=', '')->whereHas('programs', function($q) {
                $q->whereHas('departments', function($q) {
                    $q->whereIn('DEPTCODE', array(@\Auth::user()->department));
                });
            }) ;
        }

        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $courses->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $courses->where("PROGRAMME", $request->input("program", ""));
        }
        if ($request->has('level') && trim($request->input('level')) != "") {
            $courses->where("COURSE_LEVEL", $request->input("level", ""));
        }
        if ($request->has('semester') && trim($request->input('semester')) != "") {
            $courses->where("COURSE_SEMESTER", "=", $request->input("semester", ""));
        }


        $data = $courses->groupBy('COURSE_NAME')->paginate(100);

        $request->flashExcept("_token");


        return view('courses.index')->with("data", $data)->with('level', $sys->getLevelList())
            ->with('program', $sys->getProgramList());

    }
    public function viewMounted(Request $request,SystemController $sys) {
        $hod=@\Auth::user()->fund;

//      if(@\Auth::user()->department=="top"){
//           $courses= Models\MountedCourseModel::query() ;
//      }
//      elseif(@\Auth::user()->role=="Lecturer"){
//          $courses= Models\MountedCourseModel::query()->where('LECTURER',@\Auth::user()->fund) ;
//      }
//
//      else{
//          $courses= Models\MountedCourseModel::query()->where('MOUNTED_BY',$hod) ;
//      }

        if($request->user()->isSupperAdmin  ||  @\Auth::user()->department=="top" || @\Auth::user()->role=="Admin" ||  @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Lecturer" || @\Auth::user()->role=="Support"){

            $courses= Models\MountedCourse2Model::query() ;
        }
        elseif(@\Auth::user()->role=="HOD") {
            $courses =Models\MountedCourse2Model::where('COURSE', '!=', '')->whereHas('courses', function($q) {
                $q->whereHas('programs', function($q) {
                    $q->whereIn('DEPTCODE', array(@\Auth::user()->department));
                });
            }) ;
        }



        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $courses->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $courses->where("PROGRAMME", $request->input("program", ""));
        }
        if ($request->has('level') && trim($request->input('level')) != "") {
            $courses->where("COURSE_LEVEL", $request->input("level", ""));
        }
        if ($request->has('semester') && trim($request->input('semester')) != "") {
            $courses->where("COURSE_SEMESTER", "=", $request->input("semester", ""));
        }
        if ($request->has('year') && trim($request->input('year')) != "") {
            $courses->where("COURSE_YEAR", "=", $request->input("year", ""));
        }


        $data = $courses->paginate(100);

        $request->flashExcept("_token");


        return view('courses.view_mounted')->with("data", $data)
            ->with('program', $sys->getProgramList())
            ->with('level', $sys->getLevelList())
            ->with('year',$sys->years());
    }
    public function viewRegistered(Request $request,SystemController $sys , User $user, Models\AcademicRecordsModel $record) {

        //$this->authorize('update',$record); // in Controllers
        /*if(Gate::allows('updatesss',$record)){
            abort(403,"No authorization");
        }*/
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $person=@\Auth::user()->fund;
        $lecturer=@\Auth::user()->fund;

        // dd($request->user()->isSupperAdmin);
        if(@\Auth::user()->role=='Lecturer' || @\Auth::user()->role=='HOD' ||@\Auth::user()->role=='Dean'){


            /*
             * make sure that only courses mounted for a
             * lecturer is available to him
             */

            $courses= Models\AcademicRecordsModel::query()->where('lecturer', $person) ;


        }
        elseif($request->user()->isSupperAdmin){

            $courses= Models\AcademicRecordsModel::query()->where("year".$year)
                ->where("sem",$sem);

        }
        else{
            //abort(420, "Illegal access detected");
            return response('Unauthorized.', 401);
        }
        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $courses->where('course',   $sys->getCourseByIDCode($request->input("search", "")));

        }

        if ($request->has('level') && trim($request->input('level')) != "") {
            $courses->where("level", $request->input("level", ""));
        }
        if ($request->has('semester') && trim($request->input('semester')) != "") {
            $courses->where("sem", "=", $request->input("semester", ""));
        }
        if ($request->has('year') && trim($request->input('year')) != "") {
            $courses->where("year", "=", $request->input("year", ""));
        }
        $data = $courses->groupby('course')->paginate(100);

        $request->flashExcept("_token");

        foreach ($data as $key => $row) {

            $arr=$sys->getCourseCodeByID($row->code);
            // dd($arr);
            $data[$key]->CODE=$arr;

            $total=$sys->totalRegistered($sem,$year,$row->course,$row->level, $lecturer);
            $data[$key]->REGISTERED=$total;
        }




        return view('courses.registered_courses')->with("data", $data)
            ->with('program', $sys->getProgramList())
            ->with('level', $sys->getLevelList())
            ->with('year',$sys->years());


    }
    public function mountCourse(SystemController $sys) {
        if(@\Auth::user()->role == 'Admin'|| @\Auth::user()->role=='Support' || @\Auth::user()->role=='Registrar'){
            $programme=$sys->getProgramList();

            $course=$sys->getCourseList();
            //$lecturer=$sys->getLectureList();
            $allLectureres=$sys->getLectureList_All();
            // $totalLecturers = array_merge( $lecturer, $allLectureres);
            return view('courses.mount')->with('program', $programme)
                ->with('course', $course)
                ->with('level', $sys->getLevelList())
                ->with('lecturer',$allLectureres);
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }

    public function create(SystemController $sys) {
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->role == 'Admin'){
            $programme=$sys->getProgramList();
            return view('courses.create')->with('level', $sys->getLevelList())->with('programme', $programme);
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function show(Request $request) {

    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->role == 'Admin'){

            $this->validate($request, [
                'name' => 'required',
                'program' => 'required',
                'code' => 'required',
                'level' => 'required',
                'credit' => 'required',
                'semester' => 'required'
            ]);

            $user=@\Auth::user()->id;

            $name = strtoupper($request->input('name'));
            $program = strtoupper($request->input('program'));
            $level =strtoupper( $request->input('level'));
            $semester =strtoupper( $request->input('semester'));
            $credit = strtoupper($request->input('credit'));
            $code = strtoupper($request->input('code'));

            $course = new Models\CourseModel();
            $course->COURSE_NAME = $name;
            $course->COURSE_CREDIT = $credit;
            $course->PROGRAMME = $program;
            $course->COURSE_SEMESTER = $semester;
            $course->COURSE_CODE = $code;
            $course->COURSE_LEVEL = $level;
            $course->USER = $user;


            if ($course->save()) {
                //\DB::commit();
                return redirect("/courses")->with("success", "Following Courses:<span style='font-weight:bold;font-size:13px;'> $name added </span>successfully added! ");
            } else {

                return redirect("/courses")->withErrors("Following Courses N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> $name could not be added </span>could not be added!");
            }

        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function mountCourseStore(Request $request, SystemController $sys) {
        if(@\Auth::user()->role=='Registrar' || @\Auth::user()->role == 'Admin'){
            \DB::beginTransaction();
            try {
                $this->validate($request, [
                    'course' => 'required',
                    'program' => 'required',

                    'level' => 'required',
                    'credit' => 'required',
                    'semester' => 'required',

                    'year' => 'required'
                ]);


                $course = $request->input('course');
                $kojo2 = explode(',', $course);
                $program = $request->input('program');
                $level = $request->input('level');
                $semester = $request->input('semester');
                $credit = $request->input('credit');
                $year = $request->input('year');
                $lecturer = $request->input('lecturer');
                $type = $request->input('type');
                if($request->input('type')==""){
                    $type="Core";
                }
                else{
                    $type = $request->input('type');
                }
                //dd($course);
                //dd($kojo2[1]);
                $hod = @\Auth::user()->fund;
                $courseDetail=$sys->getCourseByCodeCourse($kojo2[0],$kojo2[1]);
                $mountedCourse = new Models\MountedCourseModel();
                $mountedCourse->COURSE = $sys->getCourseByCodeCourse($kojo2[0],$kojo2[1]);
                //dd($courseDetail);
                $mountedCourse->COURSE_CODE = $kojo2[0];
                $mountedCourse->COURSE_CREDIT = $credit;
                $mountedCourse->COURSE_SEMESTER = $semester;
                $mountedCourse->COURSE_LEVEL = $level;
                $mountedCourse->COURSE_TYPE = $type;
                $mountedCourse->PROGRAMME = $program;
                $mountedCourse->LECTURER = $lecturer;
                $mountedCourse->COURSE_YEAR = $year;
                $mountedCourse->MOUNTED_BY = $hod;
                $mountedCourse->save();
                // REPEAT SAME FOR EVENING




                if ($mountedCourse->save()) {
                    \DB::commit();
//                $CourseArray=$sys->getCourseCodeByIDArray($course);
//                $courseName=$CourseArray[0]->COURSE_NAME;
//                $courseCode=$CourseArray[0]->COURSE_CODE;
//                $staffArray=$sys->getLecturer($lecturer);
//                $lecturerName=$staffArray[0]->fullName;
//                $lecturePhone=$staffArray[0]->phone;
//                $lectureStaffID=$staffArray[0]->staffID;
//                $programCode=$sys->getProgram($program);
//                $message="Hi, $lecturerName, you have been assigned $courseName, $courseCode, $programCode, year $level, $year, sem $semester";
//                //dd($message);
                    // $sys->firesms($message, $lecturePhone,$lectureStaffID );
                    return redirect("/mounted_view?&program=$program&level=$level&semester=$semester&year=$year")->with("success", "Course mounted</span>successfully  ");
                } else {

                    return redirect("/mounted_view")->withErrors("Whoops N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> course could not be mounted </span>could not be added!");
                }
            } catch (\Exception $e) {
                \DB::rollback();
            }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function enterMark($course,$code,$year,$sem,$pro, SystemController $sys ,Models\AcademicRecordsModel $record ){
        //$this->authorize('update',$record); // in Controllers
        if(@\Auth::user()->role=='HOD' ||@\Auth::user()->role=='Lecturer' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'  ){
             $array=explode("_",$year);
            $array2 = $sys->getSemYear();

            $year=$array[0]."/".$array[1];

            $lecturer=@\Auth::user()->fund;
            $group=  @explode(',', @\Auth::user()->student_groups);

            $resultOpen=$array2[0]->ENTER_RESULT;
            if($resultOpen==1){
                $mark = Models\AcademicRecordsModel::where('code',$code)

                    ->where('lecturer',$lecturer)
                    ->where('year',$year)
                    ->where('sem',$sem)
                    ->where('programme',$pro)
                    ->where('resit','no')
                    ->where('grade','!=','E')
                    ->orderBy("indexno")
                    //  ->orwhereIn('groups',$group)
                    ->paginate(100);
                $total=count($mark);
                $th=$sys->getCourseCodeByIDArray2($code);
                $courseName=$th[0]->COURSE_NAME;
                return view('courses.markSheet')->with('mark', $mark)
                    ->with('year', $year)
                    ->with('sem', $sem)
                    ->with('mycode', $code)
                    ->with('course', $courseName)
                    ->with('years',$sys->years())
                    ->with('total', $total);
            }
            else{
                abort(434, "{!!<b>Entering of marks has ended contact the Dean of your School</b>!!}");
                redirect("/registered_courses");

            }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');


        }
    }
    public function marksDownloadExcel( $code, SystemController $sys )

    {

        $array=$sys->getSemYear();
        $sem=$array[0]->SEMESTER;
        $year=$array[0]->YEAR;

        $lecturer=@\Auth::user()->fund;

        $data=Models\AcademicRecordsModel::
        join('tpoly_students', 'tpoly_academic_record.indexno', '=', 'tpoly_students.INDEXNO')
            ->where('tpoly_academic_record.code',$code)
            ->where('tpoly_academic_record.lecturer',$lecturer)
            ->where('tpoly_academic_record.year',$year)
            ->where('tpoly_academic_record.sem',$sem)
            ->select('tpoly_students.INDEXNO','tpoly_students.NAME','tpoly_academic_record.quiz1','tpoly_academic_record.quiz2','tpoly_academic_record.midsem1','tpoly_academic_record.exam')
            ->orderBy("tpoly_students.INDEXNO")
            ->get();

        return Excel::create('itsolutionstuff_example', function($excel) use ($data) {

            $excel->sheet('mySheet', function($sheet) use ($data)

            {

                $sheet->fromArray($data);

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

        $kojoSense = 0;
        $array = $sys->getSemYear();
        //$sem = $request->input("sem");
        $year = $request->input("year");
        //$level = $request->input("level");
        $program = $request->input("program");
        //dd($program);
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


        $data = Models\AcademicRecordsModel::where("programme", $program)
            ->where('yrgp',$year)
            ->where('grade','!=','E')     
            ->orderBy("indexno")
            ->orderBy("level")
            ->orderBy("sem")
            ->orderBy("resit")
            ->select('id', 'course', 'code', 'credits', 'student', 'indexno', 'total as mm', 'grade', 'gpoint', 'year', 'sem', \DB::raw('substr(level, 1, 3) as level'), 'yrgp', 'groups', 'lecturer', 'resit', 'dateRegistered', 'createdAt', 'updates', 'programme', \DB::raw('concat(code, indexno)'), 'total as tt', \DB::raw('concat(code, indexno, resit)'), 'total as yes')
            ->get();

            $kojoSen2 = count($data)+7; 
            $kojoSense = count($data)+1;

        

        //dd($kojoSensible);

        return Excel::create($year.'_'.$programme, function ($excel) use ($data,$program,$kojoSen2,$yearcc,$year,$programme,$kojoSense,$dpt3,$fac3,$lectname){

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

           
//});
            });

            $data = Models\StudentModel::where("PROGRAMMECODE",$program)
            ->where("GRADUATING_GROUP",$year)
            ->where("STATUS","In school")
            ->orderBy("INDEXNO")
            ->select('INDEXNO', 'NAME')
            ->get();

            $kojoSense = count($data)+1;


            
                //list of raw score format programs
                if ($program == 'HCE' || $program == 'HCEE' || $program == 'HCEM' || $program == 'HCEME' || $program == 'HID' || $program == 'HEM' ) {

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

                            $sheet->setCellValue('F'.$k.'','=E'.$k.'/D'.$k);
                            $sheet->getStyle('F'.$k.'')->getNumberFormat()->setFormatCode('0.00'); 


                            $sheet->setCellValue('G'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('H'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('I'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',100)');

                            $sheet->setCellValue('J'.$k.'','=I'.$k.'/H'.$k);
                            $sheet->getStyle('J'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('K'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('L'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('M'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('N'.$k.'','=M'.$k.'/L'.$k);
                            $sheet->getStyle('N'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('O'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('P'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('Q'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"})');

                            $sheet->setCellValue('R'.$k.'','=Q'.$k.'/P'.$k);
                            $sheet->getStyle('R'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('S'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('T'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('U'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('V'.$k.'','=U'.$k.'/T'.$k);
                            $sheet->getStyle('V'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('W'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('X'.$k.'','=COUNTIFS(TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('Y'.$k.'','=SUMIFS(TP!G8:TP!G'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('Z'.$k.'','=Y'.$k.'/X'.$k);
                            $sheet->getStyle('Z'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AA'.$k.'','=C'.$k.'+G'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k);
                            //=SUM(SUMIF(F3,">0",F3),SUMIF(J3,">0",J3),SUMIF(N3,">0",N3),SUMIF(R3,">0",R3),SUMIF(V3,">0",V3),SUMIF(Z3,">0",Z3))

                            $sheet->setCellValue('AB'.$k.'','=SUM(SUMIF(F'.$k.',">0",F'.$k.'),SUMIF(J'.$k.',">0",J'.$k.'),SUMIF(N'.$k.',">0",N'.$k.'),SUMIF(R'.$k.',">0",R'.$k.'),SUMIF(V'.$k.',">0",V'.$k.'),SUMIF(Z'.$k.',">0",V'.$k.'))');
                            $sheet->getStyle('AB'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('AC'.$k.'','=AB'.$k.'/AA'.$k);
                            $sheet->getStyle('AC'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            //=IF(AC3>6,"",IF(AC3>=4,"CD",IF(AC3>=3,"CM",IF(AC3>=2,"C",IF(AC3<2,"NC","")))))

                            $sheet->setCellValue('AD'.$k.'','=IF(AC'.$k.'>6,"",IF(AC'.$k.'>=4,"CD",IF(AC'.$k.'>=3,"CM",IF(AC'.$k.'>=2,"C",IF(AC'.$k.'>=0,"NC","")))))');


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
                        'C'     =>  4.7,
                        'D'     =>  4.7,
                        'E'     =>  4.7,
                        'F'     =>  4.7,
                        'G'     =>  4.7,
                        'H'     =>  4.7,
                        'I'     =>  4.7,
                        'J'     =>  4.7,
                        'K'     =>  4.7,
                        'L'     =>  4.7,
                        'M'     =>  4.7,
                        'N'     =>  4.7,
                        'O'     =>  4.7,
                        'P'     =>  4.7,
                        'Q'     =>  4.7,
                        'R'     =>  4.7,
                        'S'     =>  4.7,
                        'T'     =>  4.7,
                        'U'     =>  4.7,
                        'V'     =>  4.7,
                        'W'     =>  4.7,
                        'X'     =>  4.7,
                        'Y'     =>  4.7,
                        'Z'     =>  4.7,
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
                                'size'       => '10'//,
                            //'bold'       =>  true
                                ));
                            });

                            //$sheet->setCellsValue('C2:F'.$kojoSense.'','0');
                            //$sheet->cells('C2:C5', function($cells) {
                            // $cells->setValue('0');
                            //});
                            //$sheet->cells('C2:C5', function($cell) {

                            //manipulate the cell
                            //$cell->setValue('0');
                            //});
                            // $sheet->setCellValue('G5','=SUM(C5:F5)');

                            //=SUMIFS(TP!D1:TP!D25000,TP!F1:TP!F25000,A3,TP!K1:TP!K25000,1,TP!L1:TP!L25000,"100H")
                            //=SUMIFS(TP!G1:TP!G25000,TP!F1:TP!F25000,A3,TP!K1:TP!K25000,1,TP!L1:TP!L25000,"100H")

                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            //$sheet->setCellValue('C'.$k.'','0');
                            //$sheet->setCellValue('D'.$k.'','0');
                            //$sheet->setCellValue('E'.$k.'','0');
                            //$sheet->setCellValue('F'.$k.'','0');
                            $sheet->setCellValue('C'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('D'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            

                            $sheet->setCellValue('E'.$k.'','=D'.$k.'/C'.$k);
                            $sheet->getStyle('E'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('F'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('G'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"100","500"}))');

                            $sheet->setCellValue('H'.$k.'','=G'.$k.'/F'.$k);
                            $sheet->getStyle('H'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('I'.$k.'','=(G'.$k.'+D'.$k.')/(F'.$k.'+C'.$k.')');
                            $sheet->getStyle('I'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('J'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('K'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('L'.$k.'','=K'.$k.'/J'.$k);
                            $sheet->getStyle('L'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('M'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.')');
                            $sheet->getStyle('M'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            
                            $sheet->setCellValue('N'.$k.'','=SUM(SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('O'.$k.'','=SUM(SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',{"200","600"}))');

                            $sheet->setCellValue('P'.$k.'','=O'.$k.'/N'.$k);
                            $sheet->getStyle('P'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('Q'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.')');
                            $sheet->getStyle('Q'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('R'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('S'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',1,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('T'.$k.'','=S'.$k.'/R'.$k);
                            $sheet->getStyle('T'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('U'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.')');
                            $sheet->getStyle('U'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            $sheet->setCellValue('V'.$k.'','=SUMIFS(TP!D8:TP!D'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('W'.$k.'','=SUMIFS(TP!I8:TP!I'.$kojoSen2.',TP!F8:TP!F'.$kojoSen2.',A'.$k.',TP!K8:TP!K'.$kojoSen2.',2,TP!L8:TP!L'.$kojoSen2.',"300")');

                            $sheet->setCellValue('X'.$k.'','=W'.$k.'/V'.$k);
                            $sheet->getStyle('X'.$k.'')->getNumberFormat()->setFormatCode('0.00');

                            //$sheet->setCellValue('Y'.$k.'','=(G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k.')/(F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k.')');

                            $sheet->setCellValue('Y'.$k.'','=F'.$k.'+C'.$k.'+J'.$k.'+N'.$k.'+R'.$k.'+V'.$k);

                            $sheet->setCellValue('Z'.$k.'','=G'.$k.'+D'.$k.'+K'.$k.'+O'.$k.'+S'.$k.'+W'.$k);

                            $sheet->setCellValue('AA'.$k.'','=Z'.$k.'/Y'.$k);
                            $sheet->getStyle('AA'.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            

                            $sheet->setCellValue('AB'.$k.'','=IF(AA'.$k.'>5,"",IF(AA'.$k.'>3.994,"First Class",IF(AA'.$k.'>2.994,"Second Class Upper Division",IF(AA'.$k.'>1.994,"Second Class Lower Division",IF(AA'.$k.'>1.494,"Pass",IF(AA'.$k.'<=1.494,"Fail",""))))))');


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


                            $sheet->cells('A7:AB7', function($cells) {
                            // manipulate the cell
                                ////$cell->setAlignment('center');
                            $cells->setFont(array(
                                'size'       => '10',
                                'bold'       =>  true
                            ));

                            });
                
                

                
                                ///$sheet->setCellValue('B'.$cheat2,$lectname);
                                //$sheet->setCellValue('C'.$cheat2,'___________');
                                //$sheet->setCellValue('E'.$cheat2,'___________');
                                //$sheet->setCellValue('B'.$cheat3,'(Lecturer)');
                                //$sheet->setCellValue('C'.$cheat3,'(Signature)');
                                //$sheet->setCellValue('E'.$cheat3,'(Date)');


                                    //=COUNTIF(H8:H11, "A+")
                                        // $sheet->setCellValue('G'.$k.'','=SUM(C'.$k.':F'.$k.')');

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
}
        //$arraycc = $sys->getSemYear();
        //$yearcc = $arraycc[0]->YEAR;

#selected mounted courses for the academic year
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
$datafuck = Models\AcademicRecordsModel::where("programme", $program)
            ->where('yrgp',$year)
            ->where('grade','!=','E')
            ->orderBy("level")
            ->orderBy("sem")
            ->orderBy("code")
            ->groupBY("code")
            ->select('code','sem', \DB::raw('substr(level, 1, 3) as level'))
            ->get();
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
if ($program == 'HCE' || $program == 'HCEE' || $program == 'HCEM' || $program == 'HCEME' || $program == 'HID' || $program == 'HEM' ) {
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
                            
                            $sheet->setCellValue(''.$alpha.$k.'','=IF('.$alpha.'$7="PA",RSA!$'.$GP.$k_RSA.'/RSA!$'.$GPC.$k_RSA.',IF('.$alpha.'$7="CPA",RSA!$AC'.$k_RSA.',VLOOKUP('.$alpha.'$7&$A'.$k.',TP!$U$8:TP!$V$'.$kojoSen2.',2,FALSE)))');

                            
                            
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
                          if ($e == 2500 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and !empty($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and !empty($fucksem21)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and !empty($fucksem31)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and !empty($fucksem41)) {
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and !empty($fucksem51)) {    
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        if ($e == 1400 and !empty($fucksem61)) {    
                            $k_fuck7++;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7.'',$b.' - ('.$g.') - '.$a); 
                            $g7 = $g7 + $g; 
                        }
                        if ($e == 2400 and !empty($fucksem71)) {    
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
                            @$sheet->setCellValue('C6','Semester 1');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Semester 2');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Semester 3');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Semester 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Semester 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Semester 6');
                            } 
                            if ($preyear > 6) {
                            @$sheet->setCellValue(''.$fucksem61.'6','Semester 7');
                            } 
                            if ($preyear > 7) {
                            @$sheet->setCellValue(''.$fucksem71.'6','Semester 8');
                            } 
                           
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C1'); 

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
                            
                            
                            
                            
                            
                            $sheet->setCellValue(''.$alpha.$k.'','=IF(OR('.$alpha.'$7="GPA",'.$alpha.'$7="CGPA"),(IF('.$alpha.'$7="GPA",CGPA!$'.$GP.$k_RSA.',CGPA!$'.$CGP.$k_RSA.')),VLOOKUP('.$alpha.'$7&$A'.$k.',TP!$U$8:TP!$V$'.$kojoSen2.',2,FALSE))');
                            

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
                          if ($e == 2500 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and ($fucksem21  != 3)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and !empty($fucksem31 != 5)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and ($fucksem21 != 3)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and ($fucksem31 != 5)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and ($fucksem41 != 7)) {
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and ($fucksem51 != 9)) {    
                            $k_fuck6++;
                            @$sheet->setCellValue(''.$fucksem51.$k_fuck6.'',$b.' - ('.$g.') - '.$a); 
                            $g6 = $g6 + $g; 
                        }
                        if ($e == 1400 and ($fucksem61 != 11)) {    
                            $k_fuck7++;
                            @$sheet->setCellValue(''.$fucksem61.$k_fuck7.'',$b.' - ('.$g.') - '.$a); 
                            $g7 = $g7 + $g; 
                        }
                        if ($e == 2400 and ($fucksem71 != 13)) {    
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
                            @$sheet->setCellValue('C6','Semester 1');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Semester 2');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Semester 3');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Semester 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Semester 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Semester 6');
                            } 
                            if ($preyear > 6) {
                            @$sheet->setCellValue(''.$fucksem61.'6','Semester 7');
                            } 
                            if ($preyear > 7) {
                            @$sheet->setCellValue(''.$fucksem71.'6','Semester 8');
                            } 
                           
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C1'); 

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
$datafuck = Models\AcademicRecordsModel::where("programme", $program)
            ->where('yrgp',$year)
            ->where('grade','!=','E')
            ->where('resit','=','yes')
            ->orderBy("level")
            ->orderBy("sem")
            ->orderBy("code")
            ->groupBY("code")
            ->select('code','sem', \DB::raw('substr(level, 1, 3) as level'))
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

                            @$sheet->mergeCells('A6:B6');
                            if ($checkResit1 == 1) {
                                //dd($checkResit1);
                            @$sheet->mergeCells('C6:'.$fucksem1.'6');
                            }
                            if (!empty($fucksem11) and $checkResit2 == 1) {
                              @$sheet->mergeCells(''.$fucksem11.'6:'.$fucksem2.'6');   
                             } 
                            if (!empty($fucksem21) and $checkResit3 == 1) {
                            @$sheet->mergeCells(''.$fucksem21.'6:'.$fucksem3.'6');
                            } 
                            if (!empty($fucksem31) and  $checkResit4 == 1) {
                            @$sheet->mergeCells(''.$fucksem31.'6:'.$fucksem4.'6');
                            } 
                            if (!empty($fucksem41) and $checkResit5 == 1) {
                            @$sheet->mergeCells(''.$fucksem41.'6:'.$fucksem5.'6');
                            } 
                           if (!empty($fucksem51) and $checkResit6 == 1) {
                            @$sheet->mergeCells(''.$fucksem51.'6:'.$fucksem6.'6');
                            }
                             if (!empty($fucksem61) and $checkResit7 == 1) {
                            @$sheet->mergeCells(''.$fucksem61.'6:'.$fucksem7.'6');
                            $board1 = $fucksem61;
                                $board2 = $fucksem7;
                            } 
                            if (!empty($fucksem71) and $checkResit8 == 1) {
                            @$sheet->mergeCells(''.$fucksem71.'6:'.$fucksem8.'6');
                            $board1 = $fucksem71;
                                $board2 = $fucksem8;
                            }   
                           
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

                            

                            $sheet->setFreeze('C1'); 

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


$courseMACS1 = Models\MountedCourseModel::join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->join('users','tpoly_mounted_courses.LECTURER', '=', 'users.fund')->where('tpoly_mounted_courses.COURSE_YEAR', $yearcc)
                ->where('tpoly_mounted_courses.PROGRAMME', $program)
                ->orderBy('tpoly_mounted_courses.COURSE_LEVEL')
                ->orderBy('tpoly_mounted_courses.COURSE_SEMESTER')
                ->orderBy('tpoly_mounted_courses.COURSE_CODE')
                ->groupBY('tpoly_mounted_courses.COURSE_CODE')
                ->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER', 'tpoly_mounted_courses.COURSE_LEVEL', 'users.name', 'tpoly_mounted_courses.COURSE_CREDIT')
                ->get();

               // foreach ($courseMACS1 as $key => $value) {
               //   # code...
                //  $a= $value->course->COURSE_NAME;
               //   $b= $value->course->COURSE_CODE;
               // }
#select all course codes for that year group from their academic records (results)
$datafuck = Models\AcademicRecordsModel::where("programme", $program)
            ->where('yrgp',$year)
            ->where('grade','!=','E')
            ->orderBy("level")
            ->orderBy("sem")
            ->orderBy("code")
            ->groupBY("code")
            ->select('code','sem', \DB::raw('substr(level, 1, 3) as level'))
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
$datafuck = Models\AcademicRecordsModel::where("programme", $program)
            ->where('yrgp',$year)
            ->where('grade','!=','E')
            ->orderBy("level")
            ->orderBy("sem")
            ->orderBy("code")
            ->groupBY("code")
            ->select('code','sem', \DB::raw('substr(level, 1, 3) as level'))
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

            if ($program != 'HCE' && $program != 'HCEE' && $program != 'HCEM' && $program != 'HCEME' && $program != 'HID' && $program != 'HEM' ) {
            #excel for headers
            @$excel->sheet('BOARD', function ($sheet) use ($data,$courseMACS1,$kojoSense,$fuck,$fuckcount,$fuckcountr1,$fuckcountr2,$fuckcountr3,$fuckcountr4,$fuckcountr5,$fuckcountr6,$fuckcountr7,$fuckcountr8,$fucksem1,$fucksem2,$fucksem3,$fucksem4,$fucksem5,$fucksem6,$fucksem7,$fucksem8,$fucksem11,$fucksem21,$fucksem31,$fucksem41,$fucksem51,$fucksem61,$fucksem71,$fucksem81,$explode_fuck,$kojoSen2,$program,$year,$programme,$dpt3,$fac3,$lectname, $preyear) 
                {
                    
            
                        $sheet->setWidth(array(
                        'A'     =>  15,
                        'B'     =>  35,
                        'C'     =>  8,
                        'D'     =>  8,
                        'E'     =>  8
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
                            $valueColumn = 2;
                            $fuckcou = 0;
                            $alpha_last = 'B';
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
                        for($k=2;$k<$kojoSense+1;$k++)
                            {
                            $k_fuck = $k + 2;

                            $kwhy = $k + 6;//MACS!.$alpha.$k
                                                    
                            $sheet->setCellValue(''.$alpha.$k.'','=IF(MACS!'.$alpha.$kwhy.' > 100,"",IF(MACS!'.$alpha.$kwhy.' >= 85," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"A+",IF(MACS!'.$alpha.$kwhy.' >= 80," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"A",IF(MACS!'.$alpha.$kwhy.' >= 75," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"B+",IF(MACS!'.$alpha.$kwhy.' >= 70," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"B",IF(MACS!'.$alpha.$kwhy.' >= 65," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"C+",IF(MACS!'.$alpha.$kwhy.' >= 60," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"C",IF(MACS!'.$alpha.$kwhy.' >= 55," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"D+",IF(MACS!'.$alpha.$kwhy.' >= 50," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"D",IF(MACS!'.$alpha.$kwhy.' >= 5.1," "&" "&MACS!'.$alpha.$kwhy.'&" - "&"F",MACS!'.$alpha.$kwhy.'))))))))))');

                            if ($value == 'GPA' || $value == 'CGPA') {
                                $sheet->getStyle(''.$alpha.$k.'')->getNumberFormat()->setFormatCode('0.00');
                            }

                            $sheet->cell(''.$alpha.$k.'', function($celcenter) {

                                // manipulate the cell
                                $celcenter->setAlignment('center');
                                //$cells->setFont(array(
                                //'size'       => '10'//,
                                //'bold'       =>  true
                        

                            }); 
                        }
                        }
                        
                        
                            #margin to display course codes for semesters
                            @$k_fuck1 = $k_fuck + 0;
                            @$k_fuck2 = $k_fuck + 0;
                            @$k_fuck3 = $k_fuck + 0;
                            @$k_fuck4 = $k_fuck + 0;
                            @$k_fuck5 = $k_fuck + 0;
                            @$k_fuck6 = $k_fuck + 0;

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
                          if ($e == 2500 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1600 and ($fucksem21 != 6)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2600 and ($fucksem31 != 10)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1100) {
                            $k_fuck1++;
                            @$sheet->setCellValue('C'.$k_fuck1.'',$b.' - ('.$g.') - '.$a);
                            $g1 = $g1 + $g; 
                         }
                          if ($e == 2100 and !empty($fucksem11)) {
                            $k_fuck2++;
                            @$sheet->setCellValue(''.$fucksem11.$k_fuck2.'',$b.' - ('.$g.') - '.$a); 
                            $g2 = $g2 + $g; 
                         }
                        if ($e == 1200 and ($fucksem21 !=6)) {
                            $k_fuck3++;
                            @$sheet->setCellValue(''.$fucksem21.$k_fuck3.'',$b.' - ('.$g.') - '.$a);
                            $g3 = $g3 + $g;  
                         }
                         if ($e == 2200 and ($fucksem31 !=10)) {
                            $k_fuck4++;
                            @$sheet->setCellValue(''.$fucksem31.$k_fuck4.'',$b.' - ('.$g.') - '.$a);
                            $g4 = $g4 + $g;  
                         }
                         if ($e == 1300 and ($fucksem41 !=14)) {
                            //dd($fucksem41);
                            $k_fuck5++;
                            @$sheet->setCellValue(''.$fucksem41.$k_fuck5.'',$b.' - ('.$g.') - '.$a);
                            $g5 = $g5 + $g;  
                         }
                         if ($e == 2300 and ($fucksem51 !=18)) {    
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
                            $sheet->setCellValue('N2','ACADEMIC BOARD FORMAT');
                            $sheet->setCellValue('N3',$year.' YEAR GROUP');
                            //$sheet->setCellValue('D5','Course Code :');

                            $sheet->setCellValue('F2',$year);
                            $sheet->setCellValue('F3','');
                            $sheet->setCellValue('F4','');


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
                            @$sheet->setCellValue('C6','Semester 1');
                             if ($preyear > 1) {                             
                            @$sheet->setCellValue(''.$fucksem11.'6','Semester 2');
                            } 
                            if ($preyear > 2) {
                            @$sheet->setCellValue(''.$fucksem21.'6','Semester 3');
                            } 
                            if ($preyear > 3) {
                            @$sheet->setCellValue(''.$fucksem31.'6','Semester 4');
                            } 
                            if ($preyear > 4) {
                            @$sheet->setCellValue(''.$fucksem41.'6','Semester 5');
                            } 
                            if ($preyear > 5) {
                            @$sheet->setCellValue(''.$fucksem51.'6','Semester 6');
                            } 
                            
                                            
                            $sheet->setHeight(array(
                                '1'     =>  22,
                                '2'     =>  22,
                                '3'     =>  22,
                                '4'     =>  22,
                                '5'     =>  22
                                
                            ));

                            

                            $sheet->setFreeze('C1'); 

                            $sheet->cells('C6:'.$alpha.'7', function($celcenter) {

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

//}

        })->download('xlsx');


    }


    public function downloadRegisteredExcel(Request $request, SystemController $sys )

    {

        $this->validate($request, [


            'program' => 'required',
            'sem' => 'required',
           // 'year' => 'required',
            'level' => 'required',
        ]);

        $kojoSense = 0;
        $array = $sys->getSemYear();
        $sem = $request->input("sem");
        $year = $request->input("year");
        $level = $request->input("level");
        $program = $request->input("program");
        $course = $request->input("course");
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
        $courcec = \DB::table('tpoly_courses')->where('COURSE_CODE',$course)->first();
        $courced = $courcec->COURSE_NAME;


        /* $data = Models\AcademicRecordsModel::
         join('tpoly_students', 'tpoly_academic_record.student', '=', 'tpoly_students.ID')
             ->where('tpoly_academic_record.code', $course)
             ->where('tpoly_academic_record.lecturer', $lecturer)
             ->where('tpoly_academic_record.year', $year)
             ->where('tpoly_academic_record.sem', $sem)
             ->select('tpoly_students.INDEXNO', 'tpoly_students.NAME', 'tpoly_academic_record.quiz1', 'tpoly_academic_record.quiz2', 'tpoly_academic_record.midsem1', 'tpoly_academic_record.exam', 'tpoly_academic_record.total')
             ->orderBy("tpoly_students.INDEXNO")
             ->get();*/

        $data = Models\StudentModel::where("PROGRAMMECODE",$program)
            ->where("LEVEL",$level)
            ->where("STATUS","In school")
            ->orderBy("INDEXNO")
            ->select('INDEXNO', 'NAME')
            ->get();

        $kojoSense = count($data)+1;

        return Excel::create($course.'_'.$programme, function ($excel) use ($data,$program,$level,$programme,$kojoSense,$course,$dpt3,$fac3,$courced,$lectname){

            $excel->sheet($program, function ($sheet) use ($data,$kojoSense,$program,$level,$programme,$course,$dpt3,$fac3,$courced,$lectname) {
                $sheet->setWidth(array(
                    'A'     =>  15,
                    'B'     =>  35,
                    'C'     =>  8,
                    'D'     =>  8,
                    'E'     =>  8,
                    'F'     =>  8,
                    'G'     =>  8,
                    'H'     =>  8,
                    'J'     =>  6,
                    'K'     =>  6
                ));

                $sheet->prependRow(1, array('prepended', 'prepended', 'assignment', 'quiz', 'midsem', 'exam', 'total', 'grade'));
                 //$sheet->prependRow(1, array('assignment', 'quiz', 'midsem', 'exam', 'total'));

                $sheet->fromArray($data);$sheet->setBorder('A1:H'.$kojoSense.'', 'thin');
                //$sheet->setCellsValue('C2:F'.$kojoSense.'','0');
                //$sheet->cells('C2:C5', function($cells) {

                // $cells->setValue('0');

//});
                //$sheet->cells('C2:C5', function($cell) {

                //manipulate the cell
                //$cell->setValue('0');
                //});
                // $sheet->setCellValue('G5','=SUM(C5:F5)');

                for($k=2;$k<$kojoSense+1;$k++){
                    //$sheet->setCellValue('C'.$k.'','0');
                    //$sheet->setCellValue('D'.$k.'','0');
                    //$sheet->setCellValue('E'.$k.'','0');
                    //$sheet->setCellValue('F'.$k.'','0');
                    $sheet->setCellValue('G'.$k.'','=IF(SUM(C'.$k.':F'.$k.') > 0,SUM(C'.$k.':F'.$k.'),"")');
                    $sheet->setCellValue('H'.$k.'','=IF(G'.$k.'>100,"",IF(G'.$k.'>=85,"A+",IF(G'.$k.'>=80,"A",IF(G'.$k.'>=75,"B+",IF(G'.$k.'>=70,"B",IF(G'.$k.'>=65,"C+",IF(G'.$k.'>=60,"C",IF(G'.$k.'>=55,"D+",IF(G'.$k.'>=50,"D",IF(G'.$k.'>=1,"F",""))))))))))');


                }

                $cheat = 25+$k;
                $cheat2 = $cheat + 3;
                $cheat3 = $cheat2 + 1;

                $sheet->prependRow(1, array(' '.' '.' '.''
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$courced
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$programme
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$dpt3.' DEPARTMENT'
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' '.$fac3
                ));
                $sheet->prependRow(1, array(' '.' '.' '.' '.' TAKORADI TECHNICAL UNIVERSITY'
                ));

                $sheet->setCellValue('D2','Year :');
                $sheet->setCellValue('D3','Semester :');
                $sheet->setCellValue('D4','Level :');
                $sheet->setCellValue('D5','Course Code :');

                $sheet->setCellValue('F2','2017/2018');
                $sheet->setCellValue('F3','2');
                $sheet->setCellValue('F4',$level);
                $sheet->setCellValue('F5',$course);
                $sheet->setCellValue('J3','STATISTICS');
                $sheet->setCellValue('J4','Max :');
                $sheet->setCellValue('J5','Min :');
                $sheet->setCellValue('J6','Avg :');

                $sheet->setCellValue('J8','A+ :');
                $sheet->setCellValue('J9','A :');
                $sheet->setCellValue('J10','B+ :');
                $sheet->setCellValue('J11','B :');
                $sheet->setCellValue('J12','C+ :');
                $sheet->setCellValue('J13','C :');
                $sheet->setCellValue('J14','D+ :');
                $sheet->setCellValue('J15','D :');
                $sheet->setCellValue('J16','F :');
                $sheet->setCellValue('J17','Sum :');

                $sheet->setCellValue('K4','=MAX(G8:G'.$cheat.')');
                $sheet->setCellValue('K5','=MIN(G8:G'.$cheat.')');
                $sheet->setCellValue('K6','=AVERAGE(G8:G'.$cheat.')');
                $sheet->setCellValue('K8','=COUNTIF(H8:H'.$cheat.',"A+")');
                $sheet->setCellValue('K9','=COUNTIF(H8:H'.$cheat.',"A")');
                $sheet->setCellValue('K10','=COUNTIF(H8:H'.$cheat.',"B+")');
                $sheet->setCellValue('K11','=COUNTIF(H8:H'.$cheat.',"B")');
                $sheet->setCellValue('K12','=COUNTIF(H8:H'.$cheat.',"C+")');
                $sheet->setCellValue('K13','=COUNTIF(H8:H'.$cheat.',"C")');
                $sheet->setCellValue('K14','=COUNTIF(H8:H'.$cheat.',"D+")');
                $sheet->setCellValue('K15','=COUNTIF(H8:H'.$cheat.',"D")');
                $sheet->setCellValue('K16','=COUNTIF(H8:H'.$cheat.',"F")');
                $sheet->setCellValue('K17','=SUM(K8:K16)');
                //$sheet->setCellValue('B'.$cheat2,$lectname);
                //$sheet->setCellValue('C'.$cheat2,'___________');
                //$sheet->setCellValue('E'.$cheat2,'___________');
                //$sheet->setCellValue('B'.$cheat3,'(Lecturer)');
                //$sheet->setCellValue('C'.$cheat3,'(Signature)');
                //$sheet->setCellValue('E'.$cheat3,'(Date)');


                //=COUNTIF(H8:H11, "A+")
               // $sheet->setCellValue('G'.$k.'','=SUM(C'.$k.':F'.$k.')');

                for($lisa=1;$lisa<6;$lisa++){
                    $sheet->mergeCells('A'.$lisa.':B'.$lisa);
                    $sheet->mergeCells('D'.$lisa.':E'.$lisa);
                    $sheet->mergeCells('F'.$lisa.':G'.$lisa);
                    $sheet->mergeCells('J3:K3');
                    
                    //$sheet->cell('A'.$lisa, function($cell) {
                    $sheet->cells('A1:A5', function($cells) {

                    // manipulate the cell
                     ////$cell->setAlignment('center');
                        $cells->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                        ));

                });
                    //$sheet->mergeCells('J2':'K2');
                    $sheet->cells('A1:G6', function($cells) {

                   $cells->setBackground('#ffffff');
                });
                    $sheet->cells('J3:K17', function($cells) {

                   $cells->setBackground('#262626');
                   $cells->setFontColor('#edeff6');
                });

                }

                $sheet->cells('H8:H'.$cheat.'', function($cells) {

                   $cells->setAlignment('right');
                });

                 $sheet->cells('G8:G'.$cheat.'', function($cells) {

                   $cells->setAlignment('right');
                });

                $sheet->cells('K4:K17', function($cells) {

                   $cells->setAlignment('left');
                });
                                
                $sheet->setHeight(array(
                    '1'     =>  22,
                    '2'     =>  22,
                    '3'     =>  22,
                    '4'     =>  22,
                    '5'     =>  22
                    
                ));

                $sheet->cell('J4', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K4', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J6', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K6', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J8', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K8', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J10', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K10', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J12', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K12', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J14', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K14', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J16', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('K16', function($cell) {
                $cell->setBackground('#595959');
                });

                $sheet->cell('J5', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K5', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('J7', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K7', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('J9', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K9', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('J11', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K11', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('J13', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K13', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('J15', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->cell('K15', function($cell) {
                $cell->setBackground('#404040');
                });

                $sheet->setFreeze('A8');            
//});
            });

        })->download('xlsx');


    }
    public function processMark(SystemController $sys,Request $request) {
        // dd($request);
        set_time_limit(3600000);
        ini_set('max_input_vars', '900000');
        $array = $sys->getSemYear();
        $sem =$request->input('sem');
        $year = $request->input('years');
        \Session::put('year', $year);
        \Session::put('sem', $sem);
        $resultOpen = $array[0]->ENTER_RESULT;
        $lecturer= @\Auth::user()->fund;
        $year=\Session::get('year');
        $sem=\Session::get('sem');
        if(empty($sem)){
            $sem = $array[0]->SEMESTER;
        }
        if(empty($year)){
            $year = $array[0]->YEAR;
        }

        if ($resultOpen == 1) {

            //set_time_limit(36000);
            // ini_set('max_input_vars', '9000');
            $host = $_SERVER['HTTP_HOST'];
            $ipAddr = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $studentIndexNo = $sys->getStudentByID($request->input('student'));
            //dd(\Auth::user()->staffID);
            $upper = count($request->input('student')) ;
            $key = $request->input('key');
            $student = $request->input('student');
            $quiz1 = $request->input('quiz1');
            $quiz2 = $request->input('quiz2');
            $quiz3 = $request->input('quiz3');
            $midsem1 = $request->input('midsem1');

            $course = $request->input('course');
            $exam = $request->input('exam');

            $courseArr= $sys->getCourseMountedInfo($course);
            //  dd($request);
            // dd($request->input('counter') );

            $quiz1Old = $request->input('quiz1Old');
            $quiz2Old = $request->input('quiz2Old');
            $quiz3Old = $request->input('quiz3Old');
            $midsem1Old = $request->input('midsemOld');
            $examOld = $request->input('examOld');
            for ($i = 0; $i < $upper; $i++) {
                $keyData = $key[$i];
                $studentData = $student[$i];
                $quiz1Data = $quiz1[$i];
                $quiz2Data = $quiz2[$i];
                $quiz3Data = $quiz3[$i];
                $midsem1Data = $midsem1[$i];
                $examData = $exam[$i];
                // for logging
                $quiz1OldData = $quiz1Old[$i];
                $quiz2OldData = $quiz2Old[$i];
                $quiz3OldData = $quiz3Old[$i];
                $midsem1OldData = $midsem1Old[$i];
                $examOldData = $examOld[$i];
                $fortyPercent = $quiz1Data + $quiz2Data + $quiz3Data + $midsem1Data;
                $examTotal = $examData;
                $total = $fortyPercent + $examTotal;

                $OldfortyPercent = $quiz1OldData + $quiz2OldData + $quiz3OldData + $midsem1OldData;
                $oldExam = $examOldData;
                $oldClassScore = $OldfortyPercent;

                $examLog = new Models\GradeLogModel();
                $examLog->actor = $lecturer;
                $examLog->student = $studentData;
                $examLog->course = $course;
                $examLog->oldClassScore = $oldClassScore;
                $examLog->newClassScore = $fortyPercent;
                $examLog->oldExamScore = $oldExam;
                $examLog->newExamScore = $examTotal;
                $examLog->ip = $ipAddr;
                $examLog->host = $host;
                $examLog->userAgent = $userAgent;
                if ($examLog->save()) {

                    $programme=$sys->getCourseProgrammeMounted($course);
                    // dd($total);
                    $program=$sys->getProgramArray($programme);

                    $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                    $grade = @$gradeArray[0]->grade;

                    //dd($gradeArray );
                    $gradePoint = round(($courseArr[0]->COURSE_CREDIT*@$gradeArray[0]->value),2);
                    $cgpa= number_format(@( $gradePoint/$courseArr[0]->COURSE_CREDIT), 3, '.', ',');
                    $oldCgpa= @Models\StudentModel::where("INDEXNO",$student)->select("CGPA","CLASS")->first();

                   $newCgpa=@$sys->getCGPA($student);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$student)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));

                    Models\AcademicRecordsModel::where("id", $keyData)->where('lecturer', $lecturer)->update(array("quiz1" => $quiz1Data, "quiz2" => $quiz2Data, "quiz3" => $quiz3Data, "midSem1" => $midsem1Data, "exam" => $examTotal, "total" => $total, "lecturer" => $lecturer, 'grade' => $grade, 'gpoint' => $gradePoint));


                }
            }
            return redirect()->back();
        }
    }
    public function storeMark($course,$code, SystemController $sys,Request $request){

        if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Lecturer' || @\Auth::user()->department == 'Tpmid') {
            $array = $sys->getSemYear();
            $sem =$request->input('sem');
            $year = $request->input('years');
            \Session::put('year', $year);
            \Session::put('sem', $sem);
            $resultOpen = $array[0]->ENTER_RESULT;
            $lecturer= @\Auth::user()->fund;
            $year=\Session::get('year');
            $sem=\Session::get('sem');
            if(empty($sem)){
                $sem = $array[0]->SEMESTER;
            }
            if(empty($year)){
                $year = $array[0]->YEAR;
            }


            $mark = @Models\AcademicRecordsModels::query()->where("code", $course)->where('lecturer', $lecturer);

            if ($request->has('years') && trim($request->input('years')) != "") {
                $mark->where("year", "=", $request->input("years", ""));
            }
            if ($request->has('sem') && trim($request->input('sem')) != "") {
                $mark->where("sem", "=", $request->input("sem", ""));
            }
            $request->flashExcept("_token");
            $total = @count($mark->get());
            // dd($mark);
            $courseName = @$sys->getCourse($courseArr[0]->COURSE);
            $data=$mark->paginate(70);

            return view('courses.markSheet')->with('mark', $data)
                ->with('year', $year)
                ->with('sem', $sem)
                ->with('years', $sys->years())
                ->with('mycode', $code)
                ->with('course', $courseName)
                ->with('total', $total);}


    }
    public function attendanceSheet(Request $request,SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Support' || @\Auth::user()->department=='Rector' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Planning' || @\Auth::user()->department=='Tptop'){
            if ($request->isMethod("get")) {
                $course=$sys->getProgramList();//original is mounted course list

                return view('courses.attendanceSheet')
                    ->with('courses',$course)->with('year',$sys->years())->with('level', $sys->getLevelList());
            }
            else{

                $semester = $request->input('semester');
                $year = $request->input('year');
                $course =  $request->input('course') ;
                $level = $request->input('level');

                $mark = Models\StudentModel::where("PROGRAMMECODE", $course)->where('STATUS','In school')->where('LEVEL',$level)->orderBy("INDEXNO")->paginate(500);
                // dd($mark);
               // $courseArr= $sys->getCourseMountedInfo($course);
                // dd($courseArr);
                //$courseDb= $courseArr[0]->ID;
                //$courseCreditDb= $courseArr[0]->COURSE_CREDIT;
                //$courseLecturerDb= $courseArr[0]->LECTURER;
                //$courseName=@$sys->getCourseCodeByIDArray($courseArr[0]->COURSE);
                //$displayCourse=$courseName[0]->COURSE_NAME;
                //$displayCode=$courseName[0]->COURSE_CODE;
                \Session::put('year', $year);
                $url = url('printAttendance/'.$course.'/course/'.$level.'/level/');

                $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                $request->session()->flash("success",
                    "    $print_window");
                return redirect("/attendanceSheet");

                // return view('courses.printAttendance')->with('mark', $mark)
                //     ->with('year', $year)
                //     ->with('sem', $semester)
                //     ->with('course', $displayCourse)
                //     ->with('code', $displayCode);


            }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function printAttendance(Request $request,$course,$level) {
        $year=\Session::get('year');
        $mark = Models\StudentModel::where("PROGRAMMECODE", $course)->where('STATUS','In school')->where('LEVEL',$level)->orderBy("INDEXNO")->paginate(500);

        return view('courses.printAttendance')->with('mark', $mark)
            ->with('year', $year)
           // ->with('sem', $semester)
            ->with('course', $course);
           // ->with('code', $code);


    }
    public function showFileUpload(SystemController $sys){
        if(@\Auth::user()->department=='Tptop' || @\Auth::user()->role=='Lecturer'){
            $programme=$sys->getProgramList();
            $course=$sys->getMountedCourseList3();

            return view('courses.markUpload')->with('programme', $programme)
                ->with('courses',$course)->with('level', $sys->getLevelList())->with('year',$sys->years22());
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function showFileUploadRegistered(SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->role=='Support' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer'){
            $programme=$sys->getProgramList5();
            $course=$sys->getMountedCourseList3();

            return view('courses.downloadRegistered')->with('programme', $programme)->with('courses',$course)
                ->with('level', $sys->getLevelList())->with('year',$sys->years());
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }

    public function showFileUploadResults(SystemController $sys){
        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Admin' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Lecturer'){
            $programme=$sys->getProgramList();
            $course=$sys->getMountedCourseList3();

            return view('courses.downloadResults')->with('programme', $programme)->with('courses',$course)
                ->with('level', $sys->getLevelList())->with('year',$sys->years());
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    /*
     * Uploading old academic records here
     * file format Excel
     */
    public function uploadLegacy(Request $request, SystemController $sys){

        if(@\Auth::user()->role=='HOD' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='Dean' || @\Auth::user()->role=='Support' || @\Auth::user()->role=='Registrar' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){
            if ($request->isMethod("get")) {

                $programme = $sys->getProgramList();
                $course = $sys->getCourseList();

                return view('courses.legacyGrades')->with('level', $sys->getLevelList())->with('program', $programme)->with('level', $sys->getLevelList())
                    ->with('course', $course)->with('year', $sys->years());
            }
            else{
                $this->validate($request, [

                    'file' => 'required',
                    'course' => 'required',
                    'sem' => 'required',
                    'year' => 'required',
                    'credit' => 'required',
                    'program' => 'required',
                    'level' => 'required',
                ]);



                $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
                $file = $request->file('file');
                $path = $request->file('file')->getRealPath();

                $ext = strtolower($file->getClientOriginalExtension());

                $semester = $request->input('sem');
                $year = $request->input('year');
                $course =  $request->input('course') ;
                $programme = $request->input('program');
                $level = $request->input('level');
                $credit=$request->input('credit');
                //$studentIndexNo = $sys->getStudentIDfromIndexno($request->input('student'));


                if (in_array($ext, $valid_exts)) {

                    $data = Excel::load($path, function($reader) {

                    })->get();
                    if (!empty($data) && $data->count()) {


                        foreach ($data as $key => $value) {

                            $totalRecords = count($data);



                            $studentID= $sys->getStudentIDfromIndexno($value->indexno);
                            $studentDb= $value->indexno  ;

                            //$courseArr= $sys->getCourseMountedInfo($course);
                            // dd($courseArr);
                            //$courseDb= $courseArr[0]->ID;
                            //$courseCreditDb= $courseArr[0]->COURSE_CREDIT;
                            //$courseLecturerDb= $courseArr[0]->LECTURER;
                            $courseName=$sys->getCourseCodeByIDArray($course);
                            $displayCourse=$courseName[0]->COURSE_NAME;
                            $displayCode=$courseName[0]->COURSE_CODE;
                            $studentSearch = $sys->studentSearchByIndexNo($programme); // check if the students in the file tally with registered students
                            //dd($studentDb);
                            if (@in_array($studentDb, $studentSearch)) {
                                $indexNo=$value->indexno;
                                $quiz1=$value->quiz1;
                                $quiz2=$value->quiz2;
                                $midsem=$value->midsem1;
                                $exam=$value->exam;
                                $total= round(($quiz2+$quiz1+$midsem+$exam),2);
                                $programmeDetail=$sys->getCourseProgramme($course);

                                $program=$sys->getProgramArray($programmeDetail);
                                $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                                $grade = @$gradeArray[0]->grade;

                                // dd($gradeArray );
                                $gradePoint = @$gradeArray[0]->value;
                                $test=Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("course",$course)->where("resit","!=","yes")->get()->toArray();
                                if(count($test)==0){
                                    $record = new Models\AcademicRecordsModel();
                                    $record->indexno = $indexNo;
                                    $record->course = $course;
                                    $record->sem = $semester;
                                    $record->year = $year;
                                    $record->credits = $credit;
                                    $record->student= $studentID;
                                    $record->level = $level;
                                    $record->quiz1 = $quiz1;
                                    $record->quiz2 = $quiz2;
                                    $record->quiz3 = 0;
                                    $record->resit = "no";
                                    $record->midSem1 = $midsem;
                                    $record->exam = $exam;
                                    $record->total = $total;
                                    $record->lecturer = @\Auth::user()->fund;
                                    $record->grade = $grade;
                                    $record->gpoint =round(( $credit*$gradePoint),2);
                                    $record->save();

                                    //$cgpa= number_format(@(( $credit*$gradePoint)/$credit), 2, '.', ',');
                                    //$oldCgpa= @Models\StudentModel::where("INDEXNO",$indexNo)->select("CGPA","CLASS")->first();

                                    $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));
                                    \DB::commit();

                                }
                                else{
                                    Models\AcademicRecordsModel::where("indexno",$indexNo)->where("level",$level)->where("sem",$semester)->where("course",$course)->where("resit","!=","yes")->update(
                                        array(
                                            "indexno" =>$indexNo,
                                            "course"=>$course,
                                            "sem" =>$semester,
                                            "year"=>$year,
                                            "credits"=>$credit,
                                            "student"=>$studentID,
                                            "level"=>$level,
                                            "quiz1"=>$quiz1,
                                            "quiz2" =>$quiz2,
                                            "quiz3"=>0,
                                            "midSem1"=>$midsem,
                                            "resit"=>"no",
                                            "exam" =>$exam,
                                            "total"=> $total,
                                            "lecturer"=>@\Auth::user()->fund,
                                            "grade" => $grade,
                                            "gpoint" =>round(( $credit*$gradePoint),2),
                                        )

                                    );
                                    $newCgpa=@$sys->getCGPA($indexNo);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$indexNo)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));

                                    \DB::commit();
                                }



                            } else {
                                return redirect('upload/legacy')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognized students for $programme .Please upload only  students for  $programme!</span> ");


                            }
                        }


                        return redirect('/dashboard')->with("success",  " <span style='font-weight:bold;font-size:13px;'> $totalRecords Marks  successfully uploaded !</span> ");


                    } else {
                        return redirect('upload/legacy')->with("error", " <span style='font-weight:bold;font-size:13px;'>File is empty</span> ");

                    }
                } else {
                    return redirect('upload/legacy')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload only Excel file!</span> ");

                }






            }

        }
    }
    public function uploadMarks(Request $request, SystemController $sys){
            \Config::set('excel.import.startRow', 7);
        $this->validate($request, [

            'file' => 'required',
            'course' => 'required',
            'sem' => 'required',
            'year' => 'required',
            'level' => 'required',
        ]);
        // dd($request);
        if(@\Auth::user()->department=='Tptop' || @\Auth::user()->role=='Lecturer'){
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            $resultOpen = $array[0]->ENTER_RESULT;
            if ($resultOpen == 1) {


                $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
                $file = $request->file('file');
                $path = $request->file('file')->getRealPath();

                $ext = strtolower($file->getClientOriginalExtension());

                $semester = $request->input('sem');
                $year1 = $request->input('year');
                $course =  $request->input('course') ;
                //$programme = $request->input('program');
                $level = $request->input('level');
                $studentIndexNo = $sys->getStudentIDfromIndexno($request->input('student'));

                $leecount = 0;

                if (in_array($ext, $valid_exts)) {

                    $data = Excel::load($path, function($reader) {
                      // $reader->setHeaderRow(7); 
                    })
                    ->get();
                    //  dd($data);
                                        if (!empty($data) && $data->count()) {

                            $essien = 0;

                        foreach ($data as $key => $value) {

                            $totalRecords = count($data);
                            $essien = $essien + 1;

                            $studentProgram= $sys->getStudentprogramfromIndexno($value->indexno);
                            $studentYearGroup= $sys->getStudentyeargroupfromIndexno($value->indexno);



                            $studentId= $sys->getStudentIDfromIndexno($value->indexno);
                            //print_r($value);

                            $studentDb=$value->indexno  ;
                            // dd($studentDb);
                            $courseArr= @$sys->getCourseMountedInfo2($course,$semester,$level,$year1,$studentProgram);
                            // dd($courseArr);
                            $courseDb= @$courseArr[0]->ID;
                            $courseCreditDb= @$courseArr[0]->COURSE_CREDIT;
                            $courseLecturerDb= @\Auth::user()->fund;
                            $courseName=@$sys->getCourseCodeByIDArray($courseArr[0]->COURSE);
                            $displayCourse=@$courseName[0]->COURSE_NAME;
                            $displayCode=@$courseName[0]->COURSE_CODE;
                            $studentSearch = @$sys->studentSearchByCode($year,$semester,$courseDb,$studentDb); // check if the students in the file tally with registered students
                            //dd($studentDb);
//                        if (@in_array($studentDb, $studentSearch)) {
                            $indexNo=@$value->INDEXNO;
                            $quiz1=@$value->assignment;
                            $quiz2=@$value->quiz;
                            $midsem=@$value->midsem;
                            $exam=@$value->exam;
                            $total= @round(($quiz2+$quiz1+$midsem+$exam),2);
                            $programmeDetail=$sys->getCourseProgrammeMounted($displayCode);
                            //$creditHour=$sys->getCourseMountedCredit($displayCode);
                            $program=$sys->getProgramArray($programmeDetail);
                            //dd($program);
                            $gradeArray = @$sys->getGrade($total, $program[0]->GRADING_SYSTEM);
                            $grade = @$gradeArray[0]->grade;

                            // dd($gradeArray );
                            $gradePoint =round(( @$gradeArray[0]->value * @$courseArr[0]->COURSE_CREDIT),2);
                            //$cgpa= number_format(@(( $gradePoint)/@$courseArr[0]->COURSE_CREDIT), 2, '.', ',');
                            //  $oldCgpa= @Models\StudentModel::where("INDEXNO",$studentDb)->select("CGPA","CLASS")->first();
                            // $newCgpa=@$cgpa+$oldCgpa->CGPA;
                            //$class=@$sys->getClass($newCgpa);
                            //Models\StudentModel::where("INDEXNO",$studentDb)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));

                            if ($total > 0) {
                              
                              $leecount++;  # code...
                            

                            $checker=Models\AcademicRecordsModel::where("indexno", $studentDb)->where("code", $course)->where("sem",$semester)->where("level",$level)->where("resit","!=","yes")->first();

                            if(@count($checker)==0 || @count($checker)==''){

                                $record=new Models\AcademicRecordsModel();
                                $record->indexno=$studentDb;
                                $record->student=$studentId;
                                $record->credits=$courseCreditDb;
                                $record->code=$course;
                                $record->sem=$semester;
                                $record->year=$year1;
                                $record->quiz1=$quiz1;
                                $record->quiz2=$quiz2;
                                $record->midSem1=$midsem;
                                $record->exam=$exam;
                                $record->total=$total;
                                $record->lecturer=$courseLecturerDb;
                                $record->grade=$grade;
                                $record->gpoint=$gradePoint;
                                $record->level=$level;
                                $record->resit="no";
                                $record->course=$courseDb;
                                $record->programme = $studentProgram;
                                $record->yrgp = $studentYearGroup;
                                $record->save();


                                    $newCgpa=@$sys->getCGPA($studentDb);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$studentDb)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));


                                
                                //dd($courseLecturerDb);
                            }
                            else{

                                Models\AcademicRecordsModel::where("indexno", $studentDb)->where("code", $course)->where("sem",$semester)->where("level",$level)->where("resit","!=","yes")->update(array("quiz1" => $quiz1, "quiz2" => $quiz2, "level" => $level, "programme" => $studentProgram, "yrgp" => $studentYearGroup, "student" => $studentId, "quiz3" =>0, "midSem1" => $midsem, "exam" => $exam, "total" => $total, "lecturer" =>$courseLecturerDb,'grade' => $grade,'course' => $courseDb, 'gpoint' => $gradePoint, 'resit' => "no"));

                                    $newCgpa=@$sys->getCGPA($studentDb);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$studentDb)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class));

                                


                            }


                        }
                             //else {
//                                return redirect('/upload/marks')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognized students for $displayCourse - $displayCode.please upload only registered students for  $displayCourse - $displayCode  as downloaded from the system!</span> ");
//
//
//                            }
                        }


                        return redirect('/upload_marks')->with("success",  " <span style='font-weight:bold;font-size:13px;'> $leecount Marks  successfully uploaded for  $course</span> ");


                    } else {
                        return redirect('/upload_marks')->with("success", " <span style='font-weight:bold;font-size:13px;'>There seems to be an issue. Please click on View/Edit Results to verify uploaded results</span> ");

                    }
                } else {
                    return redirect('/upload_marks')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload only Excel file!</span> ");

                }




            }
            else{
                throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
            }
        }
        else{

            redirect("/dashboard")->with('error','Entering of marks has ended contact the Dean of your School');

        }
    }
    public function batchRegistration(Request $request,SystemController $sys){

        if (@\Auth::user()->department == 'top' || @\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {
            if ($request->isMethod("get")) {

                return view('courses.batchRegister')->with('year', $sys->years())
                    ->with("course",$sys->getMountedCourseList())
                    ->with("level",$sys->getLevelList())
                    ->with('program', $sys->getProgramList());


            }
            else{

            }
        }
        else{
            return redirect("/dashboard");
        }
    }
    public function processBatchRegistration(Request $request,SystemController $sys){
        $this->validate($request, [

            'program' => 'required',
        ]);
        //dd($request);
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $status = $array[0]->STATUS;
        if ($status == 1) {

            // $policy=$sys->getRegiistrationProtocol($student);
            $level=$request->input("level");
            $program=$request->input("program");
            \DB::beginTransaction();
            try {
                if(!empty($level)){
                    $query=  Models\StudentModel::where("LEVEL",$level)->where("PROGRAMMECODE",$program)->get();
                    $courses= Models\MountedCourseModel::where("COURSE_LEVEL",$level)->where("PROGRAMME",$program)->where("COURSE_YEAR",$year)->where("COURSE_SEMESTER",$sem)->get();

                    foreach($query as $row){
                        $studentID=$sys->getStudentIDfromIndexno($row->INDEXNO);
                        $indexno=$row->INDEXNO;
                        $totalHours=0;

                        foreach($courses as $data){

                            $type=$data->COURSE_TYPE;

                            $level=$data->COURSE_LEVEL;
                            $credit=$data->COURSE_CREDIT;
                            $lecturer=$data->LECTURER;
                            $code=$data->COURSE_CODE;
                            $course=$data->ID;
                            $totalHours+=$credit;


                            $queryModel=new Models\AcademicRecordsModel();
                            $queryModel->course=$course;
                            $queryModel->code=$code;
                            $queryModel->indexno=$indexno;
                            $queryModel->credits=$credit;
                            $queryModel->student=$studentID;
                            $queryModel->yrgp=$row->GRADUATING_GROUP;
                            $queryModel->year=$year;
                            $queryModel->sem=$sem;
                            $queryModel->level=$level;
                            $queryModel->lecturer=$lecturer;
                            $queryModel->dateRegistered=\date('Y-m-d H:i:s');
                            $queryModel->save();
                            // \DB::commit();
                            $oldHours = Models\StudentModel::where("INDEXNO", $indexno)->first();
                            $durationCredit = $sys->getProgrammeMinCredit(@$oldHours->PROGRAMMECODE);

                            $newHours = @$oldHours->TOTAL_CREDIT_DONE + $totalHours;

                            $leftHours = $durationCredit - $newHours;

                            Models\StudentModel::where('INDEXNO', $indexno)->update(array('TOTAL_CREDIT_DONE' => $newHours, 'CREDIT_LEFT_COMPLETE' => $leftHours, 'REGISTERED' => '1','INDEXNO'=>$indexno,'STATUS'=>'In School'));
                            \DB::commit();
                        }
                    }
                }
                else{
                    $query=  Models\StudentModel::where("PROGRAMMECODE",$program)->where("BILL_OWING","<=","500")->get();
                    $courses= Models\MountedCourseModel::where("PROGRAMME",$program)->where("COURSE_YEAR",$year)->where("COURSE_SEMESTER",$sem)->get();

                    foreach($query as $row){
                        $studentID=$sys->getStudentIDfromIndexno($row->INDEXNO);
                        $indexno=$row->INDEXNO;
                        $totalHours=0;
                        foreach($courses as $data){

                            $type=$data->COURSE_TYPE;

                            $level=$data->COURSE_LEVEL;
                            $credit=$data->COURSE_CREDIT;
                            $lecturer=$data->LECTURER;
                            $code=$data->COURSE_CODE;
                            $course=$data->ID;
                            $totalHours+=$credit;
                            // overwrite registered courses for the sem and the year
                            @Models\AcademicRecordsModel::query()->where('indexno', $indexno)
                                ->where('year', $year)
                                ->where('sem', $sem)
                                ->delete() ;

                            $queryModel=new Models\AcademicRecordsModel();
                            $queryModel->course=$course;
                            $queryModel->code=$code;
                            $queryModel->indexno=$indexno;
                            $queryModel->credits=$credit;
                            $queryModel->student=$studentID;
                            $queryModel->yrgp=$row->GRADUATING_GROUP;
                            $queryModel->year=$year;
                            $queryModel->sem=$sem;
                            $queryModel->level=$level;
                            $queryModel->lecturer=$lecturer;
                            $queryModel->dateRegistered=\date('Y-m-d H:i:s');
                            $queryModel->save();
                            //   \DB::commit();
                            $oldHours = Models\StudentModel::where("INDEXNO", $indexno)->first();
                            $durationCredit = $sys->getProgrammeMinCredit(@$oldHours->PROGRAMMECODE);

                            $newHours = @$oldHours->TOTAL_CREDIT_DONE + $totalHours;

                            $leftHours = $durationCredit - $newHours;

                            Models\StudentModel::where('INDEXNO', $indexno)->update(array('TOTAL_CREDIT_DONE' => $newHours, 'CREDIT_LEFT_COMPLETE' => $leftHours, 'REGISTERED' => '1','INDEXNO'=>$indexno,'STATUS'=>'In School'));
                            \DB::commit();
                        }
                    }
                }
            } catch (\Exception $e) {
                \DB::rollback();
            }
            //return redirect('/courses')->with("success",  " <span style='font-weight:bold;font-size:13px;'>Courses registered successfully</span> ");

        }
        else{
            return redirect("/dashboard")->with("error","Registration has been closed");
        }
    }
    // show form for edit resource
    public function edit(Request $request,$id,SystemController $sys){
        if (@\Auth::user()->department == 'top' || @\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Support' || @\Auth::user()->role == 'Admin' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop') {
            if ($request->isMethod("get")) {

                $course = Models\CourseModel::where("ID", $id)->firstOrFail();
                $program = $sys->getProgramList2();
                return view('courses.edit')
                    ->with("program", $program)
                    ->with('data', $course);
            } else {
                $this->validate($request, [


                    'program' => 'required',
                    'name' => 'required',
                    'code' => 'required',
                ]);
                $name=$request->input("name");
                $code=$request->input("code");
                $program=$request->input("program");
                // dd($program);
                \DB::beginTransaction();
                try {

                    $query = @Models\CourseModel::where("ID", $id)->update(array("COURSE_NAME" => $name, "COURSE_CODE" => $code, "PROGRAMME" => $program));
                    \DB::commit();
                    if($query){
                        @Models\MountedCourseModel::where("COURSE",$id)->update(array("COURSE_CODE"=>$code,"PROGRAMME"=>$program));
                        \DB::commit();
                        return redirect('/courses')->with("success",  " <span style='font-weight:bold;font-size:13px;'> $name updated successfully</span> ");

                    }

                } catch (\Exception $e) {
                    \DB::rollback();
                }
            }
        } else {
            // throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');

            return redirect("/dashboard");
        }
    }

    public function update(Request $request, $id){

    }
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request,   SystemController $sys, Models\CourseModel $course)
    {
        //dd($request->input("id"));
        if(@\Auth::user()->role=='HOD' ||  @\Auth::user()->role=='Support'||  @\Auth::user()->role=='Admin' ||  @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){
            $hod=@\Auth::user()->id;
            $array=$sys->getSemYear();
            $sem=$array[0]->SEMESTER;
            $year=$array[0]->YEAR;


            $query= Models\MountedCourseModel::where('COURSE',$request->input("id"))
                ->where('COURSE_YEAR',$year)
                ->where('COURSE_SEMESTER',$sem)
                ->first();

            if($query==""){

                $query1= Models\CourseModel::where('ID',$request->input("id"))->where("USER",$hod)->delete();

                // \DB::commit();



                return redirect("/courses")->with("success","<span style='font-weight:bold;font-size:13px;'> Course  successfully deleted!</span> ");



            }
            else{
                return redirect("/courses")->with("error","<span style='font-weight:bold;font-size:13px;'>Whoops!! you cannot delete a mounted course</span> ");

            }

        }
        else {
            abort(434, "{!!<b>Unauthorize Access detected</b>!!}");
            redirect("/dashboard");
        }

    }
    // delete mounted courses
    public function destroy_mounted(Request $request,   SystemController $sys, Models\CourseModel $course)
    {
        if(@\Auth::user()->role=='HOD' ||  @\Auth::user()->role=='Support'||  @\Auth::user()->role=='Admin' ||  @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tpmid' || @\Auth::user()->department=='Tptop'){

            $array=$sys->getSemYear();
            $sem=$array[0]->SEMESTER;
            $year=$array[0]->YEAR;

            \DB::beginTransaction();
            try {


                Models\MountedCourseModel::where('ID',$request->input("id"))->delete();


                \DB::commit();
                return redirect("/mounted_view")->with("success","<span style='font-weight:bold;font-size:13px;'> Course  successfully deleted!</span> ");


            }catch (\Exception $e) {
                \DB::rollback();
            }
        }
        else {
            abort(434, "{!!<b>Unauthorize Access detected</b>!!}");
            redirect("/dashboard");
        }

    }
    public function courseDownloadExcel($type)

    {


        $data = Models\CourseModel::select('COURSE_CODE','COURSE_NAME','COURSE_LEVEL','COURSE_CREDIT','COURSE_SEMESTER','PROGRAMME')->take(5)->get()->toArray();

        return Excel::create('courses_example', function($excel) use ($data) {

            $excel->sheet('mySheet', function($sheet) use ($data)

            {

                $sheet->fromArray($data);

            });

        })->download($type);

    }

    // naptex broadsheet view
    public function naptexBroadsheet(Request $request, SystemController $sys){
        return view('courses.napbtexBroadsheet')->with('year', $sys->years())
            ->with('level', $sys->getLevelList())
            ->with("program", $sys->getProgramList());



    }
    // naptex broadsheet view
    public function processNaptexBroadsheet(Request $request, SystemController $sys){


        \Session::put('level', $request->input("level", ""));
        \Session::put('year', $request->input("year", ""));
        \Session::put('program', $request->input("program", ""));
        \Session::put('sem', $request->input("semester", ""));
        $program=$request->input("program", "");

        $level=$request->input("level", "");
        $semester=$request->input("semester", "");
        $yeargroup=$request->input("year", "");


        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $headerQuery=
                Models\StudentModel::where("GRADUATING_GROUP",$yeargroup)->where("PROGRAMMECODE",$program)->with('academic')->select("INDEXNO","LEVEL","NAME")->where("indexno",$request->input('search'))->get();
        }
        else{
            $headerQuery=Models\StudentModel::where("GRADUATING_GROUP",$yeargroup)->where("PROGRAMMECODE",$program)->with('academic')->select("INDEXNO","LEVEL","NAME")->get();


            // dd($headerQuery);
        }


        $courseArray=array();
        foreach($headerQuery as $row){
            //$courseArray=array();$course=$row['courseId'];
            $course=$row['code'];
            if($course!=""||$course==0){

                $courseArray[]=$course;
            }
            else{
                $courseArray[]="N/A";
            }
        }
        if ($request->has('search') && trim($request->input('search')) != "") {
            $studentData=  Models\StudentModel::where("GRADUATING_GROUP",$yeargroup)->where("PROGRAMMECODE",$program)->with('academic')->select("INDEXNO","LEVEL","NAME")->where("INDEXNO",$request->input('search'))->get();


        }
        else{
            $studentData= Models\StudentModel::where("GRADUATING_GROUP",$yeargroup)->where("PROGRAMMECODE",$program)->with('academic')->select("INDEXNO","LEVEL","NAME")->get();


        }


        return view('courses.napbtexBroadsheet')->with('year', $sys->years())
            ->with('level', $sys->getLevelList())
            ->with("program", $sys->getProgramList())
            ->with("headers", $headerQuery)
            ->with("course",   $courseArray)
            ->with("years", $request->input("year", ""))
            ->with("programs", $request->input("program", ""))
            ->with("levels", $request->input("level", ""))
            ->with("term", $request->input("semester", ""))
            ->with("student",  $headerQuery);


    }

    // noticeboard broadsheet

    public function noticeBoardBroadsheet(Request $request, SystemController $sys){

        return view('courses.noticeboard')->with('year', $sys->years())
            ->with('level', $sys->getLevelList())
            ->with("program", $sys->getProgramList());

    }


    public function processBroadsheet(Request $request, SystemController $sys) {
        ini_set('max_execution_time', 280000);
        \Session::put('level', $request->input("level", ""));
        \Session::put('year', $request->input("year", ""));
        \Session::put('program', $request->input("program", ""));
        \Session::put('sem', $request->input("semester", ""));
        $program=$request->input("program", "");

        $level=$request->input("level", "");
        $semester=$request->input("semester", "");
        $year=$request->input("year", "");


        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $headerQuery= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$semester)
                ->where("indexno",$request->input('search'))
                ->where("year",$year)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                })->orderBy("code")
                ->groupBy("code")
                ->get()->toArray();
        }
        else{
            $headerQuery= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$semester)
                ->where("year",$year)->whereHas('academic', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                })->orderBy("code")
                ->groupBy("code")
                ->get()->toArray();


        }


        $courseArray=array();
        foreach($headerQuery as $row){
            //$courseArray=array();$course=$row['courseId'];
            $course=$row['code'];
            if($course!=""||$course==0){

                $courseArray[]=$course;
            }
            else{
                $courseArray[]="N/A";
            }
            //dd($courseArray);
        }
        if ($request->has('search') && trim($request->input('search')) != "") {
            $studentData= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$semester)
                ->where("indexno",$request->input('search'))
                ->where("year",$year)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                })->orderBy("indexno")
                ->groupBy("indexno")
                ->select("indexno","level","grade")
                ->get();

        }
        else{
            // $studentData= Models\StudentModel::where("LEVEL",$level)->where("PROGRAMMECODE",$program)->with('academic')->select("INDEXNO","LEVEL","NAME")->get();

            /*$studentData= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$semester)

                ->where("year",$year)->whereHas('student', function($q)use ($program) {
                    $q->whereHas('programme', function($q)use ($program) {
                        $q->whereIn('PROGRAMMECODE',  array($program));
                    });
                })->orderBy("indexno")
                ->groupBy("indexno")
                ->select("indexno","level","grade")
                ->paginate(350);*/

            $studentData= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$semester)
                ->where("programme",$program)
                ->where("year",$year)

                ->orderBy("indexno")
                ->groupBy("indexno")
                ->select("indexno","level","grade")
                ->get();


        }


        return view('courses.noticeboard')->with('year', $sys->years())
            ->with('level', $sys->getLevelList())
            ->with("program", $sys->getProgramList())
            ->with("headers", $headerQuery)
            ->with("course",   $courseArray)
            ->with("years", $request->input("year", ""))
            ->with("programs", $request->input("program", ""))
            ->with("levels", $request->input("level", ""))
            ->with("term", $request->input("semester", ""))
            ->with("student", $studentData);


    }

    public function editResult(Request $request, SystemController $sys){
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        //$courses =Models\MountedCourse2Model::where('LECTURER',   \Auth::user()->fund)
            //->where("COURSE_SEMESTER",1)->where("COURSE_YEAR",$year)->paginate(100);
        $courses= Models\AcademicRecordsModel::query()->where('lecturer',   \Auth::user()->fund)
        ->where("year","2017/2018")->where("sem","2")->where("grade","!=","E")->groupBy("programme")->groupBy("code")->paginate(100);

        $program = @$courses[0]->programme;
        //dd($program);

        return view('courses.edit_result')->with("data", $courses)
            ->with('program', $program)
            ->with('level', $sys->getLevelList())
            ->with('year',$sys->years());


    }

}
