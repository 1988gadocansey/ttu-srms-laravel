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

class SupportController extends Controller {

    public function __construct() {

        $this->middleware('auth');
         ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
    }
public function fireAutomaticApplicant(Request $request, SystemController $sys) {
         ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
        // $message = $request->input("message", "");
        $query = \Session::get('students');
        //dd($query);
        $regular = "Congrats![firstname]. You have been admitted to TTU to  pursue [programme].Note, This letter supercedes the previous one sent you, Use this link; application.ttuportal.com to print your admission letter.";
         $conditional = "Congrats![firstname]. You have been offered a conditional admission to TTU to pursue [programme].Note, This letter supercedes the previous one sent you, Use this link; Use this link application.ttuportal.com to print your admission letter.";
         $technical=$regular ;
        $provisional = "Congrats![firstname]. You have been offered a provisional admission to TTU to  pursue [programme].You will be required to send your results when published to complete the admission process.Use this link application.ttuportal.com to print your admission letter.";
        $mature = "Congrats![firstname]. Your application for admission to TTU as mature student to  pursue [programme].Note, This letter supercedes the previous one sent you, Use this link; Use this link application.ttuportal.com to print your admission letter.";
        foreach ($query as $rtmt => $member) {
            $name = $member->NAME;
            $firstname = $member->FIRSTNAME;
            $id = $member->ID;
 
            $programme = $sys->getProgram($member->PROGRAMME_ADMITTED);
            if ($member->SMS_SENT == 0 && $member->ADMITTED==1) {
                
                if ($member->ADMISSION_TYPE == "conditional")
                {
                    $newstring = str_replace("]", "", "$conditional");
                    
                } 
                elseif($member->ADMISSION_TYPE == "regular" ){
                      $newstring = str_replace("]", "", "$regular");
                }
                 elseif($member->ADMISSION_TYPE == "technical" ){
                      $newstring = str_replace("]", "", "$regular");
                }
                elseif ($member->ADMISSION_TYPE== "provisional") {
                    $newstring = str_replace("]", "", "$provisional");
                } else {
                    $newstring = str_replace("]", "", "$mature");
                }
                $finalstring = str_replace("[", "$", "$newstring");
                eval("\$finalstring =\"$finalstring\" ;");

              $result= @$sys->firesms($finalstring, $member->PHONE, $member->APPLICATION_NUMBER);
              
               
                 
                
                @Models\ApplicantModel::where("ID", $id)->update(array("SMS_SENT" => 1));
                 
            } else {
                
            }
        }
       // return @redirect("applicants/view");
    }

 
    public function fireAutomaticOutreach(Request $request, SystemController $sys) {
        ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
        // $message = $request->input("message", "");
        $query = \Session::get('students');
        //dd($query);
        $regular = "Congrats! [name]. You have been admitted to TTU to pursue a programme of study leading to the award of [programme]. Your admission letter will be sent to you in due course.Your admission number is [code], print your letter using the link http://outreach.ttuportal.com";
         $conditional = "Congrats! [name]. You have been offered a conditional admission to TTU to pursue a programme of study leading to the award of [programme]. Your admission letter will be sent to you in due course.Your admission number is [code], print your letter using the link http://outreach.ttuportal.com";
      
        $provisional = "Congrats! [name]. You have been offered a provisional admission to TTU to pursue a programme of study leading to the award of [programme]. You will be required to send your results when published to complete the admission process. Your admission number is [code], print your letter using the link http://outreach.ttuportal.com";
        //$mature = "Congrats! [name]. Your application for admission to TTU as mature student to pursue a program of study leading to the award of [programme] has been received. We will contact you in due course. Thanks";
        foreach ($query as $rtmt => $member) {
            $name = $member->name;
            $code = $member->applicationNumber;
            $id = $member->id;

            $programme = $sys->getProgram($member->programme);
            if ($member->sms_sent== 0 &&$member->admitted ==1) {
                if ($member->admissionType=="conditional")
                {
                    $newstring = str_replace("]", "", "$conditional");
                    
                } 
                elseif($member->admissionType=="regular" ){
                      $newstring = str_replace("]", "", "$regular");
                }
                
                elseif ($member->admissionType=="provisional") {
                    $newstring = str_replace("]", "", "$provisional");
                } else {
                    //$newstring = str_replace("]", "", "$mature");
                }
                $finalstring = str_replace("[", "$", "$newstring");
                eval("\$finalstring =\"$finalstring\" ;");

                 @$sys->firesms($finalstring, $member->phone, $member->phone);
                    
                
                @Models\OutreachModel::where("id", $id)->update(array("sms_sent" => 1));
            } else {
                
            }
        }
        return @redirect("outreach/view");
    }

