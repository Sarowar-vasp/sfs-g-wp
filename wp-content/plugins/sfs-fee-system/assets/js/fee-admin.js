jQuery(document).ready(function ($) {

    const SELECTED_YEAR  = SFS_ADMIN.year || '';
    const SELECTED_CLASS = SFS_ADMIN.class || '';

    /* ------------------------------
       LOAD ACADEMIC SESSIONS
    ------------------------------ */
    $.post(SFS_ADMIN.ajax, { action: 'load_years' }, function (res) {

        let yearSel = $('#sfs_fee_year');
        yearSel.html('<option value="">Select Session</option>');

        if (!res) return;
        let selected = ("2025-2026" === SELECTED_YEAR) ? 'selected' : '';
        yearSel.append(
            `<option ${selected} value="2025-2026">2025-2026</option>`
        );
        res.forEach(y => {
            selected = (y.name === SELECTED_YEAR) ? 'selected' : '';
            if(y.isCurrent == "1") {
                yearSel.append(
                    `<option ${selected} value="${y.name}">${y.name}</option>`
                );
            }
        });

        if (SELECTED_YEAR) {
            yearSel.trigger('change');
        }
    });

    /* ------------------------------
       LOAD CLASSES ON SESSION CHANGE
    ------------------------------ */
    $('#sfs_fee_year').on('change', function () {

        let year = $(this).val();
        let sessyear = year.replace(/-/g, '');

        let classSel = $('#sfs_fee_class');
        classSel.html('<option>Loading...</option>');

        if (!year) return;

        $.post(SFS_ADMIN.ajax, {
            action: 'load_classes',
            fromyear: year,
            sessyear: sessyear
        }, function (res) {

            classSel.html('<option value="">Select Class</option>');

            if (!res) return;

            res.forEach(c => {
                let selected = (c.clsDisp === SELECTED_CLASS) ? 'selected' : '';
                classSel.append(
                    `<option ${selected} value="${c.clsDisp}">
                        ${c.clsDisp}
                    </option>`
                );
            });
        });
    });
    
    /* ================================
       ADD FEE ROW
    ================================= */
    $(document).on('click', '#addFeeRow', function (e) {
        e.preventDefault();

        let box = $('#feeRepeater');
        let index = box.children('.fee-row').length;

        box.append(`
            <div class="fee-row">
                <input type="text"
                       name="fee_items[${index}][admission_fee]"
                       placeholder="New Admission Fee">

                <input type="text"
                       name="fee_items[${index}][q1_fee]"
                       placeholder="Quarter-1 Fee">

                <input type="text"
                       name="fee_items[${index}][add_on_fee]"
                       placeholder="Add-on Fee">

                <button type="button" class="remove-fee">✕</button>
            </div>
        `);



    });

    /* ================================
       REMOVE FEE ROW
    ================================= */
    $(document).on('click', '.remove-fee', function () {
        let box = $('#feeRepeater');

        // prevent deleting last row
        if (box.children('.fee-row').length <= 1) {
            alert('At least one fee item is required.');
            return;
        }

        $(this).closest('.fee-row').remove();
    });
    
});
