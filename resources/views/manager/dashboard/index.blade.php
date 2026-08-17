@extends('manager.layouts.app')

@section('title', 'Analytics Dashboard')

@push('styles')
<style>
    .kpi-card {
        background: white;
        border-radius: 16px;
        padding: 1.35rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 1.15rem;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s;
        border: 1px solid #f1f5f9;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    .kpi-value {
        font-size: 1.7rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 3px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.35rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .chart-card-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Analytics Dashboard</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Tổng quan chỉ số hoạt động &amp; Báo cáo chất lượng hỗ trợ kỹ thuật TLU</p>
    </div>
    <form method="GET" action="{{ route('manager.dashboard') }}" class="d-flex align-items-center gap-2 flex-wrap" id="dashboardFilterForm">
        {{-- Radio Switch Lọc theo Tháng / Ngày --}}
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="filter_type" id="filterTypeMonth" value="month" {{ $filterType === 'month' ? 'checked' : '' }} onchange="toggleFilterInputs(this.value)">
            <label class="btn btn-outline-primary fw-bold" for="filterTypeMonth">Theo Tháng</label>

            <input type="radio" class="btn-check" name="filter_type" id="filterTypeDate" value="date" {{ $filterType === 'date' ? 'checked' : '' }} onchange="toggleFilterInputs(this.value)">
            <label class="btn btn-outline-primary fw-bold" for="filterTypeDate">Theo Ngày</label>
        </div>

        {{-- Input Lọc Theo Tháng --}}
        <div id="monthInputContainer" class="input-group input-group-sm {{ $filterType === 'date' ? 'd-none' : '' }}" style="width: auto;">
            <span class="input-group-text bg-primary text-white border-primary rounded-start-pill px-2.5">
                <i class="bi bi-calendar3"></i>
            </span>
            <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                   class="form-control form-control-sm border-primary text-primary fw-bold rounded-end-pill px-3 py-1.5"
                   style="font-size:0.83rem; cursor:pointer; background:#f0f7ff;" title="Bấm để chọn Tháng/Năm thống kê">
        </div>

        {{-- Input Lọc Theo Ngày Cụ Thể --}}
        <div id="dateInputContainer" class="input-group input-group-sm {{ $filterType === 'month' ? 'd-none' : '' }}" style="width: auto;">
            <span class="input-group-text bg-primary text-white border-primary rounded-start-pill px-2.5">
                <i class="bi bi-calendar-date"></i>
            </span>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                   class="form-control form-control-sm border-primary text-primary fw-bold rounded-end-pill px-3 py-1.5"
                   style="font-size:0.83rem; cursor:pointer; background:#f0f7ff;" title="Bấm để chọn Ngày cụ thể thống kê">
        </div>
    </form>
</div>

{{-- ── 1. TOP 4 KPI CARDS ── --}}
<div class="row g-3 mb-4">

    {{-- KPI 1: Tổng ticket trong tháng --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
            <div>
                <div class="kpi-value">{{ number_format($totalTicketsMonth) }}</div>
                <div class="kpi-label">Tổng Ticket ({{ $filterLabel }})</div>
            </div>
        </div>
    </div>

    {{-- KPI 2: Tỷ lệ đúng SLA --}}
    @php
        if ($slaRate >= 80) {
            $slaBgColor   = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            $slaTextColor = 'text-success';
        } elseif ($slaRate >= 50) {
            $slaBgColor   = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
            $slaTextColor = 'text-warning';
        } else {
            $slaBgColor   = 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)';
            $slaTextColor = 'text-danger';
        }
    @endphp
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: {{ $slaBgColor }};">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="kpi-value {{ $slaTextColor }}">{{ $slaRate }}%</div>
                <div class="kpi-label">Tỷ lệ xử lý đúng SLA</div>
            </div>
        </div>
    </div>

    {{-- KPI 3: Ticket trễ hạn chưa xong --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);">
                <i class="bi bi-alarm-fill"></i>
            </div>
            <div>
                <div class="kpi-value text-danger">{{ $overdueCount }}</div>
                <div class="kpi-label">Ticket quá hạn chưa xong</div>
            </div>
        </div>
    </div>

    {{-- KPI 4: Điểm hài lòng trung bình --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div class="kpi-value text-warning">{{ $avgRating }} <span style="font-size:1rem;" class="text-muted fw-normal">/ 5.0</span></div>
                <div class="kpi-label">Điểm hài lòng trung bình</div>
            </div>
        </div>
    </div>

</div>

{{-- ── 2. CHARTS AREA ── --}}
<div class="row g-3 mb-4">

    {{-- Bar Chart: Sự cố theo Danh mục --}}
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Số lượng sự cố theo Danh mục</h3>
                <span class="badge bg-light text-secondary border" style="font-size:0.75rem;">Phân loại lỗi</span>
            </div>
            <div style="position:relative; height:290px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart: Sự cố theo Khoa / Phòng ban --}}
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3><i class="bi bi-pie-chart-fill me-2 text-info"></i>Sự cố theo Khoa / Đơn vị</h3>
                <span class="badge bg-light text-secondary border" style="font-size:0.75rem;">Nguồn phát sinh</span>
            </div>
            <div style="position:relative; height:290px; display:flex; align-items:center; justify-content:center;">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ── 3. RECENT TICKETS TABLE ── --}}
<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
        <h3 class="h6 fw-bold mb-0" style="color:#0f172a;"><i class="bi bi-clock-history me-2 text-primary"></i>Phiếu sự cố mới gửi gần đây</h3>
        <a href="{{ route('manager.tickets.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="font-size:0.78rem;">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                <tr>
                    <th>Mã</th>
                    <th>Tiêu đề sự cố</th>
                    <th>Người báo</th>
                    <th>Danh mục</th>
                    <th>KTV phụ trách</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentTickets as $t)
                <tr>
                    <td class="font-monospace text-muted fw-bold">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-medium text-dark">{{ Str::limit($t->title, 40) }}</td>
                    <td>
                        <div class="fw-bold">{{ $t->requester->name }}</div>
                        <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $t->category?->name ?? '—' }}</span></td>
                    <td>
                        @if ($t->currentAssignee)
                            <span class="text-success fw-medium"><i class="bi bi-person-check me-1"></i>{{ $t->currentAssignee->name }}</span>
                        @else
                            <span class="text-warning fw-medium"><i class="bi bi-hourglass me-1"></i>Chưa phân công</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-status badge-{{ $t->status }}">
                            @switch($t->status)
                                @case('OPEN')        <i class="bi bi-record-circle-fill text-info me-1"></i>Mới gửi       @break
                                @case('IN_PROGRESS') <i class="bi bi-clock-history text-warning me-1"></i>Đang xử lý    @break
                                @case('RESOLVED')    <i class="bi bi-check-circle-fill text-success me-1"></i>Đã khắc phục  @break
                                @case('CLOSED')      <i class="bi bi-lock-fill text-secondary me-1"></i>Đã đóng       @break
                                @case('REOPENED')    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>Mở lại        @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('manager.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-primary rounded-2 fw-bold" style="font-size:0.78rem;">
                            <i class="bi bi-eye me-1"></i>Xem
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle display-4 text-success d-block mb-2"></i>
                        <h5>Hiện tại chưa có ticket nào trong hệ thống.</h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // 1. Render Bar Chart (Category)
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'bar',
        data: {
            labels: @json($chartCategoryLabels),
            datasets: [{
                label: 'Số lượng ticket',
                data: @json($chartCategoryCounts),
                backgroundColor: ['#0d6efd', '#0284c7', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899'],
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 2. Render Doughnut Chart (Departments)
    const ctxDept = document.getElementById('deptChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'doughnut',
        data: {
            labels: @json($chartDeptLabels),
            datasets: [{
                data: @json($chartDeptCounts),
                backgroundColor: ['#0d6efd', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
    });

    function toggleFilterInputs(type) {
        if (type === 'date') {
            document.getElementById('monthInputContainer').classList.add('d-none');
            document.getElementById('dateInputContainer').classList.remove('d-none');
        } else {
            document.getElementById('dateInputContainer').classList.add('d-none');
            document.getElementById('monthInputContainer').classList.remove('d-none');
        }
    }
</script>
@endpush
