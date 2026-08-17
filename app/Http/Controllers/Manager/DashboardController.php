<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SatisfactionSurvey;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * UC14: Trang Analytics Dashboard & Thống kê KPIs
     */
    public function index()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        // 1. Top KPI Stat Cards
        $totalTicketsMonth = Ticket::where('created_at', '>=', $startOfMonth)->count();

        // Tỷ lệ hoàn thành đúng SLA (%)
        $resolvedMonth = Ticket::where('created_at', '>=', $startOfMonth)
            ->whereIn('status', ['RESOLVED', 'CLOSED'])
            ->get();

        $totalResolved = $resolvedMonth->count();
        $onTimeResolved = $resolvedMonth->filter(function ($t) {
            return $t->resolved_at && $t->sla_deadline && $t->resolved_at->lessThanOrEqualTo($t->sla_deadline);
        })->count();

        $slaRate = $totalResolved > 0 ? round(($onTimeResolved / $totalResolved) * 100, 1) : 100;

        // Ticket quá hạn chưa xong
        $overdueCount = Ticket::whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_deadline', '<', $now)
            ->count();

        // Điểm hài lòng trung bình (⭐)
        $avgRating = round(SatisfactionSurvey::avg('rating_stars') ?? 5.0, 1);

        // 2. Dữ liệu cho Biểu đồ Thống kê theo Danh mục (Bar Chart)
        $categoriesData = TicketCategory::withCount('tickets')->get();
        $chartCategoryLabels = $categoriesData->pluck('name')->toArray();
        $chartCategoryCounts = $categoriesData->pluck('tickets_count')->toArray();

        // 3. Dữ liệu cho Biểu đồ Thống kê theo Khoa/Phòng ban (Doughnut Chart)
        $deptData = Department::select('departments.name', DB::raw('count(tickets.id) as ticket_count'))
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->leftJoin('tickets', 'tickets.requester_id', '=', 'users.id')
            ->groupBy('departments.id', 'departments.name')
            ->get();

        $chartDeptLabels = $deptData->pluck('name')->toArray();
        $chartDeptCounts = $deptData->pluck('ticket_count')->toArray();

        // Ticket mới gửi gần đây
        $recentTickets = Ticket::with(['category', 'requester', 'currentAssignee'])
            ->latest()
            ->take(5)
            ->get();

        return view('manager.dashboard.index', compact(
            'totalTicketsMonth',
            'slaRate',
            'overdueCount',
            'avgRating',
            'chartCategoryLabels',
            'chartCategoryCounts',
            'chartDeptLabels',
            'chartDeptCounts',
            'recentTickets'
        ));
    }
}
