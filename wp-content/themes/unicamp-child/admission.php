<?php /* Template Name: admission */ ?>

<?php
// get_header();
?>
<!DOCTYPE html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar-size="lg" data-sidebar="light" data-sidebar-image="none" data-preloader="disable" data-bs-theme="light" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default" data-sidebar-visibility="show">
   <meta http-equiv="content-type" content="text/html;charset=utf-8" />
   <head>
      <meta charset="utf-8" />
      <title>
         SFS Guwahati Online Admission
      </title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta content="SFS Guwahati Paperless process managed by Vasp Technologies Pvt Ltd" name="description" />
      <meta content="Vasp Technologies Pvt Ltd" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/favicon.ico" />
      <!--<script src="<?php echo get_stylesheet_directory_uri(); ?>/Scripts/jquery-3.7.1.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>-->
      <!-- Layout config Js -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/layout.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- Bootstrap Css -->
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/bootstrap.mine166.css?v=1.0.4" rel="stylesheet" type="text/css" />
      <!-- Icons Css -->
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      <!-- App Css-->
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/app.min.css" rel="stylesheet" type="text/css" />
      <!-- custom Css-->
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/jquery-confirm.min.css" rel="stylesheet" />
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/select2.minf700.css?v=1.0.1" rel="stylesheet" />
      <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/multi.js/multi.min.css" />
      <!-- autocomplete css -->
      <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/%40tarekraafat/autocomplete.js/css/autoComplete.css" />
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/extra/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css" rel="stylesheet" type="text/css" />
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/extra/css/miscellaneous/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/loadstyle.css" rel="stylesheet" />
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/forms/validate.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/forms/js_AdmissionFormc3b2.js?v=2.0.35" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <style>
         #cmbCommunity option[value="Roman Catholic"] {
         display: none;
         }
         .line-spacing {
         line-height: 2.2; /* Adjust this value to control the spacing */
         font-size: 14px;
         font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
         }
         .select2-selection--single {
         height: 38px !important
         }
         .select2-container .select2-selection--single .select2-selection__rendered {
         display: block;
         padding-top: 5px;
         padding-right: 20px;
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
         }
         .select2-container--default .select2-selection--single .select2-selection__arrow {
         height: 26px;
         position: absolute;
         top: 5px;
         right: 1px;
         width: 20px;
         }
         .form-label {
         margin-bottom: 0.2rem;
         }
      </style>
   </head>
   <body>
      <div id="layout-wrapper">
         <header id="page-topbar" style="background-color: #7e1416">
            <div class="layout-width">
               <div class="navbar-header">
                  <div class="d-flex align-items-center">
                     <span class="logo-lg">
                     <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo.png" alt="" height="80">
                     </span>
                     <form method="post" action="https://admission.sfsguwahati.ac.in/admission" id="form2">
                        <div class="aspNetHidden">
                           <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="9XuHA2pRqcDphmcyp2W/dV18yLbex+MC1kgKaakWuVZp9RZ5kdKEkUY0A2RAVvkOmKZbXc8VAPrr2bjIvhxWIINtL1O2EBzt8uR2sQdjHsZ0txziR+lbSqTwlqytkKA5Cp/be7ALIvicNcgLxWgaFKDF4QpfBx796tAUVG+Yh0jMP/mW2ry1w235w4df1Qx5E8VTlKjiVAkXvwnETj9+2PaTz2QB3S2xeOyHLyMaFY4LEe+SILtYFFd3UjwOsUyKVH6YA43NlVYAhi7/TY9CbFOuV8Fp8TF1jYDc4HfZAI3xEmO5aK6AJdxjP2ixfh3FsNl81lWreMaYFOGUhnclCnsMlG5b6FN4LRROzxfrKuI589MNayK2SGQjL8nTs36v" />
                        </div>
                        <div class="aspNetHidden">
                           <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="B91A3F2C" />
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </header>
         <!-- removeNotificationModal -->
         <!-- ========== App Menu ========== -->
         <!-- Left Sidebar End -->
         <!-- Vertical Overlay-->
         <div class="vertical-overlay"></div>
         <!-- ============================================================== -->
         <!-- Start right Content here -->
         <!-- ============================================================== -->
         <div class="main-content">
            <div class="page-content" style="margin-top: 10px !important">
               <div class="container-fluid">
                  <div class="overlaypos" id="l2" style="display: none;"></div>
                  <progress id="l1" class="loader-pos" style="display: none; z-index: 100000000;"></progress>
                  <div class="row">
                     <div class="col-xl-12">
                        <marquee width="100%" direction="left" behavior="scroll" scrollamount="4">
                           <a style="cursor: pointer;" href="acknowledgement.html">If your payment was deducted but you did not receive the acknowledgement of online application form, click here to update.</a>
                        </marquee>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-xl-12">
                        <div class="card">
                           <div class="card-header" style="background-color: #7e1416">
                              <h4 class="card-title mb-0 text-white">Online Admission Form</h4>
                              <div style="display: none">
                                 <span id="lblmainstd" class="lblmainstd">0</span>
                                 <span id="lbltype" class="lbltype"></span>
                                 <span id="lblses" class="lblses"></span>
                              </div>
                           </div>
                           <!-- end card header -->
                           <div class="card-body form-steps">
                              <form class="vertical-navs-step">
                                 <div class="row gy-5">
                                    <div class="col-lg-3">
                                       <div class="nav flex-column custom-nav nav-pills" role="tablist" aria-orientation="vertical">
                                          <button class="nav-link active nav-click-x" id="v-pills-bill-declare-tab" type="button" role="tab" aria-selected="true" data-bs-toggle="pill" data-bs-target="#v-pills-bill-declare" aria-controls="v-pills-bill-declare" data-position="0">
                                          <span class="step-title1 me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>1
                                          </span>
                                          DECLARATION
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-class-info-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-class-info" aria-controls="v-pills-class-info" data-position="1" tabindex="-1">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>2
                                          </span>
                                          ACADEMIC SESSION & CLASS
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-personal-info-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-personal-info" aria-controls="v-pills-personal-info">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>3
                                          </span>
                                          PERSONAL INFORMATION 
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-health-info-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-health-info" aria-controls="v-pills-health-info">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>4
                                          </span>
                                          CANDIDATE'S HEALTH RECORD
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-parent-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-parent" aria-controls="v-pills-parent">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>4
                                          </span>
                                          FAMILY DETAILS
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-Co-Curricular-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-Co-Curricular" aria-controls="v-pills-Co-Curricular">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>5
                                          </span>
                                          CO-CURRICULAR ACTIVITIES
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-Transportation-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-Transportation" aria-controls="v-pills-Transportation">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>6
                                          </span>
                                          TRANSPORTATION
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-Stream-Subject-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-Stream-Subject" aria-controls="v-pills-Stream-Subject">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>7
                                          </span>
                                          STREAM & SUBJECTS
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-Documents-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-Documents" aria-controls="v-pills-Documents">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>8
                                          </span>
                                          DOCUMENTS
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-source-info-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-source-info" aria-controls="v-source-info">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>10
                                          </span>
                                          SOURCE OF INFORMATION
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-Social-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-Social" aria-controls="v-pills-Social">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>11
                                          </span>
                                          PARENT’S CONSENT
                                          </button>
                                          <button class="nav-link nav-click-x" id="v-pills-payment-tab" type="button" role="tab" aria-selected="false" data-bs-toggle="pill" data-bs-target="#v-pills-payment" aria-controls="v-pills-payment">
                                          <span class="step-title me-2">
                                          <i class="ri-close-circle-fill step-icon me-2"></i>12
                                          </span>
                                          PAYMENT
                                          </button>
                                       </div>
                                       <!-- end nav -->
                                    </div>
                                    <!-- end col-->
                                    <div class="col-lg-9">
                                       <div class="px-lg-1">
                                          <div class="tab-content">
                                             <div class="tab-pane fade show active admission-panel" id="v-pills-bill-declare" role="tabpanel" aria-labelledby="v-pills-bill-declare-tab">
                                                <div>
                                                   <div class="row">
                                                      <div class="col-md-12">
                                                         <div class="line-spacing" style="text-align: justify">
                                                            I / We solemnly state that I / we have not approached, lobbied or paid or promised to pay donation for admission to
                                                            <span id="ContentPlaceHolder1_lblSchoolname" style="font-weight: bold;">ST. FRANCIS DE SALES SCHOOL, GUWAHATI UDAYAN VIHAR, SATGAON, NARENGI,GUWAHATI, ASSAM-781171</span>. 
                                                            I / We agree to the cancellation of the admission of my candidate if any such illegal practices are proved by the school management at a later stage.&nbsp;
                                                            I / We do hereby agree to abide by the rules and regulations of the school and schedule of fee and payments as amended by the school at its discretion from time to time.
                                                            I / We, hereby, declare that, the entries made by me in the Application Form are complete and true to the best of my knowledge and based on records.
                                                            <br />
                                                            <br />
                                                            <h6>NOTE</h6>
                                                            <ol>
                                                               <li>Once an application form is submitted, the student's name, parent's name, and DOB cannot be changed.</li>
                                                               <li>The management reserves the right to admit or reject any application for admission without giving any explanation.</li>
                                                            </ol>
                                                         </div>
                                                      </div>
                                                      <div class="row">
                                                         <div class="col-md-3">
                                                         </div>
                                                         <div class="col-md-6" id="divLogin" style="display: none">
                                                            <div class="col-md-12 col-lg-12 col-xl-12">
                                                               <div class="card mt-4 card-bg-fill border">
                                                                  <div class="card-body p-4">
                                                                     <div class="text-center mt-2">
                                                                        <h5 class="text-primary">Login</h5>
                                                                     </div>
                                                                     <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                                                        Enter your phone number to proceed!
                                                                     </div>
                                                                     <div class="p-2">
                                                                        <div class="mb-4">
                                                                           <label class="form-label">Phone Number</label>
                                                                           <input type="text" maxlength="10" class="form-control allownumericwithoutdecimal" id="txtphonenumber" placeholder="Enter Phone">
                                                                        </div>
                                                                        <div class="text-center mt-4">
                                                                           <button class="btn btn-danger w-100 btn-login" type="button">Send OTP</button>
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="d-flex align-items-start gap-3 mt-4 div-declare-btn">
                                                   <button type="button" class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-cls-year">
                                                   <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-class-info" role="tabpanel" aria-labelledby="v-pills-class-info-tab">
                                                <div class="row g-3">
                                                   <div class="col-md-12">
                                                      <div class="row">
                                                         <div class="col-md-8">
                                                            <h5>Academic Session & Class</h5>
                                                            <p class="text-muted">Select all information below</p>
                                                         </div>
                                                         <div class="col-md-4 search-div" style="display: none;">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                               <h5 class="fs-14 text-primary mb-0" style="cursor: pointer;">
                                                                  <a class="search-prefill">
                                                                  <i class="ri-history-fill align-middle me-2 ri-2x"></i>Continue from last step
                                                               </h5>
                                                               </a></h5>
                                                               <!--<span class="badge bg-danger rounded-pill">3</span>-->
                                                            </div>
                                                         </div>
                                                      </div>
                                                      <div class="row">
                                                         <div class="col-sm-6">
                                                            <label for="cmbYear" class="form-label">Academic Session</label>
                                                            <select class="form-control cmb-year" id="cmbYear"></select>
                                                         </div>
                                                         <div class="col-sm-6 div-cls-A">
                                                            <label for="cmbClass" class="form-label">Class</label>
                                                            <select class="form-control cmb-cls" id="cmbClass"></select>
                                                         </div>
                                                      </div>
                                                      <div class="row age-baner mt-5" style="display: none;">
                                                         <div class="col-md-12">
                                                            <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                               <div class="alert-body">
                                                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                                  <div class="d-flex">
                                                                     <div class="flex-shrink-0 me-3">
                                                                        <i class="ri-error-warning-line fs-16 align-middle"></i>
                                                                     </div>
                                                                     <div class="flex-grow-1">
                                                                        <h5 class="alert-heading">Class <span class="lblclsage"></span>Age Alert!</h5>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                               <div class="alert-content age-content">
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                      <div class="d-flex align-items-start gap-3 mt-4 div-cls-btn">
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-personal-info" role="tabpanel" aria-labelledby="v-pills-personal-info-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>Personal Details</h5>
                                                            <p class="text-muted">Fill all information below</p>
                                                         </div>
                                                         <div class="row pen-div">
                                                            <div class="col-sm-12">
                                                               <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                                  <div class="alert-body">
                                                                     <div class="d-flex">
                                                                        <div class="flex-shrink-0 me-3">
                                                                           <i class="ri-error-warning-line fs-16 align-middle"></i>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                           <h5 class="alert-heading">Please fill the Permanent Education Number if available !</h5>
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                                  <div class="alert-content pen-content">
                                                                     <div class="col-sm-12">
                                                                        <label for="txtPEN" class="form-label">Permanent Education Number <i>(Kindly obtain this from the last school attended)</i></label>
                                                                        <input type="text" style="width: 50%" class="form-control txtPEN allownumericwithoutdecimal" id="txtPEN" placeholder="Enter PEN" value="">
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <div class="row">
                                                            <div style="display: none"><span class="lblregno"></span></div>
                                                            <div class="col-md-6">
                                                               <label for="txtfirstName" class="form-label">First name <span style="color: Red;">* </span></label>
                                                               <input type="text" class="form-control txtfirstName txtcsname" id="txtfirstName" placeholder="Enter first name" value="" required>
                                                               <div class="invalid-feedback">Please enter a first name</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="txtMiddleName" class="form-label">Middle name</label>
                                                               <input type="text" class="form-control txtMiddleName txtcsname" id="txtMiddleName" placeholder="Enter middle name" value="">
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="txtlastName" class="form-label">Last name<span style="color: Red;"> * </span></label>
                                                               <input type="text" class="form-control txtlastName txtcsname" id="txtlastName" placeholder="Enter last name" value="" required>
                                                               <div class="invalid-feedback">Please enter a last name</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="cmbgender" class="form-label">Gender <span style="color: Red;">* </span></label>
                                                               <select class="js-example-basic-single form-control cmbgender" name="gender" id="cmbgender" required>
                                                                  <option value=""></option>
                                                                  <option value="Male">Male</option>
                                                                  <option value="Female">Female</option>
                                                                  <option value="Other">Other</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select gender</div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                                  <div class="col-md-6">
                                                                  <label class="form-label" for="txtlastName">Date Of Birth<span style="color: Red;"> * </span></label>
                                                                     <input type="date" id="txtdd" min="0" max="31" placeholder="DD" maxlength="2" required class="form-control txtdob allownumericwithoutdecimal">
                                                                  </div>
                                                                  <div class="invalid-feedback">Please enter dob</div>
                                                            <div class="col-md-6">
                                                               <label class="form-label" for="txtAge">Age as on (<span id="lblagedate"></span>)</label>
                                                               <div style="display: none;">
                                                                  <span class="agenumber"></span>
                                                               </div>
                                                               <input type="text" class="form-control txtAge" id="txtAge" placeholder="Age" value="" readonly required>
                                                               <span style="font-size: 11px; padding: 0px; color: blue;" class="age-content"></span>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="txtHeight" class="form-label">Height (in cms)<span style="color: Red;">*</span></label>
                                                               <input type="text" id="txtHeight" placeholder="in cms" maxlength="6"  required class="form-control allownumericwithdecimal txtHeight">
                                                               <div class="invalid-feedback">Please enter Height</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="cmbCaste" class="form-label">Weight (in kgs)<span style="color: Red;">*</span></label>
                                                               <input type="text" id="txtWeight" placeholder="in kgs" maxlength="6"  required class="form-control allownumericwithdecimal txtWeight">
                                                               <div class="invalid-feedback">Please enter Height</div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-6">
                                                               <label for="cmbCaste" class="form-label">Caste (Attested copy of Caste Certificate to be submitted)<span style="color: Red;"> * </span></label>
                                                               <select class="js-example-basic-single form-control cmbCaste" name="caste" id="cmbCaste" required>
                                                                  <option value=""></option>
                                                                  <option value="General">General</option>
                                                                  <option value="Scheduled Caste (SC)">Scheduled Caste (SC)</option>
                                                                  <option value="Scheduled Tribe (ST)">Scheduled Tribe (ST)</option>
                                                                  <option value="Other Backward Class (OBC)">Other Backward Class (OBC)</option>
                                                                  <option value="Others">Others</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select Caste</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label class="form-label">Nationality<span style="color: Red;"> * </span></label>
                                                               <div data-input-flag data-option-flag-name>
                                                                  <input type="text" class="form-control rounded-end flag-input cmbnationality" id="cmbnationality" placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" required />
                                                                  <div class="dropdown-menu w-100">
                                                                     <div class="p-2 px-3 pt-1 searchlist-input">
                                                                        <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name" />
                                                                     </div>
                                                                     <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                                                                  </div>
                                                                  <div class="invalid-feedback">Please select nationality</div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-6">
                                                               <label for="cmbCommunity" class="form-label">Religion (If Roman Catholic, kindly submit the Baptism Certificate)<span style="color: Red;"> * </span></label>
                                                               <select class="js-example-basic-single form-control cmbCommunity" name="caste" id="cmbCommunity" required>
                                                                  <option value=""></option>
                                                                  <option value="Hindu">Hindu</option>
                                                                  <option value="Muslim">Muslim</option>
                                                                  <option value="Christian">Christian</option>
                                                                  <option value="Buddhist">Buddhist</option>
                                                                  <option value="Sikh">Sikh</option>
                                                                  <option value="Jain">Jain</option>
                                                                  <option value="Others">Others</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select Community</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label class="form-label">Mother Tongue<span style="color: Red;"> * </span></label>
                                                               <select class="js-example-basic-single form-control cmbMotherToungue" name="MotherToungue" id="cmbMotherToungue" required></select>
                                                               <div class="invalid-feedback">Please select Mother Toungue</div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-6">
                                                               <label for="cmbBloodgroup" class="form-label">Blood Group<span style="color: Red;"> * </span></label>
                                                               <select class="js-example-basic-single form-control cmbBloodgroup" name="Bloodgroup" id="cmbBloodgroup" required>
                                                                  <option value=""></option>
                                                                  <option value="A+">A+</option>
                                                                  <option value="A-">A-</option>
                                                                  <option value="B+">B+</option>
                                                                  <option value="B-">B-</option>
                                                                  <option value="O+">O+</option>
                                                                  <option value="O-">O-</option>
                                                                  <option value="AB+">AB+</option>
                                                                  <option value="AB-">AB-</option>
                                                                  <option value="Unknown / Not Yet Tested">Unknown / Not Yet Tested</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please Select Blood Group</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label class="form-label" for="txtIdentification">Identification Mark<span style="color: Red;">  </span></label>
                                                               <input type="text" class="form-control txtIdentification" id="txtIdentification" placeholder="Identification Mark" value="">
                                                               <div class="invalid-feedback">Please enter Identification Mark</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label class="form-label" for="txtAadhar">Aadhar No.</label>
                                                               <input type="text" class="form-control txtAadhar allownumericwithoutdecimal" id="txtAadhar" placeholder="Aadhar No" value="">
                                                            </div>
                                                            <div class="col-md-6">
                                                               <label for="cmbDietary" class="form-label">Dietary Preferences<span style="color: Red;"> * </span></label>
                                                               <select class="js-example-basic-single form-control cmbDietary" name="Dietary" id="cmbDietary" required>
                                                                  <option value=""></option>
                                                                  <option value="Veg">Vegetarian</option>
                                                                  <option value="Non-Veg">Non Vegetarian</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please Select Dietary Preferences</div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-4 mt-4">
                                                               <label class="form-label w-75" style="font-weight: bold;">Whether the candidate is:</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                               <label for="cmbSingleGirlChild" class="form-label">Single Girl Child  <span style="color: Red;">*</span></label>
                                                               <select class="js-example-basic-single form-control cmbSingleGirlChild" name="SingleGirlChild" id="cmbSingleGirlChild" required>
                                                                  <option value=""></option>
                                                                  <option value="Yes">Yes</option>
                                                                  <option value="No">No</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select Single Girl Child(Yes/No)  </div>
                                                            </div>
                                                            <div class="col-md-4" style="display: none;">
                                                               <label for="cmbDivyanngjan" class="form-label">Specially abled (Divyanngjan)  <span style="color: Red;">*</span></label>
                                                               <select class="js-example-basic-single form-control cmbDivyanngjan" name="Divyanngjan" id="cmbDivyanngjan">
                                                                  <option value=""></option>
                                                                  <option value="Yes">Yes</option>
                                                                  <option value="No">No</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select Divyanngjan(Yes/No)</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                               <label for="cmbEWS" class="form-label">Belonging to the EWS  <span style="color: Red;">*</span></label>
                                                               <select class="js-example-basic-single form-control cmbEWS" name="EWS" id="cmbEWS" required>
                                                                  <option value=""></option>
                                                                  <option value="Yes">Yes</option>
                                                                  <option value="No">No</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select EWS (Yes/No)</div>
                                                            </div>
                                                            <div class="col-md-3" style="display: none;">
                                                               <label for="cmbEWSCategory" class="form-label">Category  <span style="color: Red;"></span></label>
                                                               <select class="js-example-basic-single form-control cmbEWSCategory" name="EWS" id="cmbEWSCategory">
                                                                  <option value=""></option>
                                                                  <option value="General">General</option>
                                                                  <option value="SC">SC</option>
                                                                  <option value="ST">ST</option>
                                                                  <option value="OBC">OBC</option>
                                                                  <option value="EWS">EWS</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select Category</div>
                                                            </div>
                                                            <div class="col-md-12 mt-4" style="display: none;">
                                                               <div class="alert alert-primary alert-solid" role="alert" style="font-size: 11px; padding: 10px 10px 10px 10px;">
                                                                  If Economically Weaker Section(EWS), Attach proof wherever applicable: Ration Card, Income Certificate, BPL Card during admission time.  
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-12 mt-3">
                                                               <label class="form-label" for="txtIdentification">Has the student participated in any State-level or National-level competitions, including Olympiads? <span style="color: Red;">* </span></label>
                                                               <div class="form-check form-check-inline form-radio-danger mb-3">
                                                                  <input class="form-check-input Rb_compet" type="radio" name="Rb_compet" checked id="Rb_compet_no" value="No" />
                                                                  <label class="form-check-label" for="Rb_compet_no">No</label>
                                                               </div>
                                                               <div class="form-check form-check-inline form-radio-success mb-3">
                                                                  <input class="form-check-input Rb_compet" type="radio" name="Rb_compet" id="Rb_compet_Yes" value="Yes" />
                                                                  <label class="form-check-label" for="Rb_Disease_Yes">Yes</label>
                                                               </div>
                                                               <input type="text" class="form-control txtcompet" id="txtcompet" style="pointer-events: none;" placeholder="Please Specify" value="No" required>
                                                               <div class="invalid-feedback">Please enter Participation</div>
                                                            </div>
                                                         </div>
                                                         <div class="row">
                                                            <div class="col-sm-4">
                                                               <label for="cmbLastSchool" class="form-label">Last School Attended <span id="lblR1" style="color: Red;">*</span></label>
                                                               <input type="text" class="js-example-basic-single1 form-control cmbLastSchool" name="LastSchool" id="cmbLastSchool" placeholder="Last School Attended" required />
                                                               <div class="mt-3" id="divlastSchool" style="display: none;">
                                                                  <input type="text" class="form-control txtLastSchool" id="txtLastSchool" placeholder="Last School Attended" value="">
                                                                  <div class="invalid-feedback">Please Enter Last School Attended</div>
                                                               </div>
                                                               <div class="invalid-feedback">Please enter Last School Attended</div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                               <label for="txtCity" class="form-label">Address of Last School Attended<span id="lblR2" style="color: Red;">*</span></label>
                                                               <input type="text" class="form-control txtLastSchoolAddress" id="txtLastSchoolAddress" placeholder="Enter Place & Address" value="" required>
                                                               <div class="invalid-feedback">Please Enter Last School Address</div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                               <label for="cmbBoardName" class="form-label">Board of Last School Attended<span  id="lblR3"  style="color: Red;">*</span></label>
                                                               <select class="js-example-basic-single form-control cmbBoardName" required name="BoardName" id="cmbBoardName">
                                                                  <option value=""></option>
                                                                  <!-- Northeast Boards -->
                                                                  <option value="CBSE">Central Board of Secondary Education (CBSE)</option>
                                                                  <option value="ICSE">Indian Certificate of Secondary Education (ICSE)</option>
                                                                  <option value="SEBA">Board of Secondary Education, Assam (SEBA)</option>
                                                                  <option value="MBOSE">Meghalaya Board of School Education (MBOSE)</option>
                                                                  <option value="NBSE">Board of School Education, Nagaland (NBSE)</option>
                                                                  <option value="BSEM">Board of Secondary Education, Manipur (BSEM)</option>
                                                                  <option value="MBSE">Mizoram Board of School Education (MBSE)</option>
                                                                  <option value="TBSE">Tripura Board of Secondary Education (TBSE)</option>
                                                                  <!-- National Boards -->
                                                                  <option value="NIOS">National Institute of Open Schooling (NIOS)</option>
                                                                  <option value="IGCSE">International General Certificate of Secondary Education (IGCSE)</option>
                                                                  <option value="IB">International Baccalaureate (IB)</option>
                                                                  <!-- North India -->
                                                                  <option value="UPBSE">Uttar Pradesh Board of Secondary Education (UPBSE)</option>
                                                                  <option value="PSEB">Punjab School Education Board (PSEB)</option>
                                                                  <option value="HBSE">Haryana Board of School Education (HBSE)</option>
                                                                  <option value="RBSE">Rajasthan Board of Secondary Education (RBSE)</option>
                                                                  <option value="HPBOSE">Himachal Pradesh Board of School Education (HPBOSE)</option>
                                                                  <option value="UBSE">Uttarakhand Board of School Education (UBSE)</option>
                                                                  <option value="JKBOSE">Jammu & Kashmir Board of School Education (JKBOSE)</option>
                                                                  <!-- East India -->
                                                                  <option value="BSEB">Bihar School Examination Board (BSEB)</option>
                                                                  <option value="BBOSE">Bihar Board of Open Schooling and Examination (BBOSE)</option>
                                                                  <option value="WBBSE">West Bengal Board of Secondary Education (WBBSE)</option>
                                                                  <option value="WBCHSE">West Bengal Council of Higher Secondary Education (WBCHSE)</option>
                                                                  <option value="BSEOdisha">Board of Secondary Education, Odisha (BSE Odisha)</option>
                                                                  <!-- West India -->
                                                                  <option value="MSBSHSE">Maharashtra State Board of Secondary and Higher Secondary Education (MSBSHSE)</option>
                                                                  <option value="GBSHSE">Goa Board of Secondary and Higher Secondary Education (GBSHSE)</option>
                                                                  <option value="GSEB">Gujarat Secondary and Higher Secondary Education Board (GSEB)</option>
                                                                  <!-- South India -->
                                                                  <option value="KSEEB">Karnataka Secondary Education Examination Board (KSEEB)</option>
                                                                  <option value="KBPE">Kerala Board of Public Examinations (KBPE)</option>
                                                                  <option value="TNBSE">Tamil Nadu State Board (TNBSE)</option>
                                                                  <option value="BSETelangana">Telangana Board of Secondary Education (BSE Telangana)</option>
                                                                  <option value="BSEAP">Board of Secondary Education, Andhra Pradesh (BSEAP)</option>
                                                                  <option value="BIEAP">Board of Intermediate Education, Andhra Pradesh (BIEAP)</option>
                                                                  <!-- Central India -->
                                                                  <option value="MPBSE">Madhya Pradesh Board of Secondary Education (MPBSE)</option>
                                                                  <option value="CGBSE">Chhattisgarh Board of Secondary Education (CGBSE)</option>
                                                                  <option value="CGSOS">Chhattisgarh State Open School (CGSOS)</option>
                                                                  <!-- Specialized Boards -->
                                                                  <option value="Madrassa">State Madrassa Boards</option>
                                                                  <option value="SanskritBoard">Sanskrit Education Boards</option>
                                                                  <!-- University-Affiliated Boards -->
                                                                  <option value="AMUBoard">Aligarh Muslim University Board</option>
                                                                  <option value="BHUBoard">Banaras Hindu University School Board</option>
                                                                  <option value="BanasthaliBoard">Banasthali Vidyapith School Board</option>
                                                                  <option value="Other">Other</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please Enter Last School Address</div>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-12">
                                                               <label class="form-label w-75" style="font-weight: bold;">
                                                                  Do you have own siblings studing at 
                                                                  <span id="ContentPlaceHolder1_lblSchool1">ST. FRANCIS DE SALES SCHOOL, GUWAHATI</span>
                                                                  <div class="form-check form-check-inline form-radio-danger mb-3">
                                                                     <input class="form-check-input Rb_Sib" type="radio" name="Rb_Sib" checked id="Rb_Sib_no" value="No" />
                                                               <label class="form-check-label" for="Rb_Sib_no">No</label>
                                                               </div>
                                                               <div class="form-check form-check-inline form-radio-success mb-3">
                                                                  <input class="form-check-input Rb_Sib" type="radio" name="Rb_Sib" id="Rb_Sib_Yes" value="Yes" />
                                                                  <label class="form-check-label" for="Rb_Sib_Yes">Yes</label>
                                                               </div>
                                                               </label>
                                                            </div>
                                                            <div class="col-md-12 div-sib" style="display: none;">
                                                               <p class="text-muted">
                                                                  Detail(s) of OWN brother(s) / Sister(s)     
                                                               </p>
                                                               <div class="row mt-3">
                                                                  <div class="col-md-12">
                                                                     <table class="table table-bordered table-sm table-striped table-hover" id="tblAdditionalInfor">
                                                                        <thead>
                                                                           <tr>
                                                                              <th style="width: 20px;">SL</th>
                                                                              <th style="width: 60px;">Reg No</th>
                                                                              <th style="width: 120px;">Name</th>
                                                                              <th style="width: 40px;">Gender</th>
                                                                              <th style="width: 40px;">Class</th>
                                                                              <th style="width: 60px;">Sec/Stream</th>
                                                                           </tr>
                                                                        </thead>
                                                                        <tbody id="tbody-adinfo">
                                                                           <tr class='a-tr' id='a_1'>
                                                                              <td style='text-align: right; width: 20px;'>1</td>
                                                                              <td style='text-align: right; width: 60px;'>
                                                                                 <input type="text" id='rega_1' placeholder="Reg No" class='a-regno form-control data-sibl allownumericwithoutdecimal' />
                                                                              </td>
                                                                              <td style='text-align: left; width: 120px;'>
                                                                                 <input type="text" id='namea_1' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='gendera_1' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='clsa_1' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 60px;'>
                                                                                 <input type="text" class="form-control readonly data-sibl" id='seca_1'>
                                                                              </td>
                                                                           </tr>
                                                                           <tr class='a-tr' id='a_2'>
                                                                              <td style='text-align: right; width: 20px;'>2</td>
                                                                              <td style='text-align: right; width: 60px;'>
                                                                                 <input type="text" id='rega_2' placeholder="Reg No" class='a-regno form-control data-sibl allownumericwithoutdecimal' />
                                                                              </td>
                                                                              <td style='text-align: left; width: 120px;'>
                                                                                 <input type="text" id='namea_2' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='gendera_2' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='clsa_2' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 60px;'>
                                                                                 <input type="text" class="form-control readonly data-sibl" id='seca_2'>
                                                                              </td>
                                                                           </tr>
                                                                           <tr class='a-tr' id='a_3'>
                                                                              <td style='text-align: right; width: 20px;'>3</td>
                                                                              <td style='text-align: right; width: 60px;'>
                                                                                 <input type="text" id='rega_3' placeholder="Reg No" class='a-regno form-control data-sibl allownumericwithoutdecimal' />
                                                                              </td>
                                                                              <td style='text-align: left; width: 120px;'>
                                                                                 <input type="text" id='namea_3' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='gendera_3' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='clsa_3' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 60px;'>
                                                                                 <input type="text" class="form-control readonly data-sibl" id='seca_3'>
                                                                              </td>
                                                                           </tr>
                                                                           <tr class='a-tr' id='a_4'>
                                                                              <td style='text-align: right; width: 20px;'>4</td>
                                                                              <td style='text-align: right; width: 60px;'>
                                                                                 <input type="text" id='rega_4' placeholder="Reg No" class='a-regno form-control data-sibl allownumericwithoutdecimal' />
                                                                              </td>
                                                                              <td style='text-align: left; width: 120px;'>
                                                                                 <input type="text" id='namea_4' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='gendera_4' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='clsa_4' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 60px;'>
                                                                                 <input type="text" class="form-control readonly data-sibl" id='seca_4'>
                                                                              </td>
                                                                           </tr>
                                                                           <tr class='a-tr' id='a_5'>
                                                                              <td style='text-align: right; width: 20px;'>5</td>
                                                                              <td style='text-align: right; width: 60px;'>
                                                                                 <input type="text" id='rega_5' placeholder="Reg No" class='a-regno form-control data-sibl allownumericwithoutdecimal' />
                                                                              </td>
                                                                              <td style='text-align: left; width: 120px;'>
                                                                                 <input type="text" id='namea_5' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='gendera_5' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 40px;'>
                                                                                 <input type="text" id='clsa_5' readonly class="form-control data-sibl">
                                                                              </td>
                                                                              <td style='text-align: left; width: 60px;'>
                                                                                 <input type="text" class="form-control readonly data-sibl" id='seca_5'>
                                                                              </td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                               <label class="form-label w-75" style="font-weight: bold;">
                                                                  Ward of SFS Guwahati Employee? 
                                                                  <span id="ContentPlaceHolder1_Label2"></span>
                                                                  <div class="form-check form-check-inline form-radio-danger mb-3">
                                                                     <input class="form-check-input Rb_Ward" type="radio" name="Rb_Ward" checked id="Rb_Ward_no" value="No" />
                                                               <label class="form-check-label" for="Rb_Ward_no">No</label>
                                                               </div>
                                                               <div class="form-check form-check-inline form-radio-success mb-3">
                                                                  <input class="form-check-input Rb_Ward" type="radio" name="Rb_Ward" id="Rb_Ward_Yes" value="Yes" />
                                                                  <label class="form-check-label" for="Rb_Ward_Yes">Yes</label>
                                                               </div>
                                                               </label>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-12">
                                                               <div>
                                                                  <p class="text-muted">Communicationl information</p>
                                                               </div>
                                                               <div class="row">
                                                                  <div class="col-sm-8">
                                                                     <label for="txtResidential" class="form-label">Residential Address  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control" id="txtResidential" placeholder="Residential Address" value="" required>
                                                                     <div class="invalid-feedback">Please enter Residential Address</div>
                                                                  </div>
                                                                  <div class="col-sm-4">
                                                                     <label for="txtCity" class="form-label">City  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control" id="txtCity" placeholder="Enter City" value="" required>
                                                                     <div class="invalid-feedback">Please enter City</div>
                                                                  </div>
                                                               </div>
                                                               <div class="row mt-3">
                                                                  <div class="col-md-6">
                                                                     <label class="form-label">State  <span style="color: Red;">*</span></label>
                                                                     <select class="js-example-basic-single form-control" name="State" id="cmbState" required></select>
                                                                     <div class="invalid-feedback">Please select State</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="txtPin" class="form-label">Postal Pin Code  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control allownumericwithoutdecimal" maxlength="6" id="txtPin" placeholder="Pin Code" value="" required>
                                                                     <div class="invalid-feedback">Please enter pin</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="txtemail1" class="form-label">Primary E-mail Address  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control" id="txtemail1" placeholder="Primary E-mail Address" value="" required>
                                                                     <div class="invalid-feedback">Please enter Primary E-mail Address</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="txtemail2" class="form-label">Secondary E-mail Address</label>
                                                                     <input type="text" class="form-control" id="txtemail2" placeholder="Secondary E-mail Address" value="">
                                                                  </div>
                                                               </div>
                                                               <div class="row mt-3">
                                                                  <div class="col-md-6">
                                                                     <label for="txtPrimaryPhone" class="form-label">Primary Phone No  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control allownumericwithoutdecimal" id="txtPrimaryPhone" maxlength="10" placeholder="For school contact" value="" required>
                                                                     <div class="invalid-feedback">Please enter Primary Phone No</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="txtSecondaryPhone" class="form-label">Secondary Phone No  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control allownumericwithoutdecimal" maxlength="10" id="txtSecondaryPhone" placeholder="For Emergency purpose only" value="" required>
                                                                     <div class="invalid-feedback">Please enter Secondary Phone No</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="txtWhatsappNo" class="form-label">Whatsapp No  <span style="color: Red;">*</span></label>
                                                                     <input type="text" class="form-control allownumericwithoutdecimal" maxlength="10" id="txtWhatsappNo" placeholder="WhatsappNo" value="" required>
                                                                     <div class="invalid-feedback">Please enter Whatsapp No</div>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                     <label for="cmbDistance" class="form-label">Distance From School  <span style="color: Red;">*</span></label>
                                                                     <select class="js-example-basic-single form-control" name="State" id="cmbDistance" required>
                                                                        <option value=""></option>
                                                                        <option value="0-5 kms">0-5 kms</option>
                                                                        <option value="6-10 kms">6-10 kms</option>
                                                                        <option value="10-15 kms">10-15 kms</option>
                                                                        <option value="Above 10 kms">Above 15 kms</option>
                                                                     </select>
                                                                     <div class="invalid-feedback">Please select Distance</div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4 div-btn-persn">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-class-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnPersonalSave" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-pers " data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-health-info" role="tabpanel" aria-labelledby="v-pills-health-info-tab">
                                                <div>
                                                   <h5>Health Record</h5>
                                                   <p class="text-muted">Fill information below</p>
                                                </div>
                                                <div class="row g-3">
                                                   <div class="col-md-12 mt-3">
                                                      <label class="form-label" for="txtIdentification">Disease or any serious illness for which candidate is on regular medication<span style="color: Red;"> * </span></label>
                                                      <div class="form-check form-check-inline form-radio-danger mb-3">
                                                         <input class="form-check-input Rb_Disease" type="radio" name="Rb_Disease" checked id="Rb_Disease_no" value="No" />
                                                         <label class="form-check-label" for="Rb_Disease_no">No</label>
                                                      </div>
                                                      <div class="form-check form-check-inline form-radio-success mb-3">
                                                         <input class="form-check-input Rb_Disease" type="radio" name="Rb_Disease" id="Rb_Disease_Yes" value="Yes" />
                                                         <label class="form-check-label" for="Rb_Disease_Yes">Yes</label>
                                                      </div>
                                                      <input type="text" class="form-control txtDisease" id="txtDisease" style="pointer-events: none;" placeholder="Disease or any serious illness" value="No" required>
                                                      <div class="invalid-feedback">Please enter Disease or any serious illness</div>
                                                   </div>
                                                   <div class="col-md-12 mt-3">
                                                      <table class="table table-bordered table-sm table-hover">
                                                         <thead class="bg-danger text-light border-light">
                                                            <tr>
                                                               <th style="width: 5%;" class="p-2">SL</th>
                                                               <th style="width: 25%;" class="p-2">Symptoms</th>
                                                               <th style="width: 12%;">Yes</th>
                                                               <th style="width: 12%;">No</th>
                                                               <th>Details</th>
                                                            </tr>
                                                         </thead>
                                                         <tbody id="tblHealth">
                                                         </tbody>
                                                      </table>
                                                   </div>
                                                   <div class="col-md-10 mt-3">
                                                      <label class="form-label">
                                                      Do you authorize the school to approve the administration of any medication as advised by the medical officer in an emergency when you cannot be contacted immediately ? &nbsp;&nbsp;
                                                      <span style="color: Red;">*</span>
                                                      </label>
                                                   </div>
                                                   <div class="col-md-2 mt-3" style="padding: 0px 0px 0px 10px;">
                                                      <div class="form-check form-check-inline form-radio-danger mb-3">
                                                         <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Medicine" checked id="Rb_Medicine_no" value="No" />
                                                         <label class="form-check-label" for="Rb_Medicine_no">No</label>
                                                      </div>
                                                      <div class="form-check form-check-inline form-radio-success mb-3">
                                                         <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Medicine" id="Rb_Medicine_Yes" value="Yes" />
                                                         <label class="form-check-label" for="Rb_Medicine_Yes">Yes</label>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="d-flex align-items-start gap-3 mt-4">
                                                   <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-personal-info-tab">
                                                   <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                   <button type="button" class="btn btn-danger btn-label right ms-auto nexttab nexttab " id="btnHealth" disabled data-nexttab="">
                                                   <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-communication" role="tabpanel" aria-labelledby="v-pills-bill-address-tab">
                                                <div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-personal-info-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-communication" id="btnCommunication" disabled data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-parent" role="tabpanel" aria-labelledby="v-pills-parent-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>Family Details</h5>
                                                            <p class="text-muted">Fill all information below</p>
                                                         </div>
                                                         <div class="row">
                                                            <div class="col-xxl-6 col-lg-6">
                                                               <div class="card   border">
                                                                  <div class="card-header">
                                                                     <h4 class="card-title mb-0">Father's/Guardian's Info</h4>
                                                                  </div>
                                                                  <div class="card-body">
                                                                     <div class="row">
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherName" class="form-label ">Father's/Guardian's Name<span style="color: Red;"> * </span></label>
                                                                           <input type="text" class="form-control txtcfname" id="txtFatherName" placeholder="Father's/Guardian's Name" value="" required>
                                                                           <div class="invalid-feedback">Please enter Father's/Guardian's Name</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="cmbfatherQualification" class="form-label">Highest Educational Qualification <span style="color: Red;">* </span></label>
                                                                           <select class="js-example-basic-single form-control" name="fatherQualification" id="cmbfatherQualification" required>
                                                                              <option value=""></option>
                                                                              <option value="Under Matriculation">Under Matriculation</option>
                                                                              <option value="Matriculation">Matriculation</option>
                                                                              <option value="Higher Secondary">Higher Secondary</option>
                                                                              <option value="Graduate">Graduate</option>
                                                                              <option value="Postgraduate/Masters">Postgraduate/Masters</option>
                                                                              <option value="PhD and Above">PhD and Above</option>
                                                                           </select>
                                                                           <div class="invalid-feedback">Please select Educational Qualification</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherPhone" class="form-label">Phone No<span style="color: Red;"> * </span></label>
                                                                           <input type="text" class="form-control allownumericwithoutdecimal" id="txtFatherPhone" maxlength="10" placeholder="Father's/Guardian's Phone" value="" required>
                                                                           <div class="invalid-feedback">Please enter Phone</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherEmail" class="form-label">Email<span style="color: Red;">   </span></label>
                                                                           <input type="text" class="form-control  " id="txtFatherEmail" placeholder="Father's/Guardian's Email" value="">
                                                                           <div class="invalid-feedback">Please enter Email</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherEmployement" class="form-label">Employment<span style="color: Red;"> * </span></label>
                                                                           <select class="js-example-basic-single form-control txtFatherEmployement" name="txtFatherEmployement" id="txtFatherEmployement" required>
                                                                              <option value=""></option>
                                                                              <option value="Self Employed">Self Employed</option>
                                                                              <option value="Government Service">Government Service</option>
                                                                              <option value="Private Service">Private Service</option>
                                                                              <option value="Retired">Retired</option>
                                                                              <option value="Other">Other</option>
                                                                           </select>
                                                                           <div class="mt-3" id="divFatherEmployement" style="display: none;">
                                                                              <input type="text" class="form-control txtFatherEmployement_other" id="txtFatherEmployement_other" placeholder="Father's Employment " value="">
                                                                           </div>
                                                                           <div class="invalid-feedback">Please select Employment</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherProfession" class="form-label">Profession<span style="color: Red;"> * </span></label>
                                                                           <select class="js-example-basic-single form-control txtFatherProfession" name="txtFatherProfession" id="txtFatherProfession" required>
                                                                              <option value=""></option>
                                                                              <option value="Architect">Architect</option>
                                                                              <option value="Banker">Banker</option>
                                                                              <option value="Chartered Accountant">Chartered Accountant</option>
                                                                              <option value="Civil Services">Civil Services</option>
                                                                              <option value="Defence Personnel">Defence Personnel</option>
                                                                              <option value="Doctor">Doctor</option>
                                                                              <option value="Engineer">Engineer</option>
                                                                              <option value="Entrepreneur">Entrepreneur</option>
                                                                              <option value="IT Professional">IT Professional</option>
                                                                              <option value="Journalist">Journalist</option>
                                                                              <option value="Lawyer">Lawyer</option>
                                                                              <option value="Nurse">Nurse</option>
                                                                              <option value="Sales Professional">Sales Professional</option>
                                                                              <option value="Social Worker">Social Worker</option>
                                                                              <option value="Teacher">Teacher</option>
                                                                              <option value="Other">Other</option>
                                                                           </select>
                                                                           <div class="mt-3" id="divFatherProfession" style="display: none;">
                                                                              <input type="text" class="form-control txtFatherProfession_other" id="txtFatherProfession_other" placeholder="Father Profession" value="">
                                                                           </div>
                                                                           <div class="invalid-feedback">Please select Profession</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2" style="display: none;">
                                                                           <label for="txtFatherDesignation" class="form-label">Designation & Details<span style="color: Red;">  * </span></label>
                                                                           <input type="text" class="form-control  txtFatherDesignation" id="txtFatherDesignation" placeholder="Father's/Guardian's Designation & Details" value="">
                                                                           <div class="invalid-feedback">Please select Designation</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherOfficeAddress" class="form-label">Office/ Bussiness Address<span style="color: Red;">   </span></label>
                                                                           <input type="text" class="form-control  " id="txtFatherOfficeAddress" placeholder="Father's Office Address" value="">
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtFatherIncome" class="form-label">Annual Income (in lakh(s))<span style="color: Red;"> * </span></label>
                                                                           <select class="js-example-basic-single form-control txtFatherIncome cls-income" name="txtFatherIncome" id="txtFatherIncome" required>
                                                                              <option value=""></option>
                                                                              <option value="LESS THAN 1 LAKH">LESS THAN 1 LAKH</option>
                                                                              <option value="1–2.5 LAKHS">1–2.5 LAKHS</option>
                                                                              <option value="2.5–5 LAKHS">2.5–5 LAKHS</option>
                                                                              <option value="5–8 LAKHS">5–8 LAKHS</option>
                                                                              <option value="8 LAKHS AND ABOVE">8 LAKHS AND ABOVE</option>
                                                                           </select>
                                                                           <div class="invalid-feedback">Please enter</div>
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="col-xxl-6 col-lg-6">
                                                               <div class="card border">
                                                                  <div class="card-header">
                                                                     <h6 class="card-title mb-0">Mother's/Guardian's Info</h6>
                                                                  </div>
                                                                  <div class="card-body">
                                                                     <div class="row">
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherName" class="form-label">Mother's/Guardian's Name<span style="color: Red;"> * </span></label>
                                                                           <input type="text" class="form-control txtcfname" id="txtMotherName" placeholder="Mother's/Guardian's Name" value="" required>
                                                                           <div class="invalid-feedback">Please enter Mother's/Guardian's Name</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="cmbMotherQualification" class="form-label">Highest Educational Qualification <span style="color: Red;">* </span></label>
                                                                           <select class="js-example-basic-single form-control" name="MotherQualification" id="cmbMotherQualification" required>
                                                                              <option value=""></option>
                                                                              <option value="Under Matriculation">Under Matriculation</option>
                                                                              <option value="Matriculation">Matriculation</option>
                                                                              <option value="Higher Secondary">Higher Secondary</option>
                                                                              <option value="Graduate">Graduate</option>
                                                                              <option value="Postgraduate/Masters">Postgraduate/Masters</option>
                                                                              <option value="PhD and Above">PhD and Above</option>
                                                                           </select>
                                                                           <div class="invalid-feedback">Please select Educational Qualification</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherPhone" class="form-label">Phone No<span style="color: Red;"> * </span></label>
                                                                           <input type="text" class="form-control allownumericwithoutdecimal" id="txtMotherPhone" maxlength="10" placeholder="Mother's/Guardian's Phone" value="" required>
                                                                           <div class="invalid-feedback">Please enter Mother's/Guardian's Phone</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherEmail" class="form-label">Email<span style="color: Red;">   </span></label>
                                                                           <input type="text" class="form-control  " id="txtMotherEmail" placeholder="Mother's/Guardian's Email" value="">
                                                                           <div class="invalid-feedback">Please enter Mother's/Guardian's Email</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherEmployement" class="form-label">Employment<span style="color: Red;"> * </span></label>
                                                                           <select class="js-example-basic-single form-control txtMotherEmployement" name="txtMotherEmployement" id="txtMotherEmployement" required>
                                                                              <option value=""></option>
                                                                              <option value="Self Employed">Self Employed</option>
                                                                              <option value="Government Service">Government Service</option>
                                                                              <option value="Private Service">Private Service</option>
                                                                              <option value="Retired">Retired</option>
                                                                              <option value="Home Maker">Home Maker</option>
                                                                              <option value="Other">Other</option>
                                                                           </select>
                                                                           <div class="mt-3" id="divMotherEmployement" style="display: none;">
                                                                              <input type="text" class="form-control txtMotherEmployement_other" id="txtMotherEmployement_other" placeholder="Mother's Employment " value="">
                                                                           </div>
                                                                           <div class="invalid-feedback">Please select Employment</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherProfession" class="form-label">Profession<span style="color: Red;"> * </span></label>
                                                                           <select class="js-example-basic-single form-control txtMotherProfession" name="txtMotherProfession" id="txtMotherProfession" required>
                                                                              <option value=""></option>
                                                                              <option value="Architect">Architect</option>
                                                                              <option value="Banker">Banker</option>
                                                                              <option value="Chartered Accountant">Chartered Accountant</option>
                                                                              <option value="Civil Services">Civil Services</option>
                                                                              <option value="Defence Personnel">Defence Personnel</option>
                                                                              <option value="Doctor">Doctor</option>
                                                                              <option value="Engineer">Engineer</option>
                                                                              <option value="Entrepreneur">Entrepreneur</option>
                                                                              <option value="IT Professional">IT Professional</option>
                                                                              <option value="Journalist">Journalist</option>
                                                                              <option value="Lawyer">Lawyer</option>
                                                                              <option value="Nurse">Nurse</option>
                                                                              <option value="Sales Professional">Sales Professional</option>
                                                                              <option value="Social Worker">Social Worker</option>
                                                                              <option value="Teacher">Teacher</option>
                                                                              <option value="House Maker">House Maker</option>
                                                                              <option value="Other">Other</option>
                                                                           </select>
                                                                           <div class="mt-3" id="divMotherProfession" style="display: none;">
                                                                              <input type="text" class="form-control txtMotherProfession_other" id="txtMotherProfession_other" placeholder="Mother's/Guardian's Profession" value="">
                                                                           </div>
                                                                           <div class="invalid-feedback">Please select Profession</div>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2" style="display: none;">
                                                                           <label for="txtMotherDesignation" class="form-label">Designation & Details<span style="color: Red;"></span></label>
                                                                           <input type="text" class="form-control  txtMotherDesignation" id="txtMotherDesignation" required placeholder="Mother's/Guardian's Designation & Details" value="">
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherOfficeAddress" class="form-label">Office/ Bussiness Address<span style="color: Red;">   </span></label>
                                                                           <input type="text" class="form-control  " id="txtMotherOfficeAddress" placeholder="Mother's/Guardian's Office Address" value="">
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                           <label for="txtMotherIncome" class="form-label">Annual Income (in lakh(s))<span style="color: Red;">   </span></label>
                                                                           <select class="js-example-basic-single form-control txtMotherIncome" name="txtMotherIncome" id="txtMotherIncome" required>
                                                                              <option value=""></option>
                                                                              <option value="LESS THAN 1 LAKH">LESS THAN 1 LAKH</option>
                                                                              <option value="1–2.5 LAKHS">1–2.5 LAKHS</option>
                                                                              <option value="2.5–5 LAKHS">2.5–5 LAKHS</option>
                                                                              <option value="5–8 LAKHS">5–8 LAKHS</option>
                                                                              <option value="8 LAKHS AND ABOVE">8 LAKHS AND ABOVE</option>
                                                                           </select>
                                                                           <div class="invalid-feedback">Please enter</div>
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <div class="row">
                                                            <div class="col-xxl-6 col-lg-12">
                                                               <div class="card   border">
                                                                  <div class="card-header p-2">
                                                                     <h4 class="card-title mb-0" style="text-align: center">Family Annual Income</h4>
                                                                  </div>
                                                                  <div class="card-body">
                                                                     <div class="row">
                                                                        <div class="col-md-4"></div>
                                                                        <div class="col-md-4 mb-2">
                                                                           <input type="text" class="form-control" style="text-align: right;" id="txtFamilyAnnualIncome" placeholder="Family Annual Income" value="" readonly>
                                                                           <div class="invalid-feedback">Please enter</div>
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-health-info-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-parentsdetails" id="btnParents" disabled data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-lastschool" role="tabpanel" aria-labelledby="v-pills-lastschool-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>Last School Information</h5>
                                                            <p class="text-muted">Fill all information below</p>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-parent-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnLastSchool" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-lastschooldetails"
                                                         data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Additional-Information" role="tabpanel" aria-labelledby="v-pills-Additional-Information-tab">
                                                <div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-lastschool-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnAdditionalInfo" class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-additionalinfo"
                                                         data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Co-Curricular" role="tabpanel" aria-labelledby="v-pills-Co-Curricular-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                            <div class="alert-body">
                                                               <div class="d-flex">
                                                                  <div class="flex-grow-1" style="display: flex;">
                                                                     <h5 class="alert-heading">CO-CURRICULAR ACTIVITIES <span style="font-size: 12px;">(Applicable for Classes III – IX & XI)</span>
                                                                     </h5>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="alert-content">
                                                               <ul>
                                                                  <li>The Co-Curricular Activities section has two parts</li>
                                                                  <p>1. Saturday Activities (Mandatory): These are co-curricular activities conducted every Saturday during school hours. Selection of one activity from this section is mandatory.</p>
                                                                  <p>
                                                                     2. After-School Sports Activities (Optional): These are sports activities held after school hours. Selection from this section is optional and based on student interest.
                                                                  </p>
                                                               </ul>
                                                            </div>
                                                         </div>
                                                      </div>
                                                      <div class="col-md-6" style="padding: 5px;">
                                                         <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                            <div class="alert-body">
                                                               <div class="d-flex">
                                                                  <div class="flex-grow-1" style="display: flex;">
                                                                     <h6 class="alert-heading">CLUB ACTIVITIES
                                                                     </h6>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="alert-content">
                                                               <div id="step-3-table">
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                      <div class="col-md-6" style="padding: 5px;">
                                                         <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                            <div class="alert-body">
                                                               <div class="d-flex">
                                                                  <div class="flex-grow-1" style="display: flex;">
                                                                     <h6 class="alert-heading">AFTER SCHOOL SPORTS ACTIVITIES
                                                                     </h6>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="alert-content">
                                                               <div id="step1-3-table">
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-parent-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnCouricular" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-cocurricular" data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                                      Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Transportation" role="tabpanel" aria-labelledby="v-pills-Transportation-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>TRANSPORTATION</h5>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-6">
                                                               <label for="cmbTransMode" class="form-label">Select Transportation <span style="color: Red;">* </span></label>
                                                               <select class="form-control cmbTransMode" id="cmbTransMode" required>
                                                               </select>
                                                            </div>
                                                            <div class="col-md-6 divBusRoute" style="display: none;">
                                                               <label for="cmbBusRoute" class="form-label">Bus Route <span style="color: Red;">* </span></label>
                                                               <select class="form-control cmbBusRoute" id="cmbBusRoute">
                                                               </select>
                                                            </div>
                                                            <div class="col-md-12 mt-3 mb-3" id="lblTrnRegF">
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-Co-Curricular-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnTransport" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-transport" data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Stream-Subject" role="tabpanel" aria-labelledby="v-pills-Stream-Subject-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>Stream & Subjects</h5>
                                                         </div>
                                                         <div class="row divStreamNa" style="display: none;">
                                                            <div class="col-md-12">
                                                               <h4 style="color: red">This option is not application for Class <span id="tblstrcls"></span>. Please click next to skip</h4>
                                                            </div>
                                                         </div>
                                                         <div class="row mt-3 divStream" style="display: none;">
                                                            <div class="col-xxl-4  col-md-6">
                                                               <div class="row">
                                                                  <div class="col-md-12 mt-4 streamlist1">
                                                                     <label for="cmbStreamSubjects1" class="form-label">1st Stream  <span style="color: Red;">* </span></label>
                                                                     <input type="text" id="cmbStreamSubjects1" class="form-control" readonly />
                                                                  </div>
                                                               </div>
                                                               <div class="row">
                                                                  <div class="col-md-12 mt-4" id="tbl1stSub">
                                                                     <table class="table table-bordered table-hover table-striped">
                                                                     </table>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="col-xxl-4  col-md-6">
                                                               <div class="row">
                                                                  <div class="col-md-12 mt-4 streamlist2">
                                                                     <label for="cmbStreamSubjects2" class="form-label">2nd Stream  <span style="color: Red;">* </span></label>
                                                                     <select class="form-control cmbStreamSubjects2" name="cmbStreamSubjects2" id="cmbStreamSubjects2" required>
                                                                     </select>
                                                                  </div>
                                                               </div>
                                                               <div class="row">
                                                                  <div class="col-md-12 mt-4" id="tbl2ndSub">
                                                                     <table class="table table-bordered table-hover table-striped">
                                                                     </table>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="col-xxl-12  col-md-12 mt-2" id="divCuet">
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="d-flex align-items-start gap-3 mt-4">
                                                   <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-Transportation-tab">
                                                   <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                   <button type="button" id="btnstreamsub" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-save-subjectstream"
                                                      data-nexttab="">
                                                   <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Documents" role="tabpanel" aria-labelledby="v-pills-Documents-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>UPLOAD DOCUMENTS</h5>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-12">
                                                               <div class="row" style="margin-top: 25px;">
                                                                  <div class="col-md-12">
                                                                     <p>
                                                                        <span style="color: red;">Note:-</span>File names must not contain any special characters. 
                                                                     </p>
                                                                  </div>
                                                                  <div class="col-md-4">
                                                                     <div class="col-md-12">
                                                                        <img id="Imgstd" style="width: 100px; height: 120px; border: solid 1px #7e1416;" />
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 15px;">
                                                                        <input type="file" name="file_photo" id="file_photo" required accept=".jpg, .jpeg" class="form-control file_photo" style="width: 100%; border: solid 1px lightgrey;">
                                                                        <span id="RegularExpressionValidator9" onservervalidate="if (!window.__cfRLUnblockHandlers) return false; ValidateFileSize" style="color: Red; display: none;" data-cf-modified-aabbd4361b787db4f22ec0aa-="">Please select a valid jpg Or png file without special characters.</span>
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 15px;">
                                                                        Photograph of the Candidate<span style="color: red">*</span>
                                                                        <br>
                                                                        (<span style="color: red">Max size 200 kb and jpg or jpeg format</span>)
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-4" style="margin-top: 0px;">
                                                                     <div class="col-md-12">
                                                                        <img id="ImageFather" style="width: 100px; height: 120px; border: solid 1px #7e1416;" />
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 15px;">
                                                                        <input type="file" name="File_father" required id="File_father" class="form-control" accept=".jpg, .jpeg" style="width: 100%; border: solid 1px lightgrey;">
                                                                        <span id="RegularExpressionValidator6" onservervalidate="if (!window.__cfRLUnblockHandlers) return false; ValidateFileSize" style="color: Red; display: none;" data-cf-modified-aabbd4361b787db4f22ec0aa-="">Please select a valid jpg Or Jpeg file without special characters.</span>
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 10px;">
                                                                        Photograph of the<span style="color: red">*</span>
                                                                        <span id="rb_fathergaurd" style="padding: 0px; font-size: 12px;">
                                                                        <input id="rb_fathergaurd_0" type="radio" name="rb_fathergaurd" value="Father" checked="checked"><label for="rb_fathergaurd_0">&nbsp;Father&nbsp;</label>
                                                                        <input id="rb_fathergaurd_1" type="radio" name="rb_fathergaurd" value="Gaurdian"><label for="rb_fathergaurd_1">&nbsp;Gaurdian</label>
                                                                        </span>
                                                                        <br>
                                                                        (<span style="color: red"> Max size 200 kb and jpg or jpeg format</span>)
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-4" style="margin-top: 0px;">
                                                                     <div class="col-md-12">
                                                                        <img id="ImageMother" style="width: 100px; height: 120px; border: solid 1px #7e1416;">
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 15px;">
                                                                        <input type="file" required name="File_mother" id="File_mother" accept=".jpg, .jpeg" class="form-control" style="width: 100%; border: solid 1px lightgrey;">
                                                                        <span id="RegularExpressionValidator5" onservervalidate="if (!window.__cfRLUnblockHandlers) return false; ValidateFileSize" style="color: Red; display: none;" data-cf-modified-aabbd4361b787db4f22ec0aa-="">Please select a valid jpg Or png file without special characters.</span>
                                                                     </div>
                                                                     <div class="col-md-12" style="margin-top: 10px;">
                                                                        Photograph of the<span style="color: red">*</span>
                                                                        <span id="rb_mothergaurdian" style="padding: 0px; font-size: 12px;">
                                                                        <input id="rb_mothergaurdian_0" type="radio" name="rb_mothergaurdian" value="Mother"
                                                                           checked="checked"><label for="rb_mothergaurdian_0">&nbsp;Mother&nbsp;</label>
                                                                        <input id="rb_mothergaurdian_1" type="radio" name="rb_mothergaurdian" value="Gaurdian"><label for="rb_mothergaurdian_1">&nbsp;Gaurdian</label>
                                                                        </span>
                                                                        <br>
                                                                        (<span style="color: red">Max size 200 kb and jpg or jpeg format</span>)
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-12">
                                                                     <div class="row">
                                                                        <div class="col-md-6" style="margin-top: 35px;">
                                                                           <div class="col-md-12" style="margin-top: 15px;">
                                                                              Birth Certificate  <span style="color: red">*</span> (<span style="color: red">Max size 200 kb and pdf format</span>)
                                                                           </div>
                                                                           <input type="file" name="File_Birth" id="File_Birth" required="" class="form-control" accept=".jpg, .jpeg, .pdf" style="width: 100%; border: solid 1px lightgrey;">
                                                                        </div>
                                                                        <div class="col-md-6" style="margin-top: 15px; display: none;">
                                                                           <div class="col-md-12" style="margin-top: 15px;">
                                                                              Bonafide/Provisional/Transfer Certificate (Previous Class Results (Term 1 / Half Yearly))<span style="color: red"></span>(<span style="color: red">  Max size 200 kb  and pdf format</span>)
                                                                           </div>
                                                                           <input type="file" name="File_TC" id="File_TC" class="form-control" accept=".jpg, .jpeg,.pdf" style="width: 100%; border: solid 1px lightgrey;">
                                                                        </div>
                                                                        <div class="col-md-6" style="margin-top: 35px;">
                                                                           <div class="col-md-12" style="margin-top: 15px;">
                                                                              Caste Certificate  (<span style="color: red">Max size 200 kb and pdf format</span>)
                                                                           </div>
                                                                           <input type="file" name="File_Caste" id="File_Caste" class="form-control" accept=".jpg, .jpeg,.pdf" style="width: 100%; border: solid 1px lightgrey;">
                                                                        </div>
                                                                     </div>
                                                                     <div class="row">
                                                                        <div class="col-md-6" style="margin-top: 15px;">
                                                                           <div class="col-md-12" style="margin-top: 15px;">
                                                                              Baptism Certificate <span style="color: red" class="lblbaptR"></span>(<span style="color: red">Max size 200 kb and pdf format</span>)
                                                                           </div>
                                                                           <input type="file" name="File_BAPTISM" id="File_BAPTISM" class="form-control" accept=".jpg, .jpeg,.pdf" style="width: 100%; border: solid 1px lightgrey;">
                                                                        </div>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-Stream-Subject-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnDocument" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab " data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-source-info" role="tabpanel" aria-labelledby="v-source-info-tab">
                                                <div class="row g-3">
                                                   <div class="col-md-12">
                                                      <div class="row">
                                                         <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                                            <div class="alert-body">
                                                               <div class="d-flex">
                                                                  <div class="flex-grow-1" style="display: flex;">
                                                                     <h5 class="alert-heading">Please mention how you came to know about our school
                                                                     </h5>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="alert-content">
                                                               <ul>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Word of Mouth (Friends/Relatives)" id="flexSwitchCheckChecked">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked">Word of Mouth (Friends/Relatives)</label>
                                                                     </div>
                                                                  </li>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Social Media" id="flexSwitchCheckChecked1">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked1">Social Media</label>
                                                                     </div>
                                                                  </li>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Google Search" id="flexSwitchCheckChecked2">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked2">Google Search</label>
                                                                     </div>
                                                                  </li>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Advertisement" id="flexSwitchCheckChecked3">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked3">Advertisement</label>
                                                                     </div>
                                                                  </li>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Existing Student" id="flexSwitchCheckChecked4">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked4">Existing Student</label>
                                                                     </div>
                                                                  </li>
                                                                  <li>
                                                                     <div class="form-check form-switch">
                                                                        <input class="form-check-input" name="Source_info" type="radio" role="switch" value="Other" id="flexSwitchCheckChecked5">
                                                                        <label class="form-check-label" for="flexSwitchCheckChecked5">Other</label>
                                                                     </div>
                                                                     <div class="alert-content other-content" style="display: none">
                                                                        <div class="col-sm-12">
                                                                           <label for="txtSource" class="form-label">Please Specify </label>
                                                                           <input type="text" class="form-control txtSource" required id="txtSource" placeholder="Source of Information" value="">
                                                                        </div>
                                                                     </div>
                                                                  </li>
                                                               </ul>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-Documents-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnSource" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab " data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-Social" role="tabpanel" aria-labelledby="v-pills-Social-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>PARENT'S CONSENT</h5>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-12">
                                                               <div class="row" style="margin-top: 25px;">
                                                                  <div class="col-md-12" style="text-align: justify">
                                                                     <p><b>Dear Parents/Guardians,</b></p>
                                                                     <p>The school occasionally captures photographs and videos of students during various academic and co-curricular activities. These may be used for school publications, our website, newsletters, or social media platforms to highlight student achievements and school events.</p>
                                                                     <p>
                                                                        To ensure we have your permission to use these images and videos, please read and click on the appropriate check box (✓) the consent form below:
                                                                     </p>
                                                                     <p>We, <b><span class="lblConsParent"></span></b>, the parents/guardians of  <b><span class="lblConsStd"></span></b>, give our consent to the school for using photographs and/or videos of our candidate in various school-related publications and media, including but not limited to:</p>
                                                                     <ul>
                                                                        <li>School website</li>
                                                                        <li>School social media platforms (e.g. Facebook, Instagram, Twitter)</li>
                                                                        <li>School newsletters </li>
                                                                        <li>Promotional materials (e.g. brochures, flyers)</li>
                                                                     </ul>
                                                                     <p>
                                                                        We understand that these images and videos may be used for promotional and educational purposes and may be shared publicly.<br>
                                                                        We also understand that these materials may be used without additional consent or notification to us.
                                                                     </p>
                                                                     <p>
                                                                        We acknowledge that we can withdraw our consent at the time of RE-ADMISSION at the beginning of new session. Upon receipt of such request, the school will cease the use of our candidate’s images and/or videos in any new materials but will not be responsible for removing materials that have already been published.
                                                                     </p>
                                                                  </div>
                                                                  <div class="col-md-12 mt-3">
                                                                     <div class="form-check form-check-inline form-radio-success mb-3">
                                                                        <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Social" id="Rb_Social_Yes" checked value="Yes" />
                                                                        <label class="form-check-label" for="Rb_Social_Yes">We agree to give our consent.</label>
                                                                     </div>
                                                                     <div class="form-check form-check-inline form-radio-danger mb-3">
                                                                        <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Social" id="Rb_Social_no" value="No" />
                                                                        <label class="form-check-label" for="Rb_Social_no">We disagree to give our consent.</label>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-source-info-tab">
                                                      <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnSocial" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab " data-nexttab="">
                                                      <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Save & Next</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="tab-pane fade admission-panel" id="v-pills-payment" role="tabpanel" aria-labelledby="v-pills-payment-tab">
                                                <div>
                                                   <div class="row g-3">
                                                      <div class="col-md-12">
                                                         <div>
                                                            <h5>Payment :  </h5>
                                                         </div>
                                                         <div class="row mt-3">
                                                            <div class="col-md-2"></div>
                                                            <div class="col-md-8">
                                                               <div class="alert bg-danger border-danger text-white material-shadow" style="text-align: center;" role="alert">
                                                                  <strong style="font-size: 20px;">₹ <i><span id="lblFormFee"></span></i>&nbsp;&nbsp; <span style="font-size: 12px;">(NON REFUNDABLE)</span></strong>
                                                               </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                               <h6>I / We, hereby, declare that the entries I made in the Application Form are complete, true to the best of my knowledge,
                                                                  and based on records.
                                                               </h6>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="d-flex align-items-start gap-3 mt-4">
                                                      <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-Social-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
                                                      <button type="button" id="btnPayment" disabled class="btn btn-danger btn-label right ms-auto nexttab nexttab btn-term" data-nexttab="v-pills-payment-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Term & Condition</button>
                                                   </div>
                                                </div>
                                             </div>
                                             <!-- end tab pane -->
                                          </div>
                                          <!-- end tab pane -->
                                       </div>
                                       <!-- end tab content -->
                                    </div>
                                 </div>
                                 <!-- end col -->
                                 <div class="" id="FilledData">
                                 </div>
                              </form>
                           </div>
                           <!-- end row -->
                        </div>
                     </div>
                     <!-- end -->
                  </div>
                  <!-- end col -->
                  <!--Model-->
                  <div class="modal fade" id="TermConditionModal" tabindex="-1" aria-labelledby="lbltermhead" aria-hidden="true">
                     <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                           <div class="modal-header bg-danger border-danger text-white text-center" style="text-align: center">
                              <h4 class="text-white fw-bold">DISCLAIMER (PLEASE READ CAREFULLY BEFORE PROCEEDING TO PAYMENT)</h4>
                              <h5 class="modal-title  text-white" style="display: none;" id="lbltermhead"></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <div class="modal-body">
                              <div class="row">
                                 <div class="col-md-12">
                                    <div id="lblterm">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <a href="javascript:void(0);" class="btn btn-light btn-label previestab" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>Close</a>
                              <button type="button" class="btn btn-danger btn-label right  ms-auto nexttab  btn-acceptpay">ACCEPT & PAY</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--End Model-->
                  <!--Model-->
                  <div class="modal fade" id="AgeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="lbltermhead" aria-hidden="true">
                     <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="lblagehead">Application Not Eligible Due to Age Limit</h5>
                           </div>
                           <div class="modal-body">
                              <div class="row">
                                 <div class="col-md-12">
                                    <div id="lblagemsg">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-accept-age">I accept age criteria</button>
                                 </div>
                              </div>
                           </div>
                           <div class="modal-footer">
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--End Model-->
                  <div class="modal fade" id="ModelExistingStudent" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" style="display: none;" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content" style="min-height: 300px;">
                           <div class="modal-body p-2">
                              <div class="mt-4">
                                 <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                    <div class="alert-body">
                                       <div class="d-flex">
                                          <div class="flex-grow-1" style="display: flex;">
                                             <h5 class="alert-heading">Are you Existing
                                                <span id="ContentPlaceHolder1_lblSchool2">ST. FRANCIS DE SALES SCHOOL, GUWAHATI</span>
                                                Student
                                             </h5>
                                             <div class="row" style="margin-right: 10px; position: absolute; right: 0">
                                                <div class="col-md-12">
                                                   <div class="form-check form-check-inline">
                                                      <input class="form-check-input cls-ext-std" type="radio" name="inlineRadioOptions1" checked id="ext_yes" value="Yes">
                                                      <label class="form-check-label" for="ext_yes">Yes</label>
                                                   </div>
                                                   <div class="form-check form-check-inline">
                                                      <input class="form-check-input cls-ext-std" type="radio" name="inlineRadioOptions1" id="ext_no" value="No">
                                                      <label class="form-check-label" for="ext_no">No</label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="alert-content divVerify" style="display: flex;">
                                       <p class="mb-0">Verify your reg no to pre-fill your data</p>
                                       <div class="row" style="margin-right: 10px; position: absolute; right: 0">
                                          <div class="col-lg-12">
                                             <div class="input-group">
                                                <input type="text" class="form-control form-control-sm txt-regNo-t" placeholder="Enter reg no" aria-label="Reg No">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="hstack gap-2 justify-content-center">
                                    <a href="javascript:void(0);" style="display: none" class="btn btn-link link-success fw-medium material-shadow-none btn-v-close" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>Close</a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-prefill">Verify</a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="modal fade" id="ModelSearchPhone" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" style="display: none;" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content" style="min-height: 300px;">
                           <div class="modal-header">
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <div class="modal-body p-2">
                              <div class="mt-4">
                                 <div class="alert alert-danger alert-dismissible alert-additional fade show material-shadow" role="alert">
                                    <div class="alert-body">
                                       <div class="d-flex">
                                          <div class="flex-grow-1" style="display: flex;">
                                             <h5 class="alert-heading">Search By Phone No.
                                             </h5>
                                             <div class="row" style="margin-right: 10px; position: absolute; right: 0">
                                                <div class="input-group">
                                                   <input type="text" class="form-control form-control-sm txt-phone-s allownumericwithoutdecimal" maxlength="10" placeholder="Enter Phone no" aria-label="Phone No">
                                                   <a href="javascript:void(0);" class="btn btn-info btn-tempData">Search</a>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="alert-content div-table" style="display: flex;">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="modal fade" id="ModelTesting" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content" style="min-height: 300px;">
                           <div class="modal-header">
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <div class="modal-body p-2">
                              <div class="mt-4">
                                 <div class="alert alert-danger alert-dismissible fade show material-shadow" role="alert">
                                    <div class="alert-body">
                                       <div class="d-flex">
                                          <div class="flex-grow-1" style="display: flex;">
                                             <h5 class="alert-heading">
                                                The admission form is currently in testing mode. Please do not apply at this time.
                                             </h5>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script type="aabbd4361b787db4f22ec0aa-text/javascript">
                     document.addEventListener("DOMContentLoaded", function () {
                         var myModal = new bootstrap.Modal(document.getElementById('ModelTesting'));
                          
                     });
                  </script>
                  <!-- end row -->
               </div>
               <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <footer class="footer">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-sm-6">
                        <script type="aabbd4361b787db4f22ec0aa-text/javascript">document.write(new Date().getFullYear())</script>
                        © <a href="https://www.sfsguwahati.ac.in/" target="_blank">SFS Guwahati.</a>
                     </div>
                     <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                           Design & Develop by <a href="https://www.vasptechnologies.com/" target="_blank">Vasp Technologies Pvt. Ltd</a>
                        </div>
                     </div>
                  </div>
               </div>
            </footer>
         </div>
         <!-- end main content-->
      </div>
      <!-- END layout-wrapper -->
      <!-- JAVASCRIPT -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/simplebar/simplebar.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/node-waves/waves.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/feather-icons/feather.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/plugins/lord-icon-2.1.0.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/plugins.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/form-wizard.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/select2.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/select2.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/%40simonwep/pickr/pickr.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/form-pickers.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- multi.js -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/multi.js/multi.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/libs/%40tarekraafat/autocomplete.js/autoComplete.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- init js -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/form-advanced.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- input spin init -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/form-input-spin.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- input flag init -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/flag-input.initf700.js?v=1.0.1" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <!-- App js -->
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/app.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pages/two-step-verification.init.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/jquery-confirm.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/js.cookie.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/moment.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/extra/js/plugin/fullcalendar/fullcalendar.min.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/extra/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js" type="aabbd4361b787db4f22ec0aa-text/javascript"></script>
      <script type="aabbd4361b787db4f22ec0aa-text/javascript">
         function easyAlert(content) {
             $.alert({
                 title: 'Alert',
                 content: content,
                 icon: 'fa fa-bell',
                 theme: 'bootstrap',
                 type: 'blue',
                 animation: 'zoom',
                 closeAnimation: 'zoom',
                 buttons: {
                     okay: {
                         text: 'OK',
                         btnClass: 'btn-blue'
                     }
                 }
             });
             $('#l1').hide();
             $('#l2').hide();
         }
         function ShowLoader() {
             $('#l1').show();
             $('#l2').show();
         
         }
         function HideLoader() {
             $('#l1').hide();
             $('#l2').hide();
         
         }
      </script>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/rocket-loader.min.js" data-cf-settings="aabbd4361b787db4f22ec0aa-|49" defer></script><script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985946d7f8027a8d',t:'MTc1ODk1ODAyNw=='};var a=document.createElement('script');a.src='cdn-cgi/challenge-platform/h/b/scripts/jsd/5783333ceb22/maind41d.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"fc16063107074754831ad9486b252c70","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
   </body>
</html>