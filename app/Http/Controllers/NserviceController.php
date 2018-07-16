<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\StudentModel;
use App\Models\ProgrammeModel;
use App\Models;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Excel;
class NserviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->generateAccounts();
//        $query=  \App\Models\StudentModel::where("LEVEL","!=","")->get();
//        foreach($query as $index=>$row){
//        $que=Models\PortalPasswordModel::where("username",$row->STNO)->first();  
//                  if(empty($que)){
//                   
//                   $real=strtoupper(str_random(9));
//                   
//                    Models\PortalPasswordModel::create([
//                    'username' => $row->STNO,
//                     'real_password' =>$real,
//                          'level' =>$row->LEVEL,
//                         'programme' =>$row->PROGRAMMECODE,
//                    'password' => bcrypt($real),
//                  ]);}
//        }
    }
    public function tpoly(Request $request) {
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
        $query=  \DB::table('table79')->get();
        foreach($query as $index=>$row){

            if($row->COL5){
                $gender="Male";
            }
            else{
                $gender="Female";
            }
            $new_bedroom=array(
                'indexno' => $row->COL2,
                'stno' => $row->COL2,
                'surname' =>$row->COL3,
                'firstname' =>$row->COL4,
                'sex' =>$gender,
                'address' =>$row->COL8,
                'telephoneno' =>$row->COL7,
                'LEVEL' =>"100H",
                'YEAR' =>"100H",
                'PROGRAMMECODE' =>"HIT",
                'NAME' => $row->COL3." , ".$row->COL4,
            );
            $bedroom = new Models\StudentModel($new_bedroom);
            $bedroom->save();

        }
    }
    public function generateAccounts() {
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
//         $user=  Models\PortalPasswordModel::where('year','2016/2017')->where('level','100H')->get();
//         foreach($user as $users=>$row){
//             
//             $student=$row->username;
//             $password=  strtoupper(str_random(9));
//             $hashedPassword = bcrypt($password);
//             
//             Models\PortalPasswordModel::where('username',$student)->where('level','100')->update(array("password" => $hashedPassword,'real_password'=>$password));
//             
//         } 
        $query=  \App\Models\StudentModel::where("LEVEL","100H")->where("PROGRAMMECODE","HIT")->get();
        foreach($query as $index=>$row){
            $que=Models\PortalPasswordModel::where("username",$row->INDEXNO)->first();
            if(empty($que)){
                $str = 'abcdefhkmnprtuvwxyz234678';
                $shuffled = str_shuffle($str);
                $vcode = substr($shuffled,0,9);
                $real=strtoupper($vcode);

                Models\PortalPasswordModel::create([
                    'username' => $row->INDEXNO,
                    'real_password' =>$real,
                    'level' =>$row->LEVEL,
                    'programme' =>$row->PROGRAMMECODE,
                    'password' => bcrypt($real),
                ]);}
        }
    }
    public function index(Request $request, SystemController $sys) {

        if($request->user()->isSupperAdmin || @\Auth::user()->role=="FO"  || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning" ||  @\Auth::user()->department=="top"  || @\Auth::user()->department == 'Examination'||  @\Auth::user()->department=="qa"){
            $student = StudentModel::query();
        }
        elseif (@\Auth::user()->role=="Registrar") {
            $student = StudentModel::where('PROGRAMMECODE', '!=', '')->whereHas('programme', function($q) {
                $q->whereHas('departments', function($q) {
                    $q->whereIn('FACCODE', array(@\Auth::user()->department));
                });
            }) ;
        }
        elseif (@\Auth::user()->department=="Finance") {
            $student = StudentModel::where("STATUS","In School");
        }
        else{
            $departmentArray=explode(",",@\Auth::user()->department);
            $student = StudentModel::where('PROGRAMMECODE', '!=', '')->whereHas('programme', function($q)use($departmentArray) {
                $q->whereHas('departments', function($q)use($departmentArray) {
                    $q->whereIn('DEPTCODE',  $departmentArray);
                });
            }) ;
        }

        if ($request->has('department') && trim($request->input('department')) != "") {
            $student->whereHas('programme', function($q)use ($request) {
                $q->whereHas('departments', function($q)use ($request) {
                    $q->whereIn('DEPTCODE', [$request->input('department')]);
                });
            });
        }
        if ($request->has('type') && trim($request->input('type')) != "") {
            $student->whereHas('programme', function($q)use ($request) {

                $q->where('TYPE', [$request->input('type')]);

            });
        }

        if ($request->has('school') && trim($request->input('school')) != "") {
            $student->whereHas('programme', function($q)use ($request) {
                $q->whereHas('departments', function($q)use ($request) {

                    $q->whereHas('school', function($q)use ($request) {
                        $q->whereIn('FACCODE', [$request->input('school')]);
                    });
                });
            });
        }



        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $student->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $student->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('level') && trim($request->input('level')) != "") {
            $student->where("LEVEL", $request->input("level", ""));
        }
        if ($request->has('qa') && trim($request->input('qa')) != "") {
            $student->where("QUALITY_ASSURANCE", $request->input("qa", ""));
        }

        if ($request->has('status') && trim($request->input('status')) != "") {
            $student->where("STATUS", $request->input("status", ""));
        }
        if ($request->has('group') && trim($request->input('group')) != "") {
            $student->where("GRADUATING_GROUP", $request->input("group", ""));
        }
        if ($request->has('nationality') && trim($request->input('nationality')) != "") {
            $student->where("COUNTRY", $request->input("country", ""));
        }
        if ($request->has('region') && trim($request->input('region')) != "") {
            $student->where("REGION", $request->input("region", ""));
        }
        if ($request->has('gender') && trim($request->input('gender')) != "") {
            $student->where("SEX", $request->input("gender", ""));
        }
        if ($request->has('sms') && trim($request->input('sms')) != "") {
            $student->where("SMS_SENT", $request->input("sms", ""));
        }
        if ($request->has('hall') && trim($request->input('hall')) != "") {
            $student->where("HALL", $request->input("hall", ""));
        }
        if ($request->has('register') && trim($request->input('register')) != "") {
            $student->where("REGISTERED", $request->input("register", ""));
        }
        if ($request->has('religion') && trim($request->input('religion')) != "") {
            $student->where("RELIGION", $request->input("religion", ""));
        }
        if ($request->has('search') && trim($request->input('search')) != "" && trim($request->input('by')) != "") {
            // dd($request);
            $student->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%")
            ;
        }
        $data = $student->orderBy('LEVEL')->orderBy('PROGRAMMECODE')->orderBy('INDEXNO')->paginate(500);

        $request->flashExcept("_token");

        \Session::put('students', $data);
        return view('students.index')->with("data", $data)
            ->with('year', $sys->years())
            ->with('nationality', $sys->getCountry())
            ->with('halls', $sys->getHalls())
            ->with('level', $sys->getLevelList())
            ->with('religion', $sys->getReligion())
            ->with('region', $sys->getRegions())
            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());

    }
    public function sms(Request $request, SystemController $sys){
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
        $message = $request->input("message", "");
        $query = \Session::get('students');



        foreach($query as $rtmt=> $member) {
            $NAME = $member->NAME;
            $FIRSTNAME = $member->FIRSTNAME;
            $SURNAME = $member->SURNAME;
            $PROGRAMME = $sys->getProgram($member->PROGRAMME);
            $INDEXNO = $member->INDEXNO;
            $CGPA = $member->CGPA;
            $BILLS = $member->BILLS;
            $BILL_OWING = $member->BILL_OWING;
            $PASSWORD=$sys->getStudentPassword($INDEXNO);
            $newstring = str_replace("]", "", "$message");
            $finalstring = str_replace("[", "$", "$newstring");
            eval("\$finalstring =\"$finalstring\" ;");
            if ($sys->firesms($finalstring,$member->TELEPHONENO,$member->INDEXNO)) {

                StudentModel::where("INDEXNO",$INDEXNO)->update(array("SMS_SENT","1"));

            } else {
                // return redirect('/students')->withErrors("SMS could not be sent.. please verify if you have sms data and internet access.");
            }
        }
        return redirect('/students')->with('success','Message sent to students successfully');

        \Session::forget('students');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {

        return view('students.index');
    }
    public function anyData(Request $request)
    {

        $students = StudentModel::join('tpoly_programme', 'tpoly_students.PROGRAMMECODE', '=', 'tpoly_programme.PROGRAMMECODE')
            ->select(['tpoly_students.ID', 'tpoly_students.NAME','tpoly_students.INDEXNO', 'tpoly_programme.PROGRAMME','tpoly_students.LEVEL','tpoly_students.INDEXNO','tpoly_students.SEX','tpoly_students.AGE','tpoly_students.TELEPHONENO','tpoly_students.COUNTRY','tpoly_students.GRADUATING_GROUP','tpoly_students.STATUS']);



        return Datatables::of($students)

            ->addColumn('action', function ($student) {
                return "<a href=\"edit_student/$student->INDEXNO/id\" class=\"\"><i title='Click to view student details' class=\"md-icon material-icons\">&#xE88F;</i></a>";
                // use <i class=\"md-icon material-icons\">&#xE254;</i> for showing editing icon
                //return' <td> <a href=" "><img class="" style="width:70px;height: auto" src="public/Albums/students/'.$student->INDEXNO.'.JPG" alt=" Picture of Employee Here"    /></a>df</td>';


            })
            ->editColumn('id', '{!! $ID!!}')
            ->addColumn('Photo', function ($student) {
                // return '<a href="#edit-'.$student->ID.'" class="md-btn md-btn-primary md-btn-small md-btn-wave-light waves-effect waves-button waves-light">View</a>';

                return' <a href="show_student/'.$student->INDEXNO.'/id"><img class="md-user-image-large" style="width:60px;height: auto" src="Albums/students/'.$student->INDEXNO.'.JPG" alt=" Picture of Student Here"    /></a>';


            })


            ->setRowId('id')
            ->setRowClass(function ($student) {
                return $student->ID % 2 == 0 ? 'uk-text-success' : 'uk-text-warning';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])

            ->make(true);

        //flash the request so it can still be available to the view or search form and the search parameters shown on the form
        //$request->flash();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(SystemController $sys)
    {
        $region=$sys->getRegions();
        $programme=$sys->getProgramList();
        $hall=$sys->getHalls();
        $religion=$sys->getReligion();
        return view('students.create')
            ->with('programme', $programme)
            ->with('country', $sys->getCountry())
            ->with('region', $region)
            ->with('hall',$hall)
            ->with('level', $sys->getLevelList())
            ->with('religion',$religion);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SystemController $sys)
    {

        set_time_limit(36000);
        /*transaction is used here so that any errror rolls
         *  back the whole process and prevents any inserts or updates
         */
        if($request->user()->isSupperAdmin || @\Auth::user()->role=="Dean" || @\Auth::user()->department=="top"|| @\Auth::user()->role=="HOD" || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department == "Admissions"){

            \DB::beginTransaction();

            $user = @\Auth::user()->id;

            $year=$request->input('year');

            $level= $year;

            $array=$sys->getSemYear();

            $fiscalYear=$array[0]->YEAR;
            $indexno = $request->input('indexno');
            $program = $request->input('programme');
            $gender = $request->input('gender');
            $category = $request->input('category');
            $hostel = $request->input('hostel');
            $hall = $request->input('halls');
            $dob = $request->input('dob');
            $gname = $request->input('gname');
            $gphone = $request->input('gphone');
            $goccupation = $request->input('goccupation');
            $gaddress = $request->input('gaddress');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $marital_status = $request->input('marital_status');
            $region = $request->input('region');
            $country = $request->input('nationality');
            $religion = $request->input('religion');
            $residentAddress = $request->input('contact');
            $address = $request->input('address');
            $hometown = $request->input('hometown');
            $nhis = $request->input('nhis');
            $type = $request->input('type');
            $disability = $request->input('disabilty');
            $title = $request->input('title');
            $age = $sys->age($dob, 'eu');
            $group = "";
            $fname = $request->input('fname');
            $bill= $request->input('bill');
            $lname = $request->input('surname');
            $othername = $request->input('othernames');

            $sql=  StudentModel::where("STNO",$indexno)->first();
            if(empty($sql)){
                /////////////////////////////////////////////////////

                $name = $lname . ' ' . $othername . ' ' . $fname;
                $query = new StudentModel();
                $query->YEAR = $year;
                $query->LEVEL = $level;
                $query->FIRSTNAME = $fname;
                $query->SURNAME = $lname;
                $query->OTHERNAMES = $othername;
                $query->TITLE = $title;
                $query->SEX = $gender;
                $query->DATEOFBIRTH = $dob;
                $query->NAME = $name;
                $query->AGE = $age;
                $query->GRADUATING_GROUP = $group;
                $query->MARITAL_STATUS = $marital_status;
                $query->HALL = $hall;
                $query->ADDRESS = $address;
                $query->RESIDENTIAL_ADDRESS = $residentAddress;
                $query->EMAIL = $email;
                $query->PROGRAMMECODE = $program;
                $query->TELEPHONENO = $phone;
                $query->COUNTRY = $country;
                $query->REGION = $region;
                $query->RELIGION = $religion;
                $query->HOMETOWN = $hometown;
                $query->GUARDIAN_NAME = $gname;
                $query->GUARDIAN_ADDRESS = $gaddress;
                $query->GUARDIAN_PHONE = $gphone;
                $query->GUARDIAN_OCCUPATION = $goccupation;
                $query->DISABILITY = $disability;
                $query->STATUS = "In School";
                $query->SYSUPDATE = "1";
                $query->NHIS = $nhis;
                $query->STUDENT_TYPE = $type;
                $query->TYPE = $category;

                $query->HOSTEL = $hostel;
                //$query->BILLS=$sys->getYearBill( $fiscalYear, $level, $program);
                // $query->BILL_OWING=$sys->getYearBill( $fiscalYear, $level, $program);
                $query->STNO =$indexno;
                $query->INDEXNO =$indexno;

                if($query->save()){
                    \DB::commit();
                    $que=Models\PortalPasswordModel::where("username",$indexno)->first();
                    if(empty($que)){
                        $program=$program;
                        $str = 'abcdefhkmnprtuvwxyz234678';
                        $shuffled = str_shuffle($str);
                        $vcode = substr($shuffled,0,9);
                        $real=strtoupper($vcode);
                        $level= $level;
                        Models\PortalPasswordModel::create([
                            'username' => $indexno,
                            'real_password' =>$real,
                            'level' =>$level,
                            'programme' =>$program,
                            'biodata_update' =>'1',
                            'password' => bcrypt($real),
                        ]);

                        $message = "Hi $fname, Please visit ttuportal.com to do update your biodata with $indexno as your username  and $real as password   ";

                        \DB::commit();
                        if ($sys->firesms($message, $phone, $indexno)) {

                        }
                    }
                    return redirect("/students")->with("success"," <span style='font-weight:bold;font-size:13px;'> student successfully added!</span> ");

                }else{

                    return redirect("/add_students")->with("error"," <span style='font-weight:bold;font-size:13px;'> student could not be added try again!</span>");


                }
            }
            else{
                return redirect("/add_students")->with("error"," <span style='font-weight:bold;font-size:13px;'>Please student exist in the system already!</span>");

            }
        } else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,  SystemController $sys,Request $request)
    {

        $region=$sys->getRegions();


        // make sure only students who are currently in school can update their data
        $query = StudentModel::where('ID', $id)->first();
        $programme=$sys->getProgramList();
        $hall=$sys->getHalls();
        $religion=$sys->getReligion();

        $trails=  Models\AcademicRecordsModel::where('student', $id)->where("grade","E")->paginate(100);

        return view('students.show')->with('student', $query)
            ->with('programme', $programme)
            ->with('country', $sys->getCountry())
            ->with('region', $region)
            ->with('hall',$hall)
            ->with('trail',$trails)
            ->with('religion',$religion);
    }
    public function uploadStaff(Request $request) {
        if($request->hasFile('file')){
            $file=$request->file('file');
            $user = \Auth::user()->id;

            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv','xlx','xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();
            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();

                dd($data);
                if(!empty($data) && $data->count()){

                    foreach ($data as $key => $value) {

                        $insert[] = ['fullName' => $value->name, 'staffID' => $value->staffID,'department'=>$value->Department,'grade'=>$value->grade,'designation'=>$value->position,'phone'=>$value->phone];

                    }

                    // dd($insert);
                    if(!empty($insert)){

                        \DB::table('tpoly_workers')->insert($insert);

                        // return redirect('/dashboard')->with("success",  " <span style='font-weight:bold;font-size:13px;'>Staff  successfully uploaded!</span> " );


                    }

                }

            }
            else{
                //  return redirect('/getStaffCSV')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload file format must be xlx,csv,xslx!</span> ");

            }
        }

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,  SystemController $sys,Request $request)
    {
        //
        if($request->user()->isSupperAdmin || @\Auth::user()->department=="top"  || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->role=="HOD" || @\Auth::user()->role=="Support" ||   @\Auth::user()->role=="Dean" || @\Auth::user()->department=="Rector" || @\Auth::user()->department=="Admissions" || @\Auth::user()->department=="Planning"){

            $query = StudentModel::where('ID', $id)->where('STATUS','In School')->first();
            //dd( $query );
        }
        else{
            $query = StudentModel::where('ID', $id)->whereHas('programme', function($q) {
                $q->whereHas('departments', function($q) {
                    $q->whereIn('DEPTCODE', array(@\Auth::user()->department));
                });
            })->first();

        }
        $region=$sys->getRegions();


        // make sure only students who are currently in school can update their data
        $programme=$sys->getProgramList();
        $hall=$sys->getHalls();
        $religion=$sys->getReligion();
        return view('students.edit')->with('data', $query)
            ->with('programme', $programme)
            ->with('country', $sys->getCountry())
            ->with('region', $region)
            ->with('level', $sys->getLevelList())
            ->with('hall',$hall)
            ->with('religion',$religion);
    }
    public function gad()
    {
        //
        return view('autocomplete');
    }

    public function updateLevel()
    {
        $students=  StudentModel::query()->where('level'," ")->get();

        foreach ($students as $key => $row) {
            //$student= new StudentModel();
            $indexno=$row->INDEXNO;

            $level= substr($indexno, 2,2);
            //dd($level);
            if($level=='15'){
                StudentModel::where('INDEXNO','LIKE','0715%')->update(array("LEVEL"=>'100',"YEAR"=>'1'));
            }
            elseif($level=='14'){

                StudentModel::where('INDEXNO','LIKE','0714%')->update(array("LEVEL"=>'200',"YEAR"=>'2'));

            }
            elseif($level=='13'){

                StudentModel::where('INDEXNO','LIKE','0713%')->update(array("LEVEL"=>'300',"YEAR"=>'3'));
            }
            else{

                //StudentModel::where('LEVEL','=','')->update(array("STATUS"=>'Alumni'));
            }

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, SystemController $sys)
    {
        if($request->user()->isSupperAdmin || @\Auth::user()->role=="HOD" || @\Auth::user()->role=="Dean"||@\Auth::user()->department=="top"  || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->role=="Support" || @\Auth::user()->department=="Rector"){
            {
                set_time_limit(36000);
                /*transaction is used here so that any errror rolls
                 *  back the whole process and prevents any inserts or updates
                 */

                \DB::beginTransaction();
                $year=$request->input('year');
                $id=$request->input('id');

                $level=$request->input('year');
                $indexno=$request->input('indexno');

                $program=$request->input('programme');
                $gender=$request->input('gender');
                $category=$request->input('category');
                $hostel=$request->input('hostel');
                $hall=$request->input('halls');
                $dob=$request->input('dob');
                $gname=$request->input('gname');
                $gphone=$request->input('gphone');
                $goccupation=$request->input('goccupation');
                $gaddress=$request->input('gaddress');
                $email=$request->input('email');
                $phone=$request->input('phone');
                $status=$request->input('status');
                $marital_status=$request->input('marital_status');
                $region=$request->input('region');
                $country=$request->input('nationality');
                $religion=$request->input('religion');
                $residentAddress=$request->input('contact');
                $address=$request->input('address');
                $hometown=$request->input('hometown');
                $nhis=$request->input('nhis');
                $type=$request->input('type');
                $disability=$request->input('disabilty');
                $title=$request->input('title');
                $age=$sys->age($dob,'eu');
                $group=0;
                $firstname=$request->input('fname');
                $surname=$request->input('surname');
                $othername=$request->input('othernames');
                if( @\Auth::user()->role=="Support"){
                    $query= StudentModel::where("ID",$id)->update(array(
                        "FIRSTNAME"=>$firstname,
                        "SURNAME"=>$surname,
                        "NAME"=>$surname." ".$othername." ".$firstname,
                        "OTHERNAMES"=>$othername));

                }
                else{
                    $array=$sys->getSemYear();

                    $fiscalYear=$array[0]->YEAR;
                    $sem=$array[0]->SEMESTER;
//            // $bill=$sys->getYearBill($fiscalYear, $level, $program);
//        // $bill_owing=$sys->getYearBill($fiscalYear, $level, $program);
//         $test=@StudentModel::where("ID",$id)->select("BILLS","BILL_OWING","PROGRAMMECODE")->first();
//         if(empty($test) || $test->PROGRAMMECODE!=$program)
//         {
//             $owe=$test->BILL_OWING+ ($bill-$test->BILLS);
//              StudentModel::where("ID",$id)->update(array(
//                 "BILLS"=>$bill,
//                  "BILL_OWING"=>$owe
//                  ));
//         }
                    $query= StudentModel::where("ID",$id)->update(array(
                        "FIRSTNAME"=>strtoupper($firstname),
                        "SURNAME"=>strtoupper($surname),
                        "NAME"=>strtoupper($surname." ".$othername." ".$firstname),
                        "OTHERNAMES"=>strtoupper($othername),
                        "TITLE"=>strtoupper($title),
                        "SEX"=>strtoupper($gender),
                        "DATEOFBIRTH"=>$dob,
                        "AGE"=>$age,
                        "LEVEL"=>$level,
                       // "GRADUATING_GROUP"=>$group,
                        "MARITAL_STATUS"=>strtoupper($marital_status),
                        "HALL"=>strtoupper($hall),
                        "ADDRESS"=>strtoupper($address),
                        "RESIDENTIAL_ADDRESS"=>strtoupper($residentAddress),
                        "EMAIL"=>strtoupper($email),
                        "TELEPHONENO"=>$phone,
                        "COUNTRY"=>strtoupper($country),
                        "REGION"=>strtoupper($region),
                        "RELIGION"=>strtoupper($religion),
                        "HOMETOWN"=>strtoupper($hometown),
                        "GUARDIAN_NAME"=>strtoupper($gname),
                        "GUARDIAN_ADDRESS"=>strtoupper($gaddress),
                        "GUARDIAN_PHONE"=>$gphone,
                        "GUARDIAN_OCCUPATION"=>strtoupper($goccupation),
                        "DISABILITY"=>strtoupper($disability),
                        "PROGRAMMECODE"=>strtoupper($program),
                        "STATUS"=>"In School",
                        "NHIS"=>$nhis,
                        "STUDENT_TYPE"=>strtoupper($type),
                        "TYPE"=>strtoupper($category),
                        "HOSTEL"=>$hostel,
                        "INDEXNO"=>$indexno,

                        "SYSUPDATE"=>"1",


                    ));
                    Models\PortalPasswordModel::where("username",$indexno)->update(array("level"=>$level,"programme"=>$program));
                    Models\FeePaymentModel::where("INDEXNO",$indexno)->update(array("LEVEL"=>$level,"PROGRAMME"=>$program));

                    \DB::commit();
                }

                if(!$query){
                    return redirect("/students")->withErrors("  N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> data</span>could not be updated!");
                }else{

//           Models\FeePaymentModel::where("INDEXNO",$indexno)->where("YEAR",$fiscalYear)->where("SEMESTER",$sem)->update(array("LEVEL"=>$level,"PROGRAMME"=>$program));
//         \DB::commit();
                    return redirect("/students")->with("success"," <span style='font-weight:bold;font-size:13px;'>data successfully updated!</span> ");

                }}}
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function showUploadForm() {
        return view("students.upload");
    }
    public function applicantUploadForm() {
        return view("students.applicantUpload");
    }
    public function indexNumberUploadForm() {
        return view("students.indexUpload");
    }
    /*
     * upload continuing students 
     */
    public function uploadIndexNumber(Request $request, SystemController $sys) {
        set_time_limit(36000);


        $user = \Auth::user()->id;
        $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
        $file = $request->file('file');
        $path = $request->file('file')->getRealPath();

        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, $valid_exts)) {

            $data = Excel::load($path, function($reader) {

            })->get();


            foreach ($data as $key => $value) {

                $num = count($data);
                $stno =$value->stno;
                $indexno =$value->indexno;



                @StudentModel::where('STNO', $stno)->update(array("INDEXNO"=>$indexno));
                @Models\AcademicRecordsModel::where('indexno', $stno)->update(array("indexno"=>$indexno));
                @Models\FeePaymentModel::where('INDEXNO', $stno)->update(array("INDEXNO"=>$indexno));
            }

            return redirect('/students')->with("success", " <span style='font-weight:bold;font-size:13px;'>$num student(s) index numbers uploaded  successfully!</span> ");




        } else {
            return redirect('/upload_students')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
        }

    }

    public function uploadData(Request $request, SystemController $sys) {
        set_time_limit(36000);


        $user = \Auth::user()->id;
        $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
        $file = $request->file('file');
        $path = $request->file('file')->getRealPath();

        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, $valid_exts)) {

            $data = Excel::load($path, function($reader) {

            })->get();


            foreach ($data as $key => $value) {

                $num = count($data);
                $stno =$value->stno;
                $indexno =$value->indexno;
                $gender= $value->gender;
                $balance= $value->balance;
                $fname= $value->fname;
                $lname= $value->surname;
                $phone= $value->telephone;
                $password= $value->pass;
                $name = $value->name;
                $program = $value->program;
                $level= $value->level;
                $address= $value->address;
                $group = $sys->graduatingGroup($indexno);


                // first check if the students exist in the system if true then update else insert
                $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
                if (in_array($program, $programme)) {

                    $testQuery = StudentModel::where('INDEXNO', $indexno)->first();
                    if (empty($testQuery)) {
                        // dd($level);

                        $student = new StudentModel();
                        $student->INDEXNO = $indexno;
                        $student->STNO = $stno;
                        $student->LEVEL = $level;
                        $student->YEAR = $level;
                        $student->SEX = $gender;
                        $student->NAME = $name;
                        $student->FIRSTNAME= $fname;
                        $student->SURNAME= $lname;
                        $student->TELEPHONENO= $phone;
                        $student->BILL_OWING = $balance;
                        $student->NAME = $name;
                        $student->PROGRAMMECODE = $program;
                        $student->STATUS = "In School";
                        $student->SYSUPDATE = "1";
                        $student->ADDRESS= $address;
                        $student->GRADUATING_GROUP = $group;
                        $student->save();
                        Models\PortalPasswordModel::create([
                            'username' => $indexno,
                            'real_password' =>$password,
                            'level' =>$level,
                            'programme' =>$program,
                            'password' => bcrypt($password),
                        ]);
                    } else {

                        StudentModel::where('INDEXNO', $indexno)->update(array("STNO"=>$stno,"ADDRESS"=>$address,"LEVEL" => @$level, "YEAR" => $level, "PROGRAMMECODE" => $program, "SYSUPDATE" => "1", "STATUS" => 'In School', "NAME" => $name, "GRADUATING_GROUP" => $group,  "BILL_OWING" => $balance));

                    }
                } else {
                    return redirect('/upload_students')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");
                }
            }
            return redirect('/students')->with("success", " <span style='font-weight:bold;font-size:13px;'>$num student(s) uploaded  successfully!</span> ");




        } else {
            return redirect('/upload_students')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
        }

    }
    public function uploadApplicants(Request $request,  SystemController $sys) {

        if($request->hasFile('file')){
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv','xls','xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();

                if(!empty($data) && $data->count()){
                    $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db

                    foreach($data as $key => $value) {

                        $name=$value->surname." ".$value->othernames." ".$value->firstname;

                        if (in_array($value->program_code, $programme)) {

                            $testQuery=StudentModel::where('STNO', $value->admission_number)->first();
                            if(empty($testQuery)){

                                if($value->gender=="M"){
                                    $gender="Male";
                                }
                                else{
                                    $gender="Female";
                                }

                                $student=new StudentModel();
                                // $student->INDEXNO=$indexno;
                                $student->LEVEL=$value->level;
                                $student->YEAR= $value->year;
                                $student->NAME=$name;
                                $student->PROGRAMMECODE=$value->program_code;
                                $student->STATUS="Admitted";
                                $student->SYSUPDATE="1";
                                $student->OTHERNAMES=$value->othernames;
                                $student->SURNAME=$value->surname;
                                $student->FIRSTNAME=$value->firstname;
                                $student->ADDRESS=$value->address;
                                $student->RESIDENTIAL_ADDRESS=$value->residential;
                                $student->SEX=$gender;
                                $student->TELEPHONENO=$value->phone;
                                $student->STNO=$value->admission_number;
                                $student->BILLS=$value->bill;
                                $student->BILL_OWING=$value->owing;
                                $student->save();

                                \DB::commit();
                            }
                            else{

                                StudentModel::where('STNO', $value->admission_number)->update(array( "YEAR" =>$value->year, "PROGRAMMECODE" =>$value->program_code, "SYSUPDATE"=>"1", "STATUS" => 'Admitted',"NAME"=>$name,"BILLS"=>$value->bill,"BILL_OWING"=>$value->owing,"SEX"=>$value->gender,"FIRSTNAME"=>$value->firstname,"TELEPHONENO"=>$value->phone,"ADDRESS"=>$value->address,"SURNAME"=>$value->surname,"RESIDENTIAL_ADDRESS"=>$value->residential));
                                \DB::commit();
                            }

                        }
                        else{

                            return redirect('/upload_applicants')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contains unrecognised programme codes.. please check your programme code!</span> ");

                        }

                    }


                    return redirect('/students')->with("success", " <span style='font-weight:bold;font-size:13px;'>File upload successfull</span> ");

                }
                else{
                    return redirect('/upload_applicants')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload an excel file!</span> ");

                }
            }
        }


    }
