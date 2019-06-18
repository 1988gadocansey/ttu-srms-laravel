<!doctype html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
    <link rel="icon" type="image/png" href="public/assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="public/assets/img/favicon-32x32.png" sizes="32x32">

    <title>University Resource Planning System - Takoradi Technical University University</title>


    <!-- uikit -->
    <link rel="stylesheet" href="<?php echo url('public/assets/plugins/uikit/css/uikit.almost-flat.min.css'); ?> " media="all">


    <link rel="stylesheet" href="<?php echo url('public/assets/css/main.min.css'); ?>" media="all">
    <link rel="stylesheet" href="<?php echo url('public/assets/css/combined.min.css'); ?>" media="all">

    <link rel="stylesheet" href="<?php echo url('plugins/sweet-alert/sweet-alert.min.css'); ?>" media="all">
    <!-- font awesome -->
    <link rel="stylesheet" href="<?php echo url('public/assets/css/select2.min.css'); ?>" media="all">
    <link rel="stylesheet" href="<?php echo url('public/assets/css/dropify.css'); ?>" media="all">
    <link rel="stylesheet" href="<?php echo url( 'public/datatables/css/jquery.dataTables.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo url( 'public/datatables/css/dataTables.uikit.min.css'); ?>">

    <!-- style switcher -->
    <link rel="stylesheet" href="<?php echo url( 'public/assets/css/style_switcher.min.css'); ?>" media="all">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        #header_main .uk-navbar {
            margin: -1px
        }
    </style>

    <?php echo $__env->yieldContent('style'); ?>

</head>
<body class="top_menu">
<!-- main header -->
<header id="header_main">
    <div class="header_main_content">
        <nav class="uk-navbar">
            <div class="main_logo_top">
                <a href="/dashboard"><img src='<?php echo e(url("public/assets/img/logo.png")); ?>' alt="" height="15"
                                          width="71"/></a>
                <span class="" style="color:white">URPS-TTU</span>

            </div>

            <div class="uk-navbar-flip">
                <!-- main sidebar switch -->

                <ul class="uk-navbar-nav user_actions">
                    <li><a href="#" id="full_screen_toggle" class="user_action_icon uk-visible-large"><i
                                    class="material-icons md-24 md-light">&#xE5D0;</i></a></li>
                    <li data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
                        <a href="#" class="user_action_icon"><i class="material-icons md-24 md-light">&#xE7F4;</i><span
                                    class="uk-badge">16</span></a>
                        <div class="uk-dropdown uk-dropdown-xlarge">
                            <div class="md-card-content">
                                <ul class="uk-tab uk-tab-grid"
                                    data-uk-tab="{connect:'#header_alerts',animation:'slide-horizontal'}">
                                    <li class="uk-width-1-2 uk-active"><a href="#" class="js-uk-prevent uk-text-small">Messages
                                            (12)</a></li>
                                    <li class="uk-width-1-2"><a href="#" class="js-uk-prevent uk-text-small">Alerts
                                            (4)</a></li>
                                </ul>
                                <ul id="header_alerts" class="uk-switcher uk-margin">
                                    <li>
                                        <ul class="md-list md-list-addon">
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <span class="md-user-letters md-bg-cyan">ao</span>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading"><a href="pages_mailbox.html">Consequatur ut repudiandae.</a></span>
                                                    <span class="uk-text-small uk-text-muted">Et sunt qui quod aut laborum et nisi.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <img class="md-user-image md-list-addon-avatar"
                                                         src="public/assets/img/avatars/avatar_07_tn.png" alt=""/>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading"><a href="pages_mailbox.html">Sunt sequi.</a></span>
                                                    <span class="uk-text-small uk-text-muted">Beatae quibusdam sed possimus pariatur optio repellendus.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <span class="md-user-letters md-bg-light-green">qi</span>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading"><a href="pages_mailbox.html">Et voluptatum ut.</a></span>
                                                    <span class="uk-text-small uk-text-muted">Est et nostrum dignissimos suscipit nihil animi voluptatem quam.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <img class="md-user-image md-list-addon-avatar"
                                                         src="public/assets/img/avatars/avatar_02_tn.png" alt=""/>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading"><a href="pages_mailbox.html">Similique iure et.</a></span>
                                                    <span class="uk-text-small uk-text-muted">Laborum et alias veritatis accusamus sit consequatur quod.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <img class="md-user-image md-list-addon-avatar"
                                                         src="public/assets/img/avatars/avatar_09_tn.png" alt=""/>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading"><a href="pages_mailbox.html">Repellendus consequuntur.</a></span>
                                                    <span class="uk-text-small uk-text-muted">Nesciunt et id eum quas est soluta aut et.</span>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="uk-text-center uk-margin-top uk-margin-small-bottom">
                                            <a href="page_mailbox.html"
                                               class="md-btn md-btn-flat md-btn-flat-primary js-uk-prevent">Show All</a>
                                        </div>
                                    </li>
                                    <li>
                                        <ul class="md-list md-list-addon">
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <i class="md-list-addon-icon material-icons uk-text-warning">&#xE8B2;</i>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading">Repudiandae accusantium.</span>
                                                    <span class="uk-text-small uk-text-muted uk-text-truncate">Et fugit ipsum quia non ducimus quo.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <i class="md-list-addon-icon material-icons uk-text-success">&#xE88F;</i>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading">Eos earum qui.</span>
                                                    <span class="uk-text-small uk-text-muted uk-text-truncate">Accusamus nisi vitae dolorem et vel repudiandae ratione tenetur.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <i class="md-list-addon-icon material-icons uk-text-danger">&#xE001;</i>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading">Rerum accusantium.</span>
                                                    <span class="uk-text-small uk-text-muted uk-text-truncate">Ut voluptatem eos sapiente hic qui molestiae.</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="md-list-addon-element">
                                                    <i class="md-list-addon-icon material-icons uk-text-primary">&#xE8FD;</i>
                                                </div>
                                                <div class="md-list-content">
                                                    <span class="md-list-heading">Id molestias.</span>
                                                    <span class="uk-text-small uk-text-muted uk-text-truncate">Excepturi delectus rem doloribus quia fugiat.</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li data-uk-dropdown="{mode:'click',pos:'bottom-right'}">

                        <a href="#" class="user_action_image">
                            <span class="" style="color:white">Hi <?php echo e(@Auth::user()->name); ?></span>


                            <div class="uk-dropdown uk-dropdown-small">
                                <ul class="uk-nav js-uk-prevent">
                                    <li><a href='<?php echo url("/change_password"); ?>'>Change Password</a></li>
                                    <li><a href='<?php echo url("/logout"); ?>'>Logout</a></li>
                                </ul>
                            </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="header_main_search_form">
        <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>

    </div>
