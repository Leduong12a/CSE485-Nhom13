<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class SlaReportController extends Controller
{
    /**
     * UC13: Báo cáo Phiếu sự cố Vi phạm / Quá hạn SLA
     */
    public function index(Request $request)
    {
        $now = now();

        // Lấy tất cả các ticket chưa RESOLVED/CLOSED mà quá hạn SLA
        $overdueTickets = Ticket::with(['category', 'requester.department', 'currentAssignee'])
            ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_deadline', '<', $now)
            ->orderBy('sla_deadline', 'asc')
            ->paginate(15);

        // KTV khả dụng phục vụ việc đổi KTV khẩn cấp
        $staffMembers = User::where('role', 'STAFF')->where('is_active', true)->get();

        return view('manager.reports.sla', compact('overdueTickets', 'staffMembers'));
    }
}
