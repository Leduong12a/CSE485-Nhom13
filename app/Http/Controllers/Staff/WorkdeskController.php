<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkdeskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('staffProfile');

        $activeTab = $request->get('tab', 'assigned');

        $assignedTickets = Ticket::with(['category', 'requester.department', 'currentAssignee'])
            ->where('current_assignee_id', $user->id)
            ->whereNotIn('status', ['CLOSED'])
            ->orderByRaw("FIELD(status, 'REOPENED', 'IN_PROGRESS', 'OPEN', 'RESOLVED')")
            ->orderBy('sla_deadline', 'asc')
            ->paginate(10, ['*'], 'assigned_page');

        $groupQueueTickets = Ticket::with(['category', 'requester.department'])
            ->whereNull('current_assignee_id')
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->orderBy('sla_deadline', 'asc')
            ->paginate(10, ['*'], 'queue_page');

        $assignedCount = Ticket::where('current_assignee_id', $user->id)
            ->whereNotIn('status', ['CLOSED', 'RESOLVED'])
            ->count();

        $queueCount = Ticket::whereNull('current_assignee_id')
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->count();

        $staffMembers = \App\Models\User::where('role', 'STAFF')
            ->where('is_active', true)
            ->with(['staffProfile', 'assignedTickets' => function ($q) {
                $q->whereIn('status', ['OPEN', 'IN_PROGRESS', 'REOPENED']);
            }])
            ->get();

        $otherStaffs = $staffMembers;

        return view('staff.workdesk.index', compact(
            'assignedTickets',
            'groupQueueTickets',
            'activeTab',
            'assignedCount',
            'queueCount',
            'staffMembers',
            'otherStaffs'
        ));
    }

    public function kanban()
    {
        $user = Auth::user();

        $todoTickets = Ticket::with(['category', 'requester.department'])
            ->where(function ($q) use ($user) {
                $q->where('current_assignee_id', $user->id)
                  ->orWhereNull('current_assignee_id');
            })
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->orderBy('sla_deadline', 'asc')
            ->get();

        $inProgressTickets = Ticket::with(['category', 'requester.department'])
            ->where('current_assignee_id', $user->id)
            ->where('status', 'IN_PROGRESS')
            ->orderBy('sla_deadline', 'asc')
            ->get();

        $doneTickets = Ticket::with(['category', 'requester.department'])
            ->where('current_assignee_id', $user->id)
            ->whereIn('status', ['RESOLVED', 'CLOSED'])
            ->latest('resolved_at')
            ->take(15)
            ->get();

        return view('staff.workdesk.kanban', compact(
            'todoTickets',
            'inProgressTickets',
            'doneTickets'
        ));
    }
}