</header><!-- main header end -->
<div id="top_bar">
    <div class="md-top-bar">

        <?php if(@Auth::user()->phone!=''): ?>

            <ul id="menu_top" class="uk-clearfix">
                <li class="uk-hidden-small"><a href='<?php echo url("/dashboard"); ?>'><i title='home' class="material-icons">&#xE88A;</i><span>Home</span></a>
                </li>
                <?php if( @Auth::user()->department=='Deans' ): ?>
                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"><i class="sidebar-menu-icon material-icons md-18">school</i><span>Students Management</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                <li><a href="addOffence.php">Book Offences</a></li>
                                <li><a href="viewOffences.php">View Offenders</a></li>
                                <li><a href="viewOffences.php">Dehalling</a></li>

                            </ul>
                        </div>
                    </li>
                    <li data-uk-dropdown="justify:'#top_bar'">
                        <a href="#"> <i
                                    class="sidebar-menu-icon material-icons">home</i><span>Properties Management </span></a>
                        <div class="uk-dropdown uk-dropdown-width-4">
                            <div class="uk-grid uk-dropdown-grid" data-uk-grid-margin>
                                <div class="uk-width-1-4">
                                    <ul class="uk-nav uk-nav-dropdown uk-panel">
                                        <li class="uk-nav-header">Income and Expenditure</li>

                                        <li><a href="deposit.php">Make Deposit</a></li>
                                        <li><a href="depositrecords.php">Deposit Records</a></li>
                                        <li><a href="withdrawal.php">Withdrawal</a></li>
                                        <li><a href="withdrawalrecords.php">Withdrawal Records</a></li>
                                        <li><a href="bankrecords.php">Bank Records</a></li>
                                        <li><a href="bankstate.php">Bank Statement</a></li>

                                        <li><a href="enterexpense.php">Enter Expense</a></li>
                                        <li><a href="viewexpense.php">View Expenses</a></li>

                                        </li>
                                        <li class="uk-nav-divider"></li>


                                    </ul>
                                </div>

                                <div class="uk-width-1-4">
                                    <div class="uk-accordion" data-uk-accordion="{showfirst:false}">
                                        <p class="uk-nav-header">Transactions</p>

                                        <h3 class="uk-accordion-title">JCRC Properties</h3>
                                        <div class="uk-accordion-content">
                                            <p><a href='<?php echo url("view_payments_master"); ?>'>Add Property</a></p>
                                            <p><a href='<?php echo url("finance/reports/programs"); ?>'>View Properties</a></p>


                                        </div>
                                        <h3 class="uk-accordion-title">Hall Properties</h3>
                                        <div class="uk-accordion-content">
                                            <p><a href='<?php echo url("view_payments_master"); ?>'>Add Property</a></p>
                                            <p><a href='<?php echo url("finance/reports/programs"); ?>'>View Properties</a></p>


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </li>
                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <i class="sidebar-menu-icon material-icons">home</i><span>Accommodations</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/add_staff"); ?>'>Create Rooms</a></li>
                                <li><a href='<?php echo url("/getStaffCSV"); ?>'>View Rooms</a></li>
                                <li><a href='<?php echo url("staff"); ?>'>Allocate Properties to Rooms</a></li>
                                <li><a href='<?php echo url("/directory"); ?>'>Check In Students</a></li>
                                <li><a href='<?php echo url("/directory"); ?>'>Check In Guests</a></li>

                            </ul>
                        </div>
                    </li>


                <?php endif; ?>


                <?php if( @Auth::user()->department=='Finance' || @Auth::user()->department=='top'): ?>


                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <i class="sidebar-menu-icon material-icons">attach_file</i><span>File</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                <li><a href='<?php echo url("/transcript"); ?>'>Results</a></li>
                                <li><a href='<?php echo url("pay_fees"); ?>'>Individual Payments</a></li>

                                <li><a href='<?php echo url("banks"); ?>'>View Banks</a></li>

                                <li><a href='<?php echo url("/finance/reports/fees"); ?>'>View Fees</a></li>

                                <li><a href='<?php echo url("/print/receipt"); ?>'>Print Fee Receipt</a></li>
                                <li><a href='<?php echo url("/print/password"); ?>'>Print Password Receipt</a></li>
                                <li><a href='<?php echo url("view_payments"); ?>'>Payments Ledger</a></li>

                                <li><a href='<?php echo url("view_payments_master"); ?>'>Payments (sum)</a></li>
                                <li><a href='<?php echo url("pay_fees_penalty"); ?>'>Late Registration payment</a></li>
                                
                            </ul>
                        </div>
                    </li>


                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <i
                                    class="sidebar-menu-icon material-icons">view_headline</i><span>Reports</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                <li><a href='<?php echo url("owing_excel"); ?>'>Owing Excel</a></li>
                                    <li><a href='<?php echo url("paid_excel"); ?>'>Paid Excel</a></li>

                                <li><a href='<?php echo url("/view_payments_master"); ?>'>Fee Payment by Students Report</a>
                                </li>
                                <li><a href='<?php echo url("/finance/reports/programs"); ?>'>Fee Payment by Programs
                                        Report</a></li>
                                <li><a href='<?php echo url("/owing_paid"); ?>'>Fees Owings By Students Reports</a></li>
                                <li><a href='<?php echo url("/finance/reports/programs"); ?>'>Fees Owings By Programs
                                        Reports</a></li>
                                <li><a href='<?php echo url("/finance/reports/ledger/student"); ?>'>Print Student Ledger</a>
                                </li>
                                <li><a href='<?php echo url("/finance/reports/cummulative"); ?>'>Fees Cummulative Reports by
                                        Program</a></li>


                            </ul>
                        </div>
                    </li>

                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <i class="sidebar-menu-icon material-icons">people</i><span>Staff</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">

                                <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>


                <?php if( @Auth::user()->department=='Admissions' ): ?>

                    <li data-uk-dropdown class="uk-hidden-small">


                        <a href="#"><i
                                    class="sidebar-menu-icon material-icons md-18">perm_identity</i><span>Applicants</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("outreach/add"); ?>'>Add Outreach Applicants</a></li>
                                <li><a href='<?php echo url("outreach/view"); ?>'>View Outreach Applicants</a></li>
                                <li><a href='http://applications.ttuportal.com' target="_">Add Applicants</a></li>


                                <li><a href='<?php echo url("applicants/view"); ?>'>View Applicants</a></li>
                                <li><a href='<?php echo url("admissions/applicant/settings"); ?>'>Open or Block Applicant</a>
                                </li>
                                <li><a href='<?php echo url("admissions/letter/bulk"); ?>'>Print bulk admission letters</a>
                                </li>
                            <!--   <li><a href='<?php echo url("/admissions/conditional"); ?>'>View Conditional admissions</a></li>
                          
                             <li><a href='<?php echo url("/admissions/applicants/selected"); ?>'>View Qualified applicants</a></li>
                              <li><a href='<?php echo url("/admissions/applicants/rejected"); ?>'>View rejected applicants</a></li>
                          
                             -->

                            </ul>
                        </div>
                    </li>

                    <li data-uk-dropdown class="uk-hidden-small">


                        <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/admissions/voucher"); ?>'>View Vouchers purchased</a></li>
                                <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                <li><a href='<?php echo url("/students"); ?>'>View Students</a></li>
                                <li><a href='<?php echo url("/finance/reports/fees/"); ?>'>View Fees</a></li>
                                <li><a href='<?php echo url("/systems/sms"); ?>'>View sms sent</a></li>

                            <!--  <li><a href='<?php echo url("/admissions/rules/create"); ?>'>Create Admission Rules</a></li>
                            <li><a href='<?php echo url("/admissions/rules"); ?>'>View Admission Rules</a></li>
                          
                              <li><a href='<?php echo url("/admissions/applicant/settings"); ?>'>Open or Block Applicant</a></li>
                            <li><a href='<?php echo url("/admissions/mature/marks"); ?>'>Upload Mature Candidate Interview Marks</a></li>
                          
                             <li><a href='<?php echo url("/admissions/letter/design"); ?>'>Design Admission Letter</a></li>
                              <li><a href='<?php echo url("/admissions/list/show"); ?>'>View Admission List</a></li>
                          
                           <li><a href='<?php echo url("/admissions/letter/print"); ?>'>Print Admission Letter</a></li>
                           <li><a href='<?php echo url("/students"); ?>'>View Reported Students</a></li>
                             
                                     -->
                            </ul>
                        </div>
                    </li>


                    <li data-uk-dropdown class="uk-hidden-small">


                        <a href="#"><i
                                    class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/admissions/statistics/admission/type"); ?>'>Admission Type</a>
                                </li>
                                <li><a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                </li>

                                <li><a href='<?php echo url("/admissions/statistics/hall"); ?>'>Hall Statistics</a></li>

                                <li><a href='<?php echo url("/results"); ?>'>Accreditation Board Regular Results</a></li>
                                <li><a href='<?php echo url("/results/mature"); ?>'>Accreditation Board Mature Results</a></li>
                                <li><a href='<?php echo url("/results/technical"); ?>'>Accreditation Board Technical
                                        Results</a></li>
                                <li><a href='<?php echo url("/results/conditional"); ?>'>Accreditation Board Conditional
                                        Results</a></li>

                            <!--                           <li><a href='<?php echo url("/admissions/statistics/faculties"); ?>'>Report by Faculties</a></li>
                            <li><a href='<?php echo url("/admissions/statistics/gender"); ?>'>Report by Gender</a></li>
                            <li><a href='<?php echo url("/admissions/statistics/groupings"); ?>'>Successful and Unsuccessful Applicants</a></li>
                          -->
                            <!--  <li><a href='<?php echo url("admissions/waec/download/grades"); ?>'>Grades to excel</a></li>
                            <li><a href='<?php echo url("/admissions/waec/verifications"); ?>'>WAEC Verifications</a></li>

                             -->


                            </ul>
                        </div>
                    </li>


                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <span class="menu_icon"><i
                                        class="material-icons">lock</i></span><span>My Account</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li><a href='<?php echo url("/change_password"); ?>'>Change Password</a></li>
                                <li><a href='<?php echo url("/logout"); ?>'>Logout</a></li>


                            </ul>
                        </div>
                    </li>
                <?php else: ?>
                    <?php if(@Auth::user()->department=='Planning' ): ?>  
                        <?php if(@Auth::user()->department!='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i
                                            class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                        <li><a href='<?php echo url("/nservice"); ?>'>National Service</a></li>
                                        <li><a href='<?php echo url("/attendanceSheet"); ?>'>Exam Attendance Sheet</a></li>
                                        <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>
                                        <li><a href='<?php echo url("/download_id_cards"); ?>'>ID Card List</a></li>
                                     <?php if(@Auth::user()->role='Academic'): ?>    
                                        
                                        <li><a href='<?php echo url("/finance/status"); ?>'>Update Status / Level</a></li>
                                    <li><a href='<?php echo url("/finance/chapro"); ?>'>Update Program</a></li>
                                    <?php endif; ?>
                                    
                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <?php if(@Auth::user()->department!='LA'): ?>
                                    <li><a href='<?php echo url("/transcriptOrig"); ?>' target="_blank">Transcript</a></li>
                                    <?php endif; ?>
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/groups/view"); ?>'>View Class Groups</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>
                                    <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>

                                </ul>
                            </div>
                        </li>

                        <?php if(@Auth::user()->department!='LA'): ?>

                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                        <?php /*  <li><a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a></li>*/ ?>
                                        <li><a href='<?php echo url("/download_reports"); ?>'>Excel Reports</a></li>
                                        <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                        <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>


                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if( @Auth::user()->department=='Tpmid' ): ?>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("/nservice"); ?>'>National Service</a></li>
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/search_password"); ?>'>Search student password</a></li>
                                    

                                    <li><a href='<?php echo url("/print/password"); ?>'>Print Password Receipt</a></li>
                                    
                                    <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>


                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>
                                    <li><a href='<?php echo url("/classes/view"); ?>'>View Classing System</a></li>
                                    <li><a href='<?php echo url("/groups/view"); ?>'>View Class Groups</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/power_users"); ?>'>View Users</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>


                                </ul>
                            </div>
                        </li>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">file_upload</i><span>Upload</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/upload_students"); ?>'>Upload bulk Student data</a></li>
                                    <li><a href='<?php echo url("/index/upload"); ?>'>Upload Index Number</a></li>

                                    <li><a href='<?php echo url("/upload/courses"); ?>'>Upload Bulk Courses</a></li>
                                    <li><a href='<?php echo url("/upload/mounted"); ?>'>Upload Bulk Mounted Courses</a></li>

                                    <li><a href='<?php echo url("/resit"); ?>'>Upload Resit</a></li>
                                    <li><a href='<?php echo url("/getStaffCSV"); ?>'>Upload Staff Data</a></li>


                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">control_point</i><span>Mount/Add</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">

                                    <li><a href='<?php echo url("/create_grade"); ?>'>Create Grading System</a></li>
                                    <li><a href='<?php echo url("/classes/create"); ?>'>Create Classing System</a></li>
                                    <li><a href='<?php echo url("/registered_courses"); ?>'>Enter Semester Marks</a></li>
                                    <li><a href='<?php echo url("/create_programme"); ?>'>Add Programmes</a></li>
                                    <li><a href='<?php echo url("/groups/create"); ?>'>Create Class Groups</a></li>
                                    <li><a href='<?php echo url("/create_course"); ?>'>Add Courses</a></li>
                                    <li><a href='<?php echo url("/mount_course"); ?>'>Mount Courses</a></li>
                                    <li><a href='<?php echo url("/system/registration/batch"); ?>'>Bulk Registration</a></li>

                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">
                            <a href="#"> <span class="menu_icon"><i
                                            class="material-icons">search</i></span><span>Search</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">

                                    <li><a href='<?php echo url("/search_password"); ?>'>Search student passwords</a></li>
                                </ul>
                            </div>
                        </li>

                        <?php if(@Auth::user()->department!='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">
                                <a href="#"> <span class="menu_icon"><i
                                                class="material-icons">view_headline</i></span><span>Reports</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                        <li><a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                        </li>
                                        <li><a href='<?php echo url("/admissions/statistics/admission/type"); ?>'>Report by
                                                Admission Type</a></li>
                                        <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                        <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>

                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if( @Auth::user()->department=='Tptop' ||  @Auth::user()->department=='Tptop4' || @Auth::user()->department=='Tptop2' || @Auth::user()->department=='Tptop3' ): ?>


                        <li data-uk-dropdown class="uk-hidden-small">
                            <a href="#"> <i
                                        class="sidebar-menu-icon material-icons">attach_file</i><span>File</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">

                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("/nservice"); ?>'>National Service</a></li>
                                    <li><a href='<?php echo url("/download_id_cards"); ?>'>ID Card List</a></li>

                                    <li><a href='<?php echo url("/download_reports"); ?>'>Excel Reports</a></li>
                                    <li><a href='<?php echo url("/download_results"); ?>'>Excel NABPTEX</a></li>
                                    <li><a href='<?php echo url("/download_error"); ?>'>Error NABPTEX</a></li>
                                    <li><a href='<?php echo url("/attendanceSheet"); ?>'>Exam Attendance Sheet</a></li>
                                    <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>
                                    <li><a href='<?php echo url("/systems/grades/recover"); ?>'>Recover Deleted grades</a></li>
                                    <li><a href='<?php echo url("/systems/grades/delete"); ?>'>Delete uploaded grades</a></li>

                                    <li><a href='<?php echo url("/finance/protocol"); ?>'>Protocols/Policies</a></li>
                                    <li><a href='<?php echo url("/finance/status"); ?>'>Update Status / Level</a></li>
                                    <li><a href='<?php echo url("/finance/chapro"); ?>'>Update Program</a></li>
                                    <li><a href='<?php echo url("/updatePassword"); ?>'>Reset Staff Password</a></li>
                                    <li><a href='<?php echo url("/calender"); ?>'>Academic Calender</a></li>
                                    
                                    <li><a href='<?php echo url("qa/report/single"); ?>'>Lecturer Assessment Report</a></li>
                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/transcriptOrig"); ?>' target="_blank">Transcript</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>
                                    <li><a href='<?php echo url("/classes/view"); ?>'>View Classing System</a></li>
                                    <li><a href='<?php echo url("/groups/view"); ?>'>View Class Groups</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/power_users"); ?>'>View Users</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>


                                </ul>
                            </div>
                        </li>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_money</i><span>Finance</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/finance/reports/fees/"); ?>'>View Fees</a></li>
                                    <li><a href='<?php echo url("/finance/upload"); ?>'>Finance Fee Upload</a></li>
                                    <li><a href='<?php echo url("/upload/fees"); ?>'>Bank Fees Upload</a></li>
                                    <li><a href='<?php echo url("banks"); ?>'>View Banks</a></li>
                                    <li><a href='<?php echo url("view_payments"); ?>'>Payments Ledger</a></li>

                                    <li><a href='<?php echo url("view_payments_master"); ?>'>Payments (sum)</a></li>
                                    <li><a href='<?php echo url("owing_paid"); ?>'>Owing reports</a></li>
                                    <li><a href='<?php echo url("pay_fees"); ?>'>Individual Payments</a></li>
                                    <li><a href='<?php echo url("owing_excel"); ?>'>Owing Excel</a></li>
                                    <li><a href='<?php echo url("paid_excel"); ?>'>Paid Excel</a></li>
                                    <li><a href='<?php echo url("/print/receipt"); ?>'>Print Fee Receipt</a></li>
                                    <li><a href='<?php echo url("/print/password"); ?>'>Print Password Receipt</a></li>

                                    <li><a href='<?php echo url("pay_fees_penalty"); ?>'>Late Registration payment</a></li>


                                </ul>
                            </div>
                        </li>











                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">file_upload</i><span>Upload</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/upload_students"); ?>'>Upload bulk Student data</a></li>
                                    <li><a href='<?php echo url("/index/upload"); ?>'>Upload Index Number</a></li>
                                    <li><a href='<?php echo url("/upload_marks"); ?>'>Upload Semester Marks</a></li>
                                    <li><a href='<?php echo url("/upload/courses"); ?>'>Upload Bulk Courses</a></li>
                                    <li><a href='<?php echo url("/upload/mounted"); ?>'>Upload Bulk Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/legacy"); ?>'>Upload Old Results</a></li>
                                    <li><a href='<?php echo url("/finance/upload"); ?>'>Finance Fee Upload</a></li>
                                    <li><a href='<?php echo url("/upload/fees"); ?>'>Bank Fees Upload</a></li>
                                    <li><a href='<?php echo url("/resit"); ?>'>Upload Resit</a></li>
                                    <li><a href='<?php echo url("/getStaffCSV"); ?>'>Upload Staff Data</a></li>


                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">control_point</i><span>Mount/Add</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/add_students"); ?>'>Add Students</a></li>
                                    <li><a href='<?php echo url("/create_grade"); ?>'>Create Grading System</a></li>
                                    <li><a href='<?php echo url("/classes/create"); ?>'>Create Classing System</a></li>
                                    <li><a href='<?php echo url("/registered_courses"); ?>'>Enter Semester Marks</a></li>
                                    <li><a href='<?php echo url("/create_programme"); ?>'>Add Programmes</a></li>
                                    <li><a href='<?php echo url("/groups/create"); ?>'>Create Class Groups</a></li>
                                    <li><a href='<?php echo url("/create_course"); ?>'>Add Courses</a></li>
                                    <li><a href='<?php echo url("/mount_course"); ?>'>Mount Courses</a></li>
                                    <li><a href='<?php echo url("/system/registration/batch"); ?>'>Bulk Registration</a></li>
                                    <li><a href='<?php echo url("/add_staff"); ?>'>Add Staff</a></li>
                                    <li><a href='<?php echo url("/power_users"); ?>'>Add Staff 2</a></li>

                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">
                            <a href="#"> <i
                                        class="sidebar-menu-icon material-icons">view_headline</i><span>Reports</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>

                                    <li><a href='<?php echo url("/view_payments_master"); ?>'>Fee Payment by Students
                                            Report</a></li>
                                    <li><a href='<?php echo url("/finance/reports/programs"); ?>'>Fee Payment by Programs
                                            Report</a></li>
                                    <li><a href='<?php echo url("/owing_paid"); ?>'>Fees Owings By Students Reports</a></li>
                                    <li><a href='<?php echo url("/finance/reports/programs"); ?>'>Fees Owings By Programs
                                            Reports</a></li>
                                    <li><a href='<?php echo url("/finance/reports/ledger/student"); ?>'>Print Student
                                            Ledger</a></li>
                                    <li><a href='<?php echo url("/finance/reports/cummulative"); ?>'>Fees Cummulative Reports
                                            by Program</a></li>
                                    <li><a href='<?php echo url("view_payments"); ?>'>Payments Ledger</a></li>
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>
                                    <li><a href='<?php echo url("/admissions/statistics/admission/type"); ?>'>Report by
                                            Admission Type</a></li>
                                    <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                    <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>


                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">
                            <a href="#"> <span class="menu_icon"><i
                                            class="material-icons">search</i></span><span>Search</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">

                                    <li><a href='<?php echo url("/search_password"); ?>'>Search student passwords</a></li>
                                </ul>
                            </div>
                        </li>


                    <?php endif; ?>



                    <?php if( @Auth::user()->department=='Rector' ): ?>
                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>

                                    <li><a href='<?php echo url("/groups/view"); ?>'>View Class Groups</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>

                                </ul>
                            </div>
                        </li>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_money</i><span>Finance</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/finance/reports/fees/"); ?>'>View Fees</a></li>
                                    <li><a href='<?php echo url("banks"); ?>'>View Banks</a></li>
                                    <li><a href='<?php echo url("view_payments"); ?>'>Payments Ledger</a></li>

                                    <li><a href='<?php echo url("view_payments_master"); ?>'>Payments (sum)</a></li>
                                    <li><a href='<?php echo url("owing_paid"); ?>'>Owing reports</a></li>


                                </ul>
                            </div>
                        </li>

                        <?php if(@Auth::user()->department!='LA'): ?>

                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                        <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>

                                        <li><a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                        </li>
                                        <li><a href='<?php echo url("/admissions/statistics/admission/type"); ?>'>Report by
                                                Admission Type</a></li>
                                        <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>
                                        <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>

                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>


                    <?php endif; ?>


                    <?php if( @Auth::user()->role=='Support' || @Auth::user()->role=='FSupport' || @Auth::user()->role=='Degree'): ?>

                        <?php if(@Auth::user()->department!='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i
                                            class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">

                                        
                                        <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                        <li><a href='<?php echo url("/download_results"); ?>'>Excel NABPTEX</a></li>
                                        <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>

                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>



                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <?php if( @Auth::user()->department!='LA'): ?>
                                        <li><a href='<?php echo url("/nservice"); ?>'>National Service</a></li>
                                        <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                        <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <?php endif; ?>

                                </ul>
                            </div>
                        </li>

                        <?php if(@Auth::user()->department!='LA'): ?>

                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                        <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                        <li><a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                        </li>
                                        <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                        <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>


                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>


                    <?php endif; ?>

                    <?php if( @Auth::user()->department=='qa' ): ?>



                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>

                                    <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                    <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>
                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>

                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>

                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>


                                </ul>
                            </div>
                        </li>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>
                                    <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                    <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>
                                    <li><a href='<?php echo url("qa/report/single"); ?>'>Lecturer Assessment Report</a></li>


                                </ul>
                            </div>
                        </li>


                    <?php endif; ?>

                    <?php if( @Auth::user()->role=='HOD' ): ?>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                    <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>
                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>
                                    <li><a href='<?php echo url("/classes/view"); ?>'>View Classing System</a></li>
                                    <li><a href='<?php echo url("/groups/view"); ?>'>View Class Groups</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/power_users"); ?>'>View Users</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>


                                </ul>
                            </div>
                        </li>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">attach_money</i><span>Finance</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("fee_summary"); ?>'>Fee Summary</a></li>
                                    <li><a href='<?php echo url("view_fees"); ?>'>View Fees</a></li>

                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>


                                    <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                    <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>


                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">phone</i><span>Results</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    


                                </ul>
                            </div>
                        </li>


                    <?php endif; ?>

                    <?php if( @Auth::user()->role=='Lecturer' ): ?>



                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>
                                </ul>
                            </div>
                        </li>

                        <?php if(@Auth::user()->department!='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                        
                                        <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                        <li><a href='<?php echo url("/download_results"); ?>'>Excel NABPTEX</a></li>
                                        <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>
                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">phone</i><span>Results</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/download_registered"); ?>'>Download Excel Sheet</a></li>
                                    <li><a href='<?php echo url("/upload_marks"); ?>'>Upload Semester Marks</a></li>
                                    <li><a href='<?php echo url("/view/edit"); ?>'>View / Edit Results</a></li>

                                </ul>
                            </div>
                        </li>


                    <?php endif; ?>



                    <?php if( @Auth::user()->department=='Registrar' ): ?>
                        <?php if(@Auth::user()->department!='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">


                                <a href="#"><i
                                            class="sidebar-menu-icon material-icons md-18">attach_file</i><span>File</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                        <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                        <li><a href='<?php echo url("/download_regList"); ?>'>Registered</a></li>
                                        <li><a href='<?php echo url("/student/resit"); ?>'>Resit List</a></li>

                                    </ul>
                                </div>
                            </li>

                        <?php endif; ?>


                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">view_list</i><span>View</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("staff"); ?>'>View Staff</a></li>
                                    <li><a href='<?php echo url("/grade_system"); ?>'>View Grading Systems</a></li>
                                    <li><a href='<?php echo url("/courses"); ?>'>View Courses</a></li>
                                    <li><a href='<?php echo url("/mounted_view"); ?>'>View Mounted Courses</a></li>
                                    <li><a href='<?php echo url("/power_users"); ?>'>View Users</a></li>
                                    <li><a href='<?php echo url("view_fees"); ?>'>View Fees</a></li>
                                    <li><a href='<?php echo url("/directory"); ?>'>Staff Directory</a></li>

                                </ul>
                            </div>
                        </li>

                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i
                                        class="sidebar-menu-icon material-icons md-18">view_headline</i><span>Reports</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/transcript"); ?>' target="_blank">Results</a></li>
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li><a href='<?php echo url("/broadsheet/noticeboard"); ?>'>GPA Academic Board</a></li>
                                    <li><a href='<?php echo url("/broadsheet/napbtex"); ?>'>Broadsheet Nabptex</a></li>
                                    <li><a href='<?php echo url("owing_paid"); ?>'>Owing reports</a></li>
                                    <li><a href='<?php echo url("fee_summary"); ?>'>Fee Summary</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>


                                </ul>
                            </div>
                        </li>




                    <?php endif; ?>


                    <?php if(@Auth::user()->department=='Examination' ): ?>
                        <li data-uk-dropdown class="uk-hidden-small">


                            <a href="#"><i class="sidebar-menu-icon material-icons md-18">school</i><span>Examinations Unit</span></a>
                            <div class="uk-dropdown uk-dropdown-scrollable">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li><a href='<?php echo url("/students"); ?>'>Students</a></li>
                                    <li><a href='<?php echo url("/programmes"); ?>'>View Programmes</a></li>
                                    <li><a href='<?php echo url("/report/registration"); ?>'>Registration</a></li>
                                    <li>
                                        <a href='<?php echo url("admissions/statistics/comprehensive"); ?>'>Applied/Registered</a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                    <?php endif; ?>







                    <?php if(@\Auth::user()->department!='Deans'|| @\Auth::user()->department!='Admissions' ||  @\Auth::user()->department!='Planning' ||  @\Auth::user()->department!='Examination' ): ?>
                        <li data-uk-dropdown class="uk-hidden-small">





                        <?php if( @Auth::user()->department=='LA'): ?>
                            <li data-uk-dropdown class="uk-hidden-small">
                                <a href="#"> <i
                                            class="sidebar-menu-icon material-icons">work</i><span>Liaison Module</span></a>
                                <div class="uk-dropdown uk-dropdown-scrollable">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href='<?php echo url("/students"); ?>'>View Students</a></li>

                                        <li><a href='<?php echo url("/liaison/print/bulk"); ?>'>Bulk Printing</a></li>
                                        <li><a href='<?php echo url("/liaison/assumption"); ?>'>Assumption of duty Data</a></li>

                                        <li><a href='<?php echo url("liaison/data"); ?>'>Industrial Attachment Data</a></li>
                                        <li><a href='<?php echo url("/liaison/create/zones"); ?>'>Add Zones</a></li>
                                        <li><a href='<?php echo url("liaison/zones"); ?>'>Add View Zones</a></li>
                                        <li><a href='<?php echo url("liaison/units/create"); ?>'>Add Unit heads</a></li>
                                        <li><a href='<?php echo url("liaison/units"); ?>'>View Unit heads</a></li>
                                        <li><a href='<?php echo url("/calender"); ?>'>Close or open online system</a></li>

                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>



                    <?php endif; ?>
                    <li data-uk-dropdown class="uk-hidden-small">
                        <a href="#"> <span class="menu_icon"><i
                                        class="material-icons">lock</i></span><span>My Account</span></a>
                        <div class="uk-dropdown uk-dropdown-scrollable">
                            <ul class="uk-nav uk-nav-dropdown">


                                <li><a href='<?php echo url("/updateProfile"); ?>'>Change Phone no</a></li>
                                <li><a href='<?php echo url("/change_password"); ?>'>Change Password</a></li>
                                <li><a href='<?php echo url("/logout"); ?>'>Logout</a></li>


                            </ul>
                        </div>
                    </li>

                    <li data-uk-dropdown="justify:'#top_bar'" class="uk-visible-small">
                        <a href="#"><i class="material-icons">&#xE5D2;</i><span>Menu</span></a>
                        <div class="uk-dropdown uk-dropdown-width-2">
                            <div class="uk-grid uk-dropdown-grid" data-uk-grid-margin>
                                <div class="uk-width-1-2">
                                    <ul class="uk-nav uk-nav-dropdown ">

                                        <li><a href='<?php echo url("/logout"); ?>'>Logou</a></li>

                                        <li data-uk-dropdown class="uk-hidden-small">
                                            <a href="#"> <span class="menu_icon"><i class="material-icons">&#xE5D2;</i></span><span>Support</span></a>
                                            <div class="uk-dropdown uk-dropdown-scrollable">
                                                <ul class="uk-nav uk-nav-dropdown">

                                                    <li><a href='<?php echo url("applicants/view"); ?>'>View Applicants</a>
                                                    </li>
                                                    <li><a href='<?php echo url("/logout"); ?>'>Logout</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php endif; ?>


                                    </ul>


                                </div>

                            </div>
                        </div>
                    </li>
            </ul>


        <?php endif; ?>
    </div>
</div>

<div id="page_content">
    <div id="page_content_inner">


        <?php echo $__env->yieldContent('content'); ?>


    </div>
</div>
<br/>
<!--    <div class="footer uk-text-small"><center><?php date("Y");?> All Rights Reserved | Takoradi Technical University - Powered by Tpconnect (<span class="uk-text-primary uk-text-bold uk-text-small">Hotlines 0505284060 (Gad), 0246091283 (Kojo),0276363053 (Timo)</span>)-->
</center></div>

 

<script>
    $(function () {
        if (isHighDensity) {
            // enable hires images
            altair_helpers.retina_images();
        }
        if (Modernizr.touch) {
            // fastClick (touch devices)
            FastClick.attach(document.body);
        }
    });
    $window.load(function () {
        // ie fixes
        altair_helpers.ie_fix();
    });
</script>
<div id="style_switcher">
    <div id="style_switcher_toggle"><i class="material-icons">&#xE8B8;</i></div>
    <div class="uk-margin-medium-bottom">
        <h4 class="heading_c uk-margin-bottom">Colors</h4>
        <ul class="switcher_app_themes" id="theme_switcher">
            <li class="app_style_default active_theme" data-app-theme="">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_a" data-app-theme="app_theme_a">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_b" data-app-theme="app_theme_b">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_c" data-app-theme="app_theme_c">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_d" data-app-theme="app_theme_d">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_e" data-app-theme="app_theme_e">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_f" data-app-theme="app_theme_f">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_g" data-app-theme="app_theme_g">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_h" data-app-theme="app_theme_h">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_i" data-app-theme="app_theme_i">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
        </ul>
    </div>
    <div class="uk-visible-large uk-margin-medium-bottom">
        <h4 class="heading_c">Sidebar</h4>
        <p>
            <input type="checkbox" name="style_sidebar_mini" id="style_sidebar_mini" data-md-icheck/>
            <label for="style_sidebar_mini" class="inline-label">Mini Sidebar</label>
        </p>
    </div>
    <div class="uk-visible-large uk-margin-medium-bottom">
        <h4 class="heading_c">Layout</h4>
        <p>
            <input type="checkbox" name="style_layout_boxed" id="style_layout_boxed" data-md-icheck/>
            <label for="style_layout_boxed" class="inline-label">Boxed layout</label>
        </p>
    </div>
    <div class="uk-visible-large">
        <h4 class="heading_c">Main menu accordion</h4>
        <p>
            <input type="checkbox" name="accordion_mode_main_menu" id="accordion_mode_main_menu" data-md-icheck/>
            <label for="accordion_mode_main_menu" class="inline-label">Accordion mode</label>
        </p>
    </div>
</div>

<script>
    $(function () {
        var $switcher = $('#style_switcher'),
            $switcher_toggle = $('#style_switcher_toggle'),
            $theme_switcher = $('#theme_switcher'),
            $mini_sidebar_toggle = $('#style_sidebar_mini'),
            $boxed_layout_toggle = $('#style_layout_boxed'),
            $accordion_mode_toggle = $('#accordion_mode_main_menu'),
            $body = $('body');


        $switcher_toggle.click(function (e) {
            e.preventDefault();
            $switcher.toggleClass('switcher_active');
        });

        $theme_switcher.children('li').click(function (e) {
            e.preventDefault();
            var $this = $(this),
                this_theme = $this.attr('data-app-theme');

            $theme_switcher.children('li').removeClass('active_theme');
            $(this).addClass('active_theme');
            $body
                .removeClass('app_theme_a app_theme_b app_theme_c app_theme_d app_theme_e app_theme_f app_theme_g app_theme_h app_theme_i')
                .addClass(this_theme);

            if (this_theme == '') {
                localStorage.removeItem('altair_theme');
            } else {
                localStorage.setItem("altair_theme", this_theme);
            }

        });

        // hide style switcher
        $document.on('click keyup', function (e) {
            if ($switcher.hasClass('switcher_active')) {
                if (
                    ( !$(e.target).closest($switcher).length )
                    || ( e.keyCode == 27 )
                ) {
                    $switcher.removeClass('switcher_active');
                }
            }
        });

        // get theme from local storage
        if (localStorage.getItem("altair_theme") !== null) {
            $theme_switcher.children('li[data-app-theme=' + localStorage.getItem("altair_theme") + ']').click();
        }


        // toggle mini sidebar

        // change input's state to checked if mini sidebar is active
        if ((localStorage.getItem("altair_sidebar_mini") !== null && localStorage.getItem("altair_sidebar_mini") == '1') || $body.hasClass('sidebar_mini')) {
            $mini_sidebar_toggle.iCheck('check');
        }

        $mini_sidebar_toggle
            .on('ifChecked', function (event) {
                $switcher.removeClass('switcher_active');
                localStorage.setItem("altair_sidebar_mini", '1');
                location.reload(true);
            })
            .on('ifUnchecked', function (event) {
                $switcher.removeClass('switcher_active');
                localStorage.removeItem('altair_sidebar_mini');
                location.reload(true);
            });


        // toggle boxed layout

        if ((localStorage.getItem("altair_layout") !== null && localStorage.getItem("altair_layout") == 'boxed') || $body.hasClass('boxed_layout')) {
            $boxed_layout_toggle.iCheck('check');
            $body.addClass('boxed_layout');
            $(window).resize();
        }

        $boxed_layout_toggle
            .on('ifChecked', function (event) {
                $switcher.removeClass('switcher_active');
                localStorage.setItem("altair_layout", 'boxed');
                location.reload(true);
            })
            .on('ifUnchecked', function (event) {
                $switcher.removeClass('switcher_active');
                localStorage.removeItem('altair_layout');
                location.reload(true);
            });

        // main menu accordion mode
        if ($sidebar_main.hasClass('accordion_mode')) {
            $accordion_mode_toggle.iCheck('check');
        }

        $accordion_mode_toggle
            .on('ifChecked', function () {
                $sidebar_main.addClass('accordion_mode');
            })
            .on('ifUnchecked', function () {
                $sidebar_main.removeClass('accordion_mode');
            });


    });
</script>
<?php echo $__env->yieldContent('js'); ?>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')}
    });    </script>

