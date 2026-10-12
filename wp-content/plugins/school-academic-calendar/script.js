jQuery(document).ready(function ($) {
    let currentMonthEvents = [];
    let currentSession = String($('.session-btn.active').data('session') || $('.session-btn.active').attr('data-session') || getCurrentAcademicSession());
    let selectedDate = null; // Currently selected day (e.g. "29-11-2025")

    // ===================================================================
    // Build Month Tabs + Auto-select correct month
    // ===================================================================
    function buildMonthTabsAndLoad(session) {
        const activeSession = session ? String(session) : getCurrentAcademicSession();
        const [fromYear, toYear] = activeSession.split('-');
        const from = parseInt(fromYear);
        const to = parseInt(toYear);
        let html = '';

        for (let m = 4; m <= 12; m++) {
            const monthName = new Date(from, m - 1, 1).toLocaleString('default', { month: 'short' });
            html += `<button class="month-tab" data-year="${from}" data-month="${m}">${monthName}</button>`;
        }
        for (let m = 1; m <= 3; m++) {
            const monthName = new Date(to, m - 1, 1).toLocaleString('default', { month: 'short' });
            html += `<button class="month-tab" data-year="${to}" data-month="${m}">${monthName}</button>`;
        }

        $('#monthTabs').html(html);

        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth() + 1;

        let yearToLoad, monthToLoad;

        if (session === getCurrentAcademicSession()) {
            if ((currentMonth >= 4 && currentYear === from) || (currentMonth <= 3 && currentYear === to)) {
                yearToLoad = currentYear;
                monthToLoad = currentMonth;
            }
        }
        if (!yearToLoad) {
            yearToLoad = from;
            monthToLoad = 4;
        }

        $(`#monthTabs .month-tab[data-year="${yearToLoad}"][data-month="${monthToLoad}"]`)
            .addClass('active')
            .siblings().removeClass('active');

        loadMonth(yearToLoad, monthToLoad);
    }

    function getCurrentAcademicSession() {
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth() + 1;
        return month >= 4 ? `${year}-${year + 1}` : `${year - 1}-${year}`;
    }

    // ===================================================================
    // Load Month Events
    // ===================================================================
    function loadMonth(year, month) {
        const monthPadded = String(month).padStart(2, '0');
        $('#calendarGrid').html('<div class="loading">Loading events...</div>');
        $('#eventsList').empty().append('<p class="text-center text-muted">Loading events...</p>');

        const activeSession = currentSession ? String(currentSession) : getCurrentAcademicSession();
        const [fromyear, toyear] = activeSession.split('-');

        $.post(ac_ajax.ajax_url, {
            action: 'get_month_events',
            nonce: ac_ajax.nonce,
            fromyear: fromyear,
            toyear: toyear,
            monthid: monthPadded
        }, function (res) {
            if (res.success && res.data) {
                currentMonthEvents = res.data;
                renderCalendar(res.data, year, month);

                // Auto-select today or first day with event
                const today = new Date();
                const todayStr = `${String(today.getDate()).padStart(2,'0')}-${String(today.getMonth()+1).padStart(2,'0')}-${today.getFullYear()}`;
                const hasTodayEvent = res.data.some(e => e.eDate === todayStr);

                const dateToSelect = hasTodayEvent ? todayStr : (res.data[0]?.eDate || null);
                if (dateToSelect) {
                    selectDate(dateToSelect);
                } else {
                    showAllEvents(); // fallback
                }
            } else {
                const errorMsg = (res && res.data) ? res.data : 'No events found';
                $('#calendarGrid').html(`<div class="no-events">${errorMsg}</div>`);
                $('#eventsList').html(`<p class="text-center text-danger">${errorMsg}</p>`);
            }
        }).fail(function (xhr, status, error) {
            const failMsg = 'AJAX Connection Failed (' + (xhr.status || status || error) + ')';
            $('#calendarGrid').html(`<div class="no-events">${failMsg}</div>`);
            $('#eventsList').html(`<p class="text-center text-danger">${failMsg}</p>`);
        });
    }

    // ===================================================================
    // Render Calendar Grid
    // ===================================================================
    function renderCalendar(events, year, month) {
        const daysInMonth = new Date(year, month, 0).getDate();
        const firstDayOfWeek = new Date(year, month - 1, 1).getDay();

        const eventMap = {};
        events.forEach(item => {
            const day = item.eDate.split('-')[0];
            if (!eventMap[day]) eventMap[day] = [];
            eventMap[day].push(...item.eventName);
        });

        let html = '';
        const headers = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        headers.forEach(h => html += `<div class="calendar-day-header">${h}</div>`);

        for (let i = 0; i < firstDayOfWeek; i++) html += '<div></div>';

        const today = new Date();
        const ty = today.getFullYear();
        const tm = today.getMonth() + 1;
        const td = today.getDate();

        for (let day = 1; day <= daysInMonth; day++) {
            const dayPadded = String(day).padStart(2, '0');
            const monthPadded = String(month).padStart(2, '0');
            const dateKey = `${dayPadded}-${monthPadded}-${year}`;

            const hasEvent = !!eventMap[dayPadded];
            const isToday = (year == ty && month == tm && day == td);

            html += `
                <div class="calendar-day ${hasEvent ? 'has-event' : ''} ${isToday ? 'today' : ''} ${selectedDate === dateKey ? 'selected' : ''}"
                     data-date="${dateKey}">
                    <div class="day-number">${day}</div>
                    ${hasEvent ? `` : ''}
                </div>`;
        }

        $('#calendarGrid').html(html);
    }

    // ===================================================================
    // Show Events for Selected Date
    // ===================================================================
    function selectDate(dateKey) {
        selectedDate = dateKey;
        $('.calendar-day').removeClass('selected');
        $(`.calendar-day[data-date="${dateKey}"]`).addClass('selected');
    
        // Find all events for this date
        const dateEvents = currentMonthEvents
            .filter(item => item.eDate === dateKey)
            .flatMap(item => item.eventName || []);
    
        const [d, m, y] = dateKey.split('-');
        const niceDate = new Date(y, m - 1, d).toLocaleDateString('en-GB', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    
        let html = `
        <div class="event-card">
            <div class="event-date-box">
                <div class="event-day">${d}</div>
                <div class="event-month">${new Date(y, m - 1).toLocaleString('en', { month: 'short' })}</div>
            </div>
            <div class="event-content">
        `;

    
        if (dateEvents.length === 0) {
            html += '<p class="no-events-msg">No events on this date</p>';
        } else {
            // Group events by typeCode
            const groupedByType = {};
    
            dateEvents.forEach(ev => {
                const type = ev.typeCode || 'OTHER';
                if (!groupedByType[type]) {
                    groupedByType[type] = [];
                }
                groupedByType[type].push(ev);
            });
    
            // Optional: Sort types in preferred order
            const typeOrder = ['HOLIDAY', 'BIRTHDAY', 'MEETING', 'ANNIVERSARY', 'REMINDER', 'OTHER'];
            const sortedTypes = Object.keys(groupedByType).sort((a, b) => {
                const ia = typeOrder.indexOf(a);
                const ib = typeOrder.indexOf(b);
                return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
            });
    
            sortedTypes.forEach(type => {
                const events = groupedByType[type];
                const isHoliday = type === 'HOLIDAY';
                const typeColor = getTypeColor(type);
    
                html += `
                    <div class="event-type-group">
                        <div class="type-header" style="border-left:4px solid ${typeColor}; padding-left:8px; margin:16px 0 8px; font-weight:600;">
                            ${formatTypeName(type)}
                            ${!isHoliday ? `<span class="type-badge" style="background:${typeColor}20; color:${typeColor}; font-size:0.75em; padding:2px 6px; border-radius:4px; margin-left:8px;"></span>` : ''}
                        </div>
                        <div class="events-list">`;
                            
                        events.forEach(ev => {
                            html += `
                                <div class="event-line">
                                    ? ${ev.eventDetails || ev.eventName}
                                </div>
                            `;
                        });

    
                html += `</div>`;
            });
        }
    
        $('#eventsList').html(html);
    }
    
    // Reuse the same helpers as in showAllEvents()
    function getTypeColor(type) {
        const map = {
            HOLIDAY: '#dc2626',
            BIRTHDAY: '#ec4899',
            MEETING: '#6366f1',
            ANNIVERSARY: '#f59e0b',
            REMINDER: '#10b981',
            OTHER: '#6b7280'
        };
        return map[type] || '#6b7280';
    }
    
    function formatTypeName(type) {
        const names = {
            HOLIDAY: 'Holiday',
            BIRTHDAY: 'Birthday',
            MEETING: 'Meeting',
            ANNIVERSARY: 'Anniversary',
            REMINDER: 'Reminder',
            OTHER: 'Other'
        };
        return names[type] || type.charAt(0) + type.slice(1).toLowerCase();
    }

    // ===================================================================
    // Show All Events of Month
    // ===================================================================
    function showAllEvents() {
        selectedDate = null;
        $('.calendar-day').removeClass('selected');
    
        // Step 1: Group by date ? then by typeCode
        const groupedByDate = {};
    
        currentMonthEvents.forEach(item => {
            const date = item.eDate; // "01-12-2025"
    
            if (!groupedByDate[date]) {
                groupedByDate[date] = {};
            }
    
            item.eventName.forEach(ev => {
                const type = ev.typeCode || 'OTHER';
    
                if (!groupedByDate[date][type]) {
                    groupedByDate[date][type] = [];
                }
                groupedByDate[date][type].push(ev);
            });
        });
    
        // Step 2: Sort dates chronologically
        const sortedDates = Object.keys(groupedByDate).sort((a, b) => {
            const [d1, m1, y1] = a.split('-');
            const [d2, m2, y2] = b.split('-');
            return new Date(y1, m1 - 1, d1) - new Date(y2, m2 - 1, d2);
        });
    
        let html = `<h3 class="custom-events-title">All Events This Month</h3>`;
    
        if (sortedDates.length === 0) {
            html += `<p class="no-events-msg">No events this month</p>`;
        } else {
    
            sortedDates.forEach(dateKey => {
                const [d, m, y] = dateKey.split('-');
                const niceDate = new Date(y, m - 1, d);
    
                const day = d;
                const month = niceDate.toLocaleString('en', { month: 'short' });
                const weekday = niceDate.toLocaleString('en', { weekday: 'short' });
    
                html += `
                    <div class="custom-event-card">
                        <div class="custom-event-date">
                            <div class="custom-day">${day}</div>
                            <div class="custom-month">${month}</div>
                            <div class="custom-weekday">${weekday}</div>
                        </div>
                        <div class="custom-event-content">
                `;
    
                // Preferred order of event types
                const typeOrder = ['HOLIDAY', 'INSTRUCTIONAL', 'MEETING', 'EXAM', 'TRAINING', 'OTHER'];
                const types = Object.keys(groupedByDate[dateKey]);
    
                types.sort((a, b) => {
                    const ia = typeOrder.indexOf(a);
                    const ib = typeOrder.indexOf(b);
                    return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
                });
    
                types.forEach(type => {
                    const events = groupedByDate[dateKey][type];
    
                    html += `
                        <div class="custom-event-type">
                            <h4>${formatTypeName(type)}</h4>
                            <ul>
                    `;
    
                    events.forEach(ev => {
                        html += `
                            <li>${ev.eventDetails || ev.eventName}</li>
                        `;
                    });
    
                    html += `
                            </ul>
                        </div>
                    `;
                });
    
                html += `
                        </div>
                    </div>
                `;
            });
        }
    
        $('#eventsList').html(html);
    }


    // ===================================================================
    // Display Events in List Below Calendar
    // ===================================================================
    function displayEvents(events, title) {
        let html = `<h3 class="events-title">${title}</h3>`;

        if (events.length === 0) {
            html += '<p class="no-events-msg">No events on this date</p>';
        } else {
            events.forEach(ev => {
                const isHoliday = ev.typeCode === 'HOLIDAY';
                html += `
                    <div class="event-card ${isHoliday ? 'holiday' : ''}">
                        <div class="event-date-badge">${ev.date ? ev.date.split('-').reverse().join('-') : ''}</div>
                        <div class="event-content">
                            <strong>${ev.eventDetails || ev.eventDetails}</strong>
                            <span class="event-type">${ev.typeCode}</span>
                        </div>
                    </div>`;
            });
        }

        $('#eventsList').html(html);
    }

    function formatDate(dateKey) {
        const [d, m, y] = dateKey.split('-');
        return new Date(y, m - 1, d).toLocaleDateString('en-GB', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
    }

    // ===================================================================
    // INITIAL LOAD
    // ===================================================================
    buildMonthTabsAndLoad(currentSession);

    // ===================================================================
    // SESSION SWITCHER
    // ===================================================================
    $(document).on('click', '.session-btn', function () {
        if ($(this).hasClass('active')) return;
        $('.session-btn').removeClass('active');
        $(this).addClass('active');
        currentSession = String($(this).data('session') || $(this).attr('data-session') || '');
        buildMonthTabsAndLoad(currentSession);
    });

    // ===================================================================
    // MONTH TAB CLICK
    // ===================================================================
    $(document).on('click', '.month-tab', function () {
        if ($(this).hasClass('active')) return;
        $('.month-tab').removeClass('active');
        $(this).addClass('active');
        loadMonth($(this).data('year'), $(this).data('month'));
    });

    // ===================================================================
    // DAY CLICK ? Show Events Below
    // ===================================================================
    $(document).on('click', '.calendar-day.has-event', function () {
        const dateKey = $(this).data('date');
        selectDate(dateKey);
    });

    // ===================================================================
    // SHOW ALL EVENTS BUTTON
    // ===================================================================
    $(document).on('click', '#showAllEventsBtn', function () {
        showAllEvents();
    });
});