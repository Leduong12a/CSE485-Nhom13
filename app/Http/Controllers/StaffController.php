<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function workdesk(Request $request)
    {
        $user = Auth::user();
        
        $assignedTickets = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->join('users as requester', 'tickets.requester_id', '=', 'requester.id')
            ->select('tickets.*', 'ticket_categories.name as category_name', 'requester.name as requester_name')
            ->where('tickets.current_assignee_id', $user->id)
            ->orderByRaw("FIELD(priority, 'HIGH', 'MEDIUM', 'LOW')")
            ->orderBy('tickets.sla_deadline', 'asc')
            ->get();

        $openTickets = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->select('tickets.*', 'ticket_categories.name as category_name')
            ->where('tickets.status', 'OPEN')
            ->orderBy('tickets.created_at', 'desc')
            ->get();

        return view('staff.workdesk', compact('assignedTickets', 'openTickets'));
    }
}