<script src="<?php echo url('public/assets/js/jquery.form.js'); ?>"></script>
<script src="<?php echo url('public/assets/js/jquery.validate.min.js'); ?>"></script>

<script language="javascript" type="text/javascript">
    $(document).ready(function () {
        $('.saves').on('click', function (e) {


            var name = $(this).closest('tr').find('.name').val();
            var program = $(this).closest('tr').find('.program').val();
            var level = $(this).closest('tr').find('.level').val();

            //alert(hall);
            UIkit.modal.confirm("Are you sure you want to add this group "
                , function () {
                    modal = UIkit.modal.blockUI("<div class='uk-text-center'>Creating Group <br/><img class='uk-thumbnail uk-margin-top' src='<?php echo url('public/assets/img/spinners/spinner.gif'); ?>' /></div>");
                    //setTimeout(function(){ modal.hide() }, 500) })()
                    $.ajax({

                        type: "POST",
                        url: "admit",
                        data: {applicant: student, program: program, hall: hall, admit: admit}, //your form data to post goes
                        dataType: "html",
                    }).done(function (data) {
                        modal.hide();

                        UIkit.modal.alert("Group created successfully");
                        // $("#ts_pager_filter").load(window.location + " #ts_pager_filter");
                        // console.log(data);
                        location.reload();
//        return (function(modal){ modal = UIkit.modal.blockUI("<div class='uk-text-center'>Processing Transcript Order<br/><img class='uk-thumbnail uk-margin-top' src='<?php echo url('public/assets/img/spinners/spinner.gif'); ?>' /></div>"); setTimeout(function(){ modal.hide() }, 500) })();
                    });
                }
            );
        });

    });

    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML =
            "<html><head><title></title></head><body>" +
            divElements + "</body>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }
