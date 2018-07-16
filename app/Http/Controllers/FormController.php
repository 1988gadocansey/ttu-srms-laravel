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
class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if(@\Auth::user()->FINALIZED==1){
            return redirect("form/preview");
        }
        
    }
    public function index() {
        
        return view("dashboard");
    }
     
    
     public function sms(){
         $sys=new SystemController();
         ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
         $applicant = @\Auth::user()->FORM_NO;
        $query = @Models\ApplicantModel::where("APPLICATION_NUMBER", $applicant)->first();
        $phone = $query->PHONE;
        $firstChoice = $sys->getProgramName($query->FIRST_CHOICE);
        $secondChoice = $sys->getProgramName($query->SECOND_CHOICE);
        $name = $query->FIRSTNAME;
        $message ="Hi $name your application with first choice $firstChoice and second choice $secondChoice has been received by our school. Your Application number is $applicant. Write it on the envelope with the printout and forward it to our university. Congrats";
             if ($sys->firesms($message,$phone,$applicant)) {

               //return redirect('form/preview')->with('success1','Form sent to school successfully');
         return redirect("/logout");
               
            } else {
            return redirect('form/preview')->with('error1','Form could not be submitted try sgain pls');
         
                }
       
          
         
    }
    public function createGrades(SystemController $sys) {
     if(@\Auth::user()->FINALIZED==1)
        {
               return redirect("form/preview");
        }
        else{
      if(@\Auth::user()->BIODATA_DONE==1){
        $applicant=@\Auth::user()->FORM_NO;
        $query=@Models\ExamResultsModel::where("APPLICATION_NUMBER",$applicant)->paginate(100);
        $total=count( $query);
        $subject=$sys->getSubjectList();
        $grades=$sys->getGradeSystemIDList();
        $examType=$sys->getExamList();
         
         
        return view('applicants.grades')
            ->with('subject', $subject)
            ->with('examType', $examType)
            ->with('grades', $grades)
            ->with('total', $total)
            ->with('data',$query);
      }
      else{
             return redirect('/form/step2')->with('error1','Fill this portion of the form');
       
      }
      
      
      
        }     
    }
   
    public function storeGrades(Request $request, SystemController $sys) {
       if(@\Auth::user()->STARTED==1 &&@\Auth::user()->PHOTO_UPLOAD=="YES" ){
          \DB::beginTransaction();
         try {
            
//            $this->validate($request, [
//                'grade' => 'required',
//                'subject' => 'required',
//                'type' => 'required',
//                'center' => 'required',
//                'indexno' => 'required',
//                'month' => 'required',
//                'sitting' => 'required',
//            ]);



            $applicantForm = @\Auth::user()->FORM_NO;
            $total = count($request->input('grade'));
            $grade = $request->input('grade');
            $subject = $request->input('subject');
            $center = $request->input('center');
            $type = $request->input('type');
            $indexno = $request->input('indexno');
            $month = $request->input('month');
            $sitting = $request->input('sitting');


            for ($i = 0; $i < $total; $i++) {
                $result = new Models\ExamResultsModel();
                $result->APPLICATION_NUMBER=$applicantForm; 
                $result->SUBJECT=$subject[$i]; 
                $result->SITTING=$sitting[$i]; 
                $result->EXAM_TYPE=$type[$i]; 
                $result->INDEX_NO=$indexno[$i]; 
                $result->CENTER=$center[$i]; 
                $result->TYPE=$sys->getSubjectType($subject[$i]); 
                $result->GRADE_VALUE=$sys->getGradeValue($grade[$i]);
                $result->MONTH=$month[$i];
                $result->GRADE=$grade[$i];
                $result->save();
                  \DB::commit();
            }
              return redirect("/form/step3")->with("success1", " <span style='font-weight:bold;font-size:13px;'> Ayeko $applicantForm grades successfully Recieved!!. </span> ");
          
            
         } catch (\Exception $e) {
             \DB::rollback();
        }
       }
       else{
            return redirect("/form/step2")->with("error1", " <span style='font-weight:bold;font-size:13px;'>Whoops $applicantForm fill this portion of the form</span> ");
          
       }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(SystemController $sys)
    {
        if(@\Auth::user()->FINALIZED==1)
        {
               return redirect("form/preview");
        }
        else{
         if(@\Auth::user()->PHOTO_UPLOAD=="YES" ){
      
        $applicant=@\Auth::user()->FORM_NO;
        $query=@Models\ApplicantModel::where("APPLICATION_NUMBER",$applicant)->first();
        $region=$sys->getRegions();
        $programme=$sys->getProgramList();
         
        $hall=$sys->getHalls();
        $religion=$sys->getReligion();
        return view('applicants.create')
            ->with('programme', $programme)
            ->with('country', $sys->getCountry())
            ->with('region', $region)
            ->with('hall',$hall)
            ->with('religion',$religion)
            ->with("data",$query);
         }
         else{
               return redirect("upload/photo")->with("error", " <span style='font-weight:bold;font-size:13px;'>Whoops $applicant upload your photo</span> ");
          
         }
          
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SystemController $sys)
    {
      
       
        /* transaction is used here so that any errror rolls
         *  back the whole process and prevents any inserts or updates
         */
        \DB::beginTransaction();
        try {


            $applicantForm = @\Auth::user()->FORM_NO;
 
            $firstChoice = strtoupper($request->input('firstChoice'));
            $secondChoice = strtoupper($request->input('secondChoice'));
            $thirdChoice = strtoupper($request->input('thirdChoice'));
            $gender = strtoupper($request->input('gender'));
            $preference = strtoupper($request->input('session'));

            $hall = strtoupper($request->input('halls'));
            $dob = strtoupper($request->input('dob'));
            $gname = strtoupper($request->input('gname'));
            $gphone =strtoupper( $request->input('gphone'));
            $goccupation = strtoupper($request->input('goccupation'));
            $gaddress =strtoupper( $request->input('gaddress'));
            $email =strtoupper( $request->input('email'));
            $phone = strtoupper($request->input('phone'));
            $marital_status = strtoupper($request->input('marital_status'));
            $region = strtoupper($request->input('region'));
            $country = strtoupper($request->input('nationality'));
            $religion = strtoupper($request->input('religion'));
            $residentAddress =strtoupper( $request->input('contact'));
            $address =strtoupper( $request->input('address'));
            $hometown =strtoupper( $request->input('hometown'));
            $grelationship =strtoupper( $request->input('grelationship'));
            $programStudy =strtoupper( $request->input('study_program'));
            $disability =strtoupper( $request->input('disability'));
            $title =strtoupper( $request->input('title'));
            $entry =strtoupper( $request->input('entry'));
            $qualification =strtoupper( $request->input('qualification'));
            $age = 4;
            $bond =strtoupper( $request->input('bond'));
            $fname =strtoupper( $request->input('fname'));
            $lname =strtoupper( $request->input('lname'));
            $finance =strtoupper( $request->input('finance'));
            $othername =strtoupper( $request->input('othernames'));
            $school =strtoupper( $request->input('school'));

            $name = $lname . ' ' . $othername . ' ' . $fname;
          if(@\Auth::user()->STARTED="0"){
            $query = new Models\ApplicantModel();
            $query->APPLICATION_NUMBER = $applicantForm;
            $query->NAME = $name;
            $query->RELATIONSHIP_TO_APPLICANT = $grelationship;
            $query->FIRSTNAME = $fname;
            $query->SURNAME = $lname;
            $query->OTHERNAME = $othername;
            $query->TITLE = $title;
            $query->GENDER = $gender;
            $query->DOB = $dob;
            $query->NAME = $name;
            $query->AGE = $age;
            $query->SOURCE_OF_FINANCE = $finance;
            $query->MARITAL_STATUS = $marital_status;
            $query->PREFERED_HALL = $hall;
            $query->ADDRESS = $address;
            $query->RESIDENTIAL_ADDRESS = $residentAddress;
            $query->EMAIL = $email;
            $query->FIRST_CHOICE = $firstChoice;
            $query->SECOND_CHOICE = $secondChoice;
            $query->THIRD_CHOICE = $thirdChoice;
            $query->PHONE = $phone;
            $query->NATIONALITY = $country;
            $query->REGION = $region;
            $query->RELIGION = $religion;
            $query->HOMETOWN = $hometown;
            $query->GURDIAN_NAME = $gname;
            $query->GURDIAN_ADDRESS = $gaddress;
            $query->GURDIAN_PHONE = $gphone;
            $query->GURDIAN_OCCUPATION = $goccupation;
            $query->PHYSICALLY_DISABLED = $disability;
            $query->STATUS = "APPLICANT";
            $query->SESSION_PREFERENCE = $preference;
            $query->PROGRAMME_STUDY = $programStudy;
            $query->YEAR_ADMISION = date("Y") . "/" . (date("Y") + 1);
            $query->ENTRY_TYPE = $entry;
            $query->BOND = $bond;
             $query->SCHOOL = $school;
            $query->FORM_TYPE = @\Auth::user()->FORM_TYPE;

            $query->ENTRY_QUALIFICATION = $qualification;


            if ($query->save()) {
                Models\FormModel::where("FORM_TYPE", $applicantForm)->update(array("STARTED" => "1"));
                \DB::commit();

                return redirect("/form/step3")->with("success1", " <span style='font-weight:bold;font-size:13px;'> Ayeko $name Form A successfully Recieved!!. <a href='/a'> click here to goto last form</a>!</span> ");
            } else {

                return redirect("/form/step2")->with("error1", "<span style='font-weight:bold;font-size:13px;'> $name form could not be save try again </span>");
          }}else{
              $query=  Models\ApplicantModel::where("APPLICATION_NUMBER",$applicantForm)
                      ->update(array(
                         "NAME" => $name,
            "RELATIONSHIP_TO_APPLICANT" => $grelationship,
            "FIRSTNAME" => $fname,
            "SURNAME" => $lname,
            "OTHERNAME" => $othername,
            "TITLE" => $title,
            "GENDER" => $gender,
            "DOB" => $dob,
            "NAME" =>$name,
            "AGE" => $age,
            "SOURCE_OF_FINANCE" => $finance,
            "MARITAL_STATUS" =>$marital_status,
            "PREFERED_HALL" =>$hall,
            "ADDRESS" => $address,
            "RESIDENTIAL_ADDRESS" => $residentAddress,
            "EMAIL" => $email,
            "FIRST_CHOICE" => $firstChoice,
            "SECOND_CHOICE" => $secondChoice,
            "THIRD_CHOICE" => $thirdChoice,
            "PHONE" => $phone,
            "NATIONALITY" => $country,
            "REGION" => $region,
            "RELIGION" => $religion,
            "HOMETOWN" => $hometown,
            "GURDIAN_NAME" => $gname,
            "GURDIAN_ADDRESS" => $gaddress,
            "GURDIAN_PHONE" => $gphone,
            "GURDIAN_OCCUPATION" => $goccupation,
            "PHYSICALLY_DISABLED" => $disability,
            "STATUS" => "APPLICANT",
            "SESSION_PREFERENCE" => $preference,
            "PROGRAMME_STUDY" => $programStudy,
             
            "BOND" => $bond,
            "SCHOOL" => $school,
           "FORM_TYPE"=> @\Auth::user()->FORM_TYPE,

            "ENTRY_QUALIFICATION" => $qualification ,
            "UPDATED" => "1" 

                          
                          
                          
                          
                          
                      ));
                if ($query) {
                    Models\FormModel::where("FORM_NO",$applicantForm)
                      ->update(array("BIODATA_DONE"=>"1"));
                   \DB::commit();

                  return redirect("/form/step3")->with("success1", " <span style='font-weight:bold;font-size:13px;'> Ayeko $name Form A successfully Recieved!!. <a href='/a'> click here to goto last form</a>!</span> ");
              } else {

                  return redirect("/form/step2")->with("error1", "<span style='font-weight:bold;font-size:13px;'> $name form could not be save try again </span>");
            }
              
          }
          
        } catch (\Exception $e) {
            \DB::rollback();
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
        return view('students.show')->with('student', $query)
            ->with('programme', $programme)
            ->with('country', $sys->getCountry())
            ->with('region', $region)
            ->with('hall',$hall)
            ->with('religion',$religion);
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

     
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function showPictureUpload() {
        if(@\Auth::user()->FINALIZED==1)
        {
               return redirect("form/preview");
        }
        else{
        return view("applicants.upload");
        }
    }
    public function uploadPicture(Request $request, SystemController $sys) {
        $valid_exts = array('jpeg', 'jpg'); // valid extensions
        $max_size = 400000; // max file size
        $file = $request->file('picture');
        $ext = strtolower($request->file('picture')->getClientOriginalExtension());
        $applicantID=@\Auth::user()->ID;
        $applicantNO=@\Auth::user()->FORM_NO;
        if (in_array($ext, $valid_exts)) {
          if (!empty($file)) {
            
              if(  $_FILES['picture']['size'] <= $max_size){
                
                $savepath = 'public/uploads/photos/';
                if(empty($applicantNO)){
                           $sql =\DB::table('tbl_form_number')->get();
                            $new_formNo = $sql[0]->FORM_NO;
                            $formNo = "TPOLY" . date("Y") . $new_formNo;
                             User::where("ID", $applicantID)->update(
                                    array(
                                        "FORM_NO" => $formNo,
                                        "PHOTO_UPLOAD" => 'YES'
                            ));
                             $path = $savepath .$formNo. '.' . $ext;
                              \DB::table('tbl_form_number')->increment('FORM_NO');
                }
                else{
                 $path = $savepath .$applicantNO. '.' . $ext;
                }
               
                    if( $request->file('picture')->move($savepath, $path)){
                        
                         

                             
                               
                            return redirect('form/step2')->with("success", " <span style='font-weight:bold;font-size:13px;'>Ayekoo photo uploaded succesfully</span> ");
       
                        
                        
                    }
 
              }
              else{
                 return redirect('/upload/photo')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload only photos with size less than or equal to 500kb!!!!</span> ");
                
              }
            }
            else{
                return redirect('/upload/photo')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please select photo to upload!!!!</span> ");
               
            }
        }
        else{
            return redirect('/upload/photo')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only .jpg or .jpeg photo format is allowed  !</span> ");
                   
        }
    }
    public function finanlize(SystemController $sys) {
      $applicant=@\Auth::user()->FORM_NO;
       $biodata=@Models\ApplicantModel::where("APPLICATION_NUMBER",$applicant)->first();
       $firstChoice = $biodata->FIRST_CHOICE;
        $secondChoice = $biodata->SECOND_CHOICE;
        $name = $biodata->NAME;
        $phone = $biodata->PHONE;
        $grades=@Models\ExamResultsModel::where("APPLICATION_NUMBER",$applicant)->get();
        if(@\Auth::user()->BIODATA_DONE=="1")
        {
            if(!$grades->isEmpty()){
                
                 @Models\ApplicantModel::where("APPLICATION_NUMBER",$applicant)
                        ->update(array("COMPLETED"=>"1"));
                  @Models\FormModel::where("FORM_NO",$applicant)
                        ->update(array("FINALIZED"=>"1"));
                  $this->sms();
                
            }
            
        }
        else{
            return redirect("/form/step2")->with("error1","Please fill this page before submiting your form");
        }
    }
    public function preview(SystemController $sys) {
        $applicant=@\Auth::user()->FORM_NO;
        $biodata=@Models\ApplicantModel::where("APPLICATION_NUMBER",$applicant)->first();
        
        $grades=@Models\ExamResultsModel::where("APPLICATION_NUMBER",$applicant)->get();
         
         
        return view('applicants.preview')
            ->with('student', $biodata)
            ->with('data', $grades);
             
             
    }
    public function destroyGrade(Request $request) {
         \DB::beginTransaction();
        try {
         $applicantForm = @\Auth::user()->FORM_NO;
         $query = Models\ExamResultsModel::where("APPLICATION_NUMBER",$applicantForm)->where('ID',$request->input("id"))->delete();
          
         if ($query) {
              \DB::commit();
           
              return redirect()->back()->with("success",  " <span style='font-weight:bold;font-size:13px;'>  Grade successfully delete!</span> ");
           
        }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function uploadCards(SystemController $sys,Request $request){

       if (@\Auth::user()->role == 'Admissons Officer' ) {

          if ($request->isMethod("get")) {

           return view('admissions.uploadScratchCards');
                            
           } 
           else{

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
 
                            
                               foreach($data as $value=>$row)
                               {
                                   $serial=$row->serial;
                                   $pin=$row->pin;
                                      
                             
                       $testQuery=Models\FormModel::where('serial', $serial)->first();
                      
                         if(empty($testQuery)){
                             
                         
                               $form = new Models\FormModel();
                                           $form->serial = $serial;
                                           $form->PIN = $pin;
                                           $form->password = bcrypt($pin);
                                            
                                           $form->save();
                                           \DB::commit();
                                       }
                         else{
                               
       Models\FormModel::where('serial', $serial)->update(array("serial" =>$serial, "PIN" => $pin, "password" => bcrypt($pin)));
                                       \DB::commit();
                         }
                                
                               
                               
                               
                               
                               
                      } 
                   }
               } else {
                    return redirect('/admissions/upload/cards')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
                                  
               }
           } else {
                return redirect('/admissions/upload/cards')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload an excel file!</span> ");
                   
           }
         }
    
       return redirect('/admissions/upload/cards')->with("success", " <span style='font-weight:bold;font-size:13px;'>$total Scratch Card(s) uploaded successfully</span> ");
              

       }
     
       
     else{

           throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
      
     }
       

    }
   
        
    
}
