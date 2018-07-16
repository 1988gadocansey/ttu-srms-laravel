<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */
/*
 * API route of quality assurance
 * 
 */
 Route::get('student/{indexno}/qa', 'APIController@qualityAssurance');
  Route::get('student/{indexno}/liaison', 'APIController@liaison');
  Route::post('receivePayment', 'APIController@payFee');
Route::group(['middleware' => ['web']], function () {
 Route::auth();
    Route::get('/', function () {
        return view('auth/login');
    })->middleware('guest');
    
    Route::get('/sms/kojo', function () {
        return view('kojo');
    });
     Route::post('/sms_kojo', 'StudentController@sendOwingSMS');
    Route::post('missHapp', 'LostPasswordController@sendNewPassword');
    Route::get('/lock', function () {
        return view('auth/screenLock');
    });
     Route::get('/generate', 'BankController@generateAccounts');
   
    Route::controller('search_password', 'PasswordController', [
        'anyData' => 'search_password.data',
        'getIndex' => 'search_password',
    ]);
     Route::controller('power_users', 'UserController', [
        'anyData' => 'power_users.data',
        'getIndex' => 'power_users',
    ]);
    
   Route::get('dashboard', 'HomeController@index');
  Route::get('/change_password', 'PasswordController@showChange');
    Route::post('/change_password', 'PasswordController@reset');
   Route::get( '/finance/upload', "FeeController@showUploadBalance");
    
    Route::post( 'finance/upload', "FeeController@uploadFeesBalance");
    
     
    
    Route::get('laracharts', 'HomeController@getLaraChart');
      Route::get('/graph', 'HomeController@buildChart');
    // student routes
    Route::controller('students', 'StudentController', [
        'anyData' => 'students.data',
        'getIndex' => 'students',
    ]);
    
    Route::get('tpoly', 'StudentController@tpoly');
    Route::match(array("get", "post"), '/outreach/add', "ApplicantController@addOutreach");
    Route::match(array("get", "post"), '/pro', "SupportController@index");
    
     Route::get('outreach/view', 'ApplicantController@outreachs');
    Route::post('outreach/sms', 'ApplicantController@fireOutreach');
    Route::get('outreach/auto/sms', 'ApplicantController@fireAutomaticOutreach');
      Route::delete('/outreach/delete', 'ApplicantController@destroy');
    Route::get('students', 'StudentController@index');
    Route::post('/sms', 'StudentController@sms');
    Route::get('/add_students', 'StudentController@create');
    Route::get('/upload_students', 'StudentController@showUploadForm');
    Route::post('/upload_students', 'StudentController@uploadData');
    Route::get('/upload_applicants', 'StudentController@applicantUploadForm');
    Route::post('/upload_applicants', 'StudentController@uploadApplicants');
    Route::post('/create_account', 'PasswordController@createStudentAccount');
    
    Route::post('/add_students', 'StudentController@store');
    Route::get('/student_show/{id}/id', 'StudentController@show'); // for printout
    Route::get('/edit_student/{id}/id', 'StudentController@edit');
    Route::post('/edit_student/{id}/id', 'StudentController@update');
    
    // routes for learning
    Route::get('autocomplete', 'SearchController@index');
    Route::get('clone', function () {
        return view('clone');
    });

    Route::controller('/banks', 'BankController', [
        'anyData' => 'banks.data',
        'getIndex' => 'banks',
        
    ]);
    Route::get('/create_bank', 'BankController@form');
    Route::post('/create_bank', 'BankController@store');
    Route::get('/edit_bank/{id}/id', 'BankController@edit');
    Route::post('/edit_bank/{id}/id', 'BankController@update');
    
    // fees route
    Route::get('/view_fees', 'FeeController@getIndex');
    Route::get('/create_fees', 'FeeController@createform');
    Route::post('/create_fees', 'FeeController@store');
    Route::get('/upload_fees', 'FeeController@showUpload');
    Route::post('/upload_fees', 'FeeController@uploadStudentsFee');
    Route::delete('/delete_fees', 'FeeController@destroy');
     
    Route::get('/run_bill/{id}/id', 'FeeController@approve');
     
    Route::get('/pay_fees', 'FeeController@showPayform');
    Route::post('/pay_fees', 'FeeController@showStudent');
    // late fee payment ie fee penalty
    Route::get('/pay_fees_penalty', 'FeeController@showPayform');
    Route::post('/pay_fees_penalty', 'FeeController@showStudentPenalty');
    Route::post('/processPayment', 'FeeController@processPayment');
    Route::delete('/delete_payment', 'FeeController@destroyPayment');
    
    Route::get('/printreceipt/{receiptno}', 'FeeController@printreceipt');
   
    Route::get('/printreceiptLate/{receiptno}', 'FeeController@printreceiptLate');
    Route::match(array("get", "post"), '/uploadDetailFees','FeeController@uploadFeesComponent');
     Route::match(array("get", "post"), '/print/receipt','FeeController@printOldReceipt');
        Route::match(array("get", "post"), '/print/password','FeeController@printPasswordReceipt');
  
     
     Route::match(array("get", "post"), '/finance/protocol','FeeController@allowRegister');
       Route::post('/processProtocol', 'FeeController@processProtocol');
   

   /* Route::controller('/view_payments', 'PaymentController', [
        'anyData' => 'view_payments.data',
        'getIndex' => 'view_payments',
        
    ]);*/
     Route::get('/view_payments', 'PaymentController@payments');
     Route::get('/view_payments_master', 'FeeController@masterLedger');
     // this route will process both get and post so am using route match
     Route::match(array("get", "post"), '/fee_summary', "FeeController@feeSummary");
     Route::get('/owing_paid', 'FeeController@owingAndPaid');
     Route::post('/fireOwingSMS', 'FeeController@sendFeeSMS');
    Route::get('search/autocomplete', 'SearchController@autocomplete');
    
    
    // updating students levels based on indexno
    Route::get('/gad', 'StudentController@gad');
    Route::post('/gad', 'StudentController@updateLevel');
    
    
    
    // load staff csv -STAFF ROUTES
    Route::controller('/staff', 'StaffController', [
        'anyData' => 'staff.data',
        'getIndex' => 'staff',
        
    ]);
    Route::get('/getStaffCSV', 'StaffController@showFileUpload');
    Route::post('/uploadStaff', 'StaffController@uploadStaff');
    Route::get('/directory', 'StaffController@directory');
    Route::get('/add_staff', 'StaffController@create');
    Route::post('/add_staff', 'StaffController@store');
    Route::post('/power_users', 'UserController@createStaffAccount');
    
     Route::get('/index/upload', 'StudentController@indexNumberUploadForm');
    Route::post('/index/upload', 'StudentController@uploadIndexNumber');
   
    // Academic routes
     Route::post('/upload_mounted','CourseController@processMountedUpload');
    Route::controller('programmes', 'ProgrammeController', [
        'anyData' => 'programmes.data',
        'getIndex' => 'programmes',
    ]);
    Route::get('/create_programme','ProgrammeController@create');
    Route::post('/create_programme','ProgrammeController@store');
    
    Route::get('/edit_programme/{id}/id','ProgrammeController@edit');
    Route::post('/edit_programme/{id}/id','ProgrammeController@update');
    Route::get('/classes/create','ProgrammeController@createClass');
    Route::post('/classes/create','ProgrammeController@storeClass');
    Route::get('/classes/view','ProgrammeController@viewClasses');
    
    Route::get('/create_grade','GradeController@create');
    Route::post('/create_grade','GradeController@store');
    
    Route::get('/grade_system','GradeController@index');
    Route::delete('/delete_grade', 'GradeController@destroy');
   
    Route::get('/grade_system/{type}/slug','GradeController@show');
    Route::post('/update_grades/','GradeController@update');
    
     Route::get('/systems/grades/delete', "CourseController@gradeModification");
     Route::post('/grades/process/delete', "CourseController@ProcessGradeModification");
    
     Route::get('/systems/grades/recover', "CourseController@gradeRecovery");
     Route::post('/grades/process/recover', "CourseController@ProcessGradeRecovery");
    
    //Academic Modules
    
    Route::get('/courses','CourseController@index');
    
    Route::get('/create_course','CourseController@create');
    Route::match(array("get", "post"), '/course/{id}/edit', "CourseController@edit");
    Route::post('/createCourse','CourseController@store');
    Route::get('/mount_course','CourseController@mountCourse');
    Route::post('/mount_course','CourseController@mountCourseStore');
    Route::get('/mounted_view','CourseController@viewMounted');
    Route::get('/registered_courses','CourseController@viewRegistered');
    Route::get('/enter_mark/{course}/course/{code}/code','CourseController@enterMark');
    Route::post('/process_mark','CourseController@processMark');
    Route::delete('/delete_course', 'CourseController@destroy');
    Route::delete('/delete_mounted', 'CourseController@destroy_mounted');
    Route::get('/upload/marks','CourseController@showFileUpload');
    Route::post('/upload_marks','CourseController@uploadMarks');
    Route::match(array("get", "post"), '/attendanceSheet', "CourseController@attendanceSheet");
    Route::match(array("get", "post"), '/transcript', "CourseController@transcript");
    Route::get( '/upload/courses', "CourseController@uploadCourse");
    Route::post( '/upload_courses', "CourseController@processCourseUploads");
    Route::get('/courseDownloadExcel/{type}', 'CourseController@courseDownloadExcel');
    Route::get('/marksDownloadExcel/{code}/code', 'CourseController@marksDownloadExcel');
    Route::get('/upload/mounted', "CourseController@uploadMounted");
    Route::match(array("get", "post"), '/upload/legacy1', "CourseController@uploadLegacy");
    Route::get('/mounted/{id}/edit', "CourseController@updateMounted");
    
     Route::post('/mounted/{id}/edit', "CourseController@processUpdateMounted");
    
    
    //Route::match(array("get", "post"), '/upload/legacy', "CourseController@uploadGad2");
    
    Route::get( 'legacy', "CourseController@uploadGad2");
    Route::match(array("get", "post"),'/print/cards', "CourseController@printCards");
    Route::get('/printCards/{program}/program/{level}/level','CourseController@processCards');
  
    Route::post( 'process_legacy', "CourseController@processsUploadLegacy");
    
    
    Route::get('/printAttendance/{semester}/sem/{course}/course/{code}/code/{level}/level/{id}/id','CourseController@printAttendance');
    Route::get( 'resit', "CourseController@uploadResit");
     Route::post('/upload_resit', "CourseController@processResit");
    Route::get('/system/registration/batch', "CourseController@batchRegistration");
    Route::post( '/system/registration/batch/process', "CourseController@processBatchRegistration");
   
    Route::get('/calender','AcademicCalenderController@index');
    Route::get('/create_calender','AcademicCalenderController@createCalender');
    Route::post('/create_calender','AcademicCalenderController@storeCalender');
    Route::delete('/delete_calender', 'AcademicCalenderController@destroy');
    Route::get('/fireCalender/{item}/id/{action}/action','AcademicCalenderController@updateCalender');
    Route::match(array("get", "post"), '/printReceipt', "PaymentController@printLostReceipt");
    // E-Payments goes here
    Route::get('/pay_transcript', 'PaymentController@showPayform');
    Route::post('/pay_transcript', 'PaymentController@showStudent');
    Route::post('/process_transcript', 'PaymentController@processTranscript');
    Route::get('/printreceiptTranscript/{receiptno}', 'PaymentController@printreceiptTranscript');
    
    
    Route::get('/view_payments_master', 'FeeController@masterLedger');
    Route::get('/createproduct', "PaymentProductController@createPaymentItem");
    Route::post('/createproduct', "PaymentTransactonsController@savePaymentItem");
     // system settings
    
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/systems/view','SettingsController@index');
    Route::get('/systems/sms','SettingsController@smsLogs');
   Route::get('/systems/user/logs','SettingsController@logs');
   
    Route::get('/systems/synchronizations','SettingsController@showSync');
     Route::get('/syncSMS','SystemController@sysSMS');
   
    Route::get('/systems/user/logs','SettingsController@logs');
    Route::match(array("get", "post"),'/report/registration','CourseController@registrationInfo');
    Route::get('/finance/reports/summary/','ReportController@summaryPayment');
   Route::get('/finance/reports/programs/','ReportController@summaryPaymentPrograms2');
   Route::get('/finance/reports/owing/','ReportController@summaryOwingsPrograms');
    Route::get('/finance/reports/fees/','ReportController@showBills');
   Route::post('finance/bills/create','FeeController@createBill');
   Route::get('/finance/reports/ledger/student','FeeController@showPayform');
   Route::post('/finance/reports/ledger/student','ReportController@studentLedger');
    Route::match(array("get", "post"), '/finance/reports/cummulative', "ReportController@programLedger");
    Route::get( '/broadsheet/noticeboard', "CourseController@noticeBoardBroadsheet");
    Route::post( '/process_broadsheet', "CourseController@processBroadsheet");
   
    
    Route::match(array("get", "post"), '/broadsheet/naptex', "CourseController@naptexBroadsheet");
    
    Route::match(array("get", "post"), '/groups/create', "GroupController@createGroup");
     Route::delete('/delete_group', 'GroupController@destroy');
    Route::get('/groups/run','GroupController@run');
    Route::post('/groups/activate','GroupController@assign');
    Route::get('/groups/view','GroupController@index');
    
    
    // system setting routes
    
    
 Route::post('/accept/bulk', 'SyncController@sendBulk');
   
 Route::get('/auth/send', 'SystemController@sendAuthOutreach');
 

    
     Route::get( '/check/course','CourseController@checkMountedCourses');
  Route::post('/check/course','CourseController@checkMountedCourses');
  
    
    
    
    
    
    
    
    
    
    
      Route::match(array("get", "post"),'/systems/users/update','SettingsController@updateUsers');
      Route::post('users/update/phone','SettingsController@updateProfile');
   Route::get('/applicants/view/','ApplicantController@index');
   Route::get('/applicants/sms/','ApplicantController@admitMessage');
   Route::get('/admissions/voucher','ApplicantController@cards');
   Route::post('/applicants/admit','ApplicantController@admit');
    Route::post('/applicants/admitOutreach','ApplicantController@admitOutreach');
   Route::get('/applicant_show/{id}/id', 'ApplicantController@show'); // for printout
   
   
    ////////////////////////////////////////////////////////////////////
    // Admissions//
//    Route::group(['middleware' => 'RoleMiddleware'], function()
//        {
//             Route::match(array("get", "post"), '/admissions/upload/cards', "FormController@uploadCards");
//
//        });applicant/auto/sms
  Route::get('/updateApplicants','ApplicantController@updateApplicantStatus');
  Route::get('/admissions/applicant/settings','ApplicantController@search');
  
  Route::get('/admissions/statistics/program','ReportController@statProgram');
  
  Route::get('/admissions/statistics/hall','ReportController@statHall');
  Route::get('/admissions/statistics/admission/type','ReportController@statType');
  
  
  
  
  Route::post('/admissions/applicant/settings','ApplicantController@showApplicant');
  Route::post('/admissions/applicant/fire','ApplicantController@action');
  Route::get('/applicant/auto/sms','ApplicantController@fireAutomaticApplicant');
  Route::get('/applicant/letter/outreach/{id}/printout', 'ApplicantController@letterOutreach');
  
  Route::get('/applicant/letter/{id}/printout', 'ApplicantController@letter');
  Route::get('/phone/{phone}/receipient/{receipient}/type/{type}/name/{name}/', 'SystemController@sendSingleSMS');
    
  Route::get('out/phone/{phone}/id/{id}/type/{type}/name/{name}/', 'SystemController@sendOutreachSingleSMS');
  
  
    });
    
  
  
  
  
  
  
  
  
Route::get('importExport', 'MaatwebsiteDemoController@importExport');

Route::get('downloadExcel/{type}', 'MaatwebsiteDemoController@downloadExcel');

Route::post('importExcel', 'MaatwebsiteDemoController@importExcel');
/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1'], function () {
        require config('infyom.laravel_generator.path.api_routes');
    });
});
