<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\FeeModel;
use App\Models\FeePaymentModel;
use App\Models\StudentModel;
use App\Models;
use App\Models\ReceiptModel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Excel;

class FeeController extends Controller
{

    public function log_query()
    {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }

    public function createBill(Request $request, SystemController $sys)
    {


        $this->validate($request, [
            'level' => 'required',
            'amount' => 'required|numeric',
            'program' => 'required',
        ]);
        $array = $sys->getSemYear();

        $year = $array[0]->YEAR;
        $amount = $request->input('amount');
        $level = $request->input('level');
        $program = $request->input('program');
        Models\BillModel::create([
            'LEVEL' => $level,
            'AMOUNT' => $amount,
            'PROGRAMME' => $program,
            'YEAR' => $year
        ]);
        return redirect("/finance/reports/fees/")->with("success", "Bill created successfully");

    }

    public function getTotalPayment($student, $yearr)
    {
        $sys = new SystemController();
        $array = $sys->getSemYear();
        if ($term == "" && $yearr == "") {
            //$term = $array[0]->SEMESTER;
            $yearr = $array[0]->YEAR;
        }

        $fee = FeePaymentModel::query()->where('YEAR', '=', $yearr)->where('INDEXNO', $student)->sum('AMOUNT');
        return $fee;


    }

    public function masterLedger(Request $request, SystemController $sys)
    {

        $array = $sys->getSemYear();
       // $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $fee = FeePaymentModel::query();

        if ($request->has('level') && trim($request->input('level')) != "") {
            $fee->where("LEVEL", $request->input("level", ""));
        }

        if ($request->has('indexno') && trim($request->input('indexno')) != "") {
            $fee->where("INDEXNO", '=', $request->input("indexno", ""));
        }
        if ($request->has('year') && trim($request->input('year')) != "") {
            $fee->where("YEAR", "=", $request->input("year", ""));
        }

        if ($request->has('program') && trim($request->input('program'))) {
            $fee->where("PROGRAMME", "=", $request->input('program'));
        }


        if ($request->has('type') && trim($request->input('type'))) {
            $fee->where("PAYMENTTYPE", "=", $request->input('type'));
        }
        $data = $fee->groupBy('INDEXNO')->orderBy('TRANSDATE', 'DESC')->paginate(200);

        $request->flashExcept("_token");
        \Session::put('students', $data);

        foreach ($data as $key => $row) {
            $a[] = $row->AMOUNT;
            //$data[$key]->TOTALS = array_sum($a);

            $t[] = $this->getTotalPayment($row->INDEXNO, $row->YEAR);
            $data[$key]->TOTALS = @array_sum($t);
        }

        $totals = @$sys->formatMoney($data[$key]->TOTALS);
        return view('finance.fees.masterLedger')->with("data", $data)
            ->with('program', $sys->getProgramList())
            ->with('year', $this->years())
            ->with('level', $sys->getLevelList())
            ->with('bank', $this->banks())
            ->with('total', $totals);

    }

    public function feeSummary(Request $request)
    {
        $sys = new SystemController();


        if ($request->isMethod("get")) {

            return view('finance.fees.fee_summary')
                ->with('program', $sys->getProgramByIDList())
                ->with('year', $this->years());

        } else {

            $fee = FeeModel::query()->where('STATUS', 'approved');

            if ($request->has('level') && trim($request->input('level')) != "") {
                $fee->where("LEVEL", $request->input("level", ""));
            }
            if ($request->has('program') && trim($request->input('program'))) {
                $fee->where("PROGRAMME", "=", $request->input('program'));
            }
            if ($request->has('year') && trim($request->input('year')) != "") {
                $fee->where("YEAR", "=", $request->input("year", ""));
            }
            $data = $fee->orderBy('PROGRAMME')->orderBy('YEAR')->paginate(100);
            $data->setPath(url("fee_summary"));
            $programm = $sys->getProgramByID($request->input('program'));
            $yearr = $request->input('year');
            $request->flashExcept("_token");
            foreach ($data as $key => $row) {
                $total[] = $row->AMOUNT;
                $data[$key]->TOTALS = array_sum($total);

            }
            return view('finance.fees.fee_summary')->with('data', $data)
                ->with('program', $sys->getProgramByIDList())
                ->with('year', $this->years())
                ->with('academicYear', $yearr)
                ->with('programme', $programm)
                ->with('level', $request->input("level", ""));


        }
    }