    public function fireOutreach(Request $request, SystemController $sys) {
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
        $message = $request->input("message", "");
        $query = \Session::get('students');



        foreach ($query as $rtmt => $member) {
            $name = $member->name;
            $id = $member->id;

            $programme = $sys->getProgram($member->programme);
            if ($member->sms_sent == 0) {
                $newstring = str_replace("]", "", "$message");
                $finalstring = str_replace("[", "$", "$newstring");
                eval("\$finalstring =\"$finalstring\" ;");
                @Models\OutreachModel::where("id", $id)->update(array("sms_sent" => 1));

                if (@$sys->firesms($finalstring, $member->phone, $member->phone)) {
                    
                }
            } else {
                
            }
        }
        return @redirect("outreach/view");
    }

    
    public function addOutreach(Request $request, SystemController $sys) {
        if ($request->isMethod("get")) {

            return view('admissions.applicants.outreachApplicant')->with('programme', $sys->getProgramList());
        } else {
            $name = strtoupper($request->input('name'));
            $program = $request->input('programme');
            $gender = strtoupper($request->input('gender'));
            $phone = $request->input('phone');
            $message = $request->input('message');
            $type = strtoupper($request->input('type'));
            $user = @\Auth::user()->fund;

              $indexNo_query = @Models\OutreachCodeModel::first();
          	
 $code ="201710".$indexNo_query->code;
            $outreach = new Models\OutreachModel();
            $outreach->name = $name;
            $outreach->phone = $phone;
            $outreach->gender = $gender;
            $outreach->programme = $program;
            $outreach->actor = $user;
             $outreach->applicationNumber =$code;
            $outreach->type = $type;

            if ($outreach->save()) {
                  $indexNo_query->increment("code", 1);
                @$sys->firesms($message, $phone, $phone);

                return response()->json(['status' => 'success', 'message' => ' Applicant admitted successfully ']);
            } else {
                return response()->json(['status' => 'error', 'message' => ' Error addmitting applicant. try again ']);
            }
        }
    }

    public function outreachs(Request $request, SystemController $sys) {
        $applicant = Models\OutreachModel::query();

         if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $applicant->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }

        if ($request->has('gender') && trim($request->input('gender')) != "") {
            $applicant->where("gender", $request->input("gender", ""));
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $applicant->where("programme", $request->input("program", ""));
        }

        if ($request->has('type') && trim($request->input('type')) != "") {
            $applicant->where("type", $request->input("type", ""));
        }
        if ($request->has('sms') && trim($request->input('sms')) != "") {
            $applicant->where("sms_sent", $request->input("sms", ""));
        }

        $data = $applicant->orderBy("name")->paginate(200);

        $request->flashExcept("_token");
        \Session::put('students', $data);

        return view('admissions.applicants.viewOutreach')->with("data", $data)
                
