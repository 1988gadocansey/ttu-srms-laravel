<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\MessagesModel;
use App\Models;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use PhpParser\Node\Expr\AssignOp\Mod;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SystemController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }
    public function getCourseGradeCounter($course,$sem,$level,$year,$program,$grade) {

        /* $data= @\DB::table('tpoly_academic_record')
             ->where("year",$year)
             ->where("sem",$sem)
             ->where("level",$level)
             ->where("code",$course)
             ->select("grade")
             ->get();*/
        $row=array();
        $data= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$sem)

            ->where("year",$year)->where("grade",$grade)->where("code",$course)->whereHas('student', function($q)use ($program) {
                $q->whereHas('programme', function($q)use ($program) {
                    $q->whereIn('PROGRAMMECODE',  array($program));
                });
            })
            ->count();
        /* foreach ($data as $col=>$val){
             array_push($row,$val->grade);

         }

          foreach (array_count_values($row) as  $key=>$val){



              echo " $val ";


          }*/

        return @$data;
    }

    public function getCourseGradeCounterTotal($course,$sem,$level,$year,$program,$grade) {

        $total=0;
        foreach($grade as $grades) {

            $data = Models\AcademicRecordsModel::where("level", $level)->where("grade", "!=", "E")->where("sem", $sem)
                ->where("year", $year)->where("grade", $grades)->where("code", $course)->whereHas('student', function ($q) use ($program) {
                    $q->whereHas('programme', function ($q) use ($program) {
                        $q->whereIn('PROGRAMMECODE', array($program));
                    });
                })
                ->count();
            $total+= $data;
        }
        return $total;
    }








    public function getCourseGradeArray($course,$sem,$level,$year,$program) {

        $rows=array();
        $data= Models\AcademicRecordsModel::where("level",$level)->where("grade","!=","E")->where("sem",$sem)

            ->where("year",$year)->where("code",$course)->whereHas('student', function($q)use ($program) {
                $q->whereHas('programme', function($q)use ($program) {
                    $q->whereIn('PROGRAMMECODE',  array($program));
                });
            })
            ->select("grade")->get();
        foreach ($data as $row){
            array_push($rows,$row->grade);
        }

        return $rows;


    }
    public function getLecturerAverage($grades) {

        $total=0.0;
        for($i=0;$i<count($grades);$i++) {
            $data = Models\GradeSystemModel::
            where("grade", $grades[$i])->select("value")->first();


            $total+= $data->value;

        }

        return round($total/count($grades),2);
    }
    function array_push_assoc($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }
    public function arrayFrequencyCounter( $array,$needle){

        return count(array_keys($array,$needle));


    }
    public function sync_to_online($url,$data){
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL,$url);
        \curl_setopt($ch, CURLOPT_POST,1);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=\curl_exec($ch);

        \curl_close ($ch);
        return $result;

    }
    public function getCourseGrade($courseId,$year,$term,$student,$level) {

        $data= @\DB::table('tpoly_academic_record')->where("indexno",$student)
            ->where("year",$year)
            ->where("sem",$term)
            ->where("level",$level)
            ->where("code",$courseId)
            ->where("grade","!=","E")
            ->select("total")
            ->first();

        return @$data->total;

    }
    public function getCourseGradeNoticeBoard($courseId,$year,$term,$student,$level) {

        $data= @\DB::table('tpoly_academic_record')->where("indexno",$student)
            ->where("year",$year)
            ->where("sem",$term)
            ->where("level",$level)
            ->where("code",$courseId)
            ->where("grade","!=","E")
            ->select("total","grade")
            ->first();

        return @$data;

    }
    public function getProgramDuration($code) {

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE', $code)->get();

        return @$programme[0]->DURATION;
    }

    public function getProgramResult($code) {

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE', $code)->get();

        return @$programme[0]->RESULT;
    }
    public function getCreditBySem($indexno,$sem,$level) {

        $total = \DB::table('tpoly_academic_record')->where('indexno', $indexno)
            ->where("level",$level)
            ->where("sem",$sem)->where("grade","!=","E")
            ->sum('credits');

        return $total;
    }
    public function getGPBySem($indexno,$sem,$level) {

        $total = \DB::table('tpoly_academic_record')->where('indexno', $indexno)
            ->where("level",$level)
            ->where("sem",$sem)->where("grade","!=","E")
            ->sum('gpoint');

        return $total;
    }
    public function getGPABySem($indexno,$sem,$level) {

        $totalCR=@$this->getCreditBySem($indexno,$sem,$level);
        $totalGP=@$this->getGPBySem($indexno,$sem,$level);

        if($totalCR<=0 ||  $totalGP<=0){
            return 0;
        }
        return round( $totalGP/$totalCR,3);
    }
    public function getCGPAPerSem($indexno,$sem,$levels) {

        $totalCR=Models\AcademicRecordsModel::where("indexno",$indexno)->where("level",$levels)
            ->where("sem",$sem)->where("grade","!=","E")->sum("credits");
        $totalGP=Models\AcademicRecordsModel::where("indexno",$indexno)->where("level",$levels)
            ->where("sem",$sem)->where("grade","!=","E")->sum("gpoint");


        if($totalCR<=0 ||  $totalGP<=0){
            return 0;
        }
        return round( $totalGP/$totalCR,3);
    }
    public function getCGPA($indexno) {

        $totalCR=Models\AcademicRecordsModel::where("indexno",$indexno)->where("grade","!=","E")->sum("credits");
        $totalGP=Models\AcademicRecordsModel::where("indexno",$indexno)->where("grade","!=","E")->sum("gpoint");


        if($totalCR<=0 ||  $totalGP<=0){
            return 0;
        }
        return round( $totalGP/$totalCR,3);
    }
    public function age($birthdate, $pattern = 'eu')
    {
        $patterns = array(
            'eu'    => 'd/m/Y',
            'mysql' => 'Y-m-d',
            'us'    => 'm/d/Y',
            'gh'    => 'd-m-Y',
        );

        $now      = new \DateTime();
        $in       = \DateTime::createFromFormat($patterns[$pattern], $birthdate);
        $interval = $now->diff($in);
        return $interval->y;
    }
    public function getReligion() {
        $religion = \DB::table('tbl_religion')
            ->lists('religion', 'religion');
        return $religion;
    }
    public function getCountry() {
        $country = \DB::table('tbl_country')->orderBy("Name")->orderBy('Name')
            ->lists('Name', 'Name');
        return $country;
    }
    public function getHalls() {
        $hall = \DB::table('tpoly_hall')->orderBy("HALL_NAME")
            ->lists('HALL_NAME', 'HALL_NAME');
        return $hall;
    }
    public function getRegions() {
        $region = \DB::table('tbl_regions')
            ->lists('Name', 'Name');
        return $region;
    }
    public function getProgramByIDList() {
        if( @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->role=='FO'){

            $program = \DB::table('tpoly_programme')->orderBy("PROGRAMME")
                ->lists('PROGRAMME', 'ID');
            return $program;
        }
        else{
            // $user_department= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->orderBy("PROGRAMME")
                ->lists('PROGRAMME', 'ID');
            return $program;
        }

    }
    public function getDepartmentByIDList() {

        if( @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'|| @\Auth::user()->role=='FO'){
            $department = \DB::table('tpoly_department')->orderBy("DEPARTMENT")
                ->lists('DEPARTMENT', 'ID');
            return $department;
        }
        elseif( @\Auth::user()->role=='Registrar' ){
            $user_department= @\Auth::user()->department;
            $department = \DB::table('tpoly_department')->where('FACCODE',$user_department)->orderBy("DEPARTMENT")
                ->lists('DEPARTMENT', 'ID');
            return $department;
        }
        else{
            $user_department= @\Auth::user()->department;
            $department = \DB::table('tpoly_department')->where('DEPTCODE',$user_department)
                ->lists('DEPARTMENT', 'ID');
            return $department;
        }

    }
    public function getDepartmentList() {
        if(@\Auth::user()->role=='Registrar'){
            $department = \DB::table('tpoly_department')->where('FACCODE',@\Auth::user()->department)->orderBy("DEPARTMENT")
                ->lists('DEPARTMENT', 'DEPTCODE');
            return $department;
        }
        elseif(@\Auth::user()->role=='Support' ||@\Auth::user()->role=='HOD' ||@\Auth::user()->role=='Lecturer' ){
            $department = \DB::table('tpoly_department')->where('DEPTCODE',@\Auth::user()->department)->orderBy("DEPARTMENT")
                ->lists('DEPARTMENT', 'DEPTCODE');
            return $department;
        }
        else{
            $department = \DB::table('tpoly_department')->orderBy("DEPARTMENT")
                ->lists('DEPARTMENT', 'DEPTCODE');
            return $department;
        }
    }
    public function getGradeSystemIDList() {


        $grade = \DB::table('tpoly_grade_system')
            ->lists('type', 'type');
        return $grade;


    }
    public function getLevelList() {


        $level = \DB::table('tpoly_levels')
            ->lists('slug', 'name');
        return $level;


    }

    public function getLevelName($name) {


        $level = \DB::table('tpoly_levels')
            ->where('name', $name)->first();
        return $level->slug;


    }
    public function getSchoolList() {


        $school = \DB::table('tpoly_faculty')->orderBy("FACULTY")
            ->lists('FACULTY', 'FACCODE');
        return $school;


    }
    public function getProgrammeType($program){
        $sql=Models\ProgrammeModel::where("PROGRAMMECODE",$program)->first();
        if(!empty($sql)) {
            return $sql->TYPE;
        }
    }

    public function assignIndex($programme){
        $type=$this->getProgrammeType($programme);
        $quote=Models\IndexNumberModel::where("programme",$programme)->where("year",date("Y"))->first();
        if(!empty($quote)) {
            if ($type == "NON TERTIARY") {
                $index = $quote->code + 1;
                Models\IndexNumberModel::where("programme", $programme)->where("year", date("Y"))
                    ->update(array("code" => $index));
                return $index;
            } else {

                $index = $quote->code + 1;

                Models\IndexNumberModel::where("programme", $programme)->where("year", date("Y"))
                    ->update(array("code" => "0" . $index));
                return "0" . $index;
            }
        }
    }
    public function getProgrammeTypes() {


        $school = \DB::table('tpoly_programme')->where("TYPE","!=","")->groupBy("TYPE")->orderBy("TYPE")
            ->lists('TYPE', 'TYPE');
        return $school;


    }
    public function hallData($hall) {
        $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();

        return $info;

    }
    public function hallAccount($hall) {
        $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();

        return $info->ACCOUNTNUMBER;

    }
    public function hallFees($hall) {
        $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();

        return $info->AMOUNT;

    }
    public function hallRoomConsumed($hall) {
        $info = \DB::table('tpoly_applicants')->where("HALL_ADMITTED",$hall)->count();

        return $info;

    }
    public function getStudentAccountInfo($indexno) {


        $info = \DB::table('tpoly_log_portal')->where("username",$indexno)->first();
        if(!empty($info )){
            return $info->biodata_update;
        }

    }
    public function getYearBill($year,$level,$program) {


        $fee = \DB::table('tpoly_bills')->where("PROGRAMME",$program)
            ->where('LEVEL',$level)
            ->where('YEAR',$year)
            ->first();

        if(!empty($fee)){
            return $fee->AMOUNT;
        }
        else{
            // throw new HttpException(Response::HTTP_UNAUTHORIZED, 'The program that you are adding the student does not have school fees in the system.create the school fee for the program first. Go back');
            return 0.00;
        }


    }
    public function getYearBillLevelPostgraduate($year,$level,$program) {


        $fee = \DB::table('tpoly_bills')->where("PROGRAMME",$program)
            ->where('LEVEL', '500MT')
            ->where('YEAR',$year)
            ->first();

        if(!empty($fee)){
            return $fee->AMOUNT;
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'The program that you are adding the student does not have school fees in the system.create the school fee for the program first. Go back');

        }


    }
    public function getYearBillLevel100($year,$level,$program) {


        $fee = \DB::table('tpoly_bills')->where("PROGRAMME",$program)
            ->where('LEVEL','LIKE','%100%')
            ->where('YEAR',$year)
            ->first();

        if(!empty($fee)){
            return $fee->AMOUNT;
        }



    }
    public function getYearBillInject($year,$level,$program) {


        $fee = \DB::table('tpoly_bills')->where("PROGRAMME",$program)
            ->where('LEVEL',$level)
            ->where('YEAR',$year)
            ->first();

        if(!empty($fee)){
            return $fee->AMOUNT;
        }
        else{
            return 0;
        }


    }
    public function graduatingGroup($indexNo) {
        $level= substr($indexNo, 2,2);
        $group="20".$level;
        $group_=($group + 3)."/".($group + 4);

        return $group_;

    }
    public function getProgramDepartment($program){

        $department = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->get();

        return @$department[0]->DEPTCODE;

    }
    public function getClass($cgpa){

        $class = \DB::table('tpoly_classes')->where('lowerBoundary','<=',$cgpa)
            ->where("upperBoundary",">=",$cgpa)
            ->first();

        return @$class->class;

    }
    public function getTrails($indexno){
        $resultData=array();
        $trailsArray = \DB::table('tpoly_academic_record')->where('indexno',$indexno)
            ->where("grade","F")
            ->get();
        foreach ($trailsArray as $row){
            array_push($resultData,$row->grade);
        }


        return implode(",",$resultData);
    }
    public function getLecturer($lecturer){

        $staff = \DB::table('tpoly_workers')->where('id',$lecturer)->get();

        return @$staff;

    }
    public function getLecturerFromStaffID($lecturer){

        $staff = @\DB::table('tpoly_workers')->where('staffID',$lecturer)->get();

        return @$staff[0]->id;

    }

    public function getDepartmentName($deptCode){

        $department = \DB::table('tpoly_department')->where('DEPTCODE',$deptCode)->get();

        return @$department[0]->DEPARTMENT;

    }
    function selectcount($table_field, $Yes_or_No, $lecturer, $coursecode,$sem,$year)
    {
        $array = $this->getSemYear();

        $qa= $array[0]->QA;

        $data=explode(",",$qa);

        //$year=$data[0];

        //$sem=$data[1];



        //$query="SELECT count($table_field)  from questionnaire_table where lecturer='$lecturer' AND programmecode='$programmecode'";
        //$query="SELECT COUNT($table_field) from questionnaire_table as total WHERE  $table_field='$Yes_or_No' and lecturer='$lecturer' and coursecode='$coursecode'";
        $total=Models\QAquestionModel::
        where("$table_field","$Yes_or_No")
            ->where("lecturer",$lecturer)
            ->where("coursecode",$coursecode)
            ->where("academic_year",$year)
            ->where("semester",$sem)
            ->count("$table_field");


        return $total;
    }
    /*//counting the lecturer involved
    $queryc="SELECT COUNT(lecturer) from questionnaire_table as total2 WHERE coursecode='$coursecode' and lecturer='$lecturer'";
    echo  $queryc;
    $query_resultc=mysql_query($queryc) or die("error in counting lecturer1");
    $datac =mysql_fetch_row($query_resultc) or die('Error here too');
    $total_course_assessed= $datac[0];*/


    function remark($percentage_score)
    {
        if ($percentage_score >=80)
        {
            $remark_var="Excellent";
        }
        else if($percentage_score >=70)
        {
            $remark_var="Very Good";
        }
        else if($percentage_score >=60)
        {
            $remark_var="Good";
        }
        else if($percentage_score >=50)
        {
            $remark_var="Satisfactory";
        }
        else
        {
            $remark_var="Poor";
        }
        return $remark_var; //return remark of lecturer
    }

    function count_poor($remark_seen,$question_no)
    {
        if  ($remark_seen =="Poor")
        {
            return $question_no;
        }
        else
        {
            //echo "";
        }
    }// end of count_poor function


    function total_mark_remark($value)
    {
        if ($value >= 16)
        {
            $t_remark_var="Excellent";
        }
        else if($value >=14)
        {
            $t_remark_var="Very Good";
        }
        else if($value >=12)
        {
            $t_remark_var="Good";
        }
        else if($value >=10)
        {
            $t_remark_var = "Satisfactory";
        }
        else
        {
            $t_remark_var="Poor";
        }
        return $t_remark_var; //return remark of lecturer
    }
    public  function qaStatistics(){
        $comprehensive_outline ='comprehensive_outline';
        $outline_based_on_sylla= 'outline_based_on_sylla';   //to be removed
        $outline_recommended_books='outline_recommended_books';
        $lecturer_person_details ='lecturer_person_details';
        $course_objective_spelt='course_objective_spelt';
        $course_material_list ='course_material_list';

        $class_start_week='class_start_week';
        $class_met_regularly='class_met_regularly';
        $lecturer_punctual='lecturer_punctual';
        $lecturer_missed_reason='lecturer_missed_reason';

        $lecturer_stays_period = 'lecturer_stays_period';    //recently added

        $demonstrate_knowledge='demonstrate_knowledge';
        $well_organised_delivery='well_organised_delivery';
        $communicate_effectively='communicate_effectively';
        $class_time_prom_learn='class_time_prom_learn';
        $varying_teaching_meth = 'varying_teaching_meth';
        $encourage_stud_participation='encourage_stud_participation';
        $encourage_problem_solving='encourage_problem_solving';
        $respond_to_stud_concerns='respond_to_stud_concerns';
        $other_media_delivery='other_media_delivery';
        $room_for_question='room_for_question';
        $adequate_assignment='adequate_assignment';
        $state_feedback_time='state_feedback_time';
        $mark_assignment='mark_assignment';
        $discuss_in_class='discuss_in_class';
        $stud_progress_concern='stud_progress_concern';
        $stud_responsibility='stud_responsibility';
        $deadline_assignment='deadline_assignment';
        $disclose_marks ='disclose_marks';
        $late_submission_policy='late_submission_policy';
        $variety_assignment_used = 'variety_assignment_used';
        $course_objective_achieved ='course_objective_achieved';
        $expectations_communicated ='expectations_communicated';
        $sold_handout='sold_handout';
        $created_friendly_atmosphere='created_friendly_atmosphere';
        $programmecode='programmecode';
    }


    public function getDepartmentProgramme($pCode){
        $program = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$pCode)->first();


        $department = \DB::table('tpoly_department')->where('DEPTCODE',$program->DEPTCODE)->get();

        return @$department[0]->DEPARTMENT;

    }



    public function getSchoolCode($dept){

        $school = \DB::table('tpoly_department')->where('DEPTCODE',$dept)->get();

        return @$school[0]->FACCODE;

    }
    public function courseSearchByCode() {

        $course = \DB::table('tpoly_courses')->get();

        foreach($course as $p=>$value){
            $courses[]=$value->COURSE_CODE;
        }
        return $courses;
    }
    public function programmeSearchByCode() {

        $program = \DB::table('tpoly_programme')->get();

        foreach($program as $p=>$value){
            $programs[]=$value->PROGRAMMECODE;
        }
        return $programs;
    }
    public function programmeCategorySearchByCode() {

        $program = \DB::table('tpoly_programme')->get();

        foreach($program as $p=>$value){
            $programs[]=$value->SLUG;
        }
        return $programs;
    }
    public function studentSearchByIndexNo($program) {

        $arr = \DB::table('tpoly_students')->where("PROGRAMMECODE",$program)->get();
        //dd($arr);
        foreach($arr as $p=>$value){
            $objects[]=$value->INDEXNO;
        }
        return $objects;
    }
    public function studentSearchByCode($year,$sem,$course,$student) {

        $studentArr= @\DB::table('tpoly_academic_record')->where('year',$year)
            ->where('sem',$sem)
            ->where('course',$course)
            ->where('indexno',$student)
            ->get();

        if(!empty($studentArr)){
            foreach($studentArr as $p=>$value){
                $array[]=$value->indexno;
            }
            return @$array;
        }
        else{

        }
    }

    public function getSchoolName($dept){

        $faculty = \DB::table('tpoly_faculty')->where('FACCODE',$dept)->get();

        return @$faculty[0]->FACULTY;

    }
    public function getProgrammeMinCredit($program) {
        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->get();

        return @$programme[0]->MINCREDITS;
    }
    public function getProgramCode($id){

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$id)->get();

        return @$programme[0]->PROGRAMMECODE;

    }
    public function getProgramName($code){

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$code)->get();

        return @$programme[0]->PROGRAMME;

    }


    public function getProgramListEvening() {
        if( @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning"  || @\Auth::user()->role=="Accountant" || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin'){
            $program = \DB::table('tpoly_programme')->where("PROGRAMME","!LIKE","%"."Evening"."%")->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }
        elseif( @\Auth::user()->role=='Registrar' ){
            $user_school= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->join('tpoly_department','tpoly_department.DEPTCODE', '=', 'tpoly_programme.DEPTCODE')->where('tpoly_department.FACCODE',$user_school)->where("PROGRAMME","!LIKE","%"."Evening"."%")->orderby("tpoly_programme.PROGRAMME")->lists('tpoly_programme.PROGRAMME', 'tpoly_programme.PROGRAMMECODE');
            return $program;


        }
        else{
            $user_department= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->where("PROGRAMME","!LIKE","%"."Evening"."%")->where('DEPTCODE',$user_department)->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }

    }
    // this is purposely for select box
    public function getProgramList() {
        $departmentArray=explode(",",@\Auth::user()->department);
        if( @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning"  || @\Auth::user()->role=="Accountant" || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin' || @\Auth::user()->role == 'Lecturer'|| @\Auth::user()->department=="qa"){
            $program = \DB::table('tpoly_programme')->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }
        elseif( @\Auth::user()->role=='Registrar' ){
            $user_school= @\Auth::user()->department;
            $programCase=Models\ProgrammeModel::where("DEPTCODE",$user_school)->first();
            $departmentCase=Models\DepartmentModel::where("DEPTCODE",$programCase->DEPTCODE)->first();
            $faculty=$departmentCase->FACCODE;

            $programStruct=Models\ProgrammeModel::query()->orderBy("TYPE");
            $data=$programStruct->whereHas('departments', function($q)use ($user_school, $faculty) {



                    $q->whereHas("school", function($q)use ($user_school, $faculty){
                        $q->where('FACCODE',  $faculty);


                });}) ->select('PROGRAMME', 'PROGRAMMECODE')->get();





            return $data;
 

        }
        else{
            // $user_department= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->whereIn('DEPTCODE', $departmentArray)->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }

    }


    // this is purposely for select box
    public function getProgramList5() {
        $departmentArray=explode(",",@\Auth::user()->department);
        if( @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning"  || @\Auth::user()->role=="Accountant" || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin'){
            $program = \DB::table('tpoly_programme')->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }
        elseif( @\Auth::user()->role=='Registrar' ){
            $user_school= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->join('tpoly_department','tpoly_department.DEPTCODE', '=', 'tpoly_programme.DEPTCODE')->where('tpoly_department.FACCODE',$user_school)->orderby("tpoly_programme.PROGRAMME")->lists('tpoly_programme.PROGRAMME', 'tpoly_programme.PROGRAMMECODE');
            return $program;


        }
        else{
            // $user_department= @\Auth::user()->department;
            $program = \DB::table('tpoly_programme')->orderby("PROGRAMME")
                ->lists('PROGRAMME', 'PROGRAMMECODE');
            return $program;
        }

    }


    public function totalRegistered($sem,$year,$course,$level,$lecturer) {
        if(@\Auth::user()->role=='Lecturer' || @\Auth::user()->role=='HOD' ||@\Auth::user()->role=='Dean'){

            $query=Models\AcademicRecordsModel::where('sem',$sem)
                ->where('year',$year)
                ->where('level',$level)

                ->where('lecturer',$lecturer)
                ->where('code',$course)->get();
        }
        else{
            $query=Models\AcademicRecordsModel::where('sem',$sem)
                ->where('year',$year)
                ->where('level',$level)


                ->where('code',$course)->get();
        }

        return count($query);

    }
    public function years() {

        for ($i =1998; $i <= 3030; $i++) {
            $year = $i - 1 . "/" . $i;
            $years[$year] = $year;
        }
        return $years;
    }

    public function years22() {

        for ($i =2018; $i <= 2018; $i++) {
            $year = $i - 1 . "/" . $i;
            $years[$year] = $year;
        }
        return $years;
    }

    // this is purposely for select box
    public function getCourseList() {
        $course = Models\CourseModel::
        select('COURSE_NAME', 'ID',"PROGRAMME","COURSE_SEMESTER","COURSE_LEVEL","COURSE_CODE")->orderBy("COURSE_NAME")->get();
        return $course;


    }
    public function getProgramList2() {
        $program= Models\ProgrammeModel::
        select('PROGRAMMECODE', "PROGRAMME")->orderBy("PROGRAMME")->get();
        return $program;


    }

    public function getMountedCourseList() {

        if(@\Auth::user()->role=='Lecturer'){
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.Lecturer',@\Auth::user()->fund )->lists('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE');
            return $course;

        }
        else {
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->lists('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE');
            return $course;
        }
    }

    public function getMountedCourseList2() {

        if(@\Auth::user()->role=='Lecturer'){
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.Lecturer',@\Auth::user()->fund )->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER')->groupby('tpoly_mounted_courses.COURSE_CODE')->get();
            return $course;

        }
        else {
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER')->get();
            return $course;
        }
    }


    public function getMountedCourseList3() {

        if(@\Auth::user()->role=='Lecturer'){
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.Lecturer',@\Auth::user()->fund )->where('tpoly_mounted_courses.COURSE_SEMESTER','=', '2')->where('tpoly_mounted_courses.COURSE_YEAR','=', '2017/2018')->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER')->groupby('tpoly_mounted_courses.COURSE_CODE')->get();
            return $course;

        }
        else {
            $course=@\DB::table('tpoly_mounted_courses')
                ->join('tpoly_courses','tpoly_courses.COURSE_CODE', '=', 'tpoly_mounted_courses.COURSE_CODE')->where('tpoly_mounted_courses.COURSE_SEMESTER','=', '2')->select('tpoly_courses.COURSE_NAME', 'tpoly_mounted_courses.COURSE_CODE','tpoly_mounted_courses.COURSE_SEMESTER')->get();
            return $course;
        }
    }
    // this is purposely for select box
    public function getLectureList() {

        $lecturer = \DB::table('tpoly_workers')->where('designation','Lecturer')
            ->where('department',$user_department)->orderby("fullName")
            ->lists('fullName', 'id');
        return $lecturer;


    }
    public function getLectureList_All() {

        $lecturer = \DB::table('tpoly_workers')->orderby("fullName")

            ->lists('fullName', 'staffID');
        return $lecturer;


    }
    public function getLectureStaffID($id) {

        $lecturer = \DB::table('tpoly_workers')->Select("staffID")->where("id",$id)->first();


        return $lecturer->staffID;


    }
    public function getLectureName($staffID) {

        $lecturer = \DB::table('tpoly_workers')->Select("fullName")->where("staffID",$staffID)->first();


        return $lecturer->fullName;


    }
    // this is purposely for select box
    public function getUsers() {
        $user= \DB::table('users')
            ->lists('name', 'id');
        return $user;


    }
    public function department() {
        $department= \DB::table('tpoly_department')->orderby("DEPARTMENT")
            ->lists('DEPARTMENT', 'DEPTCODE');
        return $department;


    }
    public function WASSCE_Grades() {
        $grade= \DB::table('tbl_waec_grades_system')
            ->lists('grade', 'grade');
        return $grade;


    }