    /*
     * this controller method handles everything about students 
     * who are owing and those who have paid
     */
    public function owingAndPaid(Request $request)
    {
        $student = StudentModel::query();
        if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $student->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $student->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('level') && trim($request->input('level')) != "") {
            $student->where("YEAR", $request->input("level", ""));
        }
        if ($request->has('season') && trim($request->input('season')) != "") {
            $student->where("TYPE", "=", $request->input("season", ""));
        }
        if ($request->has('indexno') && trim($request->input('indexno')) != "") {
            $student->where("INDEXNO", "=", $request->input("indexno", ""));
        }
        if ($request->has('type') && trim($request->input('type')) == "owing") {
            $student->where("BILL_OWING", ">", "0");
        }
        if ($request->has('filter') && trim($request->input('filter')) != "" && $request->input('amount') != "") {
            $filter = $request->input('filter');
            $amount = $request->input('amount');
            if ($filter == '=') {
                $student->where("BILL_OWING", $amount);
            } else {
                $student->where("BILL_OWING", "$filter", $amount);
                // dd($request);
            }
        }
        $sys = new SystemController();
        $data = $student->paginate(200);
        $data->setPath(url("owing_paid"));
        $request->flashExcept("_token");
        foreach ($data as $key => $row) {
            $total[] = $row->BILL_OWING;
            $data[$key]->TOTALS = array_sum($total);
            $data[$key]->TOTALS = @$sys->formatMoney($data[$key]->TOTALS);
        }
        \Session::put('students', $data);
        return view('finance.fees.owing')->with("data", $data)->with('level', $sys->getLevelList())
            ->with('program', $sys->getProgramList());

    }

    public function sendFeeSMS(Request $request)
    {
        $message = $request->input("message", "");
        $query = \Session::get('students');
        $sms = new SystemController();
        \DB::beginTransaction();
        try {

            foreach ($query as $rtmt => $member) {


                if ($sms->firesms($message, $member->TELEPHONENO, @$member->INDEXNO)) {

                    \Session::forget('students');
                    return redirect('/owing_paid')->with('success', array('Message sent to students succesfully'));

                } else {
                    return redirect('/owing_paid')->withErrors("SMS could not be sent.. please verify if you have sms data and internet access.");
                }
            }
        } catch (\Exception $e) {
            \DB::rollback();

        }
    }

    public function new_receiptno()
    {
        $receiptno_query = Models\ReceiptModel::first();
        $receiptno_query->increment("no", 1);
        $receiptno = str_pad($receiptno_query->receiptno, 12, "0", STR_PAD_LEFT);

        return $receiptno;

    }

    public function pad_receiptno($receiptno)
    {
        return str_pad($receiptno, 12, "0", STR_PAD_LEFT);
    }

    /**
     * Display a list of proposed fees.
     *
     * @param  Request $request
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $sys = new SystemController();
        $array = $sys->getSemYear();
        //$sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $fee = FeeModel::query();

        if ($request->has('level') && trim($request->input('level')) != "") {
            $fee->where("LEVEL", $request->input("level", ""));
        }

        if ($request->has('year') && trim($request->input('year')) != "") {
            $fee->where("YEAR", "=", $request->input("year", ""));
        }

        if ($request->has('program') && trim($request->input('program'))) {
            $fee->where("PROGRAMME", "=", $request->input('program'));
        }


        $data = $fee->orderBy('LEVEL', 'ASC')->paginate(100);

        $request->flashExcept("_token");

        foreach ($data as $key => $row) {
            $total[] = $row->AMOUNT;
            $data[$key]->TOTALS = array_sum($total);
            $data[$key]->TOTALSTUDENTS = @$sys->getTotalFeeByProrammeLevel($row->PROGRAMME, $row->LEVEL);
            $data[$key]->TOTALAMOUNT = @$sys->getTotalFeeByProrammeLevel($row->PROGRAMME, $row->LEVEL) * $row->AMOUNT;
            $total_proposed[] = $data[$key]->TOTALAMOUNT;
            $data[$key]->TotalProposed = array_sum($total_proposed);
        }

        $totalProposed = @$sys->formatMoney($data[$key]->TotalProposed);
        return view('finance.fees.index')->with("data", $data)
            ->with('program', $sys->getProgramByIDList())
            ->with('year', $this->years())
            ->with('bank', $this->banks())
            ->with('totalProposed', $totalProposed);

    }

    public function anyData(Request $request)
    {


        $fees = FeeModel::join('tpoly_programme', 'tpoly_fees.PROGRAMME', '=', 'tpoly_programme.ID')
            ->select(['tpoly_fees.ID', 'tpoly_fees.NAME', 'tpoly_fees.DESCRIPTION', 'tpoly_fees.AMOUNT', 'tpoly_fees.FEE_TYPE', 'tpoly_fees.SEASON_TYPE', 'tpoly_programme.PROGRAMME', 'tpoly_fees.LEVEL', 'tpoly_fees.YEAR', 'tpoly_fees.STATUS', 'tpoly_fees.NATIONALITY']);


        return Datatables::of($fees)
            ->addColumn('action', function ($fee) {
                if ($fee->STATUS == 'approved') {
                    return "<span class='uk-text-success'>Approved ready</span>";
                } else {
                    return
                        \Form::open(['action' => ['FeeController@destroy', 'id' => $fee->ID], 'method' => 'DELETE', 'name' => 'myform', 'style' => 'display: inline;'])

                        . " <button type=\"button\" class=\"md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light\" onclick=\"UIkit.modal.confirm('Are you sure you want to delete this fee?', function(){ document.forms[0].submit(); });\"><i  class=\"sidebar-menu-icon material-icons md-18\">delete</i></button>
                         <input type='hidden' name='fee' value='$fee->ID'/>  
                      " . \Form::close() . "

                    <button title='click to approve fees' type=\"button\" class=\"md-btn  md-btn-primary md-btn-small   md-btn-wave-light waves-effect waves-button waves-light\" onclick=\"UIkit.modal.confirm('Are you sure you want to bill student with this fee item?', function(){   return window.location.href='run_bill/$fee->ID/id'     ; });\"><i  class=\"sidebar-menu-icon material-icons md-18\">done</i></button> 
                       
                   ";


                }


            })
            ->editColumn('id', '{!! $ID!!}')
            ->setRowId('id')
            ->setRowClass(function ($fee) {
                // return $fee->ID % 2 == 0 ? 'uk-text-success uk-text-bold' : 'uk-text-warning uk-text-bold';
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

    // approve bill here
    public function approve(Request $request, $id)
    {
        if (@\Auth::user()->role == 'FO') {
            $sys = new SystemController();
            $array = $sys->getSemYear();
            //$sem1 = $array[0]->SEMESTER;
            $year1 = $array[0]->YEAR;
            /*
             * make sure only bills for the current semester are charged againts
             * students
             * get current semester and year
             */

            //get the current user in session
            $user = \Auth::user()->id;
            //  dd($user);
            //get the bill item
            $query = FeeModel::where('ID', $id)->get()->toArray();

            $programme = $sys->getProgramCodeByID($query[0]['PROGRAMME']);

            $amount = $query[0]['AMOUNT'];
            $level = $query[0]['LEVEL'];
            $year = $query[0]['YEAR'];
            $name = $query[0]['NAME'];
            // if the fee is actually for the current academic year
            if ($year1 == $year) {
                \DB::beginTransaction();
                try {

                    // get students details
                    $balance = StudentModel::where("PROGRAMMECODE", $programme)->where('YEAR', $level)->where('STATUS', '=', 'In school')->limit(1)->get();

                    $bill = @$balance[0]->BILLS + $amount;
                    $billOwing = @$balance[0]->BILL_OWING + $amount;

                    $sql = StudentModel::where("PROGRAMMECODE", $programme)->where('YEAR', $level)->where('STATUS', '=', 'In school')->update(array("BILLS" => $bill, 'BILL_OWING' => $billOwing));

                    if (!$sql) {

                        return redirect("/view_fees")->with("error", "Error in billing:<span style='font-weight:bold;font-size:13px;'> $name with amount GHC$amount for level $level $programme $year  academic year could not be applied!</span>");
                    } else {
                        $sql = FeeModel::where("ID", $id)->update(array("APPROVED_BY" => $user, 'STATUS' => 'approved'));

                        if ($sql) {
                            \DB::commit();
                            return redirect("/view_fees")->with("success", "Following bill:<span style='font-weight:bold;font-size:13px;'>  $name with amount GHC$amount for level $level $programme $year  academic year successfully applied!</span> ");
                        }
                    }
                } catch (\Exception $e) {
                    \DB::rollback();
                }
            } else {
                return redirect("/view_fees")->with("error", array("Error in billing:<span style='font-weight:bold;font-size:13px;'> $name with amount GHC$amount for level $level $programme $year  is not meant for the current academic year <br/> and therefore could not be applied!</span>"));
            }
        } else {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }

    public function showPayform()
    {
        return view('finance.fees.payfee');
    }

    public function showStudent(Request $request, SystemController $sys)
    {

        $array = $sys->getSemYear();
       // $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $student = explode(',', $request->input('q'));
        $student = $student[0];

        $sql = StudentModel::where("INDEXNO", $student)->orwhere("STNO", $student)->get();
//         if($sys->getStudentAccountInfo($student)==1){
        if (@\Auth::user()->department != 'Finance') {
            $sql = StudentModel::where("INDEXNO", $student)->orwhere("STNO", $student)->whereHas('programme', function ($q) {
                $q->whereHas('departments', function ($q) {
                    $q->whereIn('DEPTCODE', array(@\Auth::user()->department));
                });
            })->get();
        }
        $finance = FeePaymentModel::where("INDEXNO", $student)->orWhere("INDEXNO", $sql[0]->STNO)->paginate(10);
        //dd($finance); 
        if (count($sql) == 0) {

            return redirect("/pay_fees")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
        } else {

            return view("finance.fees.processPayment")->with('data', $sql)->with('year', $year)->with('banks', $this->banks())->with('receipt', $this->getReceipt())->with('level', $sys->getLevelList())
                ->with("finance", $finance);

        }
//         }
//         else{
//              return redirect("/students")->with(  "error" ,"<span style='font-weight:bold;font-size:13px;'> Student must update his/her data on the portal before accepting school fees from him/her!!!</span>");
//         
//         }
    }

    // this handle late fee payment ie penalty
    public function showStudentPenalty(Request $request)
    {

        $student = explode(',', $request->input('q'));
        $student = $student[0];

        $sql = StudentModel::where("INDEXNO", $student)->orwhere("STNO", $student)->get();
        //dd($sql);
        if (count($sql) == 0) {

            return redirect("/pay_fees")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
        } else {
            $sys = new SystemController();
            $array = $sys->getSemYear();
            //$sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            return view("finance.fees.process_penalty")->with('data', $sql)->with('year', $year)->with('banks', $this->banks())->with('receipt', $this->getReceipt());

        }
    }

    public function processPayment(Request $request)
    {
        if (@\Auth::user()->department == "Finance" || @\Auth::user()->department == "Tptop") {
            $sys = new SystemController();
            $array = $sys->getSemYear();
            //$sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            $phone = $request->input('phone');
            $status = $request->input('status');
            $user = \Auth::user()->fund;
            $feetype = "School Fees";
            if ($request->has('type') && $request->input('type') == "Late Registration Penalty") {
                \DB::beginTransaction();
                try {
                    \Session::put('type', 'late');

                    $amount = $request->input('amount');
                    $receipt = $request->input('receipt');
                    $indexno = $request->input('student');
                    $status = $request->input('status');
                    $program = $request->input('programme');
                    $level = $request->input('level');
                    $feeLedger = new FeePaymentModel();
                    $feeLedger->INDEXNO = $indexno;
                    $feeLedger->PROGRAMME = $program;
                    $feeLedger->AMOUNT = $amount;
                    $feeLedger->PAYMENTTYPE = "Late Registration Fee";

                    $feeLedger->PROGRAMME = $program;
                    $feeLedger->LEVEL = $level;
                    $feeLedger->RECIEPIENT = $user;

                    $feeLedger->RECEIPTNO = $receipt;
                    $feeLedger->YEAR = $year;
                    $feeLedger->FEE_TYPE = "Late Registration Fee";
                    //$feeLedger->SEMESTER = $sem;
                    if ($feeLedger->save()) {
                        \DB::commit();

                        $message = "Hi $indexno you have just paid GHC$amount as late registration fee";
//                    if ($sys->firesms($message, $phone, $indexno)) {
//                        
//                    }
                        $this->updateReceipt();
                        $url = url("printreceiptLate/" . trim($receipt));
                        $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                        $request->session()->flash("success", "Payment successfully   $print_window");
                        return redirect("/pay_fees");
                    }
                } catch (\Exception $e) {
                    \DB::rollback();
                    redirect()->back()->with('error', 'Error processing payment');
                }
            } else {

                $amount = $request->input('amount');
                $receipt = $request->input('receipt');
                $indexno = $request->input('student');
                $owing = $request->input('bill') - $amount;
                $program = $request->input('programme');
                $level = $request->input('level');
                $bank = $request->input('bank');
                //dd($level );

                $previousOwing = $request->input('prev-owing');
                if (empty($previousOwing)) {
                    $previousOwing = 0.0;
                }


                $bank_date = $request->input('bank_date');

                $details = $request->input('payment_detail');

                $transactionID = $request->input('transaction');

                if ($request->input('bill') <= $amount) {
                    $paymenttype = "Full payment";

                } else {
                    $paymenttype = "Part payment";
                }

                $sql = FeePaymentModel::where("RECEIPTNO", $receipt)->first();


                if (empty($sql)) {
                    $feeLedger = new FeePaymentModel();
                    $feeLedger->INDEXNO = $indexno;
                    $feeLedger->PROGRAMME = $program;
                    $feeLedger->AMOUNT = $amount;
                    $feeLedger->PAYMENTTYPE = $paymenttype;
                    $feeLedger->PAYMENTDETAILS = $details;
                    $feeLedger->BANK_DATE = $bank_date;
                    $feeLedger->PROGRAMME = $program;
                    $feeLedger->LEVEL = $level;
                    $feeLedger->RECIEPIENT = $user;
                    $feeLedger->BANK = $bank;
                    $feeLedger->TRANSACTION_ID = $transactionID;
                    $feeLedger->RECEIPTNO = $receipt;
                    $feeLedger->YEAR = $year;
                    $feeLedger->FEE_TYPE = $feetype;
                    //$feeLedger->SEMESTER = $sem;

                    if ($feeLedger->save()) {
                        $this->updateReceipt();
                        $newyear = substr($level, 0, 1);


                        $balance = StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $request->input('stno'))->where('STATUS', '=', 'In school')->get();
                        //dd($balance);


                        $billOwing = (@$balance[0]->BILL_OWING - $amount) + $previousOwing;
                        $billpaid = (@$balance[0]->PAID + $amount);
                        
                        StudentModel::where('INDEXNO', $indexno)->orWhere("STNO", $request->input('stno'))->update(array('BILL_OWING' => $billOwing, 'PAID' => $billpaid, 'TELEPHONENO' => $phone, 'SYSUPDATE' => '1', "ALLOW_REGISTER" => $status));
                         //dd($billOwing);


                        $smsOwing = @StudentModel::where("INDEXNO", $indexno)->orWhere("STNO", $request->input('stno'))->where('STATUS', '=', 'In school')->get();


                        $smsOwe = $smsOwing[0]->BILL_OWING;
                        $firstname = $smsOwing[0]->FIRSTNAME;
                        \Session::put('applicant', $indexno);
                        $message = "Hi $firstname, GHS$amount paid as $feetype, you owe GHS$smsOwe Please visit ttuportal.com to do your course registration. You can print your receipt using receipt no. $receipt ";
                        \DB::commit();
                       // if ($sys->firesms($message, $phone, $indexno)) {

                       // }

                        $url = url("printreceipt/" . trim($receipt));
                        $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                        $request->session()->flash("success", "Payment successfully   $print_window");
                        return redirect("/pay_fees");
                    }
                } else {
                    return redirect("/students")->with("error", " <span style='font-weight:bold;font-size:13px;'> Payment already made with this receipt  number  </span>");

                }
            }


        } else {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');

        }
    }

    // allow student to register by authority
    public function allowRegister(Request $request)
    {
        if (@\Auth::user()->department == "Finance" || @\Auth::user()->department == "Tpmid" || @\Auth::user()->department == "Tptop") {
            if ($request->isMethod("get")) {
                return view("finance.fees.allowRegister");
            } else {
                $sys = new SystemController();
                $array = $sys->getSemYear();
                //$sem = $array[0]->SEMESTER;
                $year = $array[0]->YEAR;
                $student = explode(',', $request->input('q'));
                $student = $student[0];

                $sql = StudentModel::where("INDEXNO", $student)->orwhere("STNO", $student)->get();
                //dd($sql);
                if (count($sql) == 0) {

                    return redirect("/students")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
                } else {

                    return view("finance.fees.processProtocol")->with('data', $sql)->with('year', $year);

                }
            }
        } else {
            return redirect("/dashboard");
        }

    }

    public function processProtocol(Request $request, SystemController $sys)
    {
        if (@\Auth::user()->department == "Tpmid" || @\Auth::user()->department == "Tptop") {

            $this->validate($request, [
                'action' => 'required',
                'reason' => 'required',
                'type' => 'required',
            ]);
            $array = $sys->getSemYear();
            //$sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            $action = $request->input("action");
            $student = $request->input("student");
            $reason = $request->input("reason");
            $type = $request->input("type");
            $protocol = new Models\ProtocolModel();

            $protocol->year = $year;
            //$protocol->sem = $sem;
            $protocol->student = $student;
            $protocol->reason = $reason;
            $protocol->action = $action;
            $protocol->policy = $type;
            $protocol->user = @\Auth::user()->fund;
            Models\StudentModel::where("INDEXNO", $student)->update(array('STATUS' => 'In school',"PROTOCOL" => 1));
            if ($protocol->save()) {
                return redirect("/students")->with("success", "Registration protocol subscribed for $student successful. He/She can proceed to register");
            } else {
                return redirect("/finance/protocol")->with("error", "Error processing protocol for $student. Try again later");

            }

        } else {
            return redirect("/dashboard");
        }
    }

    public function printOldReceipt(Request $request)
    {

        if (@\Auth::user()->department == "Finance" || @\Auth::user()->department == "Tpmid" || @\Auth::user()->department == "Tptop") {
            if ($request->isMethod("get")) {
                return view("finance.fees.printLostReceipt");
            } else {
                //  dd("d");
                $sys = new SystemController();
                $array = $sys->getSemYear();
                //$sem = $array[0]->SEMESTER;
                $year = $array[0]->YEAR;
                $receipt = explode(',', $request->input('q'));
                $receipt = $receipt[0];

                $sql = FeePaymentModel::where("RECEIPTNO", $receipt)->get();
                //dd($sql);
                if (count($sql) == 0) {
                    //echo "<script>alert('No fee payment receipt found for this student')</script>";
                    return redirect("/print/receipt")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
                } else {
                    //$indexNo = $sql[0]->INDEXNO;
                    $receiptQuery = FeePaymentModel::where("RECEIPTNO", $receipt)->first();
                    if (!empty($receiptQuery)) {
                        $receipt = $receiptQuery->RECEIPTNO;
                        $url = url("printreceipt/" . trim($receipt));
                        $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                        $request->session()->flash("success", "Receipt printing .....   $print_window");
                        return redirect("/print/receipt");
                    } else {
                        return redirect("/print/receipt")->with("error", "<span style='font-weight:bold;font-size:13px;'>No receipt for this student was found in the system!</span>");

                    }
                }
            }

        } else {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');

        }
    }


    public function printPasswordReceipt(Request $request)
    {

        if (@\Auth::user()->department == "Finance" || @\Auth::user()->department == "Tptop") {
            if ($request->isMethod("get")) {
                return view("finance.fees.payfee");
            } else {
                //dd("FFF");
                $sys = new SystemController();
                $array = $sys->getSemYear();
                //$sem = $array[0]->SEMESTER;
                $year = $array[0]->YEAR;
                $student = explode(',', $request->input('q'));
                $student = $student[0];

                $sql = StudentModel::where("INDEXNO", $student)->orwhere("STNO", $student)->get();
                // dd($sql);
                if (count($sql) == 0) {
                    //echo "<script>alert('No fee payment receipt found for this student')</script>";
                    return redirect("/print/password")->with("error", "<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
                } else {
                    $indexNo = $sql[0]->indexNo;
                    $receiptQuery = FeePaymentModel::where("INDEXNO", $student)->orWhere("INDEXNO", $sql[0]->STNO)->where("YEAR", $year)->orderBy("ID", "DESC")->first(); 
                    if (!empty($receiptQuery)) {
                        $receipt = $receiptQuery->RECEIPTNO;
                        $url = url("printreceipt/" . trim($receipt));
                        $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                        $request->session()->flash("success", "Receipt printing .....   $print_window");
                        return redirect("/print/password");
                    } else {
                        return redirect("/print/password")->with("error", "<span style='font-weight:bold;font-size:13px;'>No receipt for this student was found in the system!</span>");
                    }
                }
            }
        } else {
            return redirect("/dashboard");
        }
    }


    public function banks()
    {

        $banks = \DB::table('tpoly_banks')
            ->lists('NAME', 'ACCOUNT_NUMBER');
        return $banks;
    }

    public function programmes()
    {

        $program = \DB::table('tpoly_programme')->get();

        foreach ($program as $p => $value) {
            $programs[] = $value->PROGRAMMECODE;
        }
        return $programs;
    }

    public function programmeSearch()
    {

        $program = \DB::table('tpoly_programme')->get();

        foreach ($program as $p => $value) {
            $programs[] = $value->ID;
        }
        return $programs;
    }

    public function getReceipt()
    {
        \DB::beginTransaction();
        try {
            $receiptno_query = ReceiptModel::first();
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
            $query = ReceiptModel::first();

            $result = $query->increment("no");
            if ($result) {
                \DB::commit();
            }

        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    public function printreceiptLate(Request $request, $receiptno)
    {

        // $this->show_query();

        $transaction = FeePaymentModel::where("RECEIPTNO", $receiptno)->with("student", "bank"
        )->first();

        if (empty($transaction)) {
            abort(434, "No Fee payment   with this receipt <span class='uk-text-bold uk-text-large'>{{$receiptno}}</span>");
        }

        $words = $this->convert($transaction->AMOUNT);


        return view("finance.fees.late_receipt")->with("transaction", $transaction)->with('words', $words);


    }

    public function printreceipt(Request $request, $receiptno)
    {

        // $this->show_query();


        $transaction = FeePaymentModel::where("RECEIPTNO", $receiptno)->with("student", "bank"
        )->first();


        if (empty($transaction)) {
            abort(434, "No Fee payment   with this receipt <span class='uk-text-bold uk-text-large'>{{$receiptno}}</span>");
        } else {


            $data = StudentModel::where("INDEXNO", $transaction->INDEXNO)->orWhere("STNO", $transaction->INDEXNO)->first();
            //dd($data);
            $words = $this->convert($transaction->AMOUNT);


            return view("finance.fees.receipt")->with("student", $data)
                ->with("transaction", $transaction)->with('words', $words);
        }

    }

    public function uploadFeesComponent(Request $request, SystemController $sys)
    {
        //get the current user in session
        if ($request->isMethod("get")) {
            return view("finance.fees.uploadComponent");
        } else {

            $array = $sys->getSemYear();
            //$sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            $user = \Auth::user()->id;
            $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
            $file = $request->file('file');
            $path = $request->file('file')->getRealPath();

            if (!empty($file)) {

                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, $valid_exts)) {

                    $data = Excel::load($path, function ($reader) {

                    })->get();

                    foreach ($data as $key => $value) {
                        $num = count($data);


                        $category = $value->group;
                        $Component = $value->component;
                        $amount = $value->amount;
                        $level = $value->year;
                        $nationality = $value->nationality;

                        $programs = $sys->programmeCategorySearchByCode(); // check if the programmes in the file tally wat is in the db
                        if (in_array($category, $programs)) {

                            $transaction = Models\ProgrammeModel::where("SLUG", $category)->get();
                            foreach ($transaction as $key => $row) {

                                $fee = new FeeModel();
                                $fee->NAME = $Component;

                                $fee->DESCRIPTION = $Component;
                                $fee->AMOUNT = $amount;
                                $fee->FEE_TYPE = 'School Fees';
                                $fee->NATIONALITY = $nationality;
                                $fee->PROGRAMME = $row->ID;
                                $fee->LEVEL = $level;
                                //$fee->SEMESTER = $sem;
                                $fee->YEAR = $year;

                                $fee->CREATED_BY = $user;
                                if ($fee->save()) {
                                    \DB::commit();
                                } else {
                                    return redirect('/uploadDetailFees')->back()->withErrors("Fee could not be uploaded");
                                }
                            }
                        } else {
                            return redirect('/uploadDetailFees')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");


                        }
                    }


                    return redirect('/view_fees')->with("success", " <span style='font-weight:bold;font-size:13px;'>$num Fees  successfully uploaded!</span> ");

                } else {
                    return redirect('/uploadDetailFees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");

                }
            } else {
                return redirect('/uploadDetailFees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload excel file!</span> ");

            }

        }
    }

    public function showUpload()
    {
        return view("finance.fees.upload");
    }

    public function storeUpload(Request $request)
    {
        //get the current user in session
        \DB::beginTransaction();
        try {

            $user = \Auth::user()->id;
            $valid_exts = array('csv'); // valid extensions
            $file = $request->file('file');
            $name = time() . '-' . $file->getClientOriginalName();
            if (!empty($file)) {

                $ext = strtolower($file->getClientOriginalExtension());
                $destination = public_path() . '\uploads\fees';
                if (in_array($ext, $valid_exts)) {
                    // Moves file to folder on server
                    // $file->move($destination, $name);
                    if (@$file->move($destination, $name)) {


                        $handle = fopen($destination . "/" . $name, "r");
                        //  print_r($handle);
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                            $num = count($data);

                            for ($c = 0; $c < $num; $c++) {
                                $col[$c] = $data[$c];
                            }


                            $name = $col[0];
                            $description = $col[1];
                            $amount = $col[2];
                            $type = $col[3];
                            $season = $col[4];
                            $programme = $col[5];
                            $level = $col[6];
                            //$sem = $col[7];
                            $year = $col[8];
                            $nationality = $col[9];
                            $programs = $this->programmeSearch(); // check if the programmes in the file tally wat is in the db
                            if (array_search($programme, $programs)) {


                                $fee = new FeeModel();
                                $fee->NAME = $name;

                                $fee->DESCRIPTION = $description;
                                $fee->AMOUNT = $amount;
                                $fee->FEE_TYPE = $type;
                                $fee->SEASON_TYPE = $season;
                                $fee->PROGRAMME = $programme;
                                $fee->LEVEL = $level;
                                //$fee->SEMESTER = $sem;
                                $fee->YEAR = $year;
                                $fee->NATIONALITY = $nationality;
                                $fee->CREATED_BY = $user;
                                if ($fee->save()) {
                                    \DB::commit();
                                } else {
                                    return redirect('/upload_fees')->back()->withErrors("Fee could not be uploaded");
                                }
                            } else {
                                return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");


                            }
                        }

                        fclose($handle);
                        return redirect('/view_fees')->with("success", " <span style='font-weight:bold;font-size:13px;'>Fees  successfully uploaded!</span> ");

                    }
                } else {
                    return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only csv (comma delimited ) file is accepted!</span> ");

                }
            } else {
                return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload a csv file!</span> ");

            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    public function convert_number($number)
    {

        if (($number < 0) || ($number > 999999999)) {
            return "$number";
        }

        $Gn = floor($number / 1000000); /* Millions (giga) */
        $number -= $Gn * 1000000;
        $kn = floor($number / 1000); /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100); /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10); /* Tens (deca) */
        $n = $number % 10; /* Ones */

        $res = "";

        if ($Gn) {
            $res .= $this->convert_number($Gn) . " Million";
        }

        if ($kn) {
            $res .= (empty($res) ? "" : " ") .
                $this->convert_number($kn) . " Thousand";
        }

        if ($Hn) {
            $res .= (empty($res) ? "" : " ") .
                $this->convert_number($Hn) . " Hundred";
        }

        $ones = array(
            "",
            "One",
            "Two",
            "Three",
            "Four",
            "Five",
            "Six",
            "Seven",
            "Eight",
            "Nine",
            "Ten",
            "Eleven",
            "Twelve",
            "Thirteen",
            "Fourteen",
            "Fifteen",
            "Sixteen",
            "Seventeen",
            "Eighteen",
            "Nineteen");
        $tens = array(
            "",
            "",
            "Twenty",
            "Thirty",
            "Fourty",
            "Fifty",
            "Sixty",
            "Seventy",
            "Eighty",
            "Ninety");

        if ($Dn ||
            $n) {
            if (!empty($res)) {
                $res .= " and ";
            }

            if ($Dn <
                2) {
                $res .= $ones[$Dn *
                10 +
                $n];
            } else {
                $res .= $tens[$Dn];

                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res)) {
            $res = "zero";
        }

        return $res;

//$thea=explode(".",$res);
    }

    public function convert($amt)
    {
//$amt = "190120.09" ;

        $amt = number_format($amt, 2, '.', '');
        $thea = explode(".", $amt);

//echo $thea[0];

        $words = $this->convert_number($thea[0]) . " Ghana Cedis ";
        if ($thea[1] >
            0) {
            $words .= $this->convert_number($thea[1]) . " Pesewas";
        }

        return $words;
    }

    public function countries()
    {

        $country = ['Ghanaian' => 'Ghanaian', 'Foriegn' => 'Foriegn'];
        return $country;
    }

    public function createform()
    {
        $program = \DB::table('tpoly_programme')
            ->lists('PROGRAMME', 'ID');
        return view('finance.fees.create')->with('program', $program)->with('year', $this->years())->with('country', $this->countries());

    }

    public function years()
    {

        for ($i = 2008; $i <= 2030; $i++) {
            $year = $i - 1 . "/" . $i;
            $years[$year] = $year;
        }
        return $years;
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $user = \Auth::user()->id;
            $this->validate($request, ['name' => 'required', 'amount' => 'required', 'programme' => 'required', 'level' => 'required', 'year' => 'required', 'stype' => 'required']);
            if ($request->input('programme') == 'All' && $request->input('level') == 'All') {
                $program = \DB::table('tpoly_programme')->get();

                // dd($size)   ;          

                foreach ($program as $programs) {
                    $fee = new FeeModel();
                    $fee->NAME = $request->input('name');

                    $fee->DESCRIPTION = $request->input('description');
                    $fee->AMOUNT = $request->input('amount');
                    $fee->FEE_TYPE = $request->input('type');
                    $fee->SEASON_TYPE = $request->input('stype');
                    $fee->PROGRAMME = $programs->ID;
                    $fee->LEVEL = $request->input('level');
                    //$fee->SEMESTER = $request->input('semester');
                    $fee->YEAR = $request->input('year');
                    $fee->NATIONALITY = $request->input('country');
                    $name = $request->input('name');
                    $fee->CREATED_BY = $user;
                    $fee->save();
                }
            } elseif ($request->input('programme') == 'All') {
                $program = \DB::table('tpoly_programme')->get();
                foreach ($program as $programs) {
                    $fee = new FeeModel();
                    $fee->NAME = $request->input('name');

                    $fee->DESCRIPTION = $request->input('description');
                    $fee->AMOUNT = $request->input('amount');
                    $fee->FEE_TYPE = $request->input('type');
                    $fee->SEASON_TYPE = $request->input('stype');
                    $fee->PROGRAMME = $programs->ID;
                    $fee->LEVEL = $request->input('level');
                    //$fee->SEMESTER = $request->input('semester');
                    $fee->YEAR = $request->input('year');
                    $fee->NATIONALITY = $request->input('country');
                    $name = $request->input('name');
                    $fee->CREATED_BY = $user;
                    $fee->save();
                }
            }

            $fee = new FeeModel();
            $fee->NAME = $request->input('name');

            $fee->DESCRIPTION = $request->input('description');
            $fee->AMOUNT = $request->input('amount');
            $fee->FEE_TYPE = $request->input('type');
            $fee->SEASON_TYPE = $request->input('stype');
            $fee->PROGRAMME = $request->input('programme');
            $fee->LEVEL = $request->input('level');
            //$fee->SEMESTER = $request->input('semester');
            $fee->YEAR = $request->input('year');
            $fee->NATIONALITY = $request->input('country');
            $name = $request->input('name');
            $fee->CREATED_BY = $user;

            if ($fee->save()) {
                \DB::commit();
                return redirect()->back()->with("success", array(" <span style='font-weight:bold;font-size:13px;'> $name fee  successfully added!</span> "));
            } else {
                return redirect()->back()->withErrors("Fee could not be added");
            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    public function uploadStudentsFee(Request $request, SystemController $sys)
    {
        set_time_limit(36000);

        \DB::beginTransaction();
        try {
            $user = \Auth::user()->id;
            $valid_exts = array('csv'); // valid extensions
            $file = $request->file('file');
            $name = time() . '-' . $file->getClientOriginalName();
            if (!empty($file)) {

                $ext = strtolower($file->getClientOriginalExtension());
                $destination = public_path() . '\uploads\fees';
                if (in_array($ext, $valid_exts)) {
                    // Moves file to folder on server
                    // $file->move($destination, $name);
                    if (@$file->move($destination, $name)) {


                        $handle = fopen($destination . "/" . $name, "r");
                        //  print_r($handle);
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                            $num = count($data);

                            for ($c = 0; $c < $num; $c++) {
                                $col[$c] = $data[$c];
                            }


                            $program = trim($col[0]);
                            $year = $col[2];
                            $bill = trim($col[3]);
                            $owing = trim($col[3]);


                            //  dd($year);
                            // first check if the students exist in the system if true then update else insert
                            $programme = $sys->programmeSearchByCode(); // check if the programmes in the file tally wat is in the db
                            if (array_search($program, $programme)) {


                                StudentModel::where('PROGRAMMECODE', $program)->where('year', $year)->update(array("SYSUPDATE" => "1", "STATUS" => 'In School', "BILLS" => $bill, "BILL_OWING" => $owing));
                                \DB::commit();


                            } else {
                                return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>File contain unrecognize programme.please try again!</span> ");

                            }

                        }


                        fclose($handle);
                        return redirect('/students')->with("success", " <span style='font-weight:bold;font-size:13px;'>Fees uploaded  successfully!</span> ");

                    }
                } else {
                    return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only csv (comma delimited ) file is accepted!</span> ");

                }
            } else {
                return redirect('/upload_fees')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload a csv file!</span> ");

            }
        } catch (\Exception $e) {
            \DB::rollback();
        }

    }

    public function showUploadBalance(Request $request, SystemController $sys)
    {
        return view("finance.fees.uploadBalance");
    }

    public function uploadFeesBalance(Request $request, SystemController $sys)
    {
        set_time_limit(36000);


        $user = \Auth::user()->fund;
        $valid_exts = array('csv', 'xls', 'xlsx'); // valid extensions
        $file = $request->file('file');
        $path = $request->file('file')->getRealPath();

        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, $valid_exts)) {

            $data = Excel::load($path, function ($reader) {

            })->get(); //dd($data);
            $array = $sys->getSemYear();
            //$sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;

            $i = 0;
            foreach ($data as $key => $value) {
                $receipt = $this->getReceipt();
                $num = count($data);
                $stno = $value->stno;
                $semesterBill = $value->owing;

                $level = $value->level;
                $program = $value->program;
                $payment1 = $value->firstpayment;
                $payment2 = $value->secondpayment;
                $payment3 = $value->thirdpayment;
                $balance = round(($semesterBill - ($payment1 + $payment2 + $payment3)), 3);

                //dd($value->thirdpayment);
                if ($payment1 == "") {
                    $payment1 = 0.00;
                }
                if ($payment2 == "") {
                    $payment2 = 0.00;
                }
                if ($payment3 == "") {
                    $payment3 = 0.00;
                }

                //dd($payment1);
                Models\StudentModel::where('STNO', $stno)->orwhere('INDEXNO', $stno)->update(array("BILLS" => $semesterBill, "BILL_OWING" => $balance));
                $app = @Models\StudentModel::where('STNO', $stno)->first();
                // now insert into fee payment table
                //dd($app);

                if (@$app->INDEXNO == "") {
                    $index = $stno;
                } else {
                    $index = @$app->INDEXNO;
                }
                // dd($index);
                $feeLedger = new Models\FeePaymentModel();
                $feeLedger->INDEXNO = $index;
                $feeLedger->PROGRAMME = $program;
                $feeLedger->AMOUNT = $payment1;

                $feeLedger->PROGRAMME = $program;
                $feeLedger->LEVEL = $level;
                $feeLedger->RECIEPIENT = $user;
                $feeLedger->PAYMENTTYPE = "School Fees";
                $feeLedger->RECEIPTNO = $receipt + 1;
                $feeLedger->YEAR = $year;
                $feeLedger->FEE_TYPE = "School Fees";
               // $feeLedger->SEMESTER = $sem;

                $feeLedger->save();

                $this->updateReceipt();
                $feeLedger2 = new Models\FeePaymentModel();
                $feeLedger2->INDEXNO = $index;
                $feeLedger2->PROGRAMME = $program;
                $feeLedger2->AMOUNT = $payment2;

                $feeLedger2->PROGRAMME = $program;
                $feeLedger2->LEVEL = $level;
                $feeLedger2->RECIEPIENT = $user;
                $feeLedger2->PAYMENTTYPE = "School Fees";
                $feeLedger2->RECEIPTNO = $receipt + 2;
                $feeLedger2->YEAR = $year;
                $feeLedger2->FEE_TYPE = "School Fees";
                //$feeLedger2->SEMESTER = $sem;

                $feeLedger2->save();
                $this->updateReceipt();
                $feeLedger3 = new Models\FeePaymentModel();
                $feeLedger3->INDEXNO = $index;
                $feeLedger3->PROGRAMME = $program;
                $feeLedger3->AMOUNT = $payment3;

                $feeLedger3->PROGRAMME = $program;
                $feeLedger3->LEVEL = $level;
                $feeLedger3->RECIEPIENT = $user;
                $feeLedger3->PAYMENTTYPE = "School Fees";
                $feeLedger3->RECEIPTNO = $receipt + 3;
                $feeLedger3->YEAR = $year;
                $feeLedger3->FEE_TYPE = "School Fees";
                //$feeLedger3->SEMESTER = $sem;

                $feeLedger3->save();
                $this->updateReceipt();
            }


            return redirect('/students')->with("success", " <span style='font-weight:bold;font-size:13px;'>$num student(s) finances updated  successfully!</span> ");
        } else {
            return redirect('/upload_students')->with("error", " <span style='font-weight:bold;font-size:13px;'>Only excel file is accepted!</span> ");
        }
    }

    /**
     * Destroy the given task.
     *
     * @param  Request $request
     * @param  Task $task
     * @return Response
     */
    public function destroy(Request $request)
    {
        \DB::beginTransaction();
        try {

            $query = FeeModel::where('ID', $request->input("id"))->delete();

            if ($query) {
                \DB::commit();
                //\Session::flash("success", "<span style='font-weight:bold;font-size:13px;'> Fee  </span>successfully deleted!");

                return redirect()->back()->with("success", " <span style='font-weight:bold;font-size:13px;'>   successfully delete!</span> ");

            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    public function destroyPayment(Request $request)
    {
        \DB::beginTransaction();
        try {

            $query = FeePaymentModel::where('ID', $request->input("id"))->first();
            $studentIndexNo = $query->INDEXNO;
            $amount = $query->AMOUNT;
            if ($query) {

                $sql = StudentModel::where("INDEXNO", $studentIndexNo)->orWhere("STNO", $studentIndexNo)->first();
                $previousBalance = $sql->BILL_OWING;
                $newBalance = $previousBalance + $amount;
                if (FeePaymentModel::where('ID', $request->input("id"))->delete()) {
                    StudentModel::where("INDEXNO", $studentIndexNo)->orWhere("STNO", $studentIndexNo)->update(array("BILL_OWING" => $newBalance));
                    \DB::commit();
                }

                return redirect()->back()->with("success", " <span style='font-weight:bold;font-size:13px;'> Payment for student with index number $studentIndexNo amounting GHC $amount successfully deleted!</span> ");
            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }
}