                ->with('programme', $sys->getProgramList())
                 ->with('halls', $sys->getHalls())
                 ->with('year', $sys->years())
                ->with('department', $sys->getDepartmentList())
                        ->with('school', $sys->getSchoolList())
                ->with('type', $sys->getProgrammeTypes());;
        
        
       
                        
    }

    public function updateApplicantStatus() {
        $query = @Models\ApplicantModel::all();

        foreach ($query as $row) {
            $this->CheckFails($row->APPLICATION_NUMBER);
        }
    }

    /*
     * @param array of grades
     * count the number of failed subjects
     */

    public function CountFails($array) {
        $fail = 0;

        foreach ($array AS $value) {
            // echo "value:$value</br>";
            if ($value > 7) {

                $fail++;
            }
        }
        return $fail;
    }

    // list the total failed and passed subjects
    public function CheckFails($applicant) {
        $subject_array_core = array();
        $subject_array_core_alt = array();
        $subject_array_elect = array();
        $subjects = array();
        $failedSubjects = array();
        $form = $applicant;
        $qualification = array("WASSSCE", "SSSCE", "NAPTEX", "TEU/TECHNICAL CERTIFICATES");
        // $query=  mysql_query("SELECT APP_FORM,ENTRY_TYPE,FIRST_CHOICE FROM tbl_applicants WHERE   APP_FORM='$form' ");
        $query = @Models\ApplicantModel::where("APPLICATION_NUMBER", $form)->get();


        foreach ($query as $row) {
            if (in_array($row->ENTRY_QUALIFICATION, $qualification) && $row->FORM_TYPE == "MATURE" && $row->AGE < 25) {
                $qualify = "Age less than 25";
                $status = "Mature qualification not met because of age less than 25yrs";
                return @Models\ApplicantModel::where("APPLICATION_NUMBER", $form)->update(array("ELIGIBILTY" => $status, "QUALIFY" => $qualify));
            } elseif (in_array($row->ENTRY_QUALIFICATION, $qualification) && $row->FORM_TYPE != "BTECH") {
                $resultQuery = @Models\ExamResultsModel::where("APPLICATION_NUMBER", $form)->orderBy("GRADE_VALUE", "DESC")->get();

                foreach ($resultQuery as $value) {
                    if ($value->TYPE == 'core') {
                        @$subject_array_core[@$value->subject->NAME] = @$value->GRADE_VALUE;
                        @$subjects[] = @$value->subject->NAME . "=" . @$value->GRADE;
                        $failedSubjects[] = @$value->GRADE;
                    } elseif ($value->TYPE == 'core_alt') {
                        $subject_array_core_alt[@$value->subject->NAME] = @$value->GRADE_VALUE;
                        @$subjects[] = @$value->subject->NAME . "=" . @$value->GRADE;
                        $failedSubjects[] = @$value->GRADE;
                    } else {
                        $subject_array_elect[@$value->subject->NAME] = @$value->GRADE_VALUE;
                        @$subjects[] = @$value->subject->NAME . "=" . @$value->GRADE;
                        $failedSubjects[] = @$value->GRADE;
                    }
                }
                // dd($subjects);
                $subjectArray = @implode(",", $subjects);
                //dd(in_array("D7",$failedSubjects));
                $error = "";
                if (count($subject_array_core) <= 2 && in_array("E8", $failedSubjects)) {
                    $error = "Core Subjects not met.\n Minimum pass of two compulsory cores i.e Core Maths and English\n";
                    $qualify = "No";
                } else {
                    $qualify = "Yes";
                }

                if (count($subject_array_core_alt) == 0 && in_array("E8", $failedSubjects)) {
                    $error .= "Core  Alternative Subject not met. \nEither pass in Social studies or Integrated Science \n";
                    $qualify = "No";
                } else {
                    $qualify = "Yes";
                }
                if (count($subject_array_elect) <= 3 && in_array("E8", $failedSubjects)) {
                    $error .= "Elective Subjects not met. \nPasses in 3 Elective subjects required \n";
                    $qualify = "No";
                } else {
                    $qualify = "Yes";
                }

                if (count($subject_array_core) + count($subject_array_core_alt) + count($subject_array_elect) < 6) {
                    $error .= "Passes in at least 6 subjects required \n";
                    $qualify = "No";
                } else {
                    $qualify = "Yes";
                }

                @sort($subject_array_core_alt);
                @sort($subject_array_core);
                @sort($subject_array_elect);

                $elective_slice = @array_slice($subject_array_elect, 0, 3);
                $core_alt_slice = @array_slice($subject_array_core_alt, 0, 1);

                $grade = ( array_sum($subject_array_core) + array_sum($elective_slice) + array_sum($core_alt_slice));

                $total = $this->CountFails($subject_array_core) + $this->CountFails($elective_slice) + $this->CountFails($core_alt_slice);

                if ($qualify == "Yes") {
                    $status = "Qualify?" . $qualify . " - " . " Total Failed: " . $total;
                } else {
                    $status = "Qualify?" . $qualify . " - \n" . $error . " - \n" . " Total Failed: " . $total;
                }


                return @Models\ApplicantModel::where("APPLICATION_NUMBER", $form)->update(array("ELIGIBILTY" => $status, "QUALIFY" => $qualify, "GRADE" => $grade, "GRADES" => $subjectArray));
            } elseif ($row->FORM_TYPE == "BTECH") {
                $qualify = "Yes";
                $status = $qualify;
                return @Models\ApplicantModel::where("APPLICATION_NUMBER", $form)->update(array("ELIGIBILTY" => $status, "QUALIFY" => $qualify, "GRADE" => 0, "GRADES" => $row->CLASS));
            } else {
                
            }
        }
    }

    public function admitMessage(Request $request, SystemController $sys) {
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
        $array = $sys->getSemYear();

        $fiscalYear = $array[0]->YEAR;

        $sql = Models\ApplicantModel::where("ADMITTED", "1")->where("STATUS", "ADMITTED")->where("ADMISSION_FEES", ">", "0")->where("SMS_SENT", "0")->get();



        foreach ($sql as $rtmt => $member) {
            $NAME = $member->NAME;
            $FIRSTNAME = $member->FIRSTNAME;
            $SURNAME = $member->SURNAME;
            $PROGRAMME = $sys->getProgram($member->PROGRAMME_ADMITTED);
            $APPLICATION_NUMBER = $member->APPLICATION_NUMBER;


            if (strpos($member->PROGRAMME_ADMITTED, "H") === 0) {
                $level = "100H";
                $year = '100H';
            } elseif (strpos($member->PROGRAMME_ADMITTED, "D") === 0 || strpos($member->PROGRAMME_ADMITTED, "C") === 0) {
                $level = "100NT";
                $year = '100NT';
            } elseif (strpos($member->PROGRAMME_ADMITTED, "A") === 0) {
                $level = "100NT";
                $year = '100NT';
            } 
            elseif (strpos($member->PROGRAMME_ADMITTED, "B") === 0) {
               
                $level = "100BTT";
                $year = "100BTT";
            }
            
            else {
               $level = "100NT";
                $year = '100NT';
            }
            // move all the applicants to the student table
            $name = $NAME;
            $query = new StudentModel();
            $query->YEAR = $year;
            $query->LEVEL = $level;
            $query->FIRSTNAME = $FIRSTNAME;
            $query->SURNAME = $SURNAME;
            $query->OTHERNAMES = $member->OTHERNAME;
            $query->TITLE = $member->TITLE;
            $query->SEX = $member->GENDER;
            $query->DATEOFBIRTH = $member->DOB;
            $query->NAME = $name;
            $query->AGE = $sys->age($member->DOB, 'eu');
            //$query->GRADUATING_GROUP = $group;
            $query->MARITAL_STATUS = $member->MARITAL_STATUS;
            $query->HALL = $member->HALL_ADMITTED;
            $query->ADDRESS = $member->ADDRESS;
            $query->RESIDENTIAL_ADDRESS = $member->RESIDENTIAL_ADDRESS;
            $query->EMAIL = $member->EMAIL;
            $query->PROGRAMMECODE = $member->PROGRAMME_ADMITTED;
            $query->TELEPHONENO = $member->PHONE;
            $query->COUNTRY = $member->NATIONALITY;
            $query->REGION = $member->REGION;
            $query->RELIGION = $member->RELIGION;
            $query->HOMETOWN = $member->HOMETOWN;
            $query->GUARDIAN_NAME = $member->GURDIAN_NAME;
            $query->GUARDIAN_ADDRESS = $member->GURDIAN_ADDRESS;
            $query->GUARDIAN_PHONE = $member->GURDIAN_PHONE;
            $query->GUARDIAN_OCCUPATION = $member->GURDIAN_OCCUPATION;
            $query->DISABILITY = $member->PHYSICALLY_DISABLED;
            $query->STATUS = "Admitted";
            $query->SYSUPDATE = "1";
            $query->DATE_ADMITTED = "AUG" . date("Y");
            //$query->GRADUATING_GROUP =$sys->graduatingGroup($indexno);
            $query->TYPE = $member->SESSION_PREFERENCE;


            $query->HOSTEL = "";
            $query->BILLS = $member->ADMISSION_FEES;
            $query->BILL_OWING = $member->ADMISSION_FEES;
            $query->STNO = $APPLICATION_NUMBER;
            $query->INDEXNO = $APPLICATION_NUMBER;

            if ($query->save()) {

                $que = Models\PortalPasswordModel::where("username", $APPLICATION_NUMBER)->first();
                if (empty($que)) {

                    $str = 'abcdefhkmnprtuvwxyz234678';
                    $shuffled = str_shuffle($str);
                    $vcode = substr($shuffled, 0, 9);
                    $real = strtoupper($vcode);
                    $level = $level;
                    Models\PortalPasswordModel::create([
                        'username' => $APPLICATION_NUMBER,
                        'real_password' => $real,
                        'level' => $level,
                        'programme' => $member->PROGRAMME_ADMITTED,
                        'biodata_update' => '1',
                        'password' => bcrypt($real),
                    ]);

                    \DB::commit();
                    $message = "Congratulations,$FIRSTNAME you have been admitted to Takoradi Technical University to persue $PROGRAMME. Goto admissions.tpolyonline.com to print your admission letter using your serial and pin code. Thanks ";
                    if ($sys->firesms($message, $member->PHONE, $member->APPLICATION_NUMBER)) {
                        Models\ApplicantModel::where("APPLICATION_NUMBER", $APPLICATION_NUMBER)->update(array("SMS_SENT" => "1"));
                        \DB::commit();
                    }
                    $message2 = "Hi $FIRSTNAME, Please visit Accounts Office for Fee Payment Verification and proceed to portal.tpolyonline.com to do your course registration. use $APPLICATION_NUMBER as your username  and $real as password.Thanks ";


                    if ($sys->firesms($message, $member->PHONE, $APPLICATION_NUMBER)) {
                        
                    }
                }
            } else {
                // return redirect('/students')->withErrors("SMS could not be sent.. please verify if you have sms data and internet access.");
            }
        }

        return redirect('/applicants/view')->with('success', 'Message sent to Applicants successfully');
    }

    public function admit(Request $request, SystemController $sys) {
//dd($request);
        $applicant = $request->input("applicant");
        $program = $request->input("program");
        $hall = $request->input("hall");
        $admit = $request->input("admit");
         $type= $request->input("type");
         $resident= $request->input("resident");
        $conditional = $request->input("conditional");
        $programName = $sys->getProgram($program);
        $array = $sys->getSemYear();

       // $fiscalYear = $array[0]->YEAR;
        $fiscalYear ='2017/2018';
//dd($program);
            if (strpos($program, "H") == 0) {
                $level = "100H";
                $year = '100H';
            } elseif (strpos($program, "D") == 0 || strpos($program, "C") == 0) {
                $level = "100NT";
                $year = '100NT';
            } elseif (strpos($program, "A") == 0) {
                $level = "100NT";
                $year = '100NT';
            } 
            elseif (strpos($program, "B") == 0) {
               
                $level = "100BTT";
                $year = "100BTT";
            }
            
            else {
               $level = "100NT";
                $year = '100NT';
            }
//dd($level);
        $fee = $sys->getYearBillLevel100($fiscalYear, $level, $program);
        $user = @\Auth::user()->fund;
        $capacity=@$sys->hallData($hall);
        $size=@$capacity->HALL_CAPACITY;
        $spaceLeft=@$sys->hallRoomConsumed($hall);
        $left= $spaceLeft;
        
        if($left<=$size && $resident==1){
           @Models\HallModel::where("HALL_NAME",$hall)->update(array("SPACE_USED"=>$left));
          
        }
        
        if($fee > 0){


            if ($admit =="admit") {
                
                
                Models\ApplicantModel::where("APPLICATION_NUMBER", $applicant)
                        ->update(
                                array(
                                    "PROGRAMME_ADMITTED" => $program,
                                    "ADMISSION_FEES" => $fee,
                                    "ADMISSION_TYPE" => $type,
                                    "HALL_ADMITTED" => strtoupper($hall),
                                    "STATUS" => "ADMITTED",
                                    "ADMITTED" => "1",
                                    "RESIDENTIAL_STATUS" =>$resident,
                                    "DATE_ADMITTED" => date("D M d, Y"),
                                    "ADMITTED_BY_OFFICER" => $user
                                )
                );
                 
                
                
                
            } else {
                 
            }
        } else {
            return response()->json(['status' => 'error', 'message' => $programName . ' does not have fees set. please contact finance']);
        }
        // return response()->json(['status'=>'success','message'=>$member->NAME. ' completed successfully']);
        return response()->json(['status' => 'success', 'message' => $applicant . ' Admitted successfully into ' . $program]);
    }

    public function generateAccounts() {
        
    }
