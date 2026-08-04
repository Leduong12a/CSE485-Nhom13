<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalTickets = DB::table('tickets')->count();
        $inProgressTickets = DB::table('tickets')->where('status', 'IN_PROGRESS')->count();
        $resolvedTickets = DB::table('tickets')->where('status', 'RESOLVED')->count();
        $overdueTickets = DB::table('tickets')->where('status', 'OPEN')->where('created_at', '<', now()->subHours(24))->count();

        $avgRating = DB::table('satisfaction_surveys')->avg('rating_stars') ?? 5.0;

        $recentAssignments = DB::table('ticket_assignments')
            ->join('tickets', 'ticket_assignments.ticket_id', '=', 'tickets.id')
            ->join('users as staff', 'ticket_assignments.assigned_to_staff_id', '=', 'staff.id')
            ->join('users as manager', 'ticket_assignments.assigned_by_user_id', '=', 'manager.id')
            ->select('ticket_assignments.*', 'tickets.title as ticket_title', 'staff.name as staff_name', 'manager.name as manager_name')
            ->orderBy('ticket_assignments.assigned_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('totalTickets', 'inProgressTickets', 'resolvedTickets', 'overdueTickets', 'avgRating', 'recentAssignments'));
    }
}
