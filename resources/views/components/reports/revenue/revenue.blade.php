
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Revenue Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Annual revenue analytics</p>
        </div>
        <select wire:model.live="year" class="pms-select py-1.5 text-sm w-28">
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600"><i class="fas fa-chart-line text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">${{ number_format($totalAnnual, 0) }}</p><p class="text-xs text-gray-500">Total {{ $year }}</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-calendar-alt text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">${{ number_format($currentMonth, 0) }}</p><p class="text-xs text-gray-500">This Month</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $growth >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                <i class="fas {{ $growth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $growth >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                </p>
                <p class="text-xs text-gray-500">vs Last Month</p>
            </div>
        </div>
    </div>

    {{-- Monthly bar chart --}}
    <div class="pms-card p-5 mb-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-5">Monthly Revenue — {{ $year }}</h3>
        <div class="flex items-end gap-2 h-44">
            @php $maxRev = collect($monthlyRevenue)->max('revenue') ?: 1; @endphp
            @foreach($monthlyRevenue as $m)
            <div class="flex flex-col items-center flex-1 gap-1.5">
                <span class="text-xs text-gray-500 font-medium">${{ $m['revenue'] > 0 ? number_format($m['revenue']/1000, 1).'k' : '0' }}</span>
                <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-600 to-indigo-400 transition-all duration-500 min-h-[4px]"
                     style="height: {{ max(4, ($m['revenue'] / $maxRev) * 160) }}px"
                     title="{{ $m['month'] }}: ${{ number_format($m['revenue'], 2) }} ({{ $m['count'] }} check-outs)"></div>
                <span class="text-xs text-gray-400">{{ $m['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Monthly table --}}
    <div class="pms-card">
        <div class="pms-card-header"><h3 class="text-sm font-semibold text-gray-800">Monthly Breakdown</h3></div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead><tr><th>Month</th><th>Check-Outs</th><th>Revenue</th><th>Share</th></tr></thead>
                <tbody>
                    @foreach($monthlyRevenue as $m)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $m['month'] }} {{ $year }}</td>
                        <td class="text-gray-600">{{ $m['count'] }}</td>
                        <td class="font-semibold text-gray-900">${{ number_format($m['revenue'], 2) }}</td>
                        <td>
                            @php $share = $totalAnnual > 0 ? round(($m['revenue'] / $totalAnnual) * 100, 1) : 0; @endphp
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 max-w-[80px]">
                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ $share }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $share }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td class="text-gray-800">Total {{ $year }}</td>
                        <td class="text-gray-600">{{ collect($monthlyRevenue)->sum('count') }}</td>
                        <td class="text-indigo-700">${{ number_format($totalAnnual, 2) }}</td>
                        <td class="text-gray-400 text-xs">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>