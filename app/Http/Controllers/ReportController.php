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
use App\User;
use Charts;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');


    }

    public function studentLedger(Request $request, SystemController $sys)
    {
        if (@\Auth::user()->role == 'FO' || @\Auth::user()->department == 'Finance' || @\Auth::user()->department == 'top' || @\Auth::user()->department == 'Tptop') {
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            $student = explode(',', $request->input('q'));
            $student = $student[0];

            /* balance b/d ie
             * bill owing from students table
             * NB we treating students as a debtors
             * in this case except the school
             * doesn't follow the accrual concept and
             * opperates soly cash accounting
             */
            $balanceBD = StudentModel::where("INDEXNO", $student)->first();

            $transactions = FeePaymentModel::where("INDEXNO", $student)->paginate(100);
            return view("finance.reports.studentLedger")->with("student", $balanceBD)
                ->with("sem", $sem)
                ->with("year", $year)
                ->with("data", $transactions);

        } else {
            return redirect("/dashboard");
        }
    }

    public function programLedger(Request $request, SystemController $sys)
    {
        if (@\Auth::user()->role == 'FO' || @\Auth::user()->department == 'Finance' || @\Auth::user()->department == 'top') {
            $array = $sys->getSemYear();
            $sem = $array[0]->SEMESTER;
            $year = $array[0]->YEAR;
            if ($request->isMethod("get")) {

                return view('finance.reports.ledgerPrograms')
                    ->with('program', $sys->getProgramList())
                    ->with('levels', $sys->getLevelList())
                    ->with('year', $sys->years());
            } else {
                /* balance b/d ie
                 * bill owing from students table
                 * NB we treating students as a debtors 
                 * in this case except the school
                 * doesn't follow the accrual concept and
                 * opperates soly cash accounting
                 */
                $program = $request->input("program");
                $level = $request->input("level");
                $fiscalYear = $request->input("year");
                $balanceBD = StudentModel::where("PROGRAMMECODE", $program)
                    ->where("LEVEL", $level)
                    ->paginate(7800);

                $transactions = FeePaymentModel::join("tpoly_students", 'tpoly_feedetails.INDEXNO', '=', 'tpoly_students.INDEXNO')
                    ->where("tpoly_students.LEVEL", $level)
                    ->paginate(7800);
                $programm = $sys->getProgram($request->input('program'));

                return view("finance.reports.ledgerPrograms")->with("query", $balanceBD)
                    ->with("sems", $sem)
                    ->with("years", $year)
                    ->with('levels', $sys->getLevelList())
                    ->with('program', $sys->getProgramList())
                    ->with('year', $sys->years())
                    ->with('programme', $programm)
                    ->with('level', $request->input("level", ""))
                    ->with("data", $transactions);
            }
        } else {
            return redirect("/dashboard");
        }
    }

    public function showBills(Request $request, SystemController $sys)
    {
        //if(@\Auth::user()->role=='FO' || @\Auth::user()->department=='Finance' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'){
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        $query = Models\BillModel::query()->paginate(500);
        return view("finance.reports.bills")->with("data", $query)
            ->with("program", $sys->getProgramList())->with("level", $sys->getLevelList());
//          }
//          else{
//              return redirect("/dashboard");
//          }
    }

    public function getTotalPayment($student, $term, $yearr)
    {
        $sys = new SystemController();
        $array = $sys->getSemYear();
        if ($term == "" && $yearr == "") {
            $term = $array[0]->SEMESTER;
            $yearr = $array[0]->YEAR;
        }

        $fee = FeePaymentModel::query()->where('YEAR', '=', $yearr)->where('SEMESTER', $term)->where('INDEXNO', $student)->sum('AMOUNT');
        return $fee;
    }

    public function summaryPayment(Request $request, SystemController $sys)
    {
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
//            $admitted=FeePaymentModel::join('tpoly_students','tpoly_students.INDEXNO', '=', 'tpoly_feedetails.INDEXNO')
//                 
//                 ->where('tpoly_students.LEVEL','100')
//                 ->orWhere('tpoly_students.LEVEL','400/1')
//                 ->orderby("tpoly_programme.PROGRAMME")
//                 ->lists('tpoly_programme.PROGRAMME', 'tpoly_programme.PROGRAMMECODE');
//             

        $freshers = StudentModel::where("LEVEL", '100')->orWhere("LEVEL", '400/1')->count();
        $registered = StudentModel::where("REGISTERED", '1')->count();
        return view("finance.reports.summaryPayment");
    }

    public function summaryPaymentPrograms(Request $request, SystemController $sys)
    {


        $programs = Models\ProgrammeModel::query();
        if ($request->has('school') && trim($request->input('school')) != "") {

            $programs->whereHas('departments', function ($q) use ($request) {

                $q->whereHas('school', function ($q) use ($request) {
                    $q->whereIn('FACCODE', [$request->input('school')]);
                });
            });

        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $programs->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('department') && trim($request->input('department')) != "") {
            $programs->where("DEPTCODE", $request->input("department", ""));
        }
        if ($request->has('type') && trim($request->input('type')) != "") {
            $programs->where("TYPE", $request->input("TYPE", ""));
        }
        $data = $programs->orderBy("PROGRAMME")->orderBy("TYPE")->paginate(500);


        return view("finance.reports.summaryPrograms")
            ->with("programcode", $data)
            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());
    }

    // this one is for FO
    public function summaryPaymentPrograms2(Request $request, SystemController $sys)
    {

        $array = $sys->getSemYear();
        $year = $array[0]->YEAR;

        $sql = Models\StudentModel::select("PROGRAMMECODE", 'YEAR', 'LEVEL')->where("SYSUPDATE", "1");
        if ($request->has('school') && trim($request->input('school')) != "") {

            $sql->whereHas('departments', function ($q) use ($request) {

                $q->whereHas('school', function ($q) use ($request) {
                    $q->whereIn('FACCODE', [$request->input('school')]);
                });
            });

        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $sql->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('department') && trim($request->input('department')) != "") {
            $sql->where("DEPTCODE", $request->input("department", ""));
        }
        if ($request->has('type') && trim($request->input('type')) != "") {
            $sql->where("TYPE", $request->input("TYPE", ""));
        }
        $data = $sql->orderBy("PROGRAMMECODE")->orderBy("YEAR")->groupBy("PROGRAMMECODE")
            ->groupBy("YEAR")
            ->paginate(10000);


        return view("finance.reports.paymentByPrograms")
            ->with("data", $data)
            ->with("year", $year)
            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());
    }

    public function summaryOwingsPrograms(Request $request, SystemController $sys)
    {


        $programs = Models\ProgrammeModel::query();
        if ($request->has('school') && trim($request->input('school')) != "") {

            $programs->whereHas('departments', function ($q) use ($request) {

                $q->whereHas('school', function ($q) use ($request) {
                    $q->whereIn('FACCODE', [$request->input('school')]);
                });
            });

        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $programs->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('department') && trim($request->input('department')) != "") {
            $programs->where("DEPTCODE", $request->input("department", ""));
        }
        if ($request->has('type') && trim($request->input('type')) != "") {
            $programs->where("TYPE", $request->input("TYPE", ""));
        }
        $data = $programs->orderBy("PROGRAMME")->orderBy("TYPE")->paginate(500);


        return view("finance.reports.summaryOwing")
            ->with("programcode", $data)
            ->with('department', $sys->getDepartmentList())
            ->with('school', $sys->getSchoolList())
            ->with('programme', $sys->getProgramList())
            ->with('type', $sys->getProgrammeTypes());
    }

    public function statHall(Request $request, SystemController $sys)
    {
        $data = Models\HallModel::get();
        return view("admissions.reports.hallReport")
            ->with("data", $data);

    }

    public function showBulkReport(Request $request, SystemController $sys)
    {
        $data = $sys->getProgramList();
        return view("admissions.bulkAdmissionLetter")
            ->with("data", $data);

    }

    public function processBulkLetter($program)
    {
        ob_start();
        $sys = new SystemController();
        $studentSArray = array();
        $program = $program;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $query = Models\ApplicantModel::where("PROGRAMME_ADMITTED", $program)->where("STATUS", "ADMITTED")
            ->where("ADMITTED", "1")
            ->where("ADMISSION_FEES", ">", "0")
            ->get()->toArray();

        foreach ($query as $row) {

            @array_push($studentSArray, $row['APPLICATION_NUMBER']);

        }

        if (!empty($studentSArray)) {
        for ($i = 0; $i < count($studentSArray); $i++) {
            $indexNo = $studentSArray;

            $data = @Models\ApplicantModel::
            join('tpoly_students', 'tpoly_students.stno', '=', 'tpoly_applicants.APPLICATION_NUMBER')->
            where("tpoly_applicants.APPLICATION_NUMBER", $indexNo[$i])->where("tpoly_applicants.PROGRAMME_ADMITTED", $program)
                ->where('tpoly_students.stno','!=','')
                ->first();
           ob_start();

            ?>



            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <style>
                    @page {
                        size: A4;
                    }
                   /* @media print
                    {
                        #page1	{page-break-before:always;}
                    }*/
                    #page1	{page-break-before:always;}
                    .condition	{page-break-before:always;}
                    #page2	{page-break-before:always;}
                    .school	{page-break-before:always;}
                    .page9	{page-break-inside:avoid; page-break-after:auto}

                </style>
            </head>
                <body>
                    <div id='page1'>
                        <table border='0'>
                            <tr>
                                <td><img style="width:767px;height: auto"
                                         src='<?php echo url("public/assets/img/header.jpg") ?>' style=""
                                         class="image-responsive"/>

                                <td>
                                    <p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</p>
                                    <img style="width:106px;height: 134px;margin-left: -3px;    margin-top: -20px; " <?php
                                    $pic = $indexNo[$i];
                                    echo @$sys->picture(" url('public/albums/applicants/$pic.jpg') ", 90)
                                    ?>
                                         src="http://application.ttuportal.com/public/uploads/photos/<?php echo $pic ?>.jpg"
                                         alt="photo"/>

                                </td>
                            </tr>
                        </table>


                        <?php if (@$data->ADMISSION_TYPE == 'technical') { ?>

                            <div class="content" id="technical">
                                <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span
                                                    style="text-transform: capitalize"> <?php echo $data->TITLE . $data->NAME ?></span>
                                        </p>

                                        <div style="margin-left: 0px;text-align: justify">

                                            <center><b><p class="">OFFER OF ADMISSION
                                                        - <?php echo strtoupper(@$data->admitedProgram->department->schools->FACULTY) ?>
                                                        - ADMISSION
                                                        N<u>O </u>: <?php echo $data->APPLICATION_NUMBER ?></p>
                                                </b></center>
                                                <hr>
                                                <p>We write on behalf of the Academic Board to offer you admission to
                                                    Takoradi Technical University
                                                    to pursue a programme of study leading to the award of
                                                    <?php if (0 === strpos($data->PROGRAMME_ADMITTED, 'B')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'C') || 0 === strpos($data->PROGRAMME_ADMITTED, 'A')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'H')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'D')) {

                                                    } else {

                                                    }
                                                    ?>
                                                    <b> <?php echo $sys->getProgram($data->PROGRAMME_ADMITTED) ?></b>.
                                                    The duration of the programme
                                                    is <?php echo $sys->getProgramDuration($data->PROGRAMME_ADMITTED) ?>
                                                    Academic years. A change of
                                                    Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                <p><b>Note A mandatory Preparatory course in Engineering Mathematics</b>
                                                    and <b> Engineering
                                                        Science </b>will be organized for all applicants from Technical
                                                    Institutions to build up
                                                    their capacity in<b> Elective Mathematics required for HND
                                                        programme. </b>The preparatory course
                                                    starts from <b>Monday 24th July</b> and ends on <b>Friday18th August
                                                        2017</b>. You are therefore
                                                    required to pay a <b>non-refundable special tuition fee of
                                                        GH¢200</b> at any branch of <b>Capital
                                                        Bank, into Accounts Number, 2220001961011</b>. There is an <b>option
                                                        for accommodation on
                                                        campus during the preparatory course at a fee of GH¢ 75 for
                                                        interested individuals also to
                                                        be paid into the same Bank Accounts above</b>.
                                                </p>
                                                <p>1. Your admission is for the<b> <?php echo $year ?> </b>Academic
                                                    year. If you fail to enroll or
                                                    withdraw from the programme without prior approval of the
                                                    University, you will forfeits the
                                                    admission automatically.</p>

                                                <p>2. The<b> <?php echo $year ?> academic year</b> is scheduled to begin
                                                    on <b> Monday 28th
                                                        August <?php echo date('Y') ?></b>. You are expected to report
                                                    for medical examination and
                                                    registration from <b>Monday 28th August <?php echo date('Y') ?> to
                                                        Friday 8th
                                                        September <?php echo date('Y') ?></b>.You are mandated to
                                                    participate in orientation
                                                    programme which will run from <b>Monday 4th September to Friday 8th
                                                        September <?php echo date('Y') ?></b>.</p>

                                                <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of
                                                    <b>GHS<?php echo $data->ADMISSION_FEES ?></b> at any branch of

                                                    <?php if ($data->admitedProgram->TYPE == "NON TERTIARY"){ ?>
                                                    <b> UNIBANK into Account Number 1570105703613</b>. If you do not
                                                    indicate acceptance by paying
                                                    the fees before <b> Monday 28th August,<?php echo date('Y') ?></b>
                                                    your place will be offered to
                                                    another applicant on the waiting list. You are advised to make
                                                    photocopy of the Pay-in-slip for
                                                    keeps and present the original to the School Accounts Office on
                                                    arrival.Indicate your admission
                                                    number and programme of study on the Pay-in-slip. Any Applicant who
                                                    fails to make <b>PROVISIONAL
                                                        PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee
                                                        payment is for an Academic Year
                                                        and non-refundable</b>.</p>
                                                <?php } elseif (strpos($sys->getProgram($data->PROGRAMME_ADMITTED), "Evening") !== false) { ?>
                                                    <b> Ecobank into Account Number
                                                        0189104488868901</b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday
                                                        28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee payment is for an
                                                        Academic Year and non-refundable</b>.</p>

                                                <?php } else { ?>
                                                    <b><?php echo strtoupper(@$data->admitedProgram->department->schools->banks->NAME) ?>
                                                        into Account
                                                        Number <?php echo $data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER ?></b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee payment is for an
                                                        Academic Year and non-refundable</b>.</p>

                                                <?php } ?>
                                                <p>4. You will be on probation for the full duration of your programme
                                                    and may be dismissed at any time
                                                    for unsatisfactory academic work or misconduct. You will be required
                                                    to adhere to <b>ALL</b> the
                                                    rules and regulations of the University as contained in the
                                                    University Statutes, Examination Policy,
                                                    Ethics Policy and Students' Handbook.</p>

                                                <p>5. You are also to note that your admission is subject to being
                                                    declared medically fit to pursue the
                                                    programme of study in this University. You <b>are therefore required
                                                        to undergo a medical
                                                        examination at the University Clinic before registration.</b>
                                                    <b>You will be withdrawn from the
                                                        University if you fail to do the medical examination</b>.</p>

                                                <p>6. Applicants will also be held personally for any false statement or
                                                    omission made in their
                                                    applications.</p>

                                                <p>7. The University does not give financial assistance to students. It
                                                    is therefore the responsibility
                                                    of students to arrange for their own sponsorship and maintenance
                                                    during the period of study.</p>
                                        </div>
                                        <p>8. You are to note that the University is a secular institution and is
                                            therefore not bound by
                                            observance of any religious or sectarian practices. As much as possible the
                                            University lectures and
                                            / or examination would be scheduled to take place within normal working
                                            days, but where it is not
                                            feasible, lectures and examination would be held on other days.</p>
                                        <div id='page2'>
                                            <p>9. As a policy of the University, all students shall be required to
                                                register under the National
                                                Health Insurance Scheme (NHIS) on their own to enable them access
                                                medical care whilst on
                                                campus.</p>

                                            <?php if ($data->RESIDENTIAL_STATUS == 0) { ?>
                                                <p>10. You are affiliated
                                                    to<b> <?php echo strtoupper($data->HALL_ADMITTED) ?> Hall. </b></p>

                                            <?php } else { ?>
                                                <p>10. You have been given Hall Accommodation
                                                    at<b> <?php echo strtoupper($data->HALL_ADMITTED) ?> Hall </b>. You
                                                    will be required to make
                                                    payment of GHS<?php echo $sys->hallFees($data->HALL_ADMITTED) ?>
                                                    into any branch of Zenith
                                                    Bank Ghana with account
                                                    number <?php echo $sys->hallAccount($data->HALL_ADMITTED) ?>. <b/>You
                                                    shall report to your assigned hall of residence with the original
                                                    copy of pay-in-slip
                                                    NOTE: Hall fees paid is not refundable.
                                                </p>
                                            <?php } ?>
                                            <p>11. Any applicant who falsified results will be withdrawn from the
                                                university and will forfeits
                                                his/her fees paid.</p>
                                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or
                                                on Monday 28th
                                                August <?php echo date('Y') ?>. </p>

                                            <p>Please, accept my congratulations on your admission to the
                                                University.</p>

                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <p>Yours faithfully</p>
                                                            <p>
                                                                <img src="<?php echo url('public/assets/img/signature.png') ?>"
                                                                     style="width:90px;height:auto;"/></p>
                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <div class="visible-print text-center"
                                                                 style="margin-left:258px">
                                                                <?php echo \QrCode::size(100)->generate(\Request::url()); ?>

                                                            </div>
                                                        </td>

                                                    </tr>

                                                </table>
                                            </div>
                                            td><img src='<?php echo url("public/assets/img/footer.jpg") ?>' style=""
                                                     class="image-responsive"/></td>
                                            <br clear="all" style="page-break-before: always"/>
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php } elseif (@$data->ADMISSION_TYPE == 'provisional') {
                            ?>

                            <div class="content" id="provisional">
                                <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span
                                                    style="text-transform: capitalize"> <?php echo $data->TITLE ?>
                                                . <?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">

                                            <centerd><b><p class="">OFFER OF ADMISSION(<b>PROVISONAL</b>)
                                                        - <?php echo strtoupper(@$data->admitedProgram->department->schools->FACULTY) ?>
                                                        - ADMISSION N<u>O </u>: <?php echo $data->APPLICATION_NUMBER ?>
                                                    </p>
                                                </b></center>
                                                <hr>
                                                <p>We write on behalf of the Academic Board to offer you admission to
                                                    Takoradi
                                                    Technical University to pursue a programme of study leading to the
                                                    award of


                                                    <?php if (0 === strpos($data->PROGRAMME_ADMITTED, 'B')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'C') || 0 === strpos($data->PROGRAMME_ADMITTED, 'A')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'H')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'D')) {

                                                    } else {

                                                    }
                                                    ?>

                                                    <b> <?php echo $sys->getProgram($data->PROGRAMME_ADMITTED) ?></b>.
                                                    The duration
                                                    of the programme
                                                    is <?php echo $sys->getProgramDuration($data->PROGRAMME_ADMITTED) ?>
                                                    Academic
                                                    years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong>
                                                </p>
                                                <p><b><i>Note: Your admission is <b>PROVISIONAL</b>, you are therefore,
                                                            required to
                                                            present your results to the university’s admissions office
                                                            after it is
                                                            published, to enable the office regularlised your admission.</b></i>
                                                </p>

                                                <p>1. Your admission is for the<b> <?php echo $year ?> </b>Academic
                                                    year. If you
                                                    fail to enroll or withdraw from the programme without prior approval
                                                    of the
                                                    University, you will forfeits the admission automatically.</p>

                                                <p>2. The<b> <?php echo $year ?> academic year</b> is scheduled to begin
                                                    on <b>
                                                        Monday 28th August <?php echo date('Y') ?></b>. You are expected
                                                    to report
                                                    for medical examination and registration from <b>Monday 28th
                                                        August <?php echo date('Y') ?> to Friday 8th
                                                        September <?php echo date('Y') ?></b>.You are mandated to
                                                    participate in
                                                    orientation programme which will run from <b>Monday 4th September to
                                                        Friday 8th
                                                        September <?php echo date('Y') ?></b>.</p>

                                                <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of
                                                    <b>GHS<?php echo $data->ADMISSION_FEES ?></b> at any branch of
                                                    <?php if ($data->admitedProgram->TYPE == "NON TERTIARY"){ ?>
                                                    <b> UNIBANK into Account Number 1570105703613</b>. If you do not
                                                    indicate
                                                    acceptance by paying the fees before <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to
                                                    another
                                                    applicant on the waiting list. You are advised to make photocopy of
                                                    the
                                                    Pay-in-slip for keeps and present the original to the School
                                                    Accounts Office on
                                                    arrival.Indicate your admission number and programme of study on the
                                                    Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL
                                                        PAYMENT</b> of fees
                                                    forfeits his/her admission. <b>Note: Fee payment is for an Academic
                                                        Year and
                                                        non-refundable</b>.</p>
                                                <?php } elseif (strpos($sys->getProgram($data->PROGRAMME_ADMITTED), "Evening") !== false) { ?>
                                                    <b> Ecobank into Account Number
                                                        0189104488868901</b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee
                                                        payment is for an Academic Year and non-refundable</b>.</p>

                                                <?php } else { ?>
                                                    <b><?php echo strtoupper(@$data->admitedProgram->department->schools->banks->NAME) ?>
                                                        into Account
                                                        Number <?php echo @$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER ?></b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee
                                                        payment is for an Academic Year and non-refundable</b>.</p>

                                                <?php } ?>
                                                <p>4. You will be on probation for the full duration of your programme
                                                    and may be
                                                    dismissed at any time for unsatisfactory academic work or
                                                    misconduct. You will
                                                    be required to adhere to <b>ALL</b> the rules and regulations of the
                                                    University
                                                    as contained in the University Statutes, Examination Policy, Ethics
                                                    Policy and
                                                    Students' Handbook.</p>

                                                <p>5. You are also to note that your admission is subject to being
                                                    declared
                                                    medically fit to pursue the programme of study in this University.
                                                    You <b>are
                                                        therefore required to undergo a medical examination at the
                                                        University Clinic
                                                        before registration.</b> <b>You will be withdrawn from the
                                                        University if you
                                                        fail to do the medical examination</b>.</p>

                                                <p>6. Applicants will also be held personally for any false statement or
                                                    omission
                                                    made in their applications.</p>

                                                <p>7. The University does not give financial assistance to students. It
                                                    is therefore
                                                    the responsibility of students to arrange for their own sponsorship
                                                    and
                                                    maintenance during the period of study.</p>
                                        </div>
                                        <p>8. You are to note that the University is a secular institution and is
                                            therefore not
                                            bound by observance of any religious or sectarian practices. As much as
                                            possible the
                                            University lectures and / or examination would be scheduled to take place
                                            within normal
                                            working days, but where it is not feasible, lectures and examination would
                                            be held on
                                            other days.</p>
                                        <div id='page2'>
                                            <p>9. As a policy of the University, all students shall be required to
                                                register under
                                                the National Health Insurance Scheme (NHIS) on their own to enable them
                                                access
                                                medical care whilst on campus.</p>


                                            <?php if ($data->RESIDENTIAL_STATUS == 0){ ?>
                                                <p>10. You are affiliated
                                                    to<b> <?php echo strtoupper($data->HALL_ADMITTED) ?>
                                                        Hall. </b></p>
                                            <?php }else{ ?>

                                            <p>10. You have been given Hall Accommodation
                                                at<b> <?php echo strtoupper($data->HALL_ADMITTED) ?> Hall </b>. You will
                                                be required
                                                to make payment of GHS<?php echo $sys->hallFees($data->HALL_ADMITTED) ?>
                                                into any
                                                branch of Zenith Bank Ghana with account
                                                number <?php echo $sys->hallAccount($data->HALL_ADMITTED) ?>. <b/>You
                                                shall report
                                                to your assigned hall of residence with the original copy of pay-in-slip
                                                NOTE: Hall fees paid is not refundable.
                                                <?php } ?>
                                            <p>11. Any applicant who falsified results will be withdrawn from the
                                                university and
                                                will forfeits his/her fees paid.</p>
                                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or
                                                on Monday
                                                28th August <?php echo date('Y') ?>. </p>

                                            <p>Please, accept my congratulations on your admission to the
                                                University.</p>

                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <p>Yours faithfully</p>
                                                            <p>
                                                                <img src='<?php echo url("public/assets/img/signature.png") ?>'
                                                                     style="width:90px;height:auto;"/></p>
                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <div class="visible-print text-center"
                                                                 style="margin-left:258px">
                                                                <?php \QrCode::size(100)->generate(\Request::url()); ?>

                                                            </div>
                                                        </td>

                                                    </tr>

                                                </table>
                                            </div>
                                           <td><img src='<?php  echo url("public/assets/img/footer.jpg")  ?>' style=""
                                                     class="image-responsive"/></td>
                                            <br clear="all" style="page-break-before: always"/>
                                            &nbsp;

                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } elseif (@$data->ADMISSION_TYPE == 'conditional') {
                            ?>

                            <div class="content" id="conditional">
                                <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span
                                                    style="text-transform: capitalize"> <?php echo $data->TITLE ?>
                                                . <?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">

                                            <center><b><p class="">OFFER OF ADMISSION(<b>CONDITIONAL</b>)
                                                        - <?php echo strtoupper(@$data->admitedProgram->department->schools->FACULTY) ?>
                                                        - ADMISSION N<u>O </u>: <?php echo $data->APPLICATION_NUMBER ?>
                                                    </p></b></center>
                                                <hr>

                                                <p>We write on behalf of the Academic Board to offer you admission to
                                                    Takoradi Technical
                                                    University to pursue a programme of study leading to the award of

                                                    <?php if (0 === strpos($data->PROGRAMME_ADMITTED, 'B')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'C') || 0 === strpos($data->PROGRAMME_ADMITTED, 'A')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'H')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'D')) {

                                                    } else {

                                                    }
                                                    ?>
                                                    <b> <?php echo $sys->getProgram($data->PROGRAMME_ADMITTED) ?></b>.
                                                    The duration of
                                                    the programme
                                                    is <?php echo $sys->getProgramDuration($data->PROGRAMME_ADMITTED) ?>
                                                    Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong>
                                                </p>
                                                <p><i>
                                                        <b><i> Note: Your admission is conditional. Per the new
                                                                requirements you are
                                                                supposed to have a minimum of D7 in six subjects with at
                                                                least C6 in
                                                                three relevant subjects in the area of specialization.
                                                                You are therefore
                                                                required to rewrite to make good the deficiencies within
                                                                a period of one
                                                                academic year. Your eligibility to continue with the HND
                                                                programme would
                                                                be based on the outcome of the SSCE/WASSCE result. You
                                                                would be required
                                                                to present your new results in writing to the DEPUTY
                                                                REGISTRAR Academic
                                                                affairs</i></b>

                                                    </i></p>
                                                <p>1. Your admission is for the<b> <?php echo $year ?> </b>Academic
                                                    year. If you fail to
                                                    enroll or withdraw from the programme without prior approval of the
                                                    University, you
                                                    will forfeits the admission automatically.</p>

                                                <p>2. The<b> <?php echo $year ?> academic year</b> is scheduled to begin
                                                    on <b> Monday
                                                        28th August <?php echo date('Y') ?></b>. You are expected to
                                                    report for medical
                                                    examination and registration from <b>Monday 28th
                                                        August <?php echo date('Y') ?> to
                                                        Friday 8th September <?php echo date('Y') ?></b>.You are
                                                    mandated to participate
                                                    in orientation programme which will run from <b>Monday 4th September
                                                        to Friday 8th
                                                        September <?php echo date('Y') ?></b>.</p>

                                                <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of
                                                    <b>GHS<?php echo $data->ADMISSION_FEES ?></b> at any branch of
                                                    <?php if ($data->admitedProgram->TYPE == "NON TERTIARY"){ ?>
                                                    <b> UNIBANK into Account Number 1570105703613</b>. If you do not
                                                    indicate acceptance
                                                    by paying the fees before <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your
                                                    place will be offered to another applicant on the waiting list. You
                                                    are advised to
                                                    make photocopy of the Pay-in-slip for keeps and present the original
                                                    to the School
                                                    Accounts Office on arrival.Indicate your admission number and
                                                    programme of study on
                                                    the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL
                                                        PAYMENT</b> of fees
                                                    forfeits his/her admission. <b>Note: Fee payment is for an Academic
                                                        Year and
                                                        non-refundable</b>.</p>

                                                <?php } elseif (strpos($sys->getProgram($data->PROGRAMME_ADMITTED), "Evening") !== false) { ?>
                                                    <b> Ecobank into Account Number
                                                        0189104488868901</b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee payment
                                                        is for an Academic Year and non-refundable</b>.</p>

                                                <?php } else { ?>
                                                    <b><?php echo strtoupper(@$data->admitedProgram->department->schools->banks->NAME) ?>
                                                        into Account
                                                        Number <?php echo @$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER ?></b>. If you do not indicate acceptance by paying the fees before
                                                    <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                    <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                        Fee payment
                                                        is for an Academic Year and non-refundable</b>.</p>

                                                <?php } ?>
                                                <p>4. You will be on probation for the full duration of your programme
                                                    and may be
                                                    dismissed at any time for unsatisfactory academic work or
                                                    misconduct. You will be
                                                    required to adhere to <b>ALL</b> the rules and regulations of the
                                                    University as
                                                    contained in the University Statutes, Examination Policy, Ethics
                                                    Policy and
                                                    Students' Handbook.</p>

                                                <p>5. You are also to note that your admission is subject to being
                                                    declared medically
                                                    fit to pursue the programme of study in this University. You <b>are
                                                        therefore
                                                        required to undergo a medical examination at the University
                                                        Clinic before
                                                        registration.</b> <b>You will be withdrawn from the University
                                                        if you fail to do
                                                        the medical examination</b>.</p>

                                                <p>6. Applicants will also be held personally for any false statement or
                                                    omission made
                                                    in their applications.</p>

                                                <p>7. The University does not give financial assistance to students. It
                                                    is therefore the
                                                    responsibility of students to arrange for their own sponsorship and
                                                    maintenance
                                                    during the period of study.</p>
                                        </div>
                                        <p>8. You are to note that the University is a secular institution and is
                                            therefore not bound by
                                            observance of any religious or sectarian practices. As much as possible the
                                            University
                                            lectures and / or examination would be scheduled to take place within normal
                                            working days,
                                            but where it is not feasible, lectures and examination would be held on
                                            other days.</p>
                                        <div id='page2'>
                                            <p>9. As a policy of the University, all students shall be required to
                                                register under the
                                                National Health Insurance Scheme (NHIS) on their own to enable them
                                                access medical care
                                                whilst on campus.</p>


                                            <?php if ($data->RESIDENTIAL_STATUS == 0){ ?>
                                                <p>10. You are affiliated
                                                    to<b> <?php echo strtoupper($data->HALL_ADMITTED) ?>
                                                        Hall. </b></p>

                                            <?php }else{ ?>
                                            <p>10. You have been given Hall Accommodation
                                                at<b> <?php echo strtoupper($data->HALL_ADMITTED) ?> Hall </b>. You will
                                                be required to
                                                make payment of GHS<?php echo $sys->hallFees($data->HALL_ADMITTED) ?>
                                                into any branch of
                                                Zenith Bank Ghana with account
                                                number <?php echo $sys->hallAccount($data->HALL_ADMITTED) ?>. <b/>You
                                                shall report to
                                                your assigned hall of residence with the original copy of pay-in-slip
                                                NOTE: Hall fees paid is not refundable.

                                                <?php } ?>
                                            <p>11. Any applicant who falsified results will be withdrawn from the
                                                university and will
                                                forfeits his/her fees paid.</p>
                                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or
                                                on Monday 28th
                                                August <?php echo date('Y') ?>. </p>

                                            <p>Please, accept my congratulations on your admission to the
                                                University.</p>

                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <p>Yours faithfully</p>
                                                            <p>
                                                                <img src='<?php echo url("public/assets/img/signature.png") ?>'
                                                                     style="width:90px;height:auto;"/></p>
                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <div class="visible-print text-center"
                                                                 style="margin-left:258px">
                                                                <?php \QrCode::size(100)->generate(\Request::url()); ?>

                                                            </div>
                                                        </td>

                                                    </tr>

                                                </table>
                                            </div>
                                             <td><img src='<?php echo url("public/assets/img/footer.jpg")?>' style=""
                                                     class="image-responsive"/></td> 
                                            <br clear="all" style="page-break-before: always"/>
                                            &nbsp;

                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php } elseif (@$data->ADMISSION_TYPE == 'regular' || @$data->ADMISSION_TYPE == 'mature') { ?>

                            <div class="content" id="regular">
                                <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span
                                                    style="text-transform: capitalize"> <?php echo $data->TITLE ?>
                                                . <?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                             <b>
                                                 <p style="text-align:center;"class="">OFFER OF ADMISSION -
                                                        <?php if (@$data->admitedProgram->AFFILAITION == "0"){ ?>
                                                            <?php echo strtoupper(@$data->admitedProgram->department->schools->FACULTY ." - ") ?>
                                                        <?php }else{ ?>
                                                        <?php echo strtoupper(@$data->admitedProgram->PROGRAMME . "-" . @$data->admitedProgram->AFFILAITION) ?><?php } ?>
                                                        ADMISSION N<u>O </u>: <?php echo $data->APPLICATION_NUMBER ?>
                                                 </p>
                                             </b>

                                                <hr>
                                                <p>We write on behalf of the Academic Board to offer you admission to
                                                    Takoradi Technical
                                                    University to pursue a programme of study leading to the award of

                                                    <?php if (0 === strpos($data->PROGRAMME_ADMITTED, 'B')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'C') || 0 === strpos($data->PROGRAMME_ADMITTED, 'A')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'H')) {

                                                    } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'D')) {

                                                    } else {

                                                    }
                                                    ?>
                                                    <b> <?php echo $sys->getProgram($data->PROGRAMME_ADMITTED) ?></b>.
                                                    The duration of
                                                    the programme
                                                    is <?php echo $sys->getProgramDuration($data->PROGRAMME_ADMITTED) ?>
                                                    Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong>
                                                </p>

                                                <p>1. Your admission is for the<b> <?php echo $year ?> </b>Academic
                                                    year. If you fail to
                                                    enroll or withdraw from the programme without prior approval of the
                                                    University, you
                                                    will forfeits the admission automatically.</p>

                                                <p>2. The<b> <?php echo $year ?> academic year</b> is scheduled to begin
                                                    on <b> Monday
                                                        28th August <?php echo date('Y') ?></b>. You are expected to
                                                    report for medical
                                                    examination and registration from <b>Monday 28th
                                                        August <?php echo date('Y') ?> to
                                                        Friday 8th September <?php echo date('Y') ?></b>.You are
                                                    mandated to participate
                                                    in orientation programme which will run from <b>Monday 4th September
                                                        to Friday 8th
                                                        September <?php echo date('Y') ?></b>.</p>

                                                <p>3. You are required to make <b>PROVISIONAL PAYMENT</b>
                                                    of <b>GHS<?php echo $data->ADMISSION_FEES ?></b> at any branch of
                                                    <?php if ($data->admitedProgram->TYPE == "NON TERTIARY"){ ?>
                                                    <b> UNIBANK into Account Number 1570105703613</b>. If you do not
                                                    indicate acceptance
                                                    by paying the fees before <b> Monday 28th
                                                        August,<?php echo date('Y') ?></b> your
                                                    place will be offered to another applicant on the waiting list. You
                                                    are advised to
                                                    make photocopy of the Pay-in-slip for keeps and present the original
                                                    to the School
                                                    Accounts Office on arrival.Indicate your admission number and
                                                    programme of study on
                                                    the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL
                                                        PAYMENT</b> of fees
                                                    forfeits his/her admission. <b>Note: Fee payment is for an Academic
                                                        Year and
                                                        non-refundable</b>.</p>
                                            <?php } elseif (0 === strpos($data->PROGRAMME_ADMITTED, 'B')) { ?>
                                                <b>PRUDENTIAL BANK into Account Number
                                                    0271900010010 </b>. If you do not indicate acceptance by paying the fees before
                                                <b>
                                                    Monday 28th
                                                    August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                    Fee payment is
                                                    for an Academic Year and non-refundable</b>.</p>

                                            <?php } elseif (strpos($sys->getProgram($data->PROGRAMME_ADMITTED), "Evening") !== false) { ?>
                                                <b> Ecobank into Account Number
                                                    0189104488868901</b>. If you do not indicate acceptance by paying the fees before
                                                <b> Monday 28th
                                                    August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                    Fee payment is
                                                    for an Academic Year and non-refundable</b>.</p>

                                            <?php } else { ?>
                                                <b><?php echo strtoupper($data->admitedProgram->department->schools->banks->NAME) ?>
                                                    into Account
                                                    Number <?php echo $data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER ?></b>. If you do not indicate acceptance by paying the fees before
                                                <b> Monday 28th
                                                    August,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make
                                                <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note:
                                                    Fee payment is
                                                    for an Academic Year and non-refundable</b>.</p>
                                            <?php } ?>
                                                <p>4. You will be on probation for the full duration of your programme
                                                    and may be
                                                    dismissed at any time for unsatisfactory academic work or
                                                    misconduct. You will be
                                                    required to adhere to <b>ALL</b> the rules and regulations of the
                                                    University as
                                                    contained in the University Statutes, Examination Policy, Ethics
                                                    Policy and
                                                    Students' Handbook.</p>

                                                <p>5. You are also to note that your admission is subject to being
                                                    declared medically
                                                    fit to pursue the programme of study in this University. You <b>are
                                                        therefore
                                                        required to undergo a medical examination at the University
                                                        Clinic before
                                                        registration.</b> <b>You will be withdrawn from the University
                                                        if you fail to do
                                                        the medical examination</b>.</p>

                                                <p>6. Applicants will also be held personally for any false statement or
                                                    omission made
                                                    in their applications.</p>

                                                <p>7. The University does not give financial assistance to students. It
                                                    is therefore the
                                                    responsibility of students to arrange for their own sponsorship and
                                                    maintenance
                                                    during the period of study.</p>
                                        </div>
                                        <p>8. You are required to note that the University is a secular institution and
                                            is therefore not
                                            bound by observance of any religious or sectarian practices. As much as
                                            possible the
                                            University lectures and / or examination would be scheduled to take place
                                            within normal
                                            working days, but where it is not feasible, lectures and examination would
                                            be held on other
                                            days.</p>
                                        <div id='page2'>
                                            <p>9. As a policy of the University, all students shall be required to
                                                register under the
                                                National Health Insurance Scheme (NHIS) on their own to enable them
                                                access medical care
                                                whilst on campus.</p>

                                            <?php if ($data->RESIDENTIAL_STATUS == 0){ ?>
                                                <p>10. You are affiliated
                                                    to<b> <?php echo strtoupper($data->HALL_ADMITTED) ?>
                                                        Hall. </b></p>

                                            <?php }else{ ?>

                                            <p>10. You have been given Hall Accommodation
                                                at<b> <?php echo strtoupper($data->HALL_ADMITTED) ?> Hall </b>. You will
                                                be required to
                                                make payment of GHS<?php echo $sys->hallFees($data->HALL_ADMITTED) ?>
                                                into any branch of
                                                Zenith Bank Ghana with account
                                                number <?php echo $sys->hallAccount($data->HALL_ADMITTED) ?>. <b/>You
                                                shall report to
                                                your assigned hall of residence with the original copy of pay-in-slip
                                                NOTE: Hall fees paid is not refundable.

                                                <?php } ?>
                                            <p>11. Any applicant who falsified results will be withdrawn from the
                                                university and will
                                                forfeits his/her fees paid.</p>
                                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or
                                                on Monday 28th
                                                August <?php echo date('Y') ?>. </p>

                                            <p>Please, accept my congratulations on your admission to the
                                                University.</p>

                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <p>Yours faithfully</p>
                                                            <p>
                                                                <img src='<?php echo url("public/assets/img/signature.png") ?>'
                                                                     style="width:90px;height:auto;"/></p>
                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <div class="visible-print text-center"
                                                                 style="margin-left:258px">
                                                                <?php echo \QrCode::size(100)->generate(\Request::url()); ?>

                                                            </div>
                                                        </td>

                                                    </tr>

                                                </table>
                                            </div>
                                            <td><img src='<?php echo url("public/assets/img/footer.jpg") ?>' style=""
                                                     class="image-responsive"/></td>
                                            <br clear="all" style="page-break-before: always"/>
                                            &nbsp;

                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php } ?>


                    </div>
                </div>  </div>
                <?php
                echo " <p><b/></p>";


          }
        } else {
        ?>
        <p>Letter not ready yet. come back later</p>
    <?php }
        echo ob_get_clean();
    }


        public  function showBulkLetter(Request $request, SystemController $sys)
        {


            $program = $request->input('program');


            $data = $this->processBulkLetter($program);

            return view("admissions.letterBulk")->with("data", $data);


        }


        public
        function statType(Request $request, SystemController $sys)
        {
            $array = $sys->getSemYear();
            $year = $array[0]->YEAR;
            $program = Models\ProgrammeModel::paginate(200);
            $data = Models\ApplicantModel::groupBy("ADMISSION_TYPE")->get();
            return view("admissions.reports.typeReport")->with("data", $data)->with("years", $year)
                ->with('department', $sys->getDepartmentList())->with("programcode", $program)
                ->with('school', $sys->getSchoolList())
                ->with('programme', $sys->getProgramList())
                ->with('type', $sys->getProgrammeTypes());


        }

        public
        function statProgram(Request $request, SystemController $sys)
        {

            $array = $sys->getSemYear();
            $year = $array[0]->YEAR;
            $programs = Models\ApplicantModel::query();
            if ($request->has('school') && trim($request->input('school')) != "") {

                $programs->whereHas('departments', function ($q) use ($request) {

                    $q->whereHas('school', function ($q) use ($request) {
                        $q->whereIn('FACCODE', [$request->input('school')]);
                    });
                });

            }
            if ($request->has('program') && trim($request->input('program')) != "") {
                $programs->where("PROGRAMMECODE", $request->input("program", ""));
            }
            if ($request->has('department') && trim($request->input('department')) != "") {
                $programs->where("DEPTCODE", $request->input("department", ""));
            }
            if ($request->has('type') && trim($request->input('type')) != "") {
                $programs->where("TYPE", $request->input("TYPE", ""));
            }

            $data = $programs->paginate(500);

            $program = Models\ProgrammeModel::paginate(200);
            return view("admissions.reports.comprehensiveReport")
                ->with("programcode", $program)
                ->with("years", $year)
                ->with('department', $sys->getDepartmentList())
                ->with('school', $sys->getSchoolList())
                ->with('programme', $sys->getProgramList())
                ->with('type', $sys->getProgrammeTypes());
        }

        public
        function reportServiceNationality()
        {
            $users = \DB::table('tpoly_applicants')
                ->select(\DB::raw("  NATIONALITY, COUNT(id) AS count "))->groupBy("NATIONALITY")->get();
            $data = json_encode($users);
            return $data;
        }

        public
        function reportServiceGender()
        {
            header('Content-Type: application/json');
            $dataRaw = \DB::table('tpoly_applicants')
                ->select(\DB::raw("GENDER, COUNT(id) AS TOTAL "))->groupBy("GENDER")->get();
            $data = json_encode($dataRaw);
            return $data;
        }

        public
        function nationalityReport()
        {
            // to display a number of years behind, pass a int parameter. For example to display the last 10 years:

            // $sql=Models\ApplicantModel::select("NATIONALITY")->groupBy("NATIONALITY")->get()->toArray();

            $users = \DB::table('tpoly_applicants')
                ->select(\DB::raw("  NATIONALITY, COUNT(id) AS count "))->groupBy("NATIONALITY")->get();
            //dd($users);

//       /* $chart = Charts::database(Models\ApplicantModel::all(), 'donut', 'highcharts')
//            ->title('Applicants by Nationality')
//            ->labels(['Ghana', 'Cameron', 'Nigeria'])
//            ->values([5476,1,2])
//            ->dimensions(1000,500)
//            ->responsive(false);*/
//       /* $chart = Charts::database(User::all(), 'bar', 'highcharts')
//            ->elementLabel("Total")
//            ->dimensions(1000, 500)
//            ->responsive(false)
//            ->groupBy('game');*/
//        $users = User::where(\DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))
//            ->get();
//        $chart = Charts::database($users, 'bar', 'highcharts')
//            ->title("Monthly new Register Users")
//            ->elementLabel("Total Users")
//            ->dimensions(1000, 500)
//            ->responsive(false)
//            ->groupByMonth(date('Y'), true);
//       // return view('chart',compact('chart'));

            return view('admissions.reports.nationality');
        }
    }