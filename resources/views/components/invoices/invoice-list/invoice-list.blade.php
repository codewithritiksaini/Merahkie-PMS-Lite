
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Invoices</h1>
            <p class="text-sm text-gray-500 mt-0.5">View and download guest invoices</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600"><i class="fas fa-dollar-sign text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($totalAmount, 0) }}</p>
                <p class="text-xs text-gray-500">Total Revenue</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-check-circle text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $paidCount }}</p>
                <p class="text-xs text-gray-500">Paid Invoices</p>
            </div>
        </div>
    </div>

    <div class="pms-card">
        <div class="pms-card-header flex-wrap gap-3">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search by guest..." class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <select wire:model.live="statusFilter" class="pms-select py-1.5 text-sm w-40">
                <option value="">All Status</option>
                <option>Paid</option>
                <option>Pending</option>
                <option>Cancelled</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Invoice #</th><th>Guest</th><th>Room</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    @php $res = optional(optional($inv->checkout)->reservation); @endphp
                    <tr>
                        <td><span class="text-xs font-mono text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">#{{ $inv->id }}</span></td>
                        <td class="font-medium text-gray-800">{{ optional($res->guest)->name ?? '—' }}</td>
                        <td class="text-gray-600">{{ optional($res->room)->room_number ?? '—' }}</td>
                        <td class="font-semibold text-gray-900">${{ number_format(optional($inv->checkout)->total_amount, 2) }}</td>
                        <td>
                            <span class="{{ ($inv->status ?? 'Paid') === 'Paid' ? 'badge-checkedin' : 'badge-maintenance' }}">
                                {{ $inv->status ?? 'Paid' }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-xs">{{ $inv->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('invoice.view', $inv->id) }}" target="_blank"
                                   class="btn-icon text-indigo-500 hover:bg-indigo-50" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('invoice.download', $inv->id) }}" target="_blank"
                                   class="btn-icon text-emerald-500 hover:bg-emerald-50" title="Download PDF">
                                    <i class="fas fa-file-pdf text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <i class="fas fa-file-invoice text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No invoices found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>