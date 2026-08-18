<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class SlaReportController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $overdueTickets = Ticket::with(['category', 'requester.department', 'currentAssignee'])
            ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_deadline', '<', $now)
            ->orderBy('sla_deadline', 'asc')
            ->paginate(15);

        $staffMembers = User::where('role', 'STAFF')->where('is_active', true)->get();

        return view('manager.reports.sla', compact('overdueTickets', 'staffMembers'));
    }
}