</script>
<script>
    function recalculateSum() {
        var num1 = parseFloat(document.getElementById("pay").value);
        if (isNaN(num1)) {
            var num1 = 0;
        } else {
            var num1 = parseFloat(document.getElementById("pay").value);
        }
        var num2 = parseFloat(document.getElementById("bill").value);
        var num3 = parseFloat(document.getElementById("totalbill").value);
        var num4 = parseFloat(document.getElementById("financesum").value);
        document.getElementById("amount_left").value = (num3 - num4 - num1);
    }

    function MM_openBrWindow(theURL, winName, features) { //v2.0
        window.open(theURL, winName, features);
    }

    function recalculateTranAmt() {
        var copies = document.getElementById("copies").value;
        var price = parseFloat(document.getElementById("price").value);
        document.getElementById("amount").value = (copies * price);
    }
</script>
<script>


    $(document).ready(function () {

        function checkFormElements() {
        }


        $("#insertPaymentRow").bind('click', function () {

            var numOrgs = $(" table#paymentTable tr[payment_row]").length + 1;
            var newOrg = $("table#paymentTable tr:first ").clone(true);
            $(newOrg).children(' td#insertPaymentCell ').html('<button  type="button" id="removePaymentRow_' + numOrgs + '" class="md-btn md-btn-danger md-btn-small uk-margin-small-top"  title="remove this row"><i class="sidebar-menu-icon material-icons">remove</i></button>');
            var amountLine = $(newOrg).children('td')[2];
            $(amountLine).children(':last-child').prop('value', '');
            var amountInput = $(amountLine).children(':last-child');
            $(amountInput).prop('id', 'amt_' + numOrgs);
            $(newOrg).attr('id', 'paymentRow_' + numOrgs);
            $(newOrg).insertAfter($("table#paymentTable tr:last"));
            $('#removePaymentRow_' + numOrgs).bind("click", function () {
// $(amountInput).trigger('keyup');
                $('#paymentRow_' + numOrgs).remove();
                var count = 0;
            });
// $('#amt_'+numOrgs).bind('focus',function(){
//   console.log('hello from here');
// });

//});


            $('#paymentTable .pay_type  :selected').parent().each(function () {
                if ($(this).prop('selectedIndex') <= 0) {
                    //$('#new_payment_individual_form :submit').prop('disabled','disabled');
                    //  $('#alertInfo').css('display','block').html("Please select a payment type!");
                }
            });
//console.log($(this).prop('name')+"->"+$('#paymentTable .pay_type  :selected').parent().length);

        });
        $('#save').on('click', function (e) {
            return (function (modal) {
                modal = UIkit.modal.blockUI("<div class='uk-text-center'>Processing data<br/><img class='uk-thumbnail uk-margin-top' src='<?php echo url('public/assets/img/spinners/spinner_success.gif'); ?>' /></div>");
                setTimeout(function () {
                    modal.hide()
                }, 50000)
            })();
        });
    });    </script>
<!-- google web fonts -->
<script>
    WebFontConfig = {
        google: {
            families: [
                'Source+Code+Pro:400,700:latin',
                'Roboto:400,300,500,700,400italic:latin'
            ]
        }
    };
    (function () {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
    })();      </script>


</body>
</html>