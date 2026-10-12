jQuery(document).ready(function ($) {

    // ------------------------------------------------------------
    // SETTINGS
    // ------------------------------------------------------------
    // true  = mobile number must be verified by OTP before submitting
    // false = OTP is skipped and the Register button is always visible
    const OTP_REQUIRED = true;

    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];

    // ------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------
    function errorMsg(text) {
        return `<span style="color:red;">${text}</span>`;
    }

    function closedBoxHTML(cls, year) {
        return `
            <div class="closed-box" style="border: 1px solid #8a5a45; padding: 5px;">
                <center>
                <img src="/wp-content/uploads/2026/02/sorry.png" alt="Admission Closed" />
                <p style="color:#a40;">Admission Closed</p>
                <p style="color:#47996b;">Do you want Register?</p>
                <button class="btn yes btn-info" data-class="${cls}" data-year="${year}">
                    Yes
                </button>
                <button class="btn btn-danger" style="background-color: #17a2b8; color: white;">No</button>
                </center>
            </div>`;
    }

    // ------------------------------------------------------------
    // LOAD YEARS (API)
    // ------------------------------------------------------------
    $.post(SFS.ajax, { action: 'load_years' }, function (res) {

        $('#admissionYear').html('<option value="">Select Year</option>');

        if (!Array.isArray(res) || res.length === 0) {
            console.warn('load_years returned non-array, using session fallback:', res);
            const now = new Date();
            const y = now.getFullYear();
            const m = now.getMonth() + 1;
            const currentSession = m >= 4 ? `${y}-${y + 1}` : `${y - 1}-${y}`;
            const nextSession = m >= 4 ? `${y + 1}-${y + 2}` : `${y}-${y + 1}`;
            res = [
                { name: currentSession, isCurrent: "1" },
                { name: nextSession, isCurrent: "1" }
            ];
        }

        let hasCurrent = res.some(y => y.isCurrent == "1");
        res.forEach(y => {
            if (!hasCurrent || y.isCurrent == "1") {
                $('#admissionYear').append(`<option value="${y.name}">${y.name}</option>`);
            }
        });

    }, 'json').fail(function (xhr) {
        console.warn('load_years AJAX failed, using fallback');
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth() + 1;
        const currentSession = m >= 4 ? `${y}-${y + 1}` : `${y - 1}-${y}`;
        const nextSession = m >= 4 ? `${y + 1}-${y + 2}` : `${y}-${y + 1}`;
        $('#admissionYear').append(`<option value="${currentSession}">${currentSession}</option>`);
        $('#admissionYear').append(`<option value="${nextSession}">${nextSession}</option>`);
    }).always(function () {
        $('#ajaxLoader').hide();
    });

    // ------------------------------------------------------------
    // LOAD CLASSES
    // ------------------------------------------------------------
    $('#admissionYear').change(function () {

        let year = $(this).val();

        $('#admissionClass').html('<option value="">Select Class</option>');
        $('#classStatusMsg').html('');

        if (!year) return;

        let syear = year.replace(/-/g, '');

        $('#ajaxLoader').show();

        $.post(SFS.ajax, {
            action: 'load_classes',
            fromyear: year,
            sessyear: syear,
        }, function (res) {

            if (!Array.isArray(res) || res.length === 0) {
                console.warn('load_classes returned non-array, using class fallback:', res);
                res = [
                    { clsDisp: "NURSERY", openStatus: "1" },
                    { clsDisp: "LKG", openStatus: "1" },
                    { clsDisp: "UKG", openStatus: "1" },
                    { clsDisp: "1", openStatus: "1" },
                    { clsDisp: "2", openStatus: "0" },
                    { clsDisp: "3", openStatus: "0" },
                    { clsDisp: "4", openStatus: "0" },
                    { clsDisp: "5", openStatus: "0" },
                    { clsDisp: "6", openStatus: "0" },
                    { clsDisp: "7", openStatus: "0" },
                    { clsDisp: "8", openStatus: "0" },
                    { clsDisp: "9", openStatus: "0" },
                    { clsDisp: "10", openStatus: "0" },
                    { clsDisp: "11", openStatus: "0" },
                    { clsDisp: "12", openStatus: "0" }
                ];
            }

            res.forEach(c => {
                $('#admissionClass').append(`<option value="${c.clsDisp}" data-status="${c.openStatus}">${c.clsDisp}</option>`);
            });

        }, 'json').fail(function (xhr) {
            console.error('load_classes failed', xhr.status, xhr.responseText);
            $('#classStatusMsg').html(errorMsg('Could not load classes. Please try again later.'));
        }).always(function () {
            $('#ajaxLoader').hide();
        });
    });

    // ------------------------------------------------------------
    // LOAD ADMISSION CONTENT
    // ------------------------------------------------------------
    $('#admissionClass').change(function () {

        let year = $('#admissionYear').val();
        let cls  = $(this).val();
        let openStatus = $('#admissionClass option:selected').data("status");

        $('#classStatusMsg').html('');

        if (!year || !cls) return;

        $('#ajaxLoader').show();

        $.post(SFS.ajax, {
            action: 'sfs_load_content',
            year: year,
            class: cls,
            status: openStatus
        }, function (res) {

            if (!res || !res.success || !res.data) {
                $('#classStatusMsg').html(closedBoxHTML(cls, year));
                return;
            }

            if (res.data.status === 'open') {
                $('#classStatusMsg').html(res.data.content);
            } else {
                $('#classStatusMsg').html(closedBoxHTML(cls, year));
            }

        }, 'json').fail(function (xhr) {
            console.error('sfs_load_content failed', xhr.status, xhr.responseText);
            $('#classStatusMsg').html(errorMsg('Could not load admission details. Please try again later.'));
        }).always(function () {
            $('#ajaxLoader').hide();
        });
    });

    // ------------------------------------------------------------
    // "YES" (want to register) -> show info box with Register button
    // ------------------------------------------------------------
    $(document).on('click', '.yes', function () {

        let className = $(this).data('class');
        let session   = $(this).data('year');

        $('#registrationModal').remove();

        let modalHTML = `<div class="procedure-border section-hide" style="border: 1px solid #8a5a45; padding: 5px;">
                            <center><h2 class="dark-maroon text-center"><u>Admission for Session  ${session} </u></h2>
                            <p class="bold-part text-center">ADMISSION AT SFS</p>
                            </center>
                            <div class="procedure-detail">
                               <p class="text-justify">Limited seats, based on CBSE guidelines.
                                  <br>Sorry, no seats available for the Session ${session}. <br>
                                  Please register to get notifications in case of seats availability.
                                  <br>Admission forms will be available online and admission is subject to the availability of seats against withdrawal.
                               </p>
                               <center>
                               <div class="procedure-rectangle mt-4">
                                  <div class="btn btn-success btn-register" style="cursor:pointer; background-color: #ae152d; color: white;" data-class="${className}" data-year="${session}"> Register Here </div>
                               </div>
                               </center>
                            </div>
                         </div>`;

        $('#classStatusMsg').html(modalHTML);
    });

    // ------------------------------------------------------------
    // REGISTER MODAL
    // ------------------------------------------------------------
    $(document).on('click', '.btn-register', function () {

        let className = $(this).data('class');
        let session   = $(this).data('year');

        // remove existing modal if any
        $('#registrationModal').remove();

        let modalHTML = `
            <div id="registrationModal" class="custom-modal">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 style="color:white;">${className} Registration</h5>
                    <button class="close-btn closeRegModal">&times;</button>
                  </div>

                  <div class="modal-body">

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Mobile No</label>
                        <div class="mob-field-wrap">
                          <input type="text" id="txtmob" maxlength="10" class="form-control">
                          <span class="verify-tick">&#10004;</span>
                        </div>
                        <button type="button" id="sendOtpBtn" class="btn btn-info mt-2" style="display:none; margin-top: 5px;">
                          Get OTP
                        </button>
                        <div id="otpSection" style="display:none;">
                          <input type="text" id="txtotp" placeholder="Enter OTP" class="form-control mt-2" style=" margin-top: 5px;">
                          <button style=" margin-top: 5px;" class="btn btn-success mt-2" id="verifyOtpBtn">Verify OTP</button>
                        </div>
                      </div>
                      <div class="modal-col">
                        <label>Email Address</label>
                        <input type="text" id="txtmail" class="form-control">
                      </div>
                    </div>

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Student's Name</label>
                        <input type="text" id="txtstudentname" class="form-control">
                      </div>
                      <div class="modal-col">
                        <label>DOB</label>
                        <input type="text" id="txtdob" class="form-control">
                      </div>
                      <div class="modal-col">
                        <label>Class</label>
                        <input type="text" id="txtclass" value="${className}" class="form-control" readonly>
                      </div>
                    </div>

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Admission Year</label>
                        <input type="text" id="txtsession" value="${session}" class="form-control" readonly>
                      </div>
                      <div class="modal-col">
                        <label>Last Class Studied</label>
                        <input type="text" id="txtlastclass" class="form-control">
                      </div>
                      <div class="modal-col">
                        <label>Current School Name</label>
                        <input type="text" id="txtcurrentsch" class="form-control">
                      </div>
                    </div>

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Father's Name</label>
                        <input type="text" id="txtfather" class="form-control">
                      </div>
                      <div class="modal-col">
                        <label>Mother's Name</label>
                        <input type="text" id="txtmother" class="form-control">
                      </div>
                    </div>

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Any Remarks</label>
                        <textarea id="txtregrem" rows="2" class="form-control"></textarea>
                      </div>
                    </div>

                    <div class="modal-row">
                      <div class="modal-col">
                        <label>Marksheet 1 <span style="color: red;">*</span></label>
                        <input type="file" id="file1" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                      </div>
                      <div class="modal-col">
                        <label>Marksheet 2 <span style="color: red;">*</span></label>
                        <input type="file" id="file2" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                      </div>
                      <div class="modal-col">
                        <label>Marksheet 3 <span style="color: red;">*</span></label>
                        <input type="file" id="file3" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                      </div>
                    </div>

                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-secondary closeRegModal">Close</button>
                    <button class="btn btn-success" id="submitRegistration">Register Here</button>
                  </div>

                </div>
              </div>
            </div>`;

        $('body').append(modalHTML);
        $('#registrationModal').addClass('show');

        // Register button stays hidden until the mobile number is verified
        if (OTP_REQUIRED) {
            $('#submitRegistration').hide();
        }

        if (typeof flatpickr === 'function') {
            flatpickr("#txtdob", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                allowInput: true
            });
        }
    });

    // ------------------------------------------------------------
    // MOBILE NUMBER + OTP
    // ------------------------------------------------------------
    $(document).on('keyup', '#txtmob', function () {

        if (!OTP_REQUIRED) return;

        let mob = $(this).val();

        if (/^[6-9]\d{9}$/.test(mob)) {
            $('#sendOtpBtn').show();
        } else {
            $('#sendOtpBtn').hide();
            $('#otpSection').hide();
            $('#submitRegistration').hide();
        }
    });

    $(document).on('click', '#sendOtpBtn', function () {

        let mob = $('#txtmob').val();

        $('#ajaxLoader').show();

        $.post(SFS.ajax, {
            action: 'send_otp_api',
            mobile: mob
        }, function (res) {

            if (res && res.success) {
                alert(`OTP has been sent to your phone No ${mob}`);
                $('#otpSection').show();
                $('#sendOtpBtn').hide();
            } else {
                $('#txtmob').prop('readonly', false);
                alert((res && res.data) || "OTP failed");
            }

        }, 'json').fail(function (xhr) {
            console.error('send_otp_api failed', xhr.status, xhr.responseText);
            alert("Could not send OTP. Please try again.");
        }).always(function () {
            $('#ajaxLoader').hide();
        });
    });

    $(document).on('click', '#verifyOtpBtn', function () {

        let mob = $('#txtmob').val();
        let otp = $('#txtotp').val();

        if (!otp) {
            alert("Please enter the OTP");
            return;
        }

        $('#ajaxLoader').show();

        $.post(SFS.ajax, {
            action: 'verify_otp_api',
            mobile: mob,
            otp: otp
        }, function (res) {

            if (res && res.success) {
                alert("OTP has been verified");
                $('#txtmob')
                    .prop('readonly', true)
                    .addClass('mob-verified');

                $('.verify-tick').show();
                $('#submitRegistration').show();
                $('#sendOtpBtn').hide();
                $('#otpSection').hide();
            } else {
                alert("Invalid OTP");
            }

        }, 'json').fail(function (xhr) {
            console.error('verify_otp_api failed', xhr.status, xhr.responseText);
            alert("Could not verify OTP. Please try again.");
        }).always(function () {
            $('#ajaxLoader').hide();
        });
    });

    // ------------------------------------------------------------
    // CLOSE MODAL
    // ------------------------------------------------------------
    $(document).on('click', '.closeRegModal', function () {
        $('#registrationModal').removeClass('show').remove();
    });

    // ------------------------------------------------------------
    // SUBMIT REGISTRATION
    // ------------------------------------------------------------
    $(document).on('click', '#submitRegistration', function () {

        let $btn = $(this);

        let f1 = $('#file1')[0].files[0];
        let f2 = $('#file2')[0].files[0];
        let f3 = $('#file3')[0].files[0];

        // REQUIRED CHECK
        if (!f1 || !f2 || !f3) {
            alert("Please upload all three marksheets.");
            return;
        }

        // FILE TYPE CHECK
        if (!ALLOWED_TYPES.includes(f1.type) ||
            !ALLOWED_TYPES.includes(f2.type) ||
            !ALLOWED_TYPES.includes(f3.type)) {
            alert("Only JPG, PNG or PDF files allowed.");
            return;
        }

        // FILE SIZE CHECK
        if (f1.size > MAX_FILE_SIZE || f2.size > MAX_FILE_SIZE || f3.size > MAX_FILE_SIZE) {
            alert("File size must be below 2MB.");
            return;
        }

        let mob = $('#txtmob').val();

        if (!/^[6-9]\d{9}$/.test(mob)) {
            alert("Enter valid 10 digit mobile number");
            return;
        }

        let dob = $('#txtdob').val();

        if (!dob) {
            alert("Please select date of birth");
            return;
        }

        $btn.prop('disabled', true); // stop double clicks
        $('#ajaxLoader').show();

        let formData = new FormData();

        formData.append('action', 'sfs_submit_registration');
        formData.append('mobile', mob);
        formData.append('email', $('#txtmail').val());
        formData.append('student_name', $('#txtstudentname').val());
        formData.append('dob', dob);
        formData.append('class', $('#txtclass').val());
        formData.append('session', $('#txtsession').val());
        formData.append('last_class', $('#txtlastclass').val());
        formData.append('school', $('#txtcurrentsch').val());
        formData.append('father', $('#txtfather').val());
        formData.append('mother', $('#txtmother').val());
        formData.append('remarks', $('#txtregrem').val());
        formData.append('file1', f1);
        formData.append('file2', f2);
        formData.append('file3', f3);

        $.ajax({
            url: SFS.ajax,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res && res.success) {
                    $('#registrationModal').remove();
                    $('#classStatusMsg').html(`
                        <div class="closed-box" style="border: 1px solid #8a5a45; padding: 5px;">
                            <center>
                              <img src="/wp-content/uploads/2026/02/check.png" alt="Submitted" width="80px" />
                              <p style='color: green;'>Form submitted successfully</p>
                            </center>
                        </div>`);
                } else {
                    // keep the form open so the user can fix and retry
                    alert((res && res.data) || 'Submission failed');
                }
            },
            error: function (xhr) {
                console.error('sfs_submit_registration failed', xhr.status, xhr.responseText);
                alert('Submission failed. Please try again.');
            },
            complete: function () {
                $('#ajaxLoader').hide();
                $btn.prop('disabled', false);
            }
        });
    });
});
