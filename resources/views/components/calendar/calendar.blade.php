<div>
    <style>
        .fc { font-family: 'Inter', sans-serif; }
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 800 !important; color: #0f172a !important; letter-spacing: -0.02em; }
        .fc-button { font-size: 0.75rem !important; font-weight: 600 !important; border-radius: 8px !important; text-transform: capitalize !important; padding: 6px 12px !important; }
        .fc-button-primary { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }
        .fc-button-primary:hover { background-color: #4338ca !important; border-color: #4338ca !important; }
        .fc-button-primary:not(.fc-button-active) { background-color: #fff !important; border-color: #e2e8f0 !important; color: #475569 !important; }
        .fc-button-primary:not(.fc-button-active):hover { background-color: #f8fafc !important; }
        .fc-button-active { background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: #fff !important; }
        .fc-event { border-radius: 6px !important; border: none !important; font-size: 0.75rem !important; font-weight: 600 !important; padding: 4px 8px !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .fc-daygrid-event { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fc-col-header-cell { background: #f8fafc; border-bottom: 1px solid #e2e8f0 !important; }
        .fc-col-header-cell-cushion { font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 4px !important; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9 !important; }
        .fc .fc-daygrid-day.fc-day-today { background-color: rgba(99, 102, 241, 0.04) !important; }
        .fc-daygrid-day-number { font-size: 0.785rem !important; font-weight: 700 !important; color: #64748b !important; padding: 8px 10px !important; }
        .fc-daygrid-day-frame { min-height: 100px !important; }
        .fc-toolbar { margin-bottom: 1.5rem !important; gap: 0.5rem; flex-wrap: wrap; }
    </style>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Booking Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Interactive visual timeline of all reservations and guest occupancy</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-plus text-xs"></i> New Reservation
        </a>
    </div>

    {{-- Calendar Wrapper Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-calendar-alt text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Schedule Planner</h3>
                    <p class="text-[10px] text-slate-400">Drag, view, and organize reservations across dates</p>
                </div>
            </div>
            
            <div class="flex items-center flex-wrap gap-3">
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-600 bg-slate-50/80 border border-slate-100 px-3 py-1.5 rounded-xl">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Legend:</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shadow-sm shadow-indigo-200"></span>
                        Confirmed
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                        Checked-In
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm shadow-amber-200"></span>
                        Reserved
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm shadow-slate-200"></span>
                        Checked-Out
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
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
                        
                        let statusBadge = '';
                        if (p.status === 'Checked-In') {
                            statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100'><span class='w-1.5 h-1.5 rounded-full bg-emerald-500'></span>Checked-In</span>`;
                        } else if (p.status === 'Confirmed') {
                            statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100'><span class='w-1.5 h-1.5 rounded-full bg-indigo-500'></span>Confirmed</span>`;
                        } else if (p.status === 'Reserved') {
                            statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100'><span class='w-1.5 h-1.5 rounded-full bg-amber-500'></span>Reserved</span>`;
                        } else {
                            statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-50 text-slate-700 border border-slate-100'><span class='w-1.5 h-1.5 rounded-full bg-slate-400'></span>Checked-Out</span>`;
                        }

                        Swal.fire({
                            title: `<div class='text-left font-black text-slate-800 text-lg border-b border-slate-100 pb-3 flex items-center gap-2'>
                                        <div class='w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100'><i class='fas fa-calendar-check text-sm'></i></div>
                                        Reservation Details
                                    </div>`,
                            html: `<div class='text-left font-sans text-sm text-slate-600 space-y-3 pt-3'>
                                <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                    <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Guest Name</span>
                                    <span class='font-bold text-slate-800'>${p.guest || 'N/A'}</span>
                                </div>
                                <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                    <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Room Assigned</span>
                                    <span class='font-bold text-indigo-600 bg-indigo-50/50 px-2 py-0.5 rounded border border-indigo-100/50 text-xs'>Room ${p.room || 'N/A'}</span>
                                </div>
                                <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                    <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Status</span>
                                    <span>${statusBadge}</span>
                                </div>
                                <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                    <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Check-In</span>
                                    <span class='font-semibold text-slate-700'>${info.event.startStr}</span>
                                </div>
                                <div class='flex justify-between items-center py-1'>
                                    <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Check-Out</span>
                                    <span class='font-semibold text-slate-700'>${info.event.endStr}</span>
                                </div>
                                <div class='pt-4 flex gap-2'>
                                    <a href='/reservations?search=\${encodeURIComponent(p.guest)}' class='w-full text-center inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors'>
                                        <i class='fas fa-external-link-alt text-[10px]'></i> View Reservation
                                    </a>
                                </div>
                            </div>`,
                            showCloseButton: true,
                            showConfirmButton: false,
                            width: 380,
                            customClass: {
                                popup: 'rounded-2xl border border-slate-100 shadow-xl p-5',
                                closeButton: 'focus:outline-none focus:ring-0'
                            }
                        });
                    },
                    dayCellClassNames: function(arg) {
                        return arg.isToday ? ['bg-indigo-50'] : [];
                    },
                });
                calendar.render();
                $watch('sidebarOpen', () => {
                    setTimeout(() => calendar.updateSize(), 200);
                });
                $cleanup(() => calendar.destroy());
            "></div>
        </div>
    </div>
</div>