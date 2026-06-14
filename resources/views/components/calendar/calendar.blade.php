<div>
    <style>
        .fc { font-family: 'Inter', sans-serif; }
        .fc-toolbar-title { font-size: 1rem !important; font-weight: 700 !important; color: #1e293b; }
        .fc-button { font-size: 0.75rem !important; font-weight: 600 !important; }
        .fc-button-primary { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }
        .fc-button-primary:hover { background-color: #4338ca !important; border-color: #4338ca !important; }
        .fc-button-primary:not(.fc-button-active) { background-color: #fff !important; border-color: #e2e8f0 !important; color: #475569 !important; }
        .fc-button-primary:not(.fc-button-active):hover { background-color: #f8fafc !important; }
        .fc-button-active { background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: #fff !important; }
        .fc-event { border-radius: 4px !important; border: none !important; font-size: 0.72rem !important; padding: 2px 6px !important; }
        .fc-daygrid-event { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fc-col-header-cell { background: #f8fafc; }
        .fc-col-header-cell-cushion { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Booking Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Reservation overview — Cloudbeds style</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 text-xs">
                <span class="w-3 h-3 rounded-sm bg-indigo-500 inline-block"></span><span class="text-gray-500">Confirmed</span>
                <span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block ml-2"></span><span class="text-gray-500">Checked-In</span>
                <span class="w-3 h-3 rounded-sm bg-amber-500 inline-block ml-2"></span><span class="text-gray-500">Reserved</span>
            </div>
            <a href="{{ route('reservations.index') }}" class="btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Booking
            </a>
        </div>
    </div>

    <div class="pms-card p-5">
        <div id="calendar" x-init="
            const calendar = new FullCalendar.Calendar($el, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,listWeek'
                },
                events: {{ json_encode($events) }},
                height: 680,
                eventClick: function(info) {
                    const p = info.event.extendedProps;
                    Swal.fire({
                        title: info.event.title,
                        html: `<div style='text-align:left;font-size:14px'>
                            <p><strong>Guest:</strong> ${p.guest}</p>
                            <p><strong>Room:</strong> ${p.room}</p>
                            <p><strong>Status:</strong> ${p.status}</p>
                            <p><strong>Check-In:</strong> ${info.event.startStr}</p>
                            <p><strong>Check-Out:</strong> ${info.event.endStr}</p>
                        </div>`,
                        showCloseButton: true,
                        showConfirmButton: false,
                        width: 400,
                    });
                },
                dayCellClassNames: function(arg) {
                    return arg.isToday ? ['bg-indigo-50'] : [];
                },
            });
            calendar.render();
        "></div>
    </div>
</div>