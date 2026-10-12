  <?php /* Template Name: PageWithoutSidebar */ ?>

  <?php
  get_header();
  ?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


  <style>
    .custom-split-btn {
      display: inline-flex;
      align-items: center;
      border: 1px solid;
      padding: 0;
      border-radius: 4px;
      overflow: hidden;
      font-weight: 500;
      height: 40px;
      font-size: 14px;
      cursor: pointer;
    }

    /* Text section (shared) */
    .custom-split-btn .btn-text {
      padding: 0 16px;
      display: flex;
      align-items: center;
    }

    /* Arrow section (shared) */
    .custom-split-btn .btn-arrow {
      padding: 0 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.05);
      border-left: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Red variant */
    .custom-split-btn-red {
      background-color: #8b1c1c !important;
      color: white;
      border-color: #e57373;
    }

    .custom-split-btn-red:hover {
      background-color: #a93232 !important;
    }

    /* Grey variant */
    .custom-split-btn-grey {
      background-color: #6c757d !important; /* Bootstrap grey */
      color: white;
      border-color: #adb5bd;
    }

    .custom-split-btn-grey:hover {
      background-color: #5a6268 !important;
    }

    /* For reverse direction (left arrow) */
    .custom-split-btn.prev .btn-arrow {
      order: 0;
      border-left: none;
      border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    .custom-split-btn.prev .btn-text {
      order: 1;
    }

   /* Stepper Styles */
  .stepper {
    display: flex;
    justify-content: space-between;
    margin: 30px auto;
    max-width: 100%;
    overflow-x: hidden;
    position: relative;
    padding: 0 10px;
    border-left: 1px solid #b61a3c;
    border-right: 1px solid #b61a3c;
  }

  .step {
    position: relative;
    text-align: center;
    flex: 1;
    min-width: 60px;
    z-index: 2;
  }

  .step::after {
    content: "";
    position: absolute;
    top: 18px;
    left: 50%;
    width: 100%;
    height: 4px;
    background: #ddd;
    z-index: -1;
  }

  .step:last-child::after {
    display: none;
  }

  .circle {
    width: 36px;
    height: 36px;
    border-radius: 25%;
    background: #ddd;
    margin: 0 auto;
    line-height: 36px;
    font-size: 14px;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
  }

  .step.active .circle {
    background: #b61a3c;
  }

  .step.completed .circle {
    background: #28a745;
  }

  .step.completed::after {
    background: #28a745;
  }

  .step p {
    display: none;
  }

  .step-content {
    display: none;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.61);
  }

  .step-content.active {
    display: block;
  }

  
  /* Button Wrappers */
  .btn-wrapper-first {
    display: flex;
    justify-content: flex-end; /* Aligns button to the right */
    width: 100%;
  }

  .btn-wrapper,
  .custom-wrapper {
    display: flex;
    justify-content: space-between; /* Space between buttons */
    align-items: center;
    width: 100%;
  }

  /* Select2 Styling */
  .select2.select2-container {
    width: 100% !important;
  }

  /* Horizontal Rule */
  hr.style-six {
    border: 0;
    height: 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
  }

  /* Tooltip Styling (if used) */
  [data-toggle="tooltip"] {
    position: relative;
  }

  /* Override any conflicting global styles for buttons, if necessary */
  button,
  input[type="button"],
  input[type="submit"] {
    cursor: pointer;
  }
  h3 {
    background: linear-gradient(90deg, #8b1c1c, #e5737329);
    padding: 5px;
    color: white;
  }
  .custom-div {
    background-color: #b61a3c;
    padding: 15px;
    color: white !important;
  }

  table.upload-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      text-align: left;
    }

    .upload-table th,
    .upload-table td {
      padding: 12px;
      border: 1px solid #ccc;
      /*vertical-align: start;*/
    }

    .file-preview {
      margin-top: 10px;
    }

    .file-preview img {
      max-width: 100px;
      max-height: 100px;
      display: block;
    }

    .file-preview .remove-btn {
      display: inline-block;
      margin-top: 5px;
      background: #b61a3c;
      color: #fff;
      border: none;
      padding: 4px 8px;
      cursor: pointer;
      font-size: 12px;
      border-radius: 4px;
    }

    .file-preview .remove-btn:hover {
      background: #a21835;
    }

    .upload-label {
      font-weight: bold;
    }
    .upload-warning {
      display: block;
      font-size: 12px;
      color: #b61a3c;
      margin-bottom: 6px;
    }
  </style>
  <div id="primary" class="container">
      <div class="row">
          <div class="col-md-12">
              <marquee width="100%" direction="left" behavior="scroll" scrollamount="6">
                  <a style="cursor: pointer; text-decoration: underline;" href="/acknowledgement"><h4 style="color: blue;">If your payment was deducted but you did not receive the acknowledgement of online application form, click here to update.</h4></a>
              </marquee>
          </div>
      </div>
      <main id="main" class="site-main" role="main">
      <!-- Stepper -->
      <div class="stepper" id="stepper">
        <div class="step active" data-step="1"><div class="circle" data-toggle="tooltip" title="DECLARATION">1</div></div>
        <div class="step" data-step="2"><div class="circle" data-toggle="tooltip" title="ACADEMIC SESSION & CLASS">2</div></div>
        <div class="step" data-step="3"><div class="circle" data-toggle="tooltip" title="PERSONAL INFORMATION">3</div></div>
        <div class="step" data-step="4"><div class="circle" data-toggle="tooltip" title="CANDIDATE'S HEALTH RECORD">4</div></div>
        <div class="step" data-step="5"><div class="circle" data-toggle="tooltip" title="FAMILY DETAILS">5</div></div>
        <div class="step" data-step="6"><div class="circle" data-toggle="tooltip" title="CO-CURRICULAR ACTIVITIES">6</div></div>
        <div class="step" data-step="7"><div class="circle" data-toggle="tooltip" title="TRANSPORTATION">7</div></div>
        <div class="step" data-step="8"><div class="circle" data-toggle="tooltip" title="STREAM & SUBJECTS">8</div></div>
        <div class="step" data-step="9"><div class="circle" data-toggle="tooltip" title="DOCUMENTS">9</div></div>
        <div class="step" data-step="10"><div class="circle" data-toggle="tooltip" title="SOURCE OF INFORMATION">10</div></div>
        <div class="step" data-step="11"><div class="circle" data-toggle="tooltip" title="PARENT’S CONSENT">11</div></div>
        <div class="step" data-step="12"><div class="circle" data-toggle="tooltip" title="PAYMENT">12</div></div>
      </div>

      <!-- Contents -->
      <div id="step-1" class="step-content active">
        <div class="btn-wrapper-first">
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Agree & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>DECLARATION</h3>
        <div>
          <div class="row">
           <div class="col-md-12">
              <div class="line-spacing" style="text-align: justify">
                 I / We solemnly state that I / we have not approached, lobbied or paid or promised to pay donation for admission to
                 <span id="ContentPlaceHolder1_lblSchoolname" style="font-weight: bold;">ST. FRANCIS DE SALES SCHOOL, GUWAHATI UDAYAN VIHAR, SATGAON, NARENGI, GUWAHATI, ASSAM-781171</span>. 
                 I / We agree to the cancellation of the admission of my candidate if any such illegal practices are proved by the school management at a later stage.&nbsp;
                 I / We do hereby agree to abide by the rules and regulations of the school and schedule of fee and payments as amended by the school at its discretion from time to time.
                 I / We, hereby, declare that, the entries made by me in the Application Form are complete and true to the best of my knowledge and based on records.
                 <br>
                 <br>
                 <h6>NOTE</h6>
                 1. Once an application form is submitted, the student's name, parent's name, and DOB cannot be changed.<br>
                 2. The management reserves the right to admit or reject any application for admission without giving any explanation.
              </div>
           </div>
           <div class="row">
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
      </div>
      <div id="step-2" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <div class="custom-wrapper">
           <h3 style="width: 50%;">ACADEMIC SESSION & CLASS</h3>
           <h5 style="width: 50%; text-align: right;"><i class="fa fa-history"></i> Continue from last step</h5>
        </div>
        <div>
          <div class="row g-3">
             <div class="col-md-6" data-select2-id="select2-data-55-3p6l">
                <label for="Academic Session" class="form-label">Academic Session</label>
                <select class="academic_session form-select" name="academic_session">
                  <option value="2026-2027">2026-2027</option>
                </select>
             </div>
             <div class="col-md-6">
                <label for="class" class="form-label">Class</label>
                <select class="class form-control">
                   <option value="0___0">Select Class</option>
                   <option value="NURSERY__SFSN_2627">NURSERY</option>
                   <option value="LKG__SFSN_2627">LKG</option>
                   <option value="UKG__SFSN_2627">UKG</option>
                   <option value="1__SFSN_2627">1</option>
                </select>
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
              <div class="d-flex align-items-start gap-3 mt-4 div-cls-btn"></div>
          </div>
        </div>
      </div>
      <div id="step-3" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>PERSONAL INFORMATION</h3>
        <div>
            <div class="row pen-div">
                <div class="col-md-6">
                   <label for="txtPEN" class="form-label">Permanent Education Number <i>(Kindly obtain this from the last school attended)</i></label>
                   <input type="text" class="form-control txtPEN allownumericwithoutdecimal" id="txtPEN" placeholder="Please Enter the Permanent Education Number if available !" value="">
                </div>
            </div>
            <br>
            <hr class="style-six">
            <div class="row mt-3">
               <div class="col-md-6">
                  <label for="txtfirstName" class="form-label">First name <span style="color: Red;">* </span></label>
                  <input type="text" class="form-control txtfirstName txtcsname" id="txtfirstName" placeholder="Enter first name" value="" required="">
               </div>
               <div class="col-md-6">
                  <label for="txtMiddleName" class="form-label">Middle name</label>
                  <input type="text" class="form-control txtMiddleName txtcsname" id="txtMiddleName" placeholder="Enter middle name" value="">
               </div>
               <div class="col-md-6">
                  <label for="txtlastName" class="form-label">Last name<span style="color: Red;"> * </span></label>
                  <input type="text" class="form-control txtlastName txtcsname" id="txtlastName" placeholder="Enter last name" value="" required="">
               </div>
               <div class="col-md-6">
                  <label for="cmbgender" class="form-label">Gender <span style="color: Red;">* </span></label>
                  <select class="js-example-basic-single form-control cmbgender" name="gender" id="cmbgender" required="">
                     <option value=""></option>
                     <option value="Male">Male</option>
                     <option value="Female">Female</option>
                     <option value="Other">Other</option>
                  </select>
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-6">
                  <label class="form-label" for="txtlastName">Date Of Birth<span style="color: Red;"> * </span></label>
                  <input type="text" id="txtdd" min="0" max="31" placeholder="DD-MM-YYYY" maxlength="2" required="" class="form-control txtdob allownumericwithoutdecimal">
               </div>
               <div class="col-md-6">
                  <label class="form-label" for="txtAge">Age as on (<span id="lblagedate"></span>)</label>
                  <div style="display: none;">
                     <span class="agenumber"></span>
                  </div>
                  <input type="text" class="form-control txtAge" id="txtAge" placeholder="Age" value="" readonly="" required="">
                  <span style="font-size: 11px; padding: 0px; color: blue;" class="age-content"></span>
               </div>
               <div class="col-md-6">
                  <label for="txtHeight" class="form-label">Height (in cms)<span style="color: Red;">*</span></label>
                  <input type="text" id="txtHeight" placeholder="in cms" maxlength="6" required="" class="form-control allownumericwithdecimal txtHeight">
               </div>
               <div class="col-md-6">
                  <label for="cmbCaste" class="form-label">Weight (in kgs)<span style="color: Red;">*</span></label>
                  <input type="text" id="txtWeight" placeholder="in kgs" maxlength="6" required="" class="form-control allownumericwithdecimal txtWeight">
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-6">
                  <label for="cmbCaste" class="form-label">Caste (Attested copy of Caste Certificate to be submitted)<span style="color: Red;"> * </span></label>
                  <select class="js-example-basic-single form-control cmbCaste" name="caste" id="cmbCaste" required="">
                     <option value=""></option>
                     <option value="General">General</option>
                     <option value="Scheduled Caste (SC)">Scheduled Caste (SC)</option>
                     <option value="Scheduled Tribe (ST)">Scheduled Tribe (ST)</option>
                     <option value="Other Backward Class (OBC)">Other Backward Class (OBC)</option>
                     <option value="Others">Others</option>
                  </select>
               </div>
               <div class="col-md-6">
                  <label class="form-label">Nationality<span style="color: Red;"> * </span></label>
                  <div data-input-flag="" data-option-flag-name="">
                     <input type="text" class="form-control rounded-end flag-input cmbnationality" id="cmbnationality" placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" required="">
                  </div>
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-6">
                  <label for="cmbCommunity" class="form-label">Religion (If Roman Catholic, kindly submit the Baptism Certificate)<span style="color: Red;"> * </span></label>
                  <select class="js-example-basic-single form-control cmbCommunity" name="caste" id="cmbCommunity" required="">
                     <option value=""></option>
                     <option value="Hindu">Hindu</option>
                     <option value="Muslim">Muslim</option>
                     <option value="Christian">Christian</option>
                     <option value="Buddhist">Buddhist</option>
                     <option value="Sikh">Sikh</option>
                     <option value="Jain">Jain</option>
                     <option value="Others">Others</option>
                  </select>
               </div>
               <div class="col-md-6">
                  <label class="form-label">Mother Tongue<span style="color: Red;"> * </span></label>
                  <select class="js-example-basic-single form-control cmbMotherToungue" name="MotherToungue" id="cmbMotherToungue" required=""></select>
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-6">
                  <label for="cmbBloodgroup" class="form-label">Blood Group<span style="color: Red;"> * </span></label>
                  <select class="js-example-basic-single form-control cmbBloodgroup" name="Bloodgroup" id="cmbBloodgroup" required="">
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
               </div>
               <div class="col-md-6">
                  <label class="form-label" for="txtIdentification">Identification Mark<span style="color: Red;">  </span></label>
                  <input type="text" class="form-control txtIdentification" id="txtIdentification" placeholder="Identification Mark" value="">
               </div>
               <div class="col-md-6">
                  <label class="form-label" for="txtAadhar">Aadhar No.</label>
                  <input type="text" class="form-control txtAadhar allownumericwithoutdecimal" id="txtAadhar" placeholder="Aadhar No" value="">
               </div>
               <div class="col-md-6">
                  <label for="cmbDietary" class="form-label">Dietary Preferences<span style="color: Red;"> * </span></label>
                  <select class="js-example-basic-single form-control cmbDietary" name="Dietary" id="cmbDietary" required="">
                     <option value=""></option>
                     <option value="Veg">Vegetarian</option>
                     <option value="Non-Veg">Non Vegetarian</option>
                  </select>
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-4 mt-4">
                  <label class="form-label w-75" style="font-weight: bold;">Whether the candidate is:</label>
               </div>
               <div class="col-md-4">
                  <label for="cmbSingleGirlChild" class="form-label">Single Girl Child  <span style="color: Red;">*</span></label>
                  <select class="js-example-basic-single form-control cmbSingleGirlChild" name="SingleGirlChild" id="cmbSingleGirlChild" required="">
                     <option value=""></option>
                     <option value="Yes">Yes</option>
                     <option value="No">No</option>
                  </select>
               </div>
               <div class="col-md-4" style="display: none;">
                  <label for="cmbDivyanngjan" class="form-label">Specially abled (Divyanngjan)  <span style="color: Red;">*</span></label>
                  <select class="js-example-basic-single form-control cmbDivyanngjan" name="Divyanngjan" id="cmbDivyanngjan">
                     <option value=""></option>
                     <option value="Yes">Yes</option>
                     <option value="No">No</option>
                  </select>
               </div>
               <div class="col-md-4">
                  <label for="cmbEWS" class="form-label">Belonging to the EWS  <span style="color: Red;">*</span></label>
                  <select class="js-example-basic-single form-control cmbEWS" name="EWS" id="cmbEWS" required="">
                     <option value=""></option>
                     <option value="Yes">Yes</option>
                     <option value="No">No</option>
                  </select>
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
                     <input class="form-check-input Rb_compet" type="radio" name="Rb_compet" checked="" id="Rb_compet_no" value="No">
                     <label class="form-check-label" for="Rb_compet_no">No</label>
                  </div>
                  <div class="form-check form-check-inline form-radio-success mb-3">
                     <input class="form-check-input Rb_compet" type="radio" name="Rb_compet" id="Rb_compet_Yes" value="Yes">
                     <label class="form-check-label" for="Rb_Disease_Yes">Yes</label>
                  </div>
                  <input type="text" class="form-control txtcompet" id="txtcompet" style="pointer-events: none;" placeholder="Please Specify" value="No" required="">
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-sm-4">
                  <label for="cmbLastSchool" class="form-label">Last School Attended <span id="lblR1" style="color: Red;">*</span></label>
                  <input type="text" class="js-example-basic-single1 form-control cmbLastSchool" name="LastSchool" id="cmbLastSchool" placeholder="Last School Attended" required="">
                  <div class="mt-3" id="divlastSchool" style="display: none;">
                     <input type="text" class="form-control txtLastSchool" id="txtLastSchool" placeholder="Last School Attended" value="">
                  </div>
               </div>
               <div class="col-sm-4">
                  <label for="txtCity" class="form-label">Address of Last School Attended<span id="lblR2" style="color: Red;">*</span></label>
                  <input type="text" class="form-control txtLastSchoolAddress" id="txtLastSchoolAddress" placeholder="Enter Place &amp; Address" value="" required="">
               </div>
               <div class="col-sm-4">
                  <label for="cmbBoardName" class="form-label">Board of Last School Attended<span id="lblR3" style="color: Red;">*</span></label>
                  <select class="js-example-basic-single form-control cmbBoardName" required="" name="BoardName" id="cmbBoardName">
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
                     <option value="JKBOSE">Jammu &amp; Kashmir Board of School Education (JKBOSE)</option>
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
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-12">
                  <label class="form-label w-75" style="font-weight: bold;">
                     Do you have own siblings studing at 
                     <span id="ContentPlaceHolder1_lblSchool1">ST. FRANCIS DE SALES SCHOOL, GUWAHATI</span>
                     <div class="form-check form-check-inline form-radio-danger mb-3">
                        <input class="form-check-input Rb_Sib" type="radio" name="Rb_Sib" checked="" id="Rb_Sib_no" value="No">
                  <label class="form-check-label" for="Rb_Sib_no">No</label>
                  </div>
                  <div class="form-check form-check-inline form-radio-success mb-3">
                     <input class="form-check-input Rb_Sib" type="radio" name="Rb_Sib" id="Rb_Sib_Yes" value="Yes">
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
                              <tr class="a-tr" id="a_1">
                                 <td style="text-align: right; width: 20px;">1</td>
                                 <td style="text-align: right; width: 60px;">
                                    <input type="text" id="rega_1" placeholder="Reg No" class="a-regno form-control data-sibl allownumericwithoutdecimal">
                                 </td>
                                 <td style="text-align: left; width: 120px;">
                                    <input type="text" id="namea_1" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="gendera_1" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="clsa_1" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 60px;">
                                    <input type="text" class="form-control readonly data-sibl" id="seca_1">
                                 </td>
                              </tr>
                              <tr class="a-tr" id="a_2">
                                 <td style="text-align: right; width: 20px;">2</td>
                                 <td style="text-align: right; width: 60px;">
                                    <input type="text" id="rega_2" placeholder="Reg No" class="a-regno form-control data-sibl allownumericwithoutdecimal">
                                 </td>
                                 <td style="text-align: left; width: 120px;">
                                    <input type="text" id="namea_2" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="gendera_2" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="clsa_2" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 60px;">
                                    <input type="text" class="form-control readonly data-sibl" id="seca_2">
                                 </td>
                              </tr>
                              <tr class="a-tr" id="a_3">
                                 <td style="text-align: right; width: 20px;">3</td>
                                 <td style="text-align: right; width: 60px;">
                                    <input type="text" id="rega_3" placeholder="Reg No" class="a-regno form-control data-sibl allownumericwithoutdecimal">
                                 </td>
                                 <td style="text-align: left; width: 120px;">
                                    <input type="text" id="namea_3" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="gendera_3" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="clsa_3" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 60px;">
                                    <input type="text" class="form-control readonly data-sibl" id="seca_3">
                                 </td>
                              </tr>
                              <tr class="a-tr" id="a_4">
                                 <td style="text-align: right; width: 20px;">4</td>
                                 <td style="text-align: right; width: 60px;">
                                    <input type="text" id="rega_4" placeholder="Reg No" class="a-regno form-control data-sibl allownumericwithoutdecimal">
                                 </td>
                                 <td style="text-align: left; width: 120px;">
                                    <input type="text" id="namea_4" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="gendera_4" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="clsa_4" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 60px;">
                                    <input type="text" class="form-control readonly data-sibl" id="seca_4">
                                 </td>
                              </tr>
                              <tr class="a-tr" id="a_5">
                                 <td style="text-align: right; width: 20px;">5</td>
                                 <td style="text-align: right; width: 60px;">
                                    <input type="text" id="rega_5" placeholder="Reg No" class="a-regno form-control data-sibl allownumericwithoutdecimal">
                                 </td>
                                 <td style="text-align: left; width: 120px;">
                                    <input type="text" id="namea_5" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="gendera_5" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 40px;">
                                    <input type="text" id="clsa_5" readonly="" class="form-control data-sibl">
                                 </td>
                                 <td style="text-align: left; width: 60px;">
                                    <input type="text" class="form-control readonly data-sibl" id="seca_5">
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
                        <input class="form-check-input Rb_Ward" type="radio" name="Rb_Ward" checked="" id="Rb_Ward_no" value="No">
                  <label class="form-check-label" for="Rb_Ward_no">No</label>
                  </div>
                  <div class="form-check form-check-inline form-radio-success mb-3">
                     <input class="form-check-input Rb_Ward" type="radio" name="Rb_Ward" id="Rb_Ward_Yes" value="Yes">
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
                        <input type="text" class="form-control" id="txtResidential" placeholder="Residential Address" value="" required="">
                     </div>
                     <div class="col-sm-4">
                        <label for="txtCity" class="form-label">City  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control" id="txtCity" placeholder="Enter City" value="" required="">
                     </div>
                  </div>
                  <div class="row mt-3">
                     <div class="col-md-6">
                        <label class="form-label">State  <span style="color: Red;">*</span></label>
                        <select class="js-example-basic-single form-control" name="State" id="cmbState" required=""></select>
                     </div>
                     <div class="col-md-6">
                        <label for="txtPin" class="form-label">Postal Pin Code  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control allownumericwithoutdecimal" maxlength="6" id="txtPin" placeholder="Pin Code" value="" required="">
                     </div>
                     <div class="col-md-6">
                        <label for="txtemail1" class="form-label">Primary E-mail Address  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control" id="txtemail1" placeholder="Primary E-mail Address" value="" required="">
                     </div>
                     <div class="col-md-6">
                        <label for="txtemail2" class="form-label">Secondary E-mail Address</label>
                        <input type="text" class="form-control" id="txtemail2" placeholder="Secondary E-mail Address" value="">
                     </div>
                  </div>
                  <div class="row mt-3">
                     <div class="col-md-6">
                        <label for="txtPrimaryPhone" class="form-label">Primary Phone No  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control allownumericwithoutdecimal" id="txtPrimaryPhone" maxlength="10" placeholder="For school contact" value="" required="">
                     </div>
                     <div class="col-md-6">
                        <label for="txtSecondaryPhone" class="form-label">Secondary Phone No  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control allownumericwithoutdecimal" maxlength="10" id="txtSecondaryPhone" placeholder="For Emergency purpose only" value="" required="">
                     </div>
                     <div class="col-md-6">
                        <label for="txtWhatsappNo" class="form-label">Whatsapp No  <span style="color: Red;">*</span></label>
                        <input type="text" class="form-control allownumericwithoutdecimal" maxlength="10" id="txtWhatsappNo" placeholder="WhatsappNo" value="" required="">
                     </div>
                     <div class="col-md-6">
                        <label for="cmbDistance" class="form-label">Distance From School  <span style="color: Red;">*</span></label>
                        <select class="js-example-basic-single form-control" name="State" id="cmbDistance" required="">
                           <option value=""></option>
                           <option value="0-5 kms">0-5 kms</option>
                           <option value="6-10 kms">6-10 kms</option>
                           <option value="10-15 kms">10-15 kms</option>
                           <option value="Above 10 kms">Above 15 kms</option>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="step-4" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>CANDIDATE'S HEALTH RECORD</h3>
        <div>
           <div class="row g-3">
              <div class="col-md-10">
                 <label class="form-label" for="txtIdentification">Disease or any serious illness for which candidate is on regular medication
                    <span style="color: Red;"> * </span>
                 </label>
              </div>
              <div class="col-md-2">
                 <div class="form-check form-check-inline form-radio-danger mb-3">
                    <input class="form-check-input Rb_Disease" type="radio" name="Rb_Disease" checked id="Rb_Disease_no" value="No">
                    <label class="form-check-label" for="Rb_Disease_no">No</label>
                 </div>
                 <div class="form-check form-check-inline form-radio-success mb-3">
                    <input class="form-check-input Rb_Disease" type="radio" name="Rb_Disease" id="Rb_Disease_Yes" value="Yes">
                    <label class="form-check-label" for="Rb_Disease_Yes">Yes</label>
                 </div>
              </div>
              <div class="col-md-12 mb-5">
                <div class="input-group">
                    <input type="text" class="form-control txtDisease" id="txtDisease" 
                           style="pointer-events: none; width: 90%;" placeholder="Disease or any serious illness" 
                           value="No" required>
                    <button class="btn btn-success" type="button" id="btnAddDisease" disabled>Add</button>
                 </div>
              </div>
              </div>
              <br><br>
              <div class="row m-3">
              <div class="col-md-12">
                 <table class="table table-bordered table-sm table-hover">
                    <thead class="bg-danger text-light border-light" style="background-color: #b61a3c;">
                       <tr>
                          <th style="width: 5%; color: white !important;" class="p-2">SL</th>
                          <th style="width: 25%; color: white !important;" class="p-2">Disease / Illness</th>
                          <th style="width: 12%; color: white !important;">Remove</th>
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
                    <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Medicine" checked="" id="Rb_Medicine_no" value="No">
                    <label class="form-check-label" for="Rb_Medicine_no">No</label>
                 </div>
                 <div class="form-check form-check-inline form-radio-success mb-3">
                    <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Medicine" id="Rb_Medicine_Yes" value="Yes">
                    <label class="form-check-label" for="Rb_Medicine_Yes">Yes</label>
                 </div>
              </div>
           </div>
        </div>
      </div>
      <div id="step-5" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>FAMILY DETAILS</h3>
        <div>
          <div class="row g-3">
           <div class="col-md-12">
              <div class="row">
                 <div class="col-xxl-6 col-lg-6">
                    <div class="card   border">
                       <div class="card-header">
                          <h6 class="card-title mb-0">Father's/Guardian's Info</h6>
                       </div>
                       <div class="card-body">
                          <div class="row">
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherName" class="form-label ">Father's/Guardian's Name<span style="color: Red;"> * </span></label>
                                <input type="text" class="form-control txtcfname" id="txtFatherName" placeholder="Father's/Guardian's Name" value="" required="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="cmbfatherQualification" class="form-label">Highest Educational Qualification <span style="color: Red;">* </span></label>
                                <select class="js-example-basic-single form-control" name="fatherQualification" id="cmbfatherQualification" required="">
                                   <option value=""></option>
                                   <option value="Under Matriculation">Under Matriculation</option>
                                   <option value="Matriculation">Matriculation</option>
                                   <option value="Higher Secondary">Higher Secondary</option>
                                   <option value="Graduate">Graduate</option>
                                   <option value="Postgraduate/Masters">Postgraduate/Masters</option>
                                   <option value="PhD and Above">PhD and Above</option>
                                </select>
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherPhone" class="form-label">Phone No<span style="color: Red;"> * </span></label>
                                <input type="text" class="form-control allownumericwithoutdecimal" id="txtFatherPhone" maxlength="10" placeholder="Father's/Guardian's Phone" value="" required="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherEmail" class="form-label">Email<span style="color: Red;">   </span></label>
                                <input type="text" class="form-control  " id="txtFatherEmail" placeholder="Father's/Guardian's Email" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherEmployement" class="form-label">Employment<span style="color: Red;"> * </span></label>
                                <select class="js-example-basic-single form-control txtFatherEmployement" name="txtFatherEmployement" id="txtFatherEmployement" required="">
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
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherProfession" class="form-label">Profession<span style="color: Red;"> * </span></label>
                                <select class="js-example-basic-single form-control txtFatherProfession" name="txtFatherProfession" id="txtFatherProfession" required="">
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
                             </div>
                             <div class="col-md-12 mb-2" style="display: none;">
                                <label for="txtFatherDesignation" class="form-label">Designation &amp; Details<span style="color: Red;">  * </span></label>
                                <input type="text" class="form-control  txtFatherDesignation" id="txtFatherDesignation" placeholder="Father's/Guardian's Designation &amp; Details" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherOfficeAddress" class="form-label">Office/ Bussiness Address<span style="color: Red;">   </span></label>
                                <input type="text" class="form-control  " id="txtFatherOfficeAddress" placeholder="Father's Office Address" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtFatherIncome" class="form-label">Annual Income (in lakh(s))<span style="color: Red;"> * </span></label>
                                <select class="js-example-basic-single form-control txtFatherIncome cls-income" name="txtFatherIncome" id="txtFatherIncome" required="">
                                   <option value=""></option>
                                   <option value="LESS THAN 1 LAKH">LESS THAN 1 LAKH</option>
                                   <option value="1–2.5 LAKHS">1–2.5 LAKHS</option>
                                   <option value="2.5–5 LAKHS">2.5–5 LAKHS</option>
                                   <option value="5–8 LAKHS">5–8 LAKHS</option>
                                   <option value="8 LAKHS AND ABOVE">8 LAKHS AND ABOVE</option>
                                </select>
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
                                <input type="text" class="form-control txtcfname" id="txtMotherName" placeholder="Mother's/Guardian's Name" value="" required="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="cmbMotherQualification" class="form-label">Highest Educational Qualification <span style="color: Red;">* </span></label>
                                <select class="js-example-basic-single form-control" name="MotherQualification" id="cmbMotherQualification" required="">
                                   <option value=""></option>
                                   <option value="Under Matriculation">Under Matriculation</option>
                                   <option value="Matriculation">Matriculation</option>
                                   <option value="Higher Secondary">Higher Secondary</option>
                                   <option value="Graduate">Graduate</option>
                                   <option value="Postgraduate/Masters">Postgraduate/Masters</option>
                                   <option value="PhD and Above">PhD and Above</option>
                                </select>
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherPhone" class="form-label">Phone No<span style="color: Red;"> * </span></label>
                                <input type="text" class="form-control allownumericwithoutdecimal" id="txtMotherPhone" maxlength="10" placeholder="Mother's/Guardian's Phone" value="" required="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherEmail" class="form-label">Email<span style="color: Red;">   </span></label>
                                <input type="text" class="form-control  " id="txtMotherEmail" placeholder="Mother's/Guardian's Email" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherEmployement" class="form-label">Employment<span style="color: Red;"> * </span></label>
                                <select class="js-example-basic-single form-control txtMotherEmployement" name="txtMotherEmployement" id="txtMotherEmployement" required="">
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
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherProfession" class="form-label">Profession<span style="color: Red;"> * </span></label>
                                <select class="js-example-basic-single form-control txtMotherProfession" name="txtMotherProfession" id="txtMotherProfession" required="">
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
                             </div>
                             <div class="col-md-12 mb-2" style="display: none;">
                                <label for="txtMotherDesignation" class="form-label">Designation &amp; Details<span style="color: Red;"></span></label>
                                <input type="text" class="form-control  txtMotherDesignation" id="txtMotherDesignation" required="" placeholder="Mother's/Guardian's Designation &amp; Details" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherOfficeAddress" class="form-label">Office/ Bussiness Address<span style="color: Red;">   </span></label>
                                <input type="text" class="form-control  " id="txtMotherOfficeAddress" placeholder="Mother's/Guardian's Office Address" value="">
                             </div>
                             <div class="col-md-12 mb-2">
                                <label for="txtMotherIncome" class="form-label">Annual Income (in lakh(s))<span style="color: Red;">   </span></label>
                                <select class="js-example-basic-single form-control txtMotherIncome" name="txtMotherIncome" id="txtMotherIncome" required="">
                                   <option value=""></option>
                                   <option value="LESS THAN 1 LAKH">LESS THAN 1 LAKH</option>
                                   <option value="1–2.5 LAKHS">1–2.5 LAKHS</option>
                                   <option value="2.5–5 LAKHS">2.5–5 LAKHS</option>
                                   <option value="5–8 LAKHS">5–8 LAKHS</option>
                                   <option value="8 LAKHS AND ABOVE">8 LAKHS AND ABOVE</option>
                                </select>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
              <div class="row">
                 <div class="col-md-6">
                  <label for="txtMotherOfficeAddress" class="form-label">Family Annual Income<span style="color: Red;"></span></label>
                  <input type="text" class="form-control" id="txtFamilyAnnualIncome" placeholder="Family Annual Income" value="" readonly="">
                 </div>
              </div>
           </div>
        </div>
        </div>
      </div>
      <div id="step-6" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>CO-CURRICULAR ACTIVITIES</h3>
        <div>
          <div class="row g-3">
           <div class="col-md-12">
              <div style="border: 1px solid black; padding: 5px;">
                 <div class="alert-body">
                    <div class="d-flex">
                       <div class="flex-grow-1" style="display: flex;">
                        Applicable for Classes III – IX &amp; XI
                        (The Co-Curricular Activities section has two parts)
                       </div>
                       <hr>
                    </div>
                 </div>
                 <div class="alert-content">
                    <p>
                    1. Saturday Activities (Mandatory): These are co-curricular activities conducted every Saturday during school hours. Selection of one activity from this section is mandatory.
                    </p>
                    <p>
                    2. After-School Sports Activities (Optional): These are sports activities held after school hours. Selection from this section is optional and based on student interest.
                    </p>
                 </div>
              </div>
           </div>
           </div>
           <br>
           <div class="row g-3">
           <div class="col-md-6">
              <div class="custom-div">
                 <div class="alert-body">
                    <div class="d-flex">
                       <div class="flex-grow-1" style="display: flex;">
                          <span>CLUB ACTIVITIES</span>
                       </div>
                    </div>
                 </div>
                 <div class="alert-content">
                    <div id="step-3-table">
                    </div>
                 </div>
              </div>
           </div>
           <div class="col-md-6">
              <div class="custom-div">
                 <div class="alert-body">
                    <div class="d-flex">
                       <div class="flex-grow-1" style="display: flex;">
                          <span>AFTER SCHOOL SPORTS ACTIVITIES
                          </span>
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
        </div>
      </div>
      <div id="step-7" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>TRANSPORTATION</h3>
        <div>
          <div class="row mt-3">
             <div class="col-md-6">
                <label for="cmbTransMode" class="form-label">Select Transportation <span style="color: Red;">* </span></label>
                <select class="form-control cmbTransMode" id="cmbTransMode" required="">
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
      <div id="step-8" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>STREAM & SUBJECTS</h3>
        <div>
          <div class="row g-3">
             <div class="col-md-12">
                <div class="row divStreamNa">
                   <div class="col-md-12">
                      <h4 style="color: red">This option is not application for Class <span id="tblstrcls"></span>. Please click next to skip</h4>
                   </div>
                </div>
                <div class="row mt-3 divStream">
                   <div class="col-xxl-4  col-md-6">
                      <div class="row">
                         <div class="col-md-12 mt-4 streamlist1">
                            <label for="cmbStreamSubjects1" class="form-label">1st Stream  <span style="color: Red;">* </span></label>
                            <input type="text" id="cmbStreamSubjects1" class="form-control" readonly="">
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
                            <select class="form-control cmbStreamSubjects2" name="cmbStreamSubjects2" id="cmbStreamSubjects2" required="">
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
      </div>
      <div id="step-9" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>DOCUMENTS</h3>
        <div>
          <div class="row" style="margin: 1px;">
          <table class="upload-table">
            <thead style="background-color: #b61a3c;">
              <tr>
                <th style="color: white; width: 50%; font-size: 16px;">Documents</th>
                <th style="color: white; width: 5%; font-size: 16px;">Upload</th>
                <th style="color: white; width: 45%; font-size: 16px;">Preview</th>
              </tr>
            </thead>
            <tbody>
              <!-- Candidate Photo -->
              <tr>
                <td class="upload-label">Photograph of the Candidate *</td>
                <td><span class="upload-warning">Max size: 200KB | Format: JPG/JPEG</span><input type="file" accept=".jpg,.jpeg" data-type="image" data-label="Candidate Photo" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>

              <!-- Father Photo -->
              <tr>
                <td class="upload-label">Photograph of the <input type="radio" name="father"> Father&nbsp;&nbsp;<input type="radio" name="father" value="1" checked="checked"> Guardian *</td>
                <td><span class="upload-warning">Max size: 200KB | Format: JPG/JPEG</span><input type="file" accept=".jpg,.jpeg" data-type="image" data-label="Father Photo" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>

              <!-- Mother Photo -->
              <tr>
                <td class="upload-label">Photograph of the <input type="radio" name="mother"> Mother <input type="radio" name="mother" value="1" checked="checked"> Guardian *</td>
                <td><span class="upload-warning">Max size: 200KB | Format: JPG/JPEG</span>  <input type="file" accept=".jpg,.jpeg" data-type="image" data-label="Mother Photo" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>

              <!-- Birth Certificate -->
              <tr>
                <td class="upload-label">Birth Certificate *</td>
                <td><span class="upload-warning">Max size: 200KB | Format: PDF</span><input type="file" accept=".pdf" data-type="pdf" data-label="Birth Certificate" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>

              <!-- Caste Certificate -->
              <tr>
                <td class="upload-label">Caste Certificate</td>
                <td> <span class="upload-warning">Max size: 200KB | Format: PDF</span><input type="file" accept=".pdf" data-type="pdf" data-label="Caste Certificate" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>

              <!-- Baptism Certificate -->
              <tr>
                <td class="upload-label">Baptism Certificate</td>
                <td><span class="upload-warning">Max size: 200KB | Format: PDF</span><input type="file" accept=".pdf" data-type="pdf" data-label="Baptism Certificate" class="file-input"></td>
                <td class="file-preview"></td>
              </tr>
            </tbody>
          </table>
          </div>
        </div>
      </div>
      <div id="step-10" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>SOURCE OF INFORMATION</h3>
        <div>
          <h5 class="alert-heading mb-3">Please mention how you came to know about our school</h5>
          <div style="background-color: #ff000036; padding: 20px;">
            <div class="alert-content">

              <div class="form-check">
                <input class="form-check-input" name="Source_info" type="radio" value="Word of Mouth (Friends/Relatives)" id="source1">
                <label class="form-check-label" for="source1"><strong>Word of Mouth (Friends/Relatives)</strong></label>
              </div>

              <div class="form-check">
                <input class="form-check-input" name="Source_info" type="radio" value="Social Media" id="source2">
                <label class="form-check-label" for="source2"><strong>Social Media</strong></label>
              </div>

              <div class="form-check">
                <input class="form-check-input" name="Source_info" type="radio" value="Google Search" id="source3">
                <label class="form-check-label" for="source3"><strong>Google Search</strong></label>
              </div>

              <div class="form-check">
                <input class="form-check-input" name="Source_info" type="radio" value="Advertisement" id="source4">
                <label class="form-check-label" for="source4"><strong>Advertisement</strong></label>
              </div>

              <div class="form-check">
                <input class="form-check-input" name="Source_info" type="radio" value="Existing Student" id="source5">
                <label class="form-check-label" for="source5"><strong>Existing Student</strong></label>
              </div>

              <div class="form-check mb-2">
                <input class="form-check-input" name="Source_info" type="radio" value="Other" id="sourceOther">
                <label class="form-check-label" for="sourceOther"><strong>Other</strong></label>
              </div>
              <div class="form-group other-content" style="display: none;">
                <label for="txtSource" class="form-label">Please specify</label>
                <input type="text" class="form-control" id="txtSource" placeholder="Source of Information">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="step-11" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>

          <!-- Save & Next Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-red next"><span class="btn-text">Save & Next</span><span class="btn-arrow"><i class="fa fa-arrow-right"></i></span></button>
        </div>
        <hr class="style-six">
        <h3>PARENT’S CONSENT</h3>
        <div>
          <div class="row" style="margin: 1px;">
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
                 <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Social" id="Rb_Social_Yes" checked="" value="Yes">
                 <label class="form-check-label" for="Rb_Social_Yes">We agree to give our consent.</label>
              </div>
              <div class="form-check form-check-inline form-radio-danger mb-3">
                 <input class="form-check-input Rb_Medicine" type="radio" name="Rb_Social" id="Rb_Social_no" value="No">
                 <label class="form-check-label" for="Rb_Social_no">We disagree to give our consent.</label>
              </div>
           </div>
        </div>
        </div>
      </div>
      <div id="step-12" class="step-content">
        <div class="btn-wrapper">
          <!-- Previous Button -->
          <button type="button" class="btn custom-split-btn custom-split-btn-grey prev"><span class="btn-arrow"><i class="fa fa-arrow-left"></i></span><span class="btn-text">Previous</span></button>
          <button type="button" class="btn custom-split-btn" style="padding: 4px;">Submit</button>
        </div>
        <hr class="style-six">
        <h3>PAYMENT</h3>
        <div>
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
      </main>
  </div>

  <?php get_footer(); ?>

  <script type="text/javascript">
    jQuery(document).ready(function ($) {
        // Radio change
        $("input[name='Rb_Disease']").change(function () {
            if ($("#Rb_Disease_Yes").is(":checked")) {
                $("#txtDisease").val("").css("pointer-events", "auto");
                $("#btnAddDisease").prop("disabled", false);
            } else {
                $("#txtDisease").val("No").css("pointer-events", "none");
                $("#btnAddDisease").prop("disabled", true);
                $("#tblHealth").empty(); // clear table if switched back to No
            }
        });

        // Add button click
        $("#btnAddDisease").click(function () {
            let disease = $("#txtDisease").val().trim();
            if (disease === "") return;

            let rowCount = $("#tblHealth tr").length + 1;
            let row = `
                <tr>
                    <td>${rowCount}</td>
                    <td>${disease}</td>
                    <td><button type="button" class="btn btn-sm btn-danger btnRemove">×</button></td>
                </tr>
            `;
            $("#tblHealth").append(row);
            $("#txtDisease").val(""); // clear input
        });

        // Remove row
        $(document).on("click", ".btnRemove", function () {
            $(this).closest("tr").remove();

            // re-number rows
            $("#tblHealth tr").each(function (index) {
                $(this).find("td:first").text(index + 1);
            });
        });

        // Select2 init
        $('.academic_session').select2();
        $('.class').select2();

        // Initialize tooltips
        // $('[data-toggle="tooltip"]').tooltip({ container: 'body' });
    });
  </script>

  <script>
      document.addEventListener("DOMContentLoaded", function () {
          const stepEls = document.querySelectorAll('.step');
          const contentEls = document.querySelectorAll('.step-content');
          let currentStep = 1;

          // Check sessionStorage for saved step
          if (sessionStorage.getItem('currentStep')) {
              currentStep = parseInt(sessionStorage.getItem('currentStep'));
          }

          function updateStep() {
              stepEls.forEach((el, i) => {
                  el.classList.remove('active','completed');
                  const idx = i + 1;
                  if (idx < currentStep) el.classList.add('completed');
                  else if (idx === currentStep) el.classList.add('active');
              });

              contentEls.forEach(c => c.classList.remove('active'));
              document.getElementById('step-' + currentStep).classList.add('active');

              // Save the current step in sessionStorage
              sessionStorage.setItem('currentStep', currentStep);
          }

          // Next / Prev buttons
          document.querySelectorAll('.next').forEach(btn => btn.addEventListener('click', () => {
              if (currentStep < stepEls.length) {
                  currentStep++;
                  updateStep();
              }
          }));

          document.querySelectorAll('.prev').forEach(btn => btn.addEventListener('click', () => {
              if (currentStep > 1) {
                  currentStep--;
                  updateStep();
              }
          }));

          // Circle click → jump directly to step
          document.querySelectorAll('.circle').forEach(circle => {
              circle.addEventListener('click', () => {
                  const stepNum = circle.parentElement.getAttribute('data-step');
                  if (stepNum) {
                      currentStep = parseInt(stepNum);
                      updateStep();
                  }
              });
          });

          // Initialize the step on page load
          updateStep();
      });

    document.querySelectorAll('.file-input').forEach(input => {
      input.addEventListener('change', function () {
        const file = this.files[0];
        const maxSize = 200 * 1024; // 200 KB
        const previewCell = this.closest('tr').querySelector('.file-preview');
        const fileType = this.getAttribute('data-type');
        const label = this.getAttribute('data-label');

        previewCell.innerHTML = ''; // Clear previous preview

        if (!file) return;

        // Validate file size
        if (file.size > maxSize) {
          alert(`${label} must be less than 200 KB.`);
          this.value = '';
          return;
        }

        // Preview setup
        const removeBtn = document.createElement('a');
        removeBtn.innerText = 'Remove';
        removeBtn.classList.add('remove-btn');
        removeBtn.addEventListener('click', () => {
          this.value = '';
          previewCell.innerHTML = '';
        });

        if (fileType === 'image') {
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          previewCell.appendChild(img);
          previewCell.appendChild(removeBtn);
        } else if (fileType === 'pdf') {
          const link = document.createElement('a');
          link.href = URL.createObjectURL(file);
          link.target = '_blank';
          link.innerText = 'View PDF';
          link.style.display = 'block';
          previewCell.appendChild(link);
          previewCell.appendChild(removeBtn);
        }
      });
    });
  </script>

  <script>
  document.querySelectorAll('input[name="Source_info"]').forEach(radio => {
    radio.addEventListener('change', function () {
      const isOther = this.value === 'Other';
      const otherField = document.querySelector('.other-content');

      if (isOther) {
        otherField.style.display = 'block';
      } else {
        otherField.style.display = 'none';
        document.getElementById('txtSource').value = '';
      }
    });
  });
</script>


