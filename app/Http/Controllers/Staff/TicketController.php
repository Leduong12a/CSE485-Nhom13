<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'category',
            'requester.department',
            'currentAssignee.staffProfile',
            'attachments',
            'comments.user',
            'comments.attachments',
            'assignments.assignedByUser',
            'statusLogs.changedBy',
            'satisfactionSurvey',
        ]);

        $latestAssignment = $ticket->assignments()->latest('assigned_at')->first();

        return view('staff.tickets.show', compact('ticket', 'latestAssignment'));
    }

    public function claim(Ticket $ticket)
    {
        $user = Auth::user();

        $ticket->current_assignee_id = $user->id;
        if ($ticket->status === 'OPEN') {
            $ticket->status = 'IN_PROGRESS';
        }
        $ticket->save();

        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => $user->id,
            'old_status'         => 'OPEN',
            'new_status'         => $ticket->status,
        ]);

        return redirect()->back()
            ->with('success', 'Bạn đã tự nhận xử lý thành công ticket này.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:IN_PROGRESS,RESOLVED,CLOSED'],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $request->status;
        $user      = Auth::user();

        $ticket->status = $newStatus;

        if ($newStatus === 'RESOLVED' && ! $ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        if ($newStatus === 'CLOSED' && ! $ticket->closed_at) {
            $ticket->closed_at = now();
        }

        $ticket->save();

        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => $user->id,
            'old_status'         => $oldStatus,
            'new_status'         => $newStatus,
        ]);

        $statusLabels = [
            'IN_PROGRESS' => 'Đang xử lý',
            'RESOLVED'    => 'Đã khắc phục',
            'CLOSED'      => 'Đã đóng',
        ];

        return redirect()->back()
            ->with('success', "Đã chuyển trạng thái ticket sang '{$statusLabels[$newStatus]}'.");
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        if ($ticket->status === 'CLOSED') {
            return redirect()->back()
                ->with('error', 'Sự cố này đã đóng, không thể gửi thêm tin nhắn chat.');
        }

        $request->validate([
            'content'       => ['required', 'string', 'max:1000'],
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung tin nhắn.',
        ]);

        $user = Auth::user();

        $comment = TicketComment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'content'     => $request->content,
            'is_internal' => false,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = \App\Services\CloudinaryService::upload($file) ?? $file->store('comment_attachments', 'public');
                TicketAttachment::create([
                    'ticket_id'  => $ticket->id,
                    'comment_id' => $comment->id,
                    'file_path'  => $path,
                    'file_type'  => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Đã gửi tin nhắn trao đổi.');
    }

    public function reassign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'note'     => ['nullable', 'string', 'max:500'],
        ], [
            'staff_id.required' => 'Vui lòng chọn Kỹ thuật viên tiếp nhận.',
            'staff_id.exists'   => 'Kỹ thuật viên không hợp lệ.',
        ]);

        $targetStaff = \App\Models\User::where('id', $request->staff_id)->where('role', 'STAFF')->firstOrFail();
        $currentUser = Auth::user();

        $oldStatus = $ticket->status;
        $ticket->current_assignee_id = $targetStaff->id;

        if (in_array($ticket->status, ['OPEN', 'REOPENED'])) {
            $ticket->status = 'IN_PROGRESS';
        }

        $ticket->save();

        TicketAssignment::create([
            'ticket_id'            => $ticket->id,
            'assigned_to_staff_id' => $targetStaff->id,
            'assigned_by_user_id'  => $currentUser->id,
            'note'                 => $request->note ?: "Phân công / Chuyển giao công việc nội bộ nhóm KTV.",
            'assigned_at'          => now(),
        ]);

        if ($oldStatus !== $ticket->status) {
            TicketStatusLog::create([
                'ticket_id'          => $ticket->id,
                'changed_by_user_id' => $currentUser->id,
                'old_status'         => $oldStatus,
                'new_status'         => $ticket->status,
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã phân công / chuyển giao ticket thành công cho KTV {$targetStaff->name}.");
    }

    public function release(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Chỉ KTV đang phụ trách mới được trả lại
        abort_if($ticket->current_assignee_id !== $user->id, 403, 'Bạn không phải người phụ trách ticket này.');
        abort_if(in_array($ticket->status, ['RESOLVED', 'CLOSED']), 403, 'Không thể trả lại ticket đã hoàn thành.');

        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do trả lại (ít nhất 10 ký tự).',
            'reason.min'      => 'Lý do phải có ít nhất 10 ký tự.',
        ]);

        $oldStatus = $ticket->status;

        // Reset người phụ trách & trạng thái về OPEN
        $ticket->current_assignee_id = null;
        $ticket->status              = 'OPEN';
        $ticket->save();

        // Ghi comment thông báo lý do
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'content'   => '🔄 **KTV ' . $user->name . ' đã trả ticket về hàng chờ.** Lý do: ' . $request->reason,
        ]);

        // Ghi lịch sử trạng thái
        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => $user->id,
            'old_status'         => $oldStatus,
            'new_status'         => 'OPEN',
        ]);

        return redirect()->route('staff.workdesk.index')
            ->with('success', 'Đã trả ticket về hàng chờ. Quản lý sẽ phân công lại cho KTV khác.');
    }
}
