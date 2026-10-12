jQuery(document).ready(function ($) {

    loadFee();

    function loadFee() {

        $.post(SFS_ADMIN.ajax, { action: 'sfs_load_fee' }, function (res) {

            let table = $('#feeTable');
            table.empty();

            if (!res.success || !res.data.rows.length) {
                table.html('<tr><td>No fee data available</td></tr>');
                return;
            }

            /* ---------------------------------
               FIND CURRENT SESSION (LATEST)
            --------------------------------- */
            let sessions = res.data.rows.map(r => r.session);
            let currentSession = sessions.sort().reverse()[0];

            table.append(`
                <tr>
                    <th style="width: 5%;">SL.NO</th>
                    <th style="width: 15%;">CLASS</th>
                    <th style="width: 25%;">NEW ADMISSION FEE <br>(ONE TIME FEE)</th>
                    <th style="width: 25%;">QUARTER-1 SCHOOL FEE</th>
                    <th style="width: 20%;">FOUNDATIONAL COURSE FEE <br>(Quarterly)</th>
                    <th style="width: 10%;">SESSION</th>
                </tr>
            `);

            let sl = 1;

            res.data.rows.forEach(row => {
                row.fees.forEach(fee => {

                    let isCurrent = row.session === currentSession;

                    table.append(`
                        <tr class="${isCurrent ? 'current-session' : ''}">
                            <td>${sl++}</td>
                            <td>${row.class}</td>
                            <td>${fee.admission_fee}</td>
                            <td>${fee.q1_fee}</td>
                            <td>${fee.add_on_fee || 'NA'}</td>
                            <td>${row.session}</td>
                        </tr>
                    `);
                });
            });
        });
    }
});
