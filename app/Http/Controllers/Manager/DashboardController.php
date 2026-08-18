<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SatisfactionSurvey;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterType    = $request->get('filter_type', 'month');
        $selectedMonth = $request->get('month', date('Y-m'));
        $selectedDate  = $request->get('date', date('Y-m-d'));

        try {
            if ($filterType === 'date') {
                $carbonDate = \Carbon\Carbon::parse($selectedDate);
                $startRange = $carbonDate->copy()->startOfDay();
                $endRange   = $carbonDate->copy()->endOfDay();
                $filterLabel = 'Ngày ' . $carbonDate->format('d/m/Y');
            } else {
                $carbonMonth = \Carbon\Carbon::parse($selectedMonth . '-01');
                $startRange  = $carbonMonth->copy()->startOfMonth();
                $endRange    = $carbonMonth->copy()->endOfMonth();
                $filterLabel = 'Tháng ' . $carbonMonth->format('m/Y');
            }
        } catch (\Exception $e) {
            $filterType    = 'month';
            $selectedMonth = date('Y-m');
            $selectedDate  = date('Y-m-d');
            $startRange    = now()->startOfMonth();
            $endRange      = now()->endOfMonth();
            $filterLabel   = 'Tháng ' . now()->format('m/Y');
        }

        $now = now();

        $totalTicketsMonth = Ticket::whereBetween('created_at', [$startRange, $endRange])->count();

        $ticketsInRange = Ticket::whereBetween('created_at', [$startRange, $endRange])->get();
        $totalInRange   = $ticketsInRange->count();

        $resolvedInRange = $ticketsInRange->filter(function ($t) {
            return in_array($t->status, ['RESOLVED', 'CLOSED']);
        });

        if ($resolvedInRange->count() > 0) {
            $onTimeCount = $resolvedInRange->filter(function ($t) {
                $finishTime = $t->resolved_at ?? $t->closed_at ?? $t->updated_at;
                return $t->sla_deadline && $finishTime && $finishTime->lessThanOrEqualTo($t->sla_deadline);
            })->count();
            $slaRate = round(($onTimeCount / $resolvedInRange->count()) * 100, 1);
        } elseif ($totalInRange > 0) {
            $overdueInRange = $ticketsInRange->filter(function ($t) use ($now) {
                return $t->sla_deadline && $t->sla_deadline->lessThan($now);
            })->count();
            $slaRate = round((($totalInRange - $overdueInRange) / $totalInRange) * 100, 1);
        } else {
            $slaRate = 100.0;
        }

        $overdueCount = Ticket::whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_deadline', '<', $now)
            ->count();

        $avgRating = SatisfactionSurvey::avg('rating_stars') !== null
            ? round(SatisfactionSurvey::avg('rating_stars'), 1)
            : null;

        $categoriesData = TicketCategory::withCount(['tickets' => function ($q) use ($startRange, $endRange) {
            $q->whereBetween('created_at', [$startRange, $endRange]);
        }])->get();
        $chartCategoryLabels = $categoriesData->pluck('name')->toArray();
        $chartCategoryCounts = $categoriesData->pluck('tickets_count')->toArray();

        $deptData = Department::select('departments.name', DB::raw('count(tickets.id) as ticket_count'))
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->leftJoin('tickets', function ($join) use ($startRange, $endRange) {
                $join->on('tickets.requester_id', '=', 'users.id')
                     ->whereBetween('tickets.created_at', [$startRange, $endRange]);
            })
            ->groupBy('departments.id', 'departments.name')
            ->get();

        $chartDeptLabels = $deptData->pluck('name')->toArray();
        $chartDeptCounts = $deptData->pluck('ticket_count')->toArray();

        $recentTickets = Ticket::with(['category', 'requester', 'currentAssignee'])
            ->latest()
            ->take(5)
            ->get();

        return view('manager.dashboard.index', compact(
            'filterType',
            'selectedMonth',
            'selectedDate',
            'filterLabel',
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
