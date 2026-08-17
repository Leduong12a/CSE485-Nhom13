@extends('manager.layouts.app')

@section('title', 'Analytics Dashboard')

@push('styles')
<style>
    .kpi-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 2px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        height: 100%;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .chart-card-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Analytics Dashboard</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Tổng quan chỉ số hoạt động &amp; Báo cáo chất lượng hỗ trợ kỹ thuật TLU</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill" style="font-size:0.8rem;">
            <i class="bi bi-calendar3 me-1"></i> Tháng {{ date('m/Y') }}
        </span>
    </div>
</div>

{{-- ── 1. TOP 4 KPI CARDS ── --}}
<div class="row g-3 mb-4">

    {{-- KPI 1: Tổng ticket trong tháng --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff; color:#0d6efd;">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
            <div>
                <div class="kpi-value">{{ number_format($totalTicketsMonth) }}</div>
                <div class="kpi-label">Tổng Ticket tháng này</div>
            </div>
        </div>
    </div>

    {{-- KPI 2: Tỷ lệ đúng SLA --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f0fdf4; color:#16a34a;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="kpi-value text-success">{{ $slaRate }}%</div>
                <div class="kpi-label">Tỷ lệ xử lý đúng SLA</div>
            </div>
        </div>
    </div>

    {{-- KPI 3: Ticket trễ hạn chưa xong --}}
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fff1f2; color:#be123c;">
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
            <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div class="kpi-value text-warning">{{ $avgRating }} <span style="font-size:1rem;">/ 5.0</span></div>
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
                <span class="text-muted" style="font-size:0.75rem;">Phân loại lỗi</span>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart: Sự cố theo Khoa / Phòng ban --}}
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3><i class="bi bi-pie-chart-fill me-2 text-info"></i>Sự cố theo Khoa / Đơn vị</h3>
                <span class="text-muted" style="font-size:0.75rem;">Nguồn phát sinh</span>
            </div>
            <div style="position:relative; height:280px; display:flex; align-items:center; justify-content:center;">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ── 3. RECENT TICKETS TABLE ── --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h3 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Phiếu sự cố mới gửi gần đây</h3>
        <a href="{{ route('manager.tickets.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:0.78rem;">
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
                        <div>{{ $t->requester->name }}</div>
                        <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $t->category?->name ?? '—' }}</span></td>
                    <td>
                        @if ($t->currentAssignee)
                            <span class="text-success fw-medium"><i class="bi bi-person-check me-1"></i>{{ $t->currentAssignee->name }}</span>
                        @else
                            <span class="text-warning"><i class="bi bi-hourglass me-1"></i>Chưa phân công</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-status badge-{{ $t->status }}">
                            @switch($t->status)
                                @case('OPEN')        🔵 Mới gửi       @break
                                @case('IN_PROGRESS') 🟡 Đang xử lý    @break
                                @case('RESOLVED')    🟢 Đã khắc phục  @break
                                @case('CLOSED')      ⚫ Đã đóng       @break
                                @case('REOPENED')    🔴 Mở lại        @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('manager.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-primary rounded-2">
                            <i class="bi bi-eye"></i> Xem
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Chưa có ticket nào.</td>
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
                backgroundColor: ['#0d6efd', '#0dcaf0', '#ffc107', '#198754', '#6f42c1', '#d63384'],
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
                backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6610f2'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
    });
</script>
@endpush