//    public function uploadApplicants(Request $request, SystemController $sys) {
//        set_time_limit(36000);
//         \DB::beginTransaction();
//        try {
//
//            $user = \Auth::user()->id;
//            $valid_exts = array('csv'); // valid extensions
//            $file = $request->file('file');
//            $name = time() . '-' . $file->getClientOriginalName();
//            if (!empty($file)) {
//
//                $ext = strtolower($file->getClientOriginalExtension());
//                $destination = public_path() . '\uploads';
//                if (in_array($ext, $valid_exts)) {
//                    // Moves file to folder on server
//                    // $file->move($destination, $name);
//                    if (@$file->move($destination, $name)) {
//
//
//
//                        $handle = fopen($destination . "/" . $name, "r");
//                        //  print_r($handle);
//                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//
//                            $num = count($data);
//
//                            for ($c = 0; $c < $num; $c++) {
//                                $col[$c] = $data[$c];
//                            }
//
//                             $indexNo_query = Models\IndexNoModel::first();
//                	
//                            $indexno ="07".date("y").$indexNo_query->no;
//                            $year = $col[0];
//                            $level = $col[1];
//                            $surname = $col[2];
//                            $othername= $col[3];
//                            $name = $col[4];
//                            $gender = $col[5];
//                            $program = trim($col[7]);
//                            $phone = "0".$col[8];
//                            $contact= $col[9];
//                            $resident= $col[10];
//                            $group= $sys->graduatingGroup($indexno);
//                            $bill= $col[11];
//                            $owing = $col[12];
//                            $admissionNum=$col[13];
//                            
//                           // print_r($program)."<br/>"
//                        // first check if the students exist in the system if true then update else insert
//                     $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
//                            if (array_search($program, $programme)) {
//         
//                        $testQuery=StudentModel::where('STNO', $admissionNum)->first();
//                          if(empty($testQuery)){
//                              
//                          
//                                $student=new StudentModel();
//                               // $student->INDEXNO=$indexno;
//                                $student->LEVEL=@$level;
//                                $student->YEAR=$year;
//                                $student->NAME=$name;
//                                $student->PROGRAMMECODE=$program;
//                                $student->STATUS="In School";
//                                $student->SYSUPDATE="1";
//                                 
//                                $student->GRADUATING_GROUP=$group;
//                                $student->SURNAME=$surname;
//                                $student->FIRSTNAME=$othername;
//                                $student->RESIDENTIAL_ADDRESS=$resident;
//                                $student->ADDRESS=$contact;
//                                $student->SEX=$gender;
//                                $student->TELEPHONENO=$phone;
//                                $student->STNO=$admissionNum;
//                                $student->save();
//                                 $indexNo_query->increment("no", 1);
//                                 \DB::commit();
//                              }
//                             else{
//                                
//                                         StudentModel::where('INDEXNO', $indexno)->update(array("LEVEL" =>@$level, "YEAR" => $year, "PROGRAMMECODE" => $program, "SYSUPDATE"=>"1", "STATUS" => 'In School',"NAME"=>$name,"GRADUATING_GROUP"=>$group,"BILLS"=>$bill,"BILL_OWING"=>$owing,"SEX"=>$gender,"FIRSTNAME"=>$othername,"TELEPHONENO"=>$phone,"RESIDENT_ADDRESS"=>$resident,"ADDRESS"=>$contact,"SURNAME"=>$surname));
//                                        \DB::commit();
//                               }
//                             
//                            }
//                            else{
//                               return redirect('/upload_applicants')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contains unrecognised programme codes.. please check your programme code!</span> ");
//                      
//                            }
//                                               
//                        } 
//                              
//                       
//                        fclose($handle);
//                        return redirect('/students')->with("success",  " <span style='font-weight:bold;font-size:13px;'>student uploaded  successfully!</span> ");
//                             
//                    }
//                } else {
//                     return redirect('/upload_applicants')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only csv (comma delimited ) file is accepted!</span> ");
//                                   
//                }
//            } else {
//                 return redirect('/upload_students')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload a csv file!</span> ");
//                    
//        }}
//    catch (\Exception $e) {
//            \DB::rollback();
//        }
//    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendOwingSMS(Request $request, SystemController $sys) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();


                if (!empty($data) && $data->count()) {
                    //$programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db

                    foreach ($data as $key => $value) {


                        $name= $value->name;


                        $owing=$value->owing;

                        $level= $value->level;
                        $program= $value->program;
                        $testQuery=StudentModel::where("name","LIKE"."%".$name."%")->where("level",$level)->where("programmecode",$program)->first();
                        // dd($testQuery);
                        if(!empty($testQuery)){
                            @StudentModel::where("name","LIKE"."%".$name."%")->where("level",$level)->where("programmecode",$program)->update(array("BILL_OWING"=>$owing));

                            $name=$testQuery->FIRSTNAME;
                            $index=$testQuery->INDEXNO;
                            $phone=$testQuery->PHONE;
                            print_r($name);
                            $message="Hi $name, please pay your remaining fees, $owing, before this Wednesday 14th June,2017 so as to be registered for the exams";
                            $sys->firesms($message, $phone, $index);
                        }
                    }
                }
            }
        }
    }
    public function generateIndexNo(Request $request,SystemController $sys){
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $dataGenerator=Models\FeePaymentModel::where("LEVEL","100H")->orWhere("LEVEL","100NT")
            ->orWhere("LEVEL","100BTT")->orWhere("LEVEL","500")->where("YEAR",$year)
            ->where("SEMESTER",$sem)->get();
        foreach($dataGenerator as $row){
            $index=$sys->assignIndex($row->PROGRAMME);
            Models\StudentModel::where("STNO",$row->INDEXNO)->where("INDEXNO",$row->INDEXNO)->update(array("INDEXNO"=>$index));
            Models\PortalPasswordModel::where("username",$row->INDEXNO)->update(array("username"=>$index));
        }
    }
    public function generateIndexNoProgram($program,Request $request,SystemController $sys){
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $dataGenerator=Models\StudentModel::where("LEVEL","100H")->where("PROGRAMMECODE",$program)->get();
        foreach($dataGenerator as $row){
            $index=$sys->assignIndex($program);
            Models\StudentModel::where("STNO",$row->INDEXNO)->update(array("INDEXNO"=>$index));
            Models\PortalPasswordModel::where("username",$row->INDEXNO)->update(array("username"=>$index));
        }
    }
    public function loadIndexNo(Request $request,SystemController $sys){
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();
                //dd($data);

                if (!empty($data) && $data->count()) {

                    foreach ($data as $key => $value) {


                        $indexno=$value->indexno;


                        $program=$value->program;


                        @StudentModel::where("indexno", $indexno)->update(array("PROGRAMMECODE" => $program));
                        @Models\PortalPasswordModel::where("username", $indexno)->update(array("programme" => $program));


                    }
                }
            }
        }
    }

    public function loadNewIndexNo(Request $request,SystemController $sys){
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();
                //dd($data);

                if (!empty($data) && $data->count()) {

                    foreach ($data as $key => $value) {


                        $indexno=$value->indexno;


                        $stno=$value->stno;
                        $check= @StudentModel::where("stno", $stno)->get();
                        if(empty( $check)) {
                            @StudentModel::where("stno", $stno)->update(array("INDEXNO" => $indexno));
                            @Models\PortalPasswordModel::where("username", $stno)->update(array("username" => $indexno));
                        }

                    }
                }
            }
        }
    }


    public function deleteWrong(Request $request, SystemController $sys) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function($reader) {

                })->get();
                //dd($data);

                if (!empty($data) && $data->count()) {

                    foreach ($data as $key => $value) {


                        $indexno=$value->indexno;


                        $program=$value->program;

                        $test=StudentModel::where("INDEXNO",$indexno)->get();
                        foreach($test as $row) {
                            @StudentModel::where("INDEXNO", $row->INDEXNO)->update(array("INDEXNO" => $row->STNO));
                            @Models\PortalPasswordModel::where("username", $row->INDEXNO)->update(array("username" => $row->STNO));

                        }
                    }
                }
            }
        }
    }

    public function uploadPaymentZenith(Request $request, SystemController $sys)
    {
        //dd($request);

        $academicDetails = $sys->getSemYear();
        $sem = $academicDetails[0]->SEMESTER;
        $year = $academicDetails[0]->YEAR;
        //$data =  $this->fetchData("https://www.zenithbank.com.gh/realtimenotification/api/bankpaydetail");
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $user = \Auth::user()->fund;
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions

            $path = $request->file('file')->getRealPath();

            if (in_array($ext, $valid_exts)) {
                $data = Excel::load($path, function ($reader) {

                })->get();
                //dd($data);

                if (!empty($data) && $data->count()) {

                    foreach ($data as $key => $item) {

                        //dd($data);
                        //freshers
                        //dd($item->studentId);
                        $indexno=$item->studentid;
                        if(substr($indexno,0,1)==7){
                            $indexno="0".$indexno;
                        }

                        else {
                            $indexno =   $indexno;
                        }

                        $oldStudent = Models\StudentModel::where("STNO", $indexno)->orWhere("INDEXNO", $indexno)->first();
                        if(!empty($oldStudent)) {
                            $level = $oldStudent->LEVEL;
                            $index = $oldStudent->INDEXNO;
                            $program = $oldStudent->PROGRAMMECODE;
                            $bill = $sys->getYearBill($year, $level, $program);

                            $bill_owing = $bill - $item->amount;
                            if ($bill <= $item->amount) {
                                $details = "Full payment";

                            } else {
                                $details = "Part payment";
                            }

                            $date = $item->PaymentDate;

                            $checker=Models\FeePaymentModel::where("INDEXNO",$index)->where("YEAR",$year)->where("FEE_TYPE","School Fees")->get();
                            if(empty($checker)){
                                $feeLedger = new Models\FeePaymentModel();
                                $feeLedger->INDEXNO = $index;
                                $feeLedger->PROGRAMME = $program;
                                $feeLedger->AMOUNT = $item->amount;
                                $feeLedger->PAYMENTTYPE = "School Fees";
                                $feeLedger->PAYMENTDETAILS = $details;
                                $feeLedger->BANK_DATE = $item->paymentdate;
                                $feeLedger->CHECKER = rand();
                                $feeLedger->LEVEL = $level;
                                $feeLedger->RECIEPIENT = 751999;
                                $feeLedger->BANK = $item->accountnumber;
                                if(empty($item->transaction)){
                                    $feeLedger->TRANSACTION_ID = rand();
                                }
                                else{
                                    $feeLedger->TRANSACTION_ID =$item->transaction;
                                }
                                $feeLedger->RECEIPTNO = $sys->getReceipt();
                                $feeLedger->YEAR = $year;
                                $feeLedger->FEE_TYPE = "School Fees";
                                $feeLedger->SEMESTER = $sem;
                                $feeLedger->save();
                                if ($feeLedger->save()) {
                                    @StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->update(array("BILL_OWING" => $bill_owing, "BILLS" => $bill));

                                    $sys->updateReceipt();
                                }

                            }
                            else{

                                dd( Models\FeePaymentModel::where("INDEXNO",$index)->where("YEAR",$year)->where("FEE_TYPE","School Fees")->update(array("AMOUNT"=>$item->amount)));

                            }


                        }


                    }
                }


            }
        }
    }
}