public function admitOutreach(Request $request, SystemController $sys) {
//dd($request);
        $applicant = $request->input("applicant");
        $program = $request->input("program");
        $hall = $request->input("hall");
        $admit = $request->input("admit");
         $type= $request->input("type");
      $resident= $request->input("resident");
        $programName = $sys->getProgram($program);
        $array = $sys->getSemYear();

       // $fiscalYear = $array[0]->YEAR;
        $fiscalYear ='2017/2018';
//dd($program);
            if (strpos($program, "H") == 0) {
                $level = "100H";
                $year = '100H';
            } elseif (strpos($program, "D") == 0 || strpos($program, "C") == 0) {
                $level = "100NT";
                $year = '100NT';
            } elseif (strpos($program, "A") == 0) {
                $level = "100NT";
                $year = '100NT';
            } 
            elseif (strpos($program, "B") == 0) {
               
                $level = "100BTT";
                $year = "100BTT";
            }
            
            else {
               $level = "100NT";
                $year = '100NT';
            }
//dd($level);
        $fee = @$sys->getYearBillLevel100($fiscalYear, $level, $program);
        $user = @\Auth::user()->fund;
        $capacity=@$sys->hallData($hall);
        $size=@$capacity->HALL_CAPACITY;
        
        $spaceLeft=$sys->hallRoomConsumed($hall);
        $left= $spaceLeft;
       
        if($left<=$size && $resident==1){
           @Models\HallModel::where("HALL_NAME",$hall)->update(array("SPACE_USED"=>$left));
          
        }
        if ($fee > 0) {


            if ($admit =="admit") {
               
                @Models\OutreachModel::where("applicationNumber", $applicant)
                        ->update(
                                array(
                                    "programmeAdmitted" => $program,
                                    "admissionFees" => $fee,
                                    "admissionType" => $type,
                                    "hallAdmitted" => strtoupper($hall),
                                    "status" => "ADMITTED",
                                    "admitted" => "1",
                                     "residentialStatus"=>$resident,
                                    "dateAdmitted" => date("D M d, Y"),
                                    "admitedBy" => $user
                                )
                );
                
                
            } else {
                 
            }
        } else {
            return response()->json(['status' => 'error', 'message' => $programName . ' does not have fees set. please contact finance']);
        }
        // return response()->json(['status'=>'success','message'=>$member->NAME. ' completed successfully']);
        return response()->json(['status' => 'success', 'message' => $applicant . ' Admitted successfully into ' . $program]);
    }

    public function cards() {
        $query = Models\FormModel::where("SOLD_BY", "CAMPUS")->where("SOLD","1")->paginate(2500);
        return view("admissions.viewScratchCard")->with("data", $query);
    }

    public function index(Request $request, SystemController $sys) {
      ini_set('max_execution_time', 9000); //300 seconds = 5 minutes
        //$this->updateApplicantStatus();
        $student = Models\ApplicantModel::query();


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
            $student->where("FIRST_CHOICE", $request->input("program", ""))
                    ->orWhere("SECOND_CHOICE", $request->input("program", ""))
                    ->orWhere("THIRD_CHOICE", $request->input("program", ""))
            ;
        }

        if ($request->has('status') && trim($request->input('status')) != "") {
            $student->where("STATUS", $request->input("status", ""));
        }
        if ($request->has('group') && trim($request->input('group')) != "") {
            $student->where("YEAR_ADMISION", $request->input("group", ""));
        }
        if ($request->has('nationality') && trim($request->input('nationality')) != "") {
            $student->where("NATIONALITY", $request->input("nationality", ""));
        }
        if ($request->has('region') && trim($request->input('region')) != "") {
            $student->where("REGION", $request->input("region", ""));
        }
        if ($request->has('gender') && trim($request->input('gender')) != "") {
            $student->where("GENDER", $request->input("gender", ""));
        }

        if ($request->has('hall') && trim($request->input('hall')) != "") {
            $student->where("PREFERED_HALL", $request->input("hall", ""));
        }
        if ($request->has('religion') && trim($request->input('religion')) != "") {
            $student->where("RELIGION", $request->input("religion", ""));
        }
        if ($request->has('search') && trim($request->input('search')) != "" && trim($request->input('by')) != "") {
            // dd($request);
            $student->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%")
                    ->orWhere("APPLICATION_NUMBER", "LIKE", "%" . $request->input("search", "") . "%");
        }
        $data = $student->orderBy('NAME')->orderBy('APPLICATION_NUMBER')->orderBy('FIRST_CHOICE')->paginate(100);

        $request->flashExcept("_token");

        \Session::put('students', $data);
        return view('admissions.applicants.support')->with("data", $data)
                        ->with('year', $sys->years())
                        ->with('nationality', $sys->getCountry())
                        ->with('halls', $sys->getHalls())
                        ->with('religion', $sys->getReligion())
                        ->with('region', $sys->getRegions())
                        ->with('department', $sys->getDepartmentList())
                        ->with('school', $sys->getSchoolList())
                        ->with('programme', $sys->getProgramList())
                        ->with('type', $sys->getProgrammeTypes());
    }

    public function sms(Request $request, SystemController $sys) {
        ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
        $message = $request->input("message", "");
        $query = \Session::get('students');



        foreach ($query as $rtmt => $member) {
            $NAME = $member->NAME;
            $FIRSTNAME = $member->FIRSTNAME;
            $SURNAME = $member->SURNAME;
            $PROGRAMME = $sys->getProgram($member->PROGRAMME_ADMITTED);
            $APPLICATION_NUMBER = $member->APPLICATION_NUMBER;
            $HALL_ADMITTED = $member->HALL_ADMITTED;
            $ADMISSION_FEES = $member->ADMISSION_FEES;

            $newstring = str_replace("]", "", "$message");
            $finalstring = str_replace("[", "$", "$newstring");
            eval("\$finalstring =\"$finalstring\" ;");
            if ($sys->firesms($finalstring, $member->PHONE, $member->APPLICATION_NUMBER)) {

                Models\ApplicantModel::where("APPLICATION_NUMBER", $APPLICATION_NUMBER)->update(array("SMS_SENT", "1"));
            } else {
                // return redirect('/students')->withErrors("SMS could not be sent.. please verify if you have sms data and internet access.");
            }
        }
        return redirect('/students')->with('success', 'Message sent to students successfully');

        \Session::forget('students');
    }

    public function letter($id, SystemController $sys, Request $request) {
        $array=$sys->getSemYear();
                  $sem=$array[0]->SEMESTER;
                  $year="2017/2018";
        $sql= Models\ApplicantModel::where("APPLICATION_NUMBER",$id)->where("STATUS","ADMITTED")
                ->where("ADMITTED","1")
                ->where("ADMISSION_FEES",">","0")
                ->first();
        
        return view("admissions.applicants.letter")->with("data", $sql)->with('year',$year);;
    }
    public function letterOutreach($id, SystemController $sys, Request $request) {
        $array=$sys->getSemYear();
                  $sem=$array[0]->SEMESTER;
                  $year="2017/2018";
        $sql= Models\OutreachModel::where("id",$id)->where("status","ADMITTED")
                ->where("admitted","1")
                ->where("admissionFees",">","0")
                ->first();
        
        return view("admissions.applicants.outReachLetter")->with("data", $sql)->with('year',$year);;
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, SystemController $sys, Request $request) {
        


        $query = Models\ApplicantModel::where('APPLICATION_NUMBER', $id)->first();
        $grades = @Models\ExamResultsModel::where("APPLICATION_NUMBER", $id)->get();



        return view('admissions.applicants.show')->with('student', $query)
                        ->with('data', $grades);
    }

    public function search() {
        return view('admissions.search');
    }

    public function showApplicant(Request $request, SystemController $sys) {
        $applicant = explode(',', $request->input('q'));
        $applicant = $applicant[0];

        $sql = Models\ApplicantModel::where("APPLICATION_NUMBER", $applicant)->get();


        if (count($sql) == 0) {

            return redirect("/admissions/applicant/settings")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
        } else {
            $sys = new SystemController();
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            return view("admissions.applicants.settings")->with('data', $sql);
        }
    }

    public function action(SystemController $sys, Request $request) {

        $applicant = $request->input("student");
        $action = $request->input("action");

        $query = Models\FormModel::where('FORM_NO', $applicant)->update(array("FINALIZED" => $action));

        if ($query) {
            return response()->json(['status' => 'success', 'message' => 'Action completed successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Action failed']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        //
        Models\OutreachModel::where("id",$request->input("id"))->delete();
        return redirect("/outreach/view");
    }

}
