jQuery(document).ready(function ($) {
    const SELECTED_YEAR  = typeof SFS_ADMIN.year !== 'undefined' ? SFS_ADMIN.year : '';
    const SELECTED_CLASS = typeof SFS_ADMIN.class !== 'undefined' ? SFS_ADMIN.class : '';

    // LOAD YEARS
    $.post(SFS_ADMIN.ajax, { action: 'load_years' }, function (res) {

        let yearSel = $('#sfs_year');
        yearSel.html('<option value="">Select Year</option>');

        if (!Array.isArray(res) || res.length === 0) {
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
            let selected = (y.name === SELECTED_YEAR) ? 'selected' : '';
            if (!hasCurrent || y.isCurrent == "1") {
                yearSel.append(`<option ${selected} value="${y.name}">${y.name}</option>`);
            }
        });

        if (SFS_ADMIN.year) {
            yearSel.trigger('change');
        }
    }, 'json').fail(function () {
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth() + 1;
        const currentSession = m >= 4 ? `${y}-${y + 1}` : `${y - 1}-${y}`;
        const nextSession = m >= 4 ? `${y + 1}-${y + 2}` : `${y}-${y + 1}`;
        $('#sfs_year').append(`<option value="${currentSession}">${currentSession}</option>`);
        $('#sfs_year').append(`<option value="${nextSession}">${nextSession}</option>`);
    });

    // LOAD CLASSES
    $('#sfs_year').on('change', function () {

        let year = $(this).val();
        if (!year) {
            $('#sfs_class').html('<option value="">Select Year First</option>');
            return;
        }

        let sessyear = year.replace(/-/g, '');

        let classSel = $('#sfs_class');
        classSel.html('<option>Loading...</option>');

        $.post(SFS_ADMIN.ajax, {
            action: 'load_classes',
            fromyear: year,
            sessyear: sessyear
        }, function (res) {

            classSel.html('<option value="">Select Class</option>');

            if (!Array.isArray(res) || res.length === 0) {
                res = [
                    { clsDisp: "NURSERY" }, { clsDisp: "LKG" }, { clsDisp: "UKG" },
                    { clsDisp: "1" }, { clsDisp: "2" }, { clsDisp: "3" },
                    { clsDisp: "4" }, { clsDisp: "5" }, { clsDisp: "6" },
                    { clsDisp: "7" }, { clsDisp: "8" }, { clsDisp: "9" },
                    { clsDisp: "10" }, { clsDisp: "11" }, { clsDisp: "12" }
                ];
            }

            res.forEach(c => {
                let selected = (c.clsDisp === SELECTED_CLASS) ? 'selected' : '';
                classSel.append(
                    `<option ${selected} value="${c.clsDisp}">${c.clsDisp}</option>`
                );
            });
        }, 'json');
    });
    
    $(document).on('click', '.sfs-toggle-status', function () {

        let btn     = $(this);
        let postId  = btn.data('id');
        let status  = btn.data('status');
        
        console.log("btn",btn);

        btn.prop('disabled', true).text('Updating...');

        $.post(ajaxurl, {
            action: 'sfs_toggle_admission_status',
            post_id: postId,
            status: status
        }, function (res) {

            if (res.success) {
                location.reload(); // simple & safe
            } else {
                alert(res.data || 'Error');
                btn.prop('disabled', false);
            }
        });
    });

});