//     public function firesms($message,$phone,$receipient){
//
//
//
//        //print_r($contacts);
//        if (!empty($phone)&& !empty($message)&& !empty($receipient)) {
//            //$sender = "TPOLY-FEES";
//
//                //$key = "83f76e13c92d33e27895";
//                $message = urlencode($message);
//                $phone=$phone; // because most of the numbers came from excel upload
//
//                 $phone="+233".\substr($phone,1,9);
//            $url = 'http://txtconnect.co/api/send/';
//            $fields = array(
//            'token' => \urlencode('a166902c2f552bfd59de3914bd9864088cd7ac77'),
//            'msg' => \urlencode($message),
//            'from' => \urlencode("TPOLY"),
//            'to' => \urlencode($phone),
//            );
//            $fields_string = "";
//                    foreach ($fields as $key => $value) {
//                    $fields_string .= $key . '=' . $value . '&';
//                    }
//                    \rtrim($fields_string, '&');
//                    $ch = \curl_init();
//                    \curl_setopt($ch, \CURLOPT_URL, $url);
//                    \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
//                    \curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
//                    \curl_setopt($ch, \CURLOPT_POST, count($fields));
//                    \curl_setopt($ch, \CURLOPT_POSTFIELDS, $fields_string);
//                    \curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, 0);
//                    $result2 = \curl_exec($ch);
//                    \curl_close($ch);
//                    $data = \json_decode($result2);
//                    $output=@$data->error;
//                    if ($output == "0") {
//                   $result="Message was successfully sent";
//
//                    }else{
//                    $result="Message failed to send. Error: " .  $output;
//
//                    }
//
//
//                $array=  $this->getSemYear();
//                $sem=$array[0]->SEMESTER;
//                $year=$array[0]->YEAR;
//                  $user = \Auth::user()->id;
//                  $sms=new MessagesModel();
//                    $sms->dates=\DB::raw("NOW()");
//                    $sms->message=$message;
//                    $sms->phone=$phone;
//                    $sms->status=$result;
//                    $sms->type="Fees reminder";
//                    $sms->sender=$user;
//                    $sms->term=$sem;
//                    $sms->year=$year;
//                    $sms->receipient=$receipient;
//
//                   $sms->save();
//            }
//
//    }
//
//   public function firesms($message,$phone,$receipient){
//
//                    $array=  $this->getSemYear();
//        $sem=$array[0]->SEMESTER;
//               $year=$array[0]->YEAR;
//                  $user = \Auth::user()->fund;
//
//
//
//               $phone="+233".\substr($phone,1,9);
//            $phone = str_replace(' ', '', $phone);
//                 $phone = str_replace('-', '', $phone);
//                $key = "bcb86ecbc1a058663a07"; //your unique API key;
//           // $key = "83f76e13c92d33e27895"; //your unique API key;
//            $message=urlencode($message); //encode url;
//        $sender_id="TTU";
//
//        $url = "http://sms.gadeksystems.com/smsapi?key=$key&to=$phone&msg=$message&sender_id=$sender_id";
//        //print_r($url);
//           $result = file_get_contents($url); //call url and store result;
//
//                   if ($result == 1000) {
//
//                   $result="Message was successfully sent";
//
//                    }elseif ($result == 1002){
//                    $result="Message failed to send. Error: " .  $result;
//
//                    }
//                    elseif ($result == 1003){
//                    $result="insufficient balance ";
//
//                    }
//                     elseif ($result == 1004){
//                    $result="invalid API key ";
//
//                    }
//                    elseif ($result == 1005){
//                    $result="invalid Phone number ";
//
//                    }
//                    elseif ($result == 1006){
//                        $result="invalid Sender ID. Sender ID must not be more than 11 Characters. Characters include white space";
//                    }
//                     elseif ($result == 1007){
//                    $result=" Message scheduled for later delivery ";
//
//                    }
//                    else{
//                         $result=" Empty Message ";
//                    }
//
//
//
//                  $sms=new MessagesModel();
//                    $sms->dates=\DB::raw("NOW()");
//                    $sms->message=$message;
//                    $sms->phone=$phone;
//                    $sms->status=$result;
//                    $sms->type="Admission Notifications";
//
//                    $sms->sender=$user;
//              $sms->term=$sem;
//                   $sms->year=$year;
//                    $sms->receipient=$receipient;
//
//                   $sms->save();
//
//           return $result;
//
//
//
//
//
//    }

    public function getMessage($applicantNumber) {
        $data=@Models\MessagesModel::where("receipient",$applicantNumber)->where("type","Admission Notifications")->groupBy("receipient")->first();

        //dd($data);
        if($data!=null){
            return $data->message;
        }
        else{
            return "No sms sent";
        }

    }
    /**
     * Get current sem and year
     *
     * @param  Request  $request
     * @return Response
     */
    public function getSemYear()
    {
        $sql =\DB::table('tpoly_academic_settings')->where('ID', \DB::raw("(select max(`ID`) from tpoly_academic_settings)"))->get();
        return $sql;
    }

    public function getProgram($code){

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$code)->get();

        return @$programme[0]->PROGRAMME;

    }
    public function getStudentPassword($user){

        $userArr = \DB::table('tpoly_log_portal')->where('username',$user)->get();

        return @$userArr[0]->real_password;

    }
    public function getProgramArray($code){

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$code)->get();

        return @$programme;

    }
    public function getCreditHour($courseCode,$sem,$level,$program) {
        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$courseCode)->where('COURSE_SEMESTER',$sem)->where('COURSE_LEVEL',$level)
            ->where("PROGRAMME",$program)
            ->get();

        return @$course[0]->COURSE_CREDIT;
    }
    public function getMountedCreditHour($courseCode,$sem,$level,$program) {
        $course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$courseCode)->where('COURSE_SEMESTER',$sem)->where('COURSE_LEVEL',$level)
            ->where("PROGRAMME",$program)
            ->get();

        return @$course[0]->COURSE_CREDIT;
    }
    public function getStudentByID($id){

        $student = \DB::table('tpoly_students')->where('ID',$id)->get();

        return @$student[0]->INDEXNO;

    }
    public function getStudentIDfromIndexno($indexno) {
        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexno)->get();

        return  @$student[0]->ID;
    }
    public function getStudentprogramfromIndexno($indexno) {
        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexno)->get();

        return  @$student[0]->PROGRAMMECODE;
    }
    public function getStudentyeargroupfromIndexno($indexno) {
        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexno)->get();

        return  @$student[0]->GRADUATING_GROUP;
    }
    public function getStudentNameByID($id){

        $student = \DB::table('tpoly_students')->where('ID',$id)->get();

        return @$student[0]->NAME;

    }
    public function getStudent($indexNo){

        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexNo)->get();

        return @$student;

    }


    public function getStudentsTotalPerLevel2($level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')->where('LEVEL',$level)->where("REGISTERED","1")
            ->count();
        return $total;
    }

    //$total = \DB::table('tpoly_students')->where('LEVEL',$level)->where("REGISTERED","1")
    //          ->where("STATUS","In School")
    //            ->count();
    //    return $total;

    public function getStudentsTotalPerLevel($level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')->leftJoin('tpoly_academic_record', 'tpoly_students.ID', '=', 'tpoly_academic_record.student')

            //->where('tpoly_students.PROGRAMMECODE', $program)
            ->where('tpoly_academic_record.level', "LIKE", "%". $level . "%")
            ->where('tpoly_academic_record.year', $year)
            ->where('tpoly_academic_record.sem', $sem)
            ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }

    public function getStudentsTotalOverAll($level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_levelgender')
            //->where('STATUS','In School')
            //->where('tpoly_students.PROGRAMMECODE', $program)
            ->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            //->get();
            //return view('Male.getStudentsTotalOverAll', ['tpoly_levelgender'=>$total]);
            //->value('Total');
            ->first();
        return $total;
    }

    public function getPaidTotalOverAll(){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_levelgender')
            //->where('STATUS','In School')
            //->where('tpoly_students.PROGRAMMECODE', $program)
            //->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            //->get();
            //return view('Male.getStudentsTotalOverAll', ['tpoly_levelgender'=>$total]);
            //->value('Total');
            ->sum('Paid');
        return ($total);
    }

    public function getOweTotalOverAll(){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_levelgender')
            //->where('STATUS','In School')
            //->where('tpoly_students.PROGRAMMECODE', $program)
            //->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_levelgender.LEVEL', "LIKE", "%". $level . "%")
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            //->get();
            //return view('Male.getStudentsTotalOverAll', ['tpoly_levelgender'=>$total]);
            //->value('Total');
            ->sum('Owing');
        return ($total);
    }

    public function getStudentsHighestCGPA_HND($skip){
        //$array = $this->getSemYear();

        //$year = $array[0]->YEAR;
        //$sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')
            ->join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')
            ->where('tpoly_students.STATUS','In School')
            ->where('tpoly_students.LEVEL', "LIKE", "%"."H")
            ->where('tpoly_students.LEVEL', "=", "300H")
            ->where('tpoly_students.TOTAL_CREDIT_DONE', ">", "75")
            ->orderby("tpoly_students.CGPA",'DESC')
            ->skip($skip)
            ->take(1)
            //->lists('INDEXNO', 'CGPA');
            // ->where('tpoly_academic_record.year', $year)
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            ->get();
        return ($total);
    }

    //$users = DB::table('users')->skip(10)->take(5)->get();

    public function getStudentsTotalPerLevelAll($level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')
            ->where('STATUS','In School')
            //->where('tpoly_students.PROGRAMMECODE', $program)
            ->where('tpoly_students.LEVEL', "LIKE", "%". $level . "%")
            // ->where('tpoly_academic_record.year', $year)
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }

    public function getStudentsTotalPerLevelAllGen($level,$gen){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')
            ->where('STATUS','In School')
            ->where('tpoly_students.SEX', $gen)
            ->where('tpoly_students.LEVEL', "LIKE", "%". $level . "%")
            // ->where('tpoly_academic_record.year', $year)
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }




    public function getStudentsTotalPerLevelAllRegistered($level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')
            ->where('STATUS','In School')
            ->where('tpoly_students.REGISTERED',1)
            ->where('tpoly_students.LEVEL', "LIKE", "%". $level . "%")
            // ->where('tpoly_academic_record.year', $year)
            //->where('tpoly_academic_record.sem', $sem)
            // ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }

    public function getStudentsTotalPerProgramLevel($program,$level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;
//         $total = \DB::select( \DB::raw(" s.level,s.year,s.PROGRAMMECODE,s.indexno,a.student FROM `tpoly_students` as s JOIN tpoly_academic_record as a on s.id=a.student WHERE s.PROGRAMMECODE='$program'and "
//                 . ""
//                 . "a.level='$level' and a.year='$year'
//        )"))->groupBy("a.student")
//                 ->get();
//         dd($total);

        $total = \DB::table('tpoly_students')->leftJoin('tpoly_academic_record', 'tpoly_students.ID', '=', 'tpoly_academic_record.student')

            ->where('tpoly_students.PROGRAMMECODE', $program)
            ->where('tpoly_academic_record.level', "LIKE", "%". $level . "%")
            ->where('tpoly_academic_record.year', $year)
            ->where('tpoly_academic_record.sem', $sem)
            ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }



    public function getStudentsTotalPerProgramLevel2($program,$level){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;
//         $total = \DB::select( \DB::raw(" s.level,s.year,s.PROGRAMMECODE,s.indexno,a.student FROM `tpoly_students` as s JOIN tpoly_academic_record as a on s.id=a.student WHERE s.PROGRAMMECODE='$program'and "
//                 . ""
//                 . "a.level='$level' and a.year='$year'
//        )"))->groupBy("a.student")
//                 ->get();
//         dd($total);

        $total = \DB::table('tpoly_students')
            ->where('PROGRAMMECODE', $program)
            ->where('LEVEL', "LIKE", "%". $level . "%")
            ->where('REGISTERED', 1)
            ->get();
        return count($total);
    }


    public function getStudentsTotalPerProgram($program){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_students')->leftJoin('tpoly_academic_record', 'tpoly_students.ID', '=', 'tpoly_academic_record.student')

            ->where('tpoly_students.PROGRAMMECODE', $program)
            // ->where('tpoly_academic_record.level', "LIKE", "%". $level . "%")
            ->where('tpoly_academic_record.year', $year)
            ->where('tpoly_academic_record.sem', $sem)
            ->groupBy('tpoly_academic_record.student')
            ->get();
        return count($total);
    }
    public function getApplicantAdmitted(){
        $array = $this->getSemYear();

        $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_applicants')->where("ADMITTED",1)->where("YEAR_ADMISSION",$year)->count();

        return  $total ;
    }
    public function getApplicantTotalPerProgram($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMITTED",1)->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }

    public function getApplicantTotalPerProgramConditional($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE","conditional")->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }

    public function getApplicantTotalPerProgramProvisional($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE","provisional")->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }

    public function getApplicantTotalPerProgramTechnical($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE","technical")->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }

    public function getApplicantTotalPerProgramRegular($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE","regular")->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }


    public function getApplicantTotalPerProgramMature($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE","mature")->where("PROGRAMME_ADMITTED",$program)->count();

        return  $total ;
    }

    public function allApplicantsChoice1($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("FIRST_CHOICE",$program)->count();

        return  $total ;
    }

    public function allApplicantsChoice2($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("SECOND_CHOICE",$program)->count();

        return  $total ;
    }


    public function allApplicantsChoice3($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("THIRD_CHOICE",$program)->count();

        return  $total ;
    }

    public function allApplicants($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("FIRST_CHOICE",$program)->count();

        return  $total ;
    }
    public function allApplicantGender($program,$gender){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("FIRST_CHOICE",$program)->where("GENDER",$gender)->count();

        return  $total ;
    }


    // qualification
    public function allQualifiedApplicant($program){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("QUALIFY","Yes")->where("FIRST_CHOICE",$program)->count();

        return  $total ;
    }
    public function allQualifiedApplicantGender($program,$gender){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;


        $total = \DB::table('tpoly_applicants')->where("QUALIFY","Yes")->where("FIRST_CHOICE",$program)->where("GENDER",$gender)->count();

        return  $total ;
    }

    public function getApplicantTotalPerProgramAdmissionType($type){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_applicants')->where("ADMISSION_TYPE",$type)->where("ADMITTED",1)->count();

        return  $total ;
    }
    public function getApplicantTotalPerProgramGender($program,$gender){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_applicants')->where("ADMITTED","1")->where("PROGRAMME_ADMITTED",$program)->where("GENDER",$gender)->count();

        return  $total ;
    }
    public function getApplicantAccommodation($hall){
        // $array = $this->getSemYear();

        // $year = $array[0]->YEAR;
        // $sem=$array[0]->SEMESTER;

        $total = \DB::table('tpoly_applicants')->where("HALL_ADMITTED",$hall)->first();

        return count($total);
    }
    public function getStudentsTotalPerProgram4($program,$level=NULL){
        if($level==NULL){
            $total = \DB::table('tpoly_students')->where('PROGRAMMECODE',$program)
                ->where("STATUS","In School")->where("SYSUPDATE","1")
                ->count();
            return $total;
        }
        else{
            $total = \DB::table('tpoly_students')->where('PROGRAMMECODE',$program)
                ->where("year",$level)->where("STATUS","In School")->where("SYSUPDATE","1")

                ->count();
            return $total;
        }


    }
    public function getStudentsTotalPerProgram2($level){

        $total = \DB::table('tpoly_students')->where('LEVEL',$level)->where("REGISTERED","1")
            ->where("STATUS","In School")
            ->count();
        return $total;



    }
    public function getTotalStudentsByProgramCount($program,$level){
        $array=$this->getSemYear();

        $year=$array[0]->YEAR;
        $total= \DB::table('tpoly_students')
            ->join('tpoly_feedetails', 'tpoly_feedetails.INDEXNO', '=', 'tpoly_students.INDEXNO')

            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_feedetails.LEVEL',$level)
            ->where('tpoly_feedetails.YEAR',$year)
            ->count("tpoly_feedetails.ID");

        return @$total;

    }


    public function getTotalPaymentByProgram($program,$level){
        $array=$this->getSemYear();

        $year=$array[0]->YEAR;
        $amount= \DB::table('tpoly_students')
            ->join('tpoly_feedetails', 'tpoly_feedetails.INDEXNO', '=', 'tpoly_students.INDEXNO')

            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_feedetails.LEVEL',$level)
            ->where('tpoly_feedetails.YEAR',$year)
            ->sum("tpoly_feedetails.AMOUNT");

        return @$amount;

    }
    public function getTotalRegistered($program,$level){

        $total= \DB::table('tpoly_students')
            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_students.LEVEL',$level)
            ->where('tpoly_students.REGISTERED',1)
            ->count("tpoly_students.ID");

        return @$total;

    }

    public function getTotalRegistered100($program){
        $level=100;

        $total= \DB::table('tpoly_students')
            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_students.LEVEL','LIKE',$level.'%')
            ->where('tpoly_students.REGISTERED',1)
            ->count("tpoly_students.ID");

        return @$total;

    }
    public function getTotalOwingbyProgram($program,$level){

        $total= \DB::table('tpoly_students')
            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_students.LEVEL',$level)
            ->where('tpoly_students.STATUS','In School')
            ->sum("tpoly_students.BILL_OWING");

        return @$total;

    }
    public function getTotalStudentOwing($program,$level){

        $total= \DB::table('tpoly_students')
            ->where('tpoly_students.PROGRAMMECODE',$program)
            ->where('tpoly_students.LEVEL',$level)
            ->where('tpoly_students.STATUS','In School')
            ->where("tpoly_students.BILL_OWING",">",0)
            ->count("tpoly_students.ID");
        return @$total;

    }
    public function getTotalBillForProgram($program,$level ){
        $array=$this->getSemYear();

        $year=$array[0]->YEAR;
        $amount= \DB::table('tpoly_bills')
            ->where('tpoly_bills.PROGRAMME',$program)
            ->where('tpoly_bills.LEVEL',$level)
            ->where('tpoly_bills.YEAR',$year)
            ->first();

        return @$amount->AMOUNT;

    }
    public function getStaffAccount($id){

        $staff = \DB::table('tpoly_workers')->where('staffID',$id)->get();

        return $staff;

    }
    public function getProgramCodeByID($id){

        $programme = \DB::table('tpoly_programme')->where('ID',$id)->get();

        return @$programme[0]->PROGRAMMECODE;

    }
    // return course array based on code
    public function getCourseByCodeObject($id) {

        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();

        if($course!=""){
            return @$course;
        }
        else{
            $course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->get();
            return @$course;
        }

    }


    // return course array based on mouted courses
    public function getCourseByCodeProgramObject($id,$program) {

        $courseObject = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->select
        ("COURSE")->get();
        if(!empty($courseObject)){
            $courseMount=$courseObject[0]->COURSE;
            $course = \DB::table('tpoly_courses')->where('ID',$courseMount)->get();
            if(!empty($course)){
                return @$course;
            }
            else{
                //$course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->get();
                $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();
                return @$course;
            }
        }
        else{
            //$course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->get();
            $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();
            return @$course;
        }
    }


    public function getCourseByCode($code) {
        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$code)->get();

        return @$course[0]->ID;
    }
    public function getCourseByCode2($code,$program) {
        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$code)
            ->where("PROGRAMME",$program)
            ->get();

        return @$course[0]->ID;
    }

    public function getCourseByCodeCourse($code, $course) {
        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$code)
            ->where("COURSE_NAME",$course)
            ->get();

        return @$course[0]->ID;

    }
    public function getProgramByID($id) {
        $programme = \DB::table('tpoly_programme')->where('ID',$id)->get();

        return @$programme[0]->PROGRAMME;
    }
    public function getProgramByGradeSystem($program) {
        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->get();

        return @$programme[0]->GRADING_SYSTEM;
    }
    public function getCourseProgrammeMounted($course) {

        $programme= \DB::table('tpoly_courses')->where('COURSE_CODE',$course)->get();

        return @$programme[0]->PROGRAMME;
    }
    public function getCourseProgramme($course) {

        $programme= \DB::table('tpoly_courses')->where('ID',$course)->get();

        return @$programme[0]->PROGRAMME;
    }
    public function getCourseProgramme2($course) {

        $programme= \DB::table('tpoly_courses')->where('COURSE_CODE',$course)->get();

        return @$programme[0]->PROGRAMME;
    }
    public function getGrade($mark,$type){

        $grade = \DB::table('tpoly_grade_system')
            ->where('lower','<=',$mark)
            ->where('upper','>=',$mark)
            ->where('type',$type)
            ->get();

        return $grade;

    }

    public function getGradeLetter($mark,$type){

        $grade = \DB::table('tpoly_grade_system')
            ->where('lower','<=',$mark)
            ->where('upper','>=',$mark)
            ->where('type',$type)
            ->where("grade","!=","E")
            ->get();

        return @$grade[0]->grade;

    }
    public function getGradeValue($mark,$type){

        $grade = \DB::table('tpoly_grade_system')
            ->where('lower','<=',$mark)
            ->where('uppers','>=',$mark)
            ->where('type',$type)
            ->where("grade","!=","E")
            ->select('value')->get();

        return @$grade[0]->value;

    }


    public function getCourseCodeByID($id){

        $course= \DB::table('tpoly_academic_record')
            ->join('tpoly_mounted_courses', 'tpoly_academic_record.code', '=', 'tpoly_mounted_courses.COURSE_CODE')
            ->join('tpoly_courses', 'tpoly_mounted_courses.COURSE_CODE', '=', 'tpoly_courses.COURSE_CODE')
            ->select('tpoly_courses.COURSE_CODE')->where('tpoly_academic_record.code',$id)
            ->get();

        return @$course[0]->COURSE_CODE;

    }
    public function getCourseName($id){

        $course= \DB::table('tpoly_courses')
            ->join('tpoly_mounted_courses',  'tpoly_mounted_courses.COURSE', '=', 'tpoly_courses.ID')

            ->select('*')->where('tpoly_mounted_courses.PROGRAMME',"!=","")->where('tpoly_mounted_courses.ID',$id)
            ->get();

        return @$course;

    }

    public function getCourseCodeByIDArray($id){

        $course= \DB::table('tpoly_courses')->where('ID',$id)
            ->get();

        return @$course;

    }
    public function getCourseCodeByIDArray2($id){

        $course= \DB::table('tpoly_courses')->where('COURSE_CODE',$id)
            ->get();

        return  @$course;

    }
    public function getCourseMountedInfo($id){

        $course= \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)
            ->get();

        return @$course;

    }

    public function getCourseMountedInfo2($courseCode,$sem,$level,$year,$program){

        $course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$courseCode)->where('COURSE_SEMESTER',$sem)->where('COURSE_LEVEL',$level)->where('COURSE_YEAR',$year)
            ->where("PROGRAMME",$program)
            ->get();

        return @$course;

    }

    public function getCourseByIDCode($code){

        $course= \DB::table('tpoly_academic_record')
            ->leftjoin('tpoly_mounted_courses', 'tpoly_academic_record.course', '=', 'tpoly_mounted_courses.ID')
            ->leftjoin('tpoly_courses', 'tpoly_mounted_courses.COURSE', '=', 'tpoly_courses.ID')
            ->select('tpoly_academic_record.course')->where('tpoly_courses.COURSE_CODE',$code)
            ->get();

        return @$course[0]->course;

    }
    public function getCourseByID($id){

        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();

        return @$course[0]->COURSE_NAME;

    }
    public function getCourse($id){

        $course = \DB::table('tpoly_courses')->where('ID',$id)->get();

        return @$course[0]->COURSE_NAME;

    }
    public function getTotalFeeByProrammeLevel($program,$level){
        $program=  $this->getProgramCodeByID($program);
        $total = \DB::table('tpoly_students')->where('PROGRAMMECODE',$program)->where('YEAR',$level)->where('STATUS','=','In school')->COUNT('*');
        // dd($total);
        return @$total;

    }
    public function picture($path,$target){
        if(file_exists($path)){
            $mypic = getimagesize($path);

            $width=$mypic[0];
            $height=$mypic[1];

            if ($width > $height) {
                $percentage = ($target / $width);
            } else {
                $percentage = ($target / $height);
            }

            //gets the new value and applies the percentage, then rounds the value
            $width = round($width * $percentage);
            $height = round($height * $percentage);

            return "width=\"$width\" height=\"$height\"";



        }else{}


    }


    public function pictureid($stuid) {

        return str_replace('/', '', $stuid);
    }

    function formatMoney($number, $fractional=false) {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }
    public function formatCurrency($amount) {
        return number_format($amount,3);

    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $request->user()->tasks()->create([
            'name' => $request->name,
        ]);

        return redirect('/tasks');
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
    public function firesms($message,$phone,$receipient){


        if (!empty($phone)&& !empty($message)&& !empty($receipient)) {
            \DB::beginTransaction();
            try {


                $phone="+233".\substr($phone,-9);
                $phone = str_replace(' ', '', $phone);
                $phone = str_replace('-', '', $phone);
                if (!empty($message) && !empty($phone)) {

                    $key = "bcb86ecbc1a058663a07"; //your unique API key;

                    // $sender_id="TTU";




                    $message=urlencode($message); //encode url;
                    $sender_id="TTU";

                    $url = "http://sms.gadeksystems.com/smsapi?key=$key&to=$phone&msg=$message&sender_id=$sender_id";



                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $url);
                    \curl_setopt($ch, CURLOPT_HEADER, 0);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $result = \curl_exec($ch);
                    \curl_close($ch);

                    $result="Message was successfully sent";


                    $array=  $this->getSemYear();
                    $sem=$array[0]->SEMESTER;
                    $year=$array[0]->YEAR;
                    $user = \Auth::user()->serial;


                    $user = \Auth::user()->fund;
                    $sms=new MessagesModel();
                    $sms->dates=\DB::raw("NOW()");
                    $sms->message=$message;
                    $sms->phone=$phone;
                    $sms->status=$result;
                    $sms->type="Admission Notifications";

                    $sms->sender=$user;
                    $sms->term=$sem;
                    $sms->year=$year;
                    $sms->receipient=$receipient;

                    $sms->save();
                    \DB::commit();
                }

            }
            catch (\Exception $e) {
                \DB::rollback();
            }
        }



    }

    public function getPinSerial($applicant) {
        $data= \DB::table('tpoly_forms')->where("FORM_NO",$applicant)->first();
        return $data;

    }

    public function sysSMS(){
        $data= Models\ApplicantModel::where("ADMITTED",1)->where("ADMISSION_TYPE","mature")->get();

        foreach($data as $key){
            // dd();
            $info= $this->getPinSerial($key->APPLICATION_NUMBER);
            $serial=$info->serial;
            $pin=$info->PIN;
            $receipient=$key->APPLICATION_NUMBER;
            $phone=$key->PHONE;
            $name=$key->FIRSTNAME;
            $type="mature";
            $program= $this->getProgram($key->PROGRAMME_ADMITTED);
            $regular = "Congrats! $name. You have been admitted to TTU to pursue $program.Your serial no is $serial and pin code is $pin,Print your letter using the link http://admissions.ttuportal.com";
            $conditional = "Congrats! $name. You have been offered a conditional admission to TTU to pursue $program.Your serial no is $serial and pin code is $pin,Print your letter using the link http://admissions.ttuportal.com";

            $provisional = "Congrats! $name. You have been offered a provisional admission to TTU to pursue $program. You will be required to send your results when published to complete the admission process.Your serial no is $serial and pin code is $pin, Print your letter using the link http://admissions.ttuportal.com";

            if ($type=="conditional")
            {
                $message=$conditional;

            }
            elseif($type=="regular" ){
                $message= $regular;
            }

            elseif ($type=="provisional") {
                $message= $provisional;
            }
            @Models\ApplicantModel::where("APPLICATION_NUMBER", $receipient)->update(array("SMS_SENT" => 1));
            @$this->firesms($message, $phone, $receipient);
        }
        return redirect("/applicants/view");
    }







    public function sendSingleSMS( $phone,$receipient,$type,$name){
        $info= $this->getPinSerial($receipient);
        $serial=$info->serial;
        $pin=$info->PIN;

        $data= Models\ApplicantModel::where("ADMITTED",1)->where("APPLICATION_NUMBER",$receipient)->first();
        $program= $this->getProgram($data->PROGRAMME_ADMITTED);
        $regular = "Congrats! $name. You have been admitted to TTU to pursue $program.Your serial no is $serial and pin code is $pin,Print your letter using the link http://admissions.ttuportal.com";
        $conditional = "Congrats! $name. You have been offered a conditional admission to TTU to pursue $program.Your serial no is $serial and pin code is $pin,Print your letter using the link http://admissions.ttuportal.com";

        $provisional = "Congrats! $name. You have been offered a provisional admission to TTU to pursue $program. You will be required to send your results when published to complete the admission process.Your serial no is $serial and pin code is $pin, Print your letter using the link http://admissions.ttuportal.com";

        if ($type=="conditional")
        {
            $message=$conditional;

        }
        elseif($type=="regular" ){
            $message= $regular;
        }

        elseif ($type=="provisional") {
            $message= $provisional;
        }
        @Models\ApplicantModel::where("APPLICATION_NUMBER", $receipient)->update(array("SMS_SENT" => 1));
        @$this->firesms($message, $phone, $receipient);
        return redirect("/applicants/view");
    }
    public function sendOutreachSingleSMS( $phone,$id,$type,$name){

        $data= Models\OutreachModel::where("admitted",1)->where("id",$id)->first();
        $receipient=$data->applicationNumber;
        $program= $this->getProgram($data->programmeAdmitted);
        $regular = "Congrats! $name. You have been admitted to TTU to pursue $program.Your application number is $receipient, Print your letter using the link http://outreach.ttuportal.com";
        $conditional = "Congrats! $name. You have been offered a conditional admission to TTU to pursue $program.Your application number is $receipient, Print your letter using the link http://outreach.ttuportal.com";

        $provisional = "Congrats! $name. You have been offered a provisional admission to TTU to pursue $program. You will be required to send your results when published to complete the admission process.Your application number is $receipient, Print your letter using the link http://outreach.ttuportal.com";

        if ($type=="conditional")
        {
            $message=$conditional;

        }
        elseif($type=="regular" ){
            $message= $regular;
        }

        elseif ($type=="provisional") {
            $message= $provisional;
        }
        @Models\OutreachModel::where("applicationNumber", $receipient)->update(array("sms_sent" => 1));
        @$this->firesms($message, $phone, $receipient);
        return redirect("/outreach/view");
    }
    public function getStudentOwingaAmount($indexno){

        $total= \DB::table('tpoly_students')->where("INDEXNO",$indexno)->first();
        return $total->BILL_OWING;
    }



    public function sendAuthOutreach(){

        $data= Models\OutreachModel::where("admitted",1)->get();
        $auth= Models\FormModel::where("serial","LIKE","TTUCR17%")->where("FORM_NO","0")->get();

        foreach($data as $row){

            foreach($auth as $salt){
                //dd($salt);
                $message = "Hi $row->name, kindly login into admissions.ttuportal.com with serial $salt->serial and pin  $salt->PIN to update your data.Thanks";
                //dd($message);
                @$this->firesms($message, $row->phone, $row->applicationNumber);
            }
        }


        //return redirect("/outreach/view");
    }
    public function sendSrms($applicant){
        $url="/send";

        $content = json_encode($applicant);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);



        curl_close($curl);

        $response = json_decode($json_response, true);
    }
    public function fetchData($url){

        $postdata = http_build_query(
            array(
                'userName' => 'sisaemma@yahoo.co.uk',
                'passWord' => 'PRINT45dull',
                'startDate'   => '2017-09-01',
                'endDate'   => date("Y-m-d"),
                'accountNumber'=>'6010406900',
                'processType'=>''
            )
        );
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data);
    }
    public function fireLostSMS(){
        $academicDetails=$this->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;
        // $data =  $this->fetchData("https://www.zenithbank.com.gh/realtimenotification/api/bankpaydetail");

        $sql=Models\ApplicantModel::where("ADMITTED","1")->where("ID",">","2000")->get();

        foreach ($sql as $student)
        {
            //freshers

            if(!empty($student->APPLICATION_NUMBER)) {
                $program=$student->PROGRAMME_ADMITTED;
                $ptype=$this->getProgrammeType($program);
                if($ptype=="NON TERTIARY"){
                    $level="100NT";
                }
                elseif($ptype=="HND"){
                    $level="100H";
                }
                elseif($ptype=="BTECH"){
                    $level="100BTT";
                }
                else{
                    $level="500";
                }
//dd($level);
                if($program=="MTECHT"||$program=="MTECHP"||$program=="MTECHG"){
                    $fee = $this->getYearBillLevelPostgraduate($year, $level, $program);
                }
                else{
                    $fee = $this->getYearBillLevel100($year, $level, $program);
                }




                //$this->getPassword($student->APPLICATION_NUMBER);
                $que = Models\PortalPasswordModel::where("username", $student->APPLICATION_NUMBER)->first();
                $indexno=$student->APPLICATION_NUMBER;
                $real=$que->real_password;


                $message = "TTU Online Credentials: visit records.ttuportal.com with $student->APPLICATION_NUMBER as username and $real as password and follow the course registration steps \n\nYour registration will be revoked if full fees payment is not made before mid sem Exams.\n\nApproach personnel in TPConnect branded attire for enquiries.";



                @$this->firesms($message, $student->PHONE, $student->APPLICATION_NUMBER);
            }
        }




    }
    public function pullApplicants(){
        $academicDetails=$this->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;
        // $data =  $this->fetchData("https://www.zenithbank.com.gh/realtimenotification/api/bankpaydetail");

        $sql=Models\ApplicantModel::where("ADMITTED","1")->get();

        foreach ($sql as $student)
        {
            //freshers

            if(!empty($student->APPLICATION_NUMBER)) {
                $program=$student->PROGRAMME_ADMITTED;
                $ptype=$this->getProgrammeType($program);
                if($ptype=="NON TERTIARY"){
                    $level="100NT";
                }
                elseif($ptype=="HND"){
                    $level="100H";
                }
                elseif($ptype=="BTECH"){
                    $level="100BTT";
                }
                else{
                    $level="500";
                }
//dd($level);
                if($program=="MTECHT"||$program=="MTECHP"||$program=="MTECHG"){
                    $fee = $this->getYearBillLevelPostgraduate($year, $level, $program);
                }
                else{
                    $fee = $this->getYearBillLevel100($year, $level, $program);
                }



                $checker=Models\StudentModel::where("INDEXNO",$student->APPLICATION_NUMBER)
                    ->first();
                if(empty($checker)) {



                    /////////////////////////////////////////////////////


                    $query = new Models\StudentModel();
                    $query->YEAR = $level;
                    $query->LEVEL = $level;
                    $query->FIRSTNAME = $student->FIRSTNAME;
                    $query->SURNAME = $student->SURNAME;
                    $query->OTHERNAMES = $student->OTHERNAME;
                    $query->TITLE = $student->TITLE;
                    $query->SEX = $student->GENDER;
                    $query->DATEOFBIRTH = $student->DOB;
                    $query->NAME = $student->NAME;
                    $query->AGE = $student->AGE;

                    $query->MARITAL_STATUS = $student->MARITAL_STATUS;
                    $query->HALL = $student->HALL_ADMITTED;
                    $query->ADDRESS = $student->ADDRESS;
                    $query->RESIDENTIAL_ADDRESS = $student->RESIDENTIAL_ADDRESS;
                    $query->EMAIL = $student->EMAIL;
                    $query->PROGRAMMECODE = $student->PROGRAMME_ADMITTED;
                    $query->TELEPHONENO = $student->PHONE;
                    $query->COUNTRY = $student->NATIONALITY;
                    $query->REGION = $student->REGION;
                    $query->RELIGION = $student->RELIGION;
                    $query->HOMETOWN = $student->HOMETOWN;
                    $query->GUARDIAN_NAME = $student->GURDIAN_NAME;
                    $query->GUARDIAN_ADDRESS = $student->GURDIAN_ADDRESS;
                    $query->GUARDIAN_PHONE = $student->GURDIAN_PHONE;
                    $query->GUARDIAN_OCCUPATION = $student->GURDIAN_OCCUPATION;
                    $query->DISABILITY = $student->PHYSICALLY_DISABLED;
                    $query->STATUS = "In School";
                    $query->SYSUPDATE = "1";


                    $query->BILLS = $student->ADMISSION_FEES;
                    $query->BILL_OWING = $student->ADMISSION_FEES;
                    $query->STNO = $student->APPLICATION_NUMBER;
                    $query->INDEXNO = $student->APPLICATION_NUMBER;
                    $query->save();
                }
                //$this->getPassword($student->APPLICATION_NUMBER);
                $que = Models\PortalPasswordModel::where("username", $student->APPLICATION_NUMBER)->first();
                $indexno=$student->APPLICATION_NUMBER;
                $ptype=$this->getProgrammeType($student->PROGRAMME_ADMITTED);
                if($ptype=="NON TERTIARY"){
                    $level="100NT";
                }
                elseif($ptype=="HND"){
                    $level="100H";
                }
                elseif($ptype=="BTECH"){
                    $level="100BTT";
                }
                else{
                    $level="500";
                }
                if (empty($que) && !empty($indexno)) {
                    $program = $student->PROGRAMME_ADMITTED;
                    $str = 'abcdefhkmnprtuvwxy34678abcdefhkmnprtuvwxy34678';
                    $shuffled = str_shuffle($str);
                    $vcode = substr($shuffled, 0, 8);
                    $real = strtoupper($vcode);
                    $level = $level;
                    Models\PortalPasswordModel::create([
                        'username' => $indexno,
                        'real_password' => $real,
                        'level' => $level,
                        'programme' => $program,
                        'biodata_update' => '1',
                        'password' => bcrypt($real),
                    ]);


                    $message = "TTU Online Credentials: visit records.ttuportal.com with $student->APPLICATION_NUMBER as username and $real as password and follow the course registration steps \n\nYour registration will be revoked if full fees payment is not made before mid sem Exams.\n\nApproach personnel in TPConnect branded attire for enquiries.";



                    @$this->firesms($message, $student->PHONE, $student->APPLICATION_NUMBER);
                }
            }



        }
    }

    public function getZenith(){
        $academicDetails=$this->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;
        // $data =  $this->fetchData("https://www.zenithbank.com.gh/realtimenotification/api/bankpaydetail");
        foreach ($data->records as $item)
        {

            //freshers
            $student=Models\ApplicantModel::where("APPLICATION_NUMBER",$item->StudentID)->first();

            if(!empty($student->APPLICATION_NUMBER)) {
                $program = $student->PROGRAMME_ADMITTED;
                $ptype = $this->getProgrammeType($program);
                if ($ptype == "NON TERTIARY") {
                    $level = "100NT";
                } elseif ($ptype == "HND") {
                    $level = "100H";
                } elseif ($ptype == "BTECH") {
                    $level = "100BTT";
                } else {
                    $level = "500";
                }
//dd($level);
                if ($program == "MTECHT" || $program == "MTECHP" || $program == "MTECHG") {
                    $fee = $this->getYearBillLevelPostgraduate($year, $level, $program);
                } else {
                    $fee = $this->getYearBillLevel100($year, $level, $program);
                }


                if ($student->ADMISSION_FEES <= $item->Amount) {
                    $details = "Full payment";

                } else {
                    $details = "Part payment";
                }
                $date = $item->PaymentDate;
                $checker = Models\FeePaymentModel::where("SEMESTER", $sem)
                    ->where("YEAR", $year)->where("INDEXNO", $item->StudentID)
                    ->where("CHECKER", $date)->first();
                if (empty($checker)) {
                    $feeLedger = new Models\FeePaymentModel();
                    $feeLedger->INDEXNO = $item->StudentID;
                    $feeLedger->PROGRAMME = $student->PROGRAMME_ADMITTED;
                    $feeLedger->AMOUNT = $item->Amount;
                    $feeLedger->PAYMENTTYPE = "School Fees";
                    $feeLedger->PAYMENTDETAILS = $details;
                    $feeLedger->BANK_DATE = date("Y-m-d");
                    $feeLedger->CHECKER = $item->PaymentDate;
                    $feeLedger->LEVEL = $level;
                    $feeLedger->RECIEPIENT = 751999;
                    $feeLedger->BANK = $item->AccountNumber;
                    $feeLedger->TRANSACTION_ID = rand();
                    $feeLedger->RECEIPTNO = $this->getReceipt();
                    $feeLedger->YEAR = $year;
                    $feeLedger->FEE_TYPE = "School Fees";
                    $feeLedger->SEMESTER = $sem;
                    $feeLedger->save();


                    //$message = "Online credential: visit records.ttuportal.com with $indexno as your username  and $real as password and follow the course registration steps.";


                    // @$this->firesms($message, $student->PHONE, $student->APPLICATION_NUMBER);
                    $this->updateReceipt();


                    $sql = Models\StudentModel::where("STNO", $student->APPLICATION_NUMBER)->first();
                    if (empty($sql)) {
                        /////////////////////////////////////////////////////


                        $query = new Models\StudentModel();
                        $query->YEAR = $level;
                        $query->LEVEL = $level;
                        $query->FIRSTNAME = $student->FIRSTNAME;
                        $query->SURNAME = $student->SURNAME;
                        $query->OTHERNAMES = $student->OTHERNAME;
                        $query->TITLE = $student->TITLE;
                        $query->SEX = $student->GENDER;
                        $query->DATEOFBIRTH = $student->DOB;
                        $query->NAME = $student->NAME;
                        $query->AGE = $student->AGE;

                        $query->MARITAL_STATUS = $student->MARITAL_STATUS;
                        $query->HALL = $student->HALL_ADMITTED;
                        $query->ADDRESS = $student->ADDRESS;
                        $query->RESIDENTIAL_ADDRESS = $student->RESIDENTIAL_ADDRESS;
                        $query->EMAIL = $student->EMAIL;
                        $query->PROGRAMMECODE = $student->PROGRAMME_ADMITTED;
                        $query->TELEPHONENO = $student->PHONE;
                        $query->COUNTRY = $student->NATIONALITY;
                        $query->REGION = $student->REGION;
                        $query->RELIGION = $student->RELIGION;
                        $query->HOMETOWN = $student->HOMETOWN;
                        $query->GUARDIAN_NAME = $student->GURDIAN_NAME;
                        $query->GUARDIAN_ADDRESS = $student->GURDIAN_ADDRESS;
                        $query->GUARDIAN_PHONE = $student->GURDIAN_PHONE;
                        $query->GUARDIAN_OCCUPATION = $student->GURDIAN_OCCUPATION;
                        $query->DISABILITY = $student->PHYSICALLY_DISABLED;
                        $query->STATUS = "In School";
                        $query->SYSUPDATE = "1";


                        $query->BILLS = $student->ADMISSION_FEES;
                        $query->BILL_OWING = $student->ADMISSION_FEES - $item->Amount;
                        $query->STNO = $student->APPLICATION_NUMBER;
                        $query->INDEXNO = $student->APPLICATION_NUMBER;
                        $query->save();
                        $this->getPassword($student->APPLICATION_NUMBER);
                    } else {
                        $owing = $student->ADMISSION_FEES - $item->Amount;
                        Models\StudentModel::where("STNO", $item->StudentID)->update(array("BILL_OWING" => $owing));
                    }
                }
            }elseif(empty($student->APPLICATION_NUMBER)) {

                $oldStudent = Models\StudentModel::where("STNO",  $item->StudentID)->orWhere("INDEXNO",  $item->StudentID)->orWhere("INDEXNO","!=","07160052")->first();
                $level = $oldStudent->LEVEL;
                $index = $oldStudent->INDEXNO;
                $program = $oldStudent->PROGRAMMECODE;
                $bill = $this->getYearBill($year, $level, $program);

                $bill_owing = $bill - $item->Amount;
                if ($bill <= $item->Amount) {
                    $details = "Full payment";

                } else {
                    $details = "Part payment";
                }

                $date = $item->PaymentDate;
                $checker = Models\FeePaymentModel::where("SEMESTER", $sem)
                    ->where("YEAR", $year)->where("INDEXNO", $item->StudentID)
                    ->where("CHECKER", $date)->get();
                if (empty($checker)) {
                    $feeLedger = new Models\FeePaymentModel();
                    $feeLedger->INDEXNO = $index;
                    $feeLedger->PROGRAMME = $program;
                    $feeLedger->AMOUNT = $item->Amount;
                    $feeLedger->PAYMENTTYPE = "School Fees";
                    $feeLedger->PAYMENTDETAILS = $details;
                    $feeLedger->BANK_DATE = date("Y-m-d");
                    $feeLedger->CHECKER = $checker;
                    $feeLedger->LEVEL = $level;
                    $feeLedger->RECIEPIENT = 751999;
                    $feeLedger->BANK = $item->AccountNumber;
                    $feeLedger->TRANSACTION_ID = rand();
                    $feeLedger->RECEIPTNO = $this->getReceipt();
                    $feeLedger->YEAR = $year;
                    $feeLedger->FEE_TYPE = "School Fees";
                    $feeLedger->SEMESTER = $sem;
                    $feeLedger->save();
                    if ($feeLedger->save()) {
                        @StudentModel::where("INDEXNO",  $item->StudentID)->orWhere("STNO",  $item->StudentID)->update(array("BILL_OWING" => $bill_owing, "BILLS" => $bill));

                        $this->updateReceipt();
                    }


                }
            }
            else{
                continue;
            }
        }


    }

    public function pushData(){
        $academicDetails=$this->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;
        // $data =  $this->fetchData("https://www.zenithbank.com.gh/realtimenotification/api/bankpaydetail");
        foreach ($data->records as $item)
        {
            //freshers
            $student=Models\ApplicantModel::where("APPLICATION_NUMBER",$item->StudentID)->first();
            if(!empty($student->APPLICATION_NUMBER)) {
                $program=$student->PROGRAMME_ADMITTED;
                $ptype=$this->getProgrammeType($program);
                if($ptype=="NON TERTIARY"){
                    $level="100NT";
                }
                elseif($ptype=="HND"){
                    $level="100H";
                }
                elseif($ptype=="BTECH"){
                    $level="100BTT";
                }
                else{
                    $level="500";
                }
                //dd($level);
                if($program=="MTECHT"||$program=="MTECHP"||$program=="MTECHG"){
                    $fee = $this->getYearBillLevelPostgraduate($year, $level, $program);
                }
                else{
                    $fee = $this->getYearBillLevel100($year, $level, $program);
                }


                if ($student->ADMISSION_FEES<= $item->Amount) {
                    $details = "Full payment";

                } else {
                    $details = "Part payment";
                }
                $date=$item->PaymentDate;
                $checker=Models\FeePaymentModel::where("SEMESTER",$sem)
                    ->where("YEAR",$year)->where("INDEXNO",$item->StudentID)
                    ->where("CHECKER",$date)->get();
                if(empty($checker)) {
                    $feeLedger = new Models\FeePaymentModel();
                    $feeLedger->INDEXNO = $item->StudentID;
                    $feeLedger->PROGRAMME = $student->PROGRAMME_ADMITTED;
                    $feeLedger->AMOUNT = $item->Amount;
                    $feeLedger->PAYMENTTYPE = "School Fees";
                    $feeLedger->PAYMENTDETAILS = $details;
                    $feeLedger->BANK_DATE = date("Y-m-d");
                    $feeLedger->CHECKER = $checker;
                    $feeLedger->LEVEL = $level;
                    $feeLedger->RECIEPIENT = 751999;
                    $feeLedger->BANK = $item->AccountNumber;
                    $feeLedger->TRANSACTION_ID = rand();
                    $feeLedger->RECEIPTNO = $this->getReceipt();
                    $feeLedger->YEAR = $year;
                    $feeLedger->FEE_TYPE = "School Fees";
                    $feeLedger->SEMESTER = $sem;
                    $feeLedger->save();


                    //$message = "Online credential: visit records.ttuportal.com with $indexno as your username  and $real as password and follow the course registration steps.";


                    // @$this->firesms($message, $student->PHONE, $student->APPLICATION_NUMBER);
                    $this->updateReceipt();


                    $sql = Models\StudentModel::where("INDEXNO", $student->APPLICATION_NUMBER)->first();
                    if (empty($sql)) {
                        /////////////////////////////////////////////////////


                        $query = new Models\StudentModel();
                        $query->YEAR = $level;
                        $query->LEVEL = $level;
                        $query->FIRSTNAME = $student->FIRSTNAME;
                        $query->SURNAME = $student->SURNAME;
                        $query->OTHERNAMES = $student->OTHERNAME;
                        $query->TITLE = $student->TITLE;
                        $query->SEX = $student->GENDER;
                        $query->DATEOFBIRTH = $student->DOB;
                        $query->NAME = $student->NAME;
                        $query->AGE = $student->AGE;

                        $query->MARITAL_STATUS = $student->MARITAL_STATUS;
                        $query->HALL = $student->HALL_ADMITTED;
                        $query->ADDRESS = $student->ADDRESS;
                        $query->RESIDENTIAL_ADDRESS = $student->RESIDENTIAL_ADDRESS;
                        $query->EMAIL = $student->EMAIL;
                        $query->PROGRAMMECODE = $student->PROGRAMME_ADMITTED;
                        $query->TELEPHONENO = $student->PHONE;
                        $query->COUNTRY = $student->NATIONALITY;
                        $query->REGION = $student->REGION;
                        $query->RELIGION = $student->RELIGION;
                        $query->HOMETOWN = $student->HOMETOWN;
                        $query->GUARDIAN_NAME = $student->GURDIAN_NAME;
                        $query->GUARDIAN_ADDRESS = $student->GURDIAN_ADDRESS;
                        $query->GUARDIAN_PHONE = $student->GURDIAN_PHONE;
                        $query->GUARDIAN_OCCUPATION = $student->GURDIAN_OCCUPATION;
                        $query->DISABILITY = $student->PHYSICALLY_DISABLED;
                        $query->STATUS = "In School";
                        $query->SYSUPDATE = "1";


                        $query->BILLS = $student->ADMISSION_FEES;
                        $query->BILL_OWING = $student->ADMISSION_FEES - $item->Amount;
                        $query->STNO = $student->APPLICATION_NUMBER;
                        $query->INDEXNO = $student->APPLICATION_NUMBER;
                        $query->save();
                    }
                    $this->getPassword($student->APPLICATION_NUMBER);

                    //$message = "Online credential: visit records.ttuportal.com with $indexno as your username  and $real as password and follow the course registration steps.";


                    // @$this->firesms($message, $student->PHONE, $student->APPLICATION_NUMBER);
                }
            }
            // else continues students
            // else{

            /* $oldStudent=Models\StudentModel::where("STNO",$item->StudentID)->orWhere("INDEXNO",$item->StudentID)->first();
             $level=$oldStudent->LEVEL;
             $index=$oldStudent->INDEXNO;
             $program=$oldStudent->PROGRAMMECODE;
             $bill=$this->getYearBill( $year, $level, $program);

             $bill_owing=$bill-$item->Amount;
             if ($bill<= $item->Amount) {
                 $details = "Full payment";

             } else {
                 $details = "Part payment";
             }

              $date=$item->PaymentDate;
         $checker=Models\FeePaymentModel::where("SEMESTER",$sem)
             ->where("YEAR",$year)->where("INDEXNO",$item->StudentID)
             ->where("CHECKER",$date)->get();
         if(empty($checker)) {
             $feeLedger = new Models\FeePaymentModel();
             $feeLedger->INDEXNO = $index;
             $feeLedger->PROGRAMME = $program;
             $feeLedger->AMOUNT = $item->Amount;
             $feeLedger->PAYMENTTYPE = "School Fees";
             $feeLedger->PAYMENTDETAILS =$details;
             $feeLedger->BANK_DATE = date("Y-m-d");
              $feeLedger->CHECKER = $checker;
             $feeLedger->LEVEL = $level;
             $feeLedger->RECIEPIENT = 751999;
             $feeLedger->BANK = $item->AccountNumber;
             $feeLedger->TRANSACTION_ID =rand( );
             $feeLedger->RECEIPTNO = $this->getReceipt();
             $feeLedger->YEAR = $year;
             $feeLedger->FEE_TYPE = "School Fees";
             $feeLedger->SEMESTER = $sem;
             $feeLedger->save();
             if ($feeLedger->save()) {
                 @StudentModel::where("INDEXNO", $item->StudentID)->orWhere("STNO", $item->StudentID)->update(array("BILL_OWING" => $bill_owing, "BILLS" => $bill));

                 $this->updateReceipt();
             }

         }

         }*/









        }
    }

    public  function getPassword($indexno)
    {
        $data = Models\StudentModel::where("STNO", $indexno)->orWhere("INDEXNO", $indexno)->first();
        $que = Models\PortalPasswordModel::where("student", $data->ID)->orWhere("username", $data->INDEXNO)->first();
        $ptype=$this->getProgrammeType($data->PROGRAMMECODE);
        if($ptype=="NON TERTIARY"){
            $level="100NT";
        }
        elseif($ptype=="HND"){
            $level="100H";
        }
        elseif($ptype=="BTECH"){
            $level="100BTT";
        }
        elseif($ptype=="DEGREE"){
            $level="100B";
        }
        else{
            $level="500MT";
        }
        $program = $data->PROGRAMMECODE;
        if (empty($que) && !empty($indexno)) {
            $studentID=$this->getStudentIDfromIndexno($indexno);
            $str = 'abcdefhkmnprtuvwxy34678abcdefhkmnprtuvwxy34678';
            $shuffled = str_shuffle($str);
            $vcode = substr($shuffled, 0, 9);
            $real = strtoupper($vcode);
            $level = $level;
            Models\PortalPasswordModel::create([
                'username' => $indexno,
                'real_password' => $real,
                'student' => $studentID,
                'level' => $level,
                'programme' => $program,
                'biodata_update' => '1',
                'password' => bcrypt($real),
            ]);

        }
        else{
            Models\PortalPasswordModel::where("username",$indexno)->update(array("level"=>$level,"programme"=>$program));
        }
    }
    public function getReceipt() {
        \DB::beginTransaction();
        try {
            $receiptno_query = Models\ReceiptModel::first();
            $receiptno = date('Y') . str_pad($receiptno_query->no, 5, "0", STR_PAD_LEFT);
            \DB::commit();
            return $receiptno;
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    public function updateReceipt() {
        \DB::beginTransaction();
        try {
            $query = Models\ReceiptModel::first();

            $result = $query->increment("no");
            if ($result) {
                \DB::commit();
            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function generateIndexNumbers()
    {
        $sql=Models\StudentModel::select("INDEXNO","PROGRAMMECODE")->groupBy("PROGRAMMECODE")->get();
        foreach ($sql as $row){
            $index=new Models\IndexNumberModel();
            $index->programme=$row->PROGRAMMECODE;

            $program=$row->PROGRAMMECODE;

            if (strpos($program, "H") == 0) {
                $index->code="07".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";
            } elseif (strpos($program, "D") == 0 || strpos($program, "C") == 0|| strpos($program, "E") == 0) {
                $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";
            } elseif (strpos($program, "A") == 0) {
                $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";
            }
            elseif (strpos($program, "B") == 0) {

                $index->code="075".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";
            }

            elseif($program=="MTECHT"||$program=="MTECHP"||$program=="MTECHG"){
                //$index->code="07".substr(date("Y"),2,2);
            }
            else {
                $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";
            }




            $index->year=date("Y");
            $index->save();
        }
    }
    public function getSittingGrade($applicant,$sit){
        $data=Models\ExamResultsModel::where("APPLICATION_NUMBER",$applicant)
            ->where("SITTING",$sit)->get();
        $subjects=array();
        if(!empty($data)) {



            $no = 1;
            foreach ($data as $value) {
                //array_push($subjects,@$value->subject->NAME . "=" . @$value->GRADE) ;

                ?>

                <?php echo $no;?>
                <?php echo @$value->subject->NAME;?>
                <?php echo @$value->GRADE; ?>
                <br/>



                <?php
                $no++;
            }


            if (!empty($subjects)) {
                return $subjects;
            }
        }
        else{
            return "None";
        }
    }
}
