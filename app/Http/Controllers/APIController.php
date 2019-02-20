<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Models\StudentModel;
use App\Models\ApplicantModel;
use App\Models;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
class APIController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }
    public function getFees(Request $request,$program,$level,$year){
        header('Content-Type: application/json');
        $data=Models\BillModel::where("PROGRAMME",$program)
                ->where("YEAR",$year)
                ->where("LEVEL",$level)
                ->select("AMOUNT")->first();
        if($data) {
            return response()->json($data->AMOUNT);
        }
        else{
            return response()->json(0.00);
        }
    }
    public function generateBulkPassword(Request $request, SystemController $sys){


            $student = Models\StudentModel::where("LEVEL", "LIKE", "%" . "100" . "%")->where("HAS_PASSWORD", "0")->get();

            $password = "";
            foreach ($student as $row) {

                $password = Models\PortalPasswordModel::where("username", $row->INDEXNO)->orWhere("username", $row->STNO)->get();

                if (count($password) == 0) {

                    $sys->getPassword($row->INDEXNO);
                    Models\StudentModel::where("INDEXNO", $row->INDEXNO)->orWhere("STNO", $row->STNO)->update(array("HAS_PASSWORD" => 1));


                }

            }

            return $password;



    }
    public function getApplicant(Request $request, SystemController $sys){


        foreach ($request->all() as $student) {
            # code...




            $program = $student["program"];
            $ptype = $sys->getProgrammeType($program);
            if ($ptype == "NON TERTIARY") {
                $level = "100NT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            } elseif ($ptype == "HND") {
                $level = "100H";
                $group=date("Y")+2 . "/".(date("Y") + 3);
            } elseif ($ptype == "BTECH") {
                $level = "100BTT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            }
            elseif ($ptype == "DEGREE") {
                $level = "100BT";
                $group=date("Y") + 3 . "/".(date("Y") + 4);
            }
            else {
                $level = "500MT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            }
            /////////////////////////////////////////////////////
            $checker=Models\StudentModel::where("STNO",$student['stno'])->get();
            if(count($checker)==0) {
                $query = new Models\StudentModel();
                $query->YEAR = $level;
                $query->LEVEL = $level;
                $query->FIRSTNAME = $student['firstname'];
                $query->SURNAME = $student['lastname'];
                $query->OTHERNAMES = $student['othernames'];
                $query->TITLE = $student['title'];
                $query->SEX = $student['gender'];
                $query->DATEOFBIRTH = $student['dob'];
                $query->NAME = $student['name'];
                $query->AGE = $student['age'];
                $query->MARITAL_STATUS = $student['marital'];
                $query->DATE_ADMITTED = $student['date-admitted'];
                $query->GRADUATING_GROUP = $group;
                $query->HAS_PASSWORD = 1;
                $query->HALL = $student['hall'];
                $query->ADDRESS = $student['address'];
                $query->RESIDENTIAL_ADDRESS = $student['address'];
                $query->EMAIL = $student['email'];
                $query->PROGRAMMECODE = $student['program'];
                $query->TELEPHONENO = $student['phone'];
                $query->COUNTRY = $student['country'];
                $query->REGION = $student['region'];
                $query->RELIGION = $student['religion'];
                $query->HOMETOWN = $student['hometown'];
                $query->GUARDIAN_NAME = $student['guardian-name'];
                $query->GUARDIAN_ADDRESS = $student['guardian-address'];
                $query->GUARDIAN_PHONE = $student['guardian-phone'];
                $query->GUARDIAN_OCCUPATION = $student['guardian-occupation'];
                $query->DISABILITY = $student['disable'];
                $query->STNO = $student['stno'];
                $query->INDEXNO = $student['stno'];
                $query->TYPE = $student['type'];
                $query->STUDENT_TYPE = $student['resident'];
                $query->ALLOW_REGISTER = 1;
                $query->STATUS = "Admitted";
                $query->SYSUPDATE = "1";
                $query->BILLS = $student['fees'];
                $query->BILL_OWING = $student['fees'];
                $query->PAID = 0.00;
                @$query->save();
                @$sys->getPassword($student['stno']);
            }
            else{
                Models\StudentModel::where("STNO",$student['stno'])->update(
                    array("FIRSTNAME"=> $student['firstname'],
                        "SURNAME"=> $student['lastname'],
                        "INDEXNO"=> $student['stno'],
                        "OTHERNAMES"=>$student['othernames'],
                        "NAME"=>$student['name'],
                        "LEVEL"=>$level,
                        "YEAR"=>$level,


                        "BILLS"=> $student['fees'],
                        "SMS_SENT"=> 0,

                        "PROGRAMMECODE"=> $student['program'],
                        "HALL"=> $student['hall'],
                        "GRADUATING_GROUP"=> $group,
                    )
                );
                @$sys->getPassword($student['stno']);
            }
        }
        return Models\StudentModel::count();

    }
    public function pushToSRMS(Request $request, SystemController $sys)
    {
        ini_set('max_execution_time', 280000);
        $data = file_get_contents("http://127.0.0.1:3030/admissions/srms/forward"); // put the contents of the file into a variable
        $records = json_decode($data, true, JSON_PRETTY_PRINT); // decode the JSON feed



        foreach ($records as $student) {



            $program = $student["program"];
            $ptype = $sys->getProgrammeType($program);
            if ($ptype == "NON TERTIARY") {
                $level = "100NT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            } elseif ($ptype == "HND") {
                $level = "100H";
                $group=date("Y")+2 . "/".(date("Y") + 3);
            } elseif ($ptype == "BTECH") {
                $level = "100BTT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            }
            elseif ($ptype == "DEGREE") {
                $level = "100BT";
                $group=date("Y") + 3 . "/".(date("Y") + 4);
            }
            else {
                $level = "500MT";
                $group=date("Y") + 1 . "/".(date("Y") + 2);
            }
            /////////////////////////////////////////////////////
            $checker=Models\StudentModel::where("STNO",$student['stno'])->get();
            if(count($checker)==0) {
                $query = new Models\StudentModel();
                $query->YEAR = $level;
                $query->LEVEL = $level;
                $query->FIRSTNAME = $student['firstname'];
                $query->SURNAME = $student['lastname'];
                $query->OTHERNAMES = $student['othernames'];
                $query->TITLE = $student['title'];
                $query->SEX = $student['gender'];
                $query->DATEOFBIRTH = $student['dob'];
                $query->NAME = $student['name'];
                $query->AGE = $student['age'];
                $query->MARITAL_STATUS = $student['marital'];
                $query->DATE_ADMITTED = $student['date-admitted'];
                $query->GRADUATING_GROUP = $group;
                $query->HAS_PASSWORD = 1;
                $query->HALL = $student['hall'];
                $query->ADDRESS = $student['address'];
                $query->RESIDENTIAL_ADDRESS = $student['address'];
                $query->EMAIL = $student['email'];
                $query->PROGRAMMECODE = $student['program'];
                $query->TELEPHONENO = $student['phone'];
                $query->COUNTRY = $student['country'];
                $query->REGION = $student['region'];
                $query->RELIGION = $student['religion'];
                $query->HOMETOWN = $student['hometown'];
                $query->GUARDIAN_NAME = $student['guardian-name'];
                $query->GUARDIAN_ADDRESS = $student['guardian-address'];
                $query->GUARDIAN_PHONE = $student['guardian-phone'];
                $query->GUARDIAN_OCCUPATION = $student['guardian-occupation'];
                $query->DISABILITY = $student['disable'];
                $query->STNO = $student['stno'];
                $query->INDEXNO = $student['stno'];
                $query->TYPE = $student['type'];
                $query->STUDENT_TYPE = $student['resident'];
                $query->ALLOW_REGISTER = 1;
                $query->STATUS = "Admitted";
                $query->SYSUPDATE = "1";
                $query->BILLS = $student['fees'];
                $query->BILL_OWING = $student['fees'];
                $query->PAID = 0.00;
                @$query->save();
                @$sys->getPassword($student['stno']);
            }
            else{
                Models\StudentModel::where("STNO",$student['stno'])->update(
                    array("FIRSTNAME"=> $student['firstname'],
                        "SURNAME"=> $student['lastname'],
                        "OTHERNAMES"=>$student['othernames'],
                        "NAME"=>$student['name'],
                        "BILLS"=> $student['fees'],
                        "BILL_OWING"=> $student['fees'],
                        "PROGRAMMECODE"=> $student['program'],
                        "HALL"=> $student['hall'],
                        "GRADUATING_GROUP"=> $group,
                    )
                );
            }
        }
        return Models\StudentModel::count();
    }
    public function pushToSrms2(Request $request)
    {


        header('Content-Type: application/json');


        //return response()->json(array('data'=>"Student with index number $student does not exist."));
        //$json = json_decode(file_get_contents("http://45.33.4.164/admissions/srms/forward"), true, JSON_PRETTY_PRINT);
        $json = json_decode(file_get_contents("http://127.0.0.1:8000/admissions/srms/forward"), true, JSON_PRETTY_PRINT);


        $a[] = (array)$json;

        foreach ($a as $i) {
            /*$data["admission_number"] = $i["application_number"];
            $data["name"] = $i["name"];
            $data["programme"] = $i["programme"];
            $data["fees"] = $i["fees"];
            $data["hall"] = $i["hall"];
            $data["type"] = "Newly admited applicant";*/


            $sql = Models\StudentModel::where("STNO", $i["application_number"])->first();
            if (empty($sql)) {
                /////////////////////////////////////////////////////


                $query = new Models\StudentModel();
                $query->YEAR = "100H";
                $query->LEVEL = "100H";
                $query->FIRSTNAME = $i["firstname"];
                $query->SURNAME = $i["lastname"];
                $query->OTHERNAMES = $i["firstname"];
                $query->NAME= $i["name"];
                $query->TITLE = $i["title"];
                $query->SEX = $i["gender"];
                $query->DATEOFBIRTH = $i["dob"];

                $query->AGE = $i["age"];


                $query->HALL = $i["hall"];
                //$query->ADDRESS = $student->ADDRESS;
                // $query->RESIDENTIAL_ADDRESS = $student->RESIDENTIAL_ADDRESS;
                // $query->EMAIL = $student->EMAIL;
                $query->PROGRAMMECODE = $i["program"];
                $query->TELEPHONENO = $i["phone"];
                /* $query->COUNTRY = $student->NATIONALITY;
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
                 $this->getPassword($student->APPLICATION_NUMBER);*/
                $query->save();
            } else {
                // $owing = $student->ADMISSION_FEES - $item->Amount;
                // Models\StudentModel::where("STNO", $item->StudentID)->update(array("BILL_OWING" => $owing));
            }

        }


        //return response()->json(array('data' => $data));


    }

    // api to call student password
    public function getStudentPassword(Request $request, $indexno,$token)
    {
        /*
         * 2dh838lXUEUE9zx@2hCELSKSA is for the radius authentication
         * the other is for TPCONNECT
         */

        $auth=["2dh838lXUEUE9zx@2hCELSKSA","TPC0NN@#123newe"];

        if(in_array($token,$auth)) {
                header('Content-Type: application/json');

                $record = @Models\PortalPasswordModel::where("username", $indexno)->first();
                $student = @Models\StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->first();
                $data = [];

                if (!empty($record)) {


                    $data["name"] = $student->NAME;
                    $data["phone"] = $student->TELEPHONENO;
                    $data["email"] = $student->EMAIL;
                    $data["password"] = $record->real_password;


                }

                return response()->json(array('data' => $data));

        }
        return response("Unauthorized access detected!", 401);

    }
    public  function getAllStudentPassword(Request $request,$token,SystemController $sys)
    {
        $auth = ["2dh838lXUEUE9zx@2hCELSKSA", "TPC0NN@#123newe"];

        if (in_array($token, $auth)) {
            header('Content-Type: application/json');

            //$record = @Models\PortalPasswordModel::get();
            $student = @Models\StudentModel::where("STATUS", 'In school')->get();


            $data = [];
            $a=[];


            if (!empty($student)) {

                foreach($student as $row) {


                    $data["indexno"] = $row->INDEXNO;
                    $data["name"] = $row->NAME;
                    $data["phone"] = $row->TELEPHONENO;
                    $data["email"] = $row->EMAIL;
                    $data["password"] = $sys->getStudentPassword($row->INDEXNO);
                    array_push($a,$data);
                }



            }


            return response()->json(array('data' => $a));
        }
        else {
            return response("Unauthorized access detected!", 401);
        }
    }
    // api to call staff password
    public function getStaffPassword(Request $request,$token)
    {
        /*
         * 2dh838lXUEUE9zx@2hCELSKSA is for the radius authentication
         * the other is for TPCONNECT
         */

        $auth = ["2dh838lXUEUE9zx@2hCELSKSA", "TPC0NN@#123newe"];

        if (in_array($token, $auth)) {
            header('Content-Type: application/json');

            //$record = @Models\PortalPasswordModel::get();
            $student = @User::get();


            $data = [];
            $a=[];


            if (!empty($student)) {

                foreach($student as $row) {


                    $data["name"] = $row->name;
                    $data["phone"] = $row->phone;
                    $data["email"] = $row->email;
                    $data["fund"] = $row->fund;
                    $data["password"] = $row->password;
                    array_push($a,$data);
                }



            }


            return response()->json(array('data' => $a));
        }
        else {
            return response("Unauthorized access detected!", 401);
        }

    }
    public function indexNumFormater($stuid){

        return str_replace('/','',$stuid);
    }
    public function getStudentData(Request $request, $student)
    {
        header('Content-Type: application/json');
        //$student=$this->indexNumFormater($student);
        //type-checking comparison operator is necessary

        if(!empty($student)) {
            $data = @Models\StudentModel::where("INDEXNO", $student)->orWhere("STNO", $student)->select("INDEXNO", "STNO", "NAME", "PROGRAMMECODE", "LEVEL", "BILLS", "STATUS")->first();


            if (empty($data)) {

                //return response()->json(array('data'=>"Student with index number $student does not exist."));
                $json = json_decode(file_get_contents("http://45.33.4.164/admissions/applicant/$student"), true, JSON_PRETTY_PRINT);

                $a[] = (array)$json;

                /*foreach ($a as $i) {
                    $data["admission_number"] = $i["application_number"];
                    $data["name"] = $i["name"];
                    $data["programme"] = $i["programme"];
                    $data["fees"] = $i["fees"];
                    $data["hall"] = $i["hall"];
                    $data["type"] = "Newly admited applicant";
                }*/

                foreach ($a as $i) {
                    $data["INDEXNO"] = $i["application_number"];
                    $data["STNO"] = $i["application_number"];
                    $data["NAME"] = $i["name"];
                    $data["PROGRAMMECODE"] = $i["programme"];
                    $data["LEVEL"] = '100';
                    $data["BILLS"] = $i["fees"];
                    $data["STATUS"] = "Applicant";

                }


            }

            if(!empty($data)) {

                return response()->json(array('data' => $data));

            }
            else{
                return response("No data found", 401);
            }

        }
        else{
            return response("Unauthorized access detected!", 401);
        }


    }

    public function fireVoucher(Request $request, SystemController $sys)
    {
        $data = Models\FormModel::where("PHONE", $request->input('phone'))->first();

        $pin = $data->serial;
        $serial = $data->PIN;
        $phone = $request->input('phone');
        $message = "Admission voucher: serial: $serial  pin code: $pin . Login at admissions.ttuportal.com Thanks";


        $sys->firesms($message, $phone, $phone);
        //return redirect("http://admissions.ttuportal.com");
        return redirect()->back();

    }

    public function getStudentProgram(Request $request, $program)
    {
        header('Content-Type: application/json');
        $indexno = $request->input("student");
        $data = @Models\StudentModel::where("PROGRAMMECODE", $program)->get();
        return response()->json(array('data' => $data));

    }

    public function getStudentHall(Request $request)
    {
        header('Content-Type: application/json');
        $indexno = $request->input("student");
        $data = @ApplicantModel::where("APPLICATION_NUMBER", $indexno)->first();
        if (!empty($data)) {
            return $data->HALL_ADMITTED;
        } else {
            return "Non Resident";
        }

    }


    public function qualityAssurance(Request $request, $indexno)
    {
        @StudentModel::where("INDEXNO", $indexno)->update(array("QUALITY_ASSURANCE" => 1));
        // return $this->response->json("status","Student Lecturer Assessment received at main system");
        return Response::json("Student Lecturer Assessment received at main system", "200");
    }

    public function liaison(Request $request, $indexno)
    {
        @ StudentModel::where("INDEXNO", $indexno)->update(array("LIAISON" => 1));
        return Response::json("Student Liaison forms received at main system", "200");
    }

    public function getReceipt()
    {
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

    public function updateReceipt()
    {
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

    public function  getLevel($ptype){
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
            $level="100BT";
        }
        else{
            $level="500MT";
        }
        return $level;
    }

    public function payFeeLive(Request $request, SystemController $sys)
    {
        header('Content-Type: application/json');
        $bankAuth = ["128ashbx393932", "1nm383ypmwd123"];
        $indexno = $request->input("indexno");
        $amount = $request->input("amount");
        $bank = $request->input("accountNumber");
        $type = $request->input("fee_type");
        $transactionId = $request->input("transactionId");
        $date = $request->input("transactionDate");
        $auth = $request->input("auth");
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        \DB::beginTransaction();
        try {

            if (in_array($auth, $bankAuth)) {


                $bankDetail = @Models\BankModel::where("ACCOUNT_NUMBER", $bank)->first();

                if ($bankDetail) {


                    $data = @Models\StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->first();



                    if (empty($data)) {



                        $json = json_decode(file_get_contents("http://45.33.4.164/admissions/applicant/$indexno"), true, JSON_PRETTY_PRINT);

                        $a[] = (array)$json;


                        foreach ($a as $i) {


                            if ($i["fees"] <= $amount) {
                                $details = "Full payment";


                            } else {
                                $details = "Part payment";
                            }


                            $receipt = $this->getReceipt();

                            $feeLedger = new Models\FeePaymentModel();
                            $feeLedger->INDEXNO = $indexno;
                            $feeLedger->PROGRAMME = $i["pcode"];
                            $feeLedger->STUDENT = $sys->getStudentIDfromIndexno($indexno);
                            $feeLedger->AMOUNT = $amount;
                            $feeLedger->PAYMENTTYPE = $type;
                            $feeLedger->PAYMENTDETAILS = $details . " of " . $type;
                            $feeLedger->BANK_DATE = $date;

                            $feeLedger->LEVEL = $this->getLevel($i["ptype"]);
                            $feeLedger->RECIEPIENT = "API_CALL";
                            $feeLedger->BANK = $bank;
                            $feeLedger->TRANSACTION_ID = $transactionId;
                            $feeLedger->RECEIPTNO = $receipt;
                            $feeLedger->YEAR = $year;
                            $feeLedger->FEE_TYPE = $type;
                            $feeLedger->SEMESTER = $sem;
                            if ($feeLedger->save()) {

                                // @StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->update(array("BILL_OWING" => $owing, "PAID" => $paid));
                                @$this->updateReceipt();
                                \DB::commit();
                                //return Response::json("Success", "01");
                                header('Content-Type: application/json');
                                // return  json_encode(array('responseCode'=>'01','responseMessage'=>'Successfully Processed'));
                                return response()->json(array('responseCode' => '01', 'responseMessage' => 'Successfully Processed'));

                            } else {
                                header('Content-Type: application/json');
                                // return  json_encode(array('responseCode'=>'09','responseMessage'=>'Failed'));

                                return response()->json(array('responseCode' => '09', 'responseMessage' => $i['pcode']));
                            }

                        }
                    }
                    else{






                        if ($data->BILL_OWING <= $amount) {
                            $details = "Full payment";


                        } else {
                            $details = "Part payment";
                        }

                        $receipt = $this->getReceipt();

                        $feeLedger = new Models\FeePaymentModel();
                        $feeLedger->INDEXNO = $data->INDEXNO;
                        $feeLedger->STUDENT= $data->ID;
                        $feeLedger->PROGRAMME = $data->PROGRAMMECODE;
                        $feeLedger->AMOUNT = $amount;
                        $feeLedger->PAYMENTTYPE = $type;
                        $feeLedger->PAYMENTDETAILS = $details . " of " . $type;
                        $feeLedger->BANK_DATE = $date;


                        $level=mb_substr($data->INDEXNO, 0, 3);

                        $owing=$data->BILL_OWING - $amount;
                        $paid=$data->PAID + $amount;



                        $feeLedger->LEVEL = $data->LEVEL;
                        $feeLedger->RECIEPIENT = "API_CALL";
                        $feeLedger->BANK = $bank;
                        $feeLedger->TRANSACTION_ID = $transactionId;
                        $feeLedger->RECEIPTNO = $receipt;
                        $feeLedger->YEAR = $year;
                        $feeLedger->FEE_TYPE = $type;
                        $feeLedger->SEMESTER = $sem;
                        if ($feeLedger->save()) {

                            @StudentModel::where("INDEXNO", $data->INDEXNO)->orWhere("STNO", $data->INDEXNO)->update(array("BILL_OWING" => $owing, "PAID" => $paid));
                            @$this->updateReceipt();
                            \DB::commit();
                            //return Response::json("Success", "01");
                            header('Content-Type: application/json');
                            // return  json_encode(array('responseCode'=>'01','responseMessage'=>'Successfully Processed'));
                            return response()->json(array('responseCode' => '01', 'responseMessage' => 'Successfully Processed'));

                        } else {
                            header('Content-Type: application/json');
                            // return  json_encode(array('responseCode'=>'09','responseMessage'=>'Failed'));

                            return response()->json(array('responseCode' => '09', 'responseMessage' => $data->PROGRAMMECODE));
                        }


                    }

                }

                else {
                    return response()->json(array('responseCode' => '08', 'responseMessage' => 'Bank Account does not exist'));

                }
            } else {
                return response()->json(array('responseCode' => '08', 'responseMessage' => 'Unknown Bank Entity'));

            }
        } catch (\Exception $e) {
            \DB::rollback();
        }

    }

    public function payFee(Request $request, SystemController $sys)
    {
        header('Content-Type: application/json');
        $bankAuth = ["128ashbx393932", "1nm383ypmwd123"];
        $indexno = $request->input("indexno");
        $amount = $request->input("amount");
        $bank = $request->input("accountNumber");
        $type = $request->input("fee_type");
        $transactionId = $request->input("transactionId");
        $date = $request->input("transactionDate");
        $auth = $request->input("auth");
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        \DB::beginTransaction();
        try {

            $data = @StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->first();

            $bankDetail = @Models\BankModel::where("ACCOUNT_NUMBER", $bank)->first();

            if ($bankDetail) {

                if (!empty($data)) {

                    if (!empty($data)) {
                        // $bill = $sys->getYearBill($year, $data->LEVEL, $data->PROGRAMMECODE);
                        $billOwing = $data->BILL_OWING;
                        $owing = $billOwing - $amount;
                        if ($billOwing <= $amount) {
                            $details = "Full payment";


                        } else {
                            $details = "Part payment";
                        }
                        $paid = $data->PAID + $amount;
                        $que = Models\PortalPasswordModel::where("username", $indexno)->first();
                        if (empty($que) && !empty($indexno)) {
                            $program = $data->PROGRAMMECODE;
                            $str = 'abcdefhkmnprtuvwxy34678abcdefhkmnprtuvwxy34678';
                            $shuffled = str_shuffle($str);
                            $vcode = substr($shuffled, 0, 9);
                            $real = strtoupper($vcode);
                            $level = $data->LEVEL;
                            Models\PortalPasswordModel::create([
                                'username' => $indexno,
                                'real_password' => $real,
                                'level' => $level,
                                'programme' => $program,
                                'biodata_update' => '1',
                                'password' => bcrypt($real),
                            ]);
                            $phone = $data->TELEPHONENO;
                            $fname = $data->FIRSTNAME;

                            $message = "Online credential: visit records.ttuportal.com with $indexno as your username  and $real as password and follow the course registration steps.";


                            // @$sys->firesms($message, $phone, $indexno);

                            \DB::commit();

                        }

                        $receipt = $this->getReceipt();

                        $feeLedger = new Models\FeePaymentModel();
                        $feeLedger->INDEXNO = $indexno;
                        $feeLedger->PROGRAMME = $data->PROGRAMMECODE;

                        $feeLedger->AMOUNT = $amount;
                        $feeLedger->PAYMENTTYPE = $type;
                        $feeLedger->PAYMENTDETAILS = $details . " of " . $type;
                        $feeLedger->BANK_DATE = $date;

                        $feeLedger->LEVEL = $data->LEVEL;
                        $feeLedger->RECIEPIENT = "API_CALL";
                        $feeLedger->BANK = $bank;
                        $feeLedger->TRANSACTION_ID = $transactionId;
                        $feeLedger->RECEIPTNO = $receipt;
                        $feeLedger->YEAR = $year;
                        $feeLedger->FEE_TYPE = $type;
                        $feeLedger->SEMESTER = $sem;
                        if ($feeLedger->save()) {

                            @StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $indexno)->update(array("BILL_OWING" => $owing, "PAID" => $paid));
                            @$this->updateReceipt();
                            \DB::commit();
                            //return Response::json("Success", "01");
                            header('Content-Type: application/json');
                            // return  json_encode(array('responseCode'=>'01','responseMessage'=>'Successfully Processed'));
                            return response()->json(array('responseCode' => '01', 'responseMessage' => 'Successfully Processed'));

                        } else {
                            header('Content-Type: application/json');
                            // return  json_encode(array('responseCode'=>'09','responseMessage'=>'Failed'));
                            return response()->json(array('responseCode' => '09', 'responseMessage' => 'Failed'));
                        }

                    } else {

                        return response()->json(array('responseCode' => '09', 'responseMessage' => 'Student or Applicant does not exist'));

                    }


                } else {

                    return response()->json(array('responseCode' => '09', 'responseMessage' => 'Student or Applicant does not exist'));

                }


            } else {
                return response()->json(array('responseCode' => '08', 'responseMessage' => 'Bank Account does not exist'));

            }
        } catch (\Exception $e) {
            \DB::rollback();
        }


    }


    /**
     * Destroy the given task.
     *
     * @param  Request $request
     * @param  Task $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }

}