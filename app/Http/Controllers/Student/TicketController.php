<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketStatusLog;
use App\Models\SatisfactionSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * UC03: Danh sách Ticket cá nhân của Sinh viên
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['category'])
            ->where('requester_id', Auth::id())
            ->latest();

        // Lọc theo trạng thái
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tiêu đề hoặc mã ticket
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('student.tickets.index', compact('tickets'));
    }

    /**
     * UC02: Form tạo Ticket mới
     */
    public function create()
    {
        $categories = TicketCategory::orderBy('name')->get();
        return view('student.tickets.create', compact('categories'));
    }

    /**
     * UC02: Lưu Ticket mới vào CSDL
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'location'    => ['nullable', 'string', 'max:150'],
            'priority'    => ['required', 'in:LOW,MEDIUM,HIGH'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'title.required'       => 'Vui lòng nhập tiêu đề mô tả sự cố.',
            'description.required' => 'Vui lòng mô tả chi tiết sự cố.',
            'category_id.required' => 'Vui lòng chọn danh mục sự cố.',
            'priority.required'    => 'Vui lòng chọn mức độ ưu tiên.',
            'attachments.max'      => 'Chỉ được đính kèm tối đa 5 tệp.',
            'attachments.*.mimes'  => 'Chỉ chấp nhận tệp .jpg, .jpeg, .png, .pdf.',
            'attachments.*.max'    => 'Mỗi tệp không được vượt quá 5MB.',
        ]);

        $category = TicketCategory::findOrFail($validated['category_id']);

        $ticket = Ticket::create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'category_id'  => $validated['category_id'],
            'location'     => $validated['location'] ?? null,
            'priority'     => $validated['priority'],
            'requester_id' => Auth::id(),
            'status'       => 'OPEN',
            'sla_deadline' => now()->addHours($category->sla_hours),
        ]);

        // Ghi log trạng thái ban đầu
        TicketStatusLog::create([
            'ticket_id'         => $ticket->id,
            'changed_by_user_id' => Auth::id(),
            'old_status'        => null,
            'new_status'        => 'OPEN',
        ]);

        // Upload ảnh đính kèm
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = \App\Services\CloudinaryService::upload($file) ?? $file->store('attachments', 'public');
                TicketAttachment::create([
                    'ticket_id'  => $ticket->id,
                    'comment_id' => null,
                    'file_path'  => $path,
                    'file_type'  => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('student.tickets.show', $ticket->id)
            ->with('success', 'Yêu cầu hỗ trợ của bạn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }

    /**
     * UC04: Chi tiết Ticket
     */
    public function show(Ticket $ticket)
    {
        // Chỉ sinh viên tạo ticket mới được xem
        abort_if($ticket->requester_id !== Auth::id(), 403, 'Bạn không có quyền xem phiếu này.');

        $ticket->load([
            'category',
            'currentAssignee',
            'attachments' => fn($q) => $q->whereNull('comment_id'),
            'comments.user',
            'comments.attachments',
            'statusLogs.changedBy',
            'satisfactionSurvey',
        ]);

        return view('student.tickets.show', compact('ticket'));
    }

    /**
     * UC04: Gửi bình luận/chat trong Ticket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        abort_if($ticket->requester_id !== Auth::id(), 403);

        $request->validate([
            'content'     => ['required', 'string', 'max:2000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'content'   => $request->content,
        ]);

        // Upload ảnh đính kèm trong chat
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = \App\Services\CloudinaryService::upload($file) ?? $file->store('attachments', 'public');
                TicketAttachment::create([
                    'ticket_id'  => $ticket->id,
                    'comment_id' => $comment->id,
                    'file_path'  => $path,
                    'file_type'  => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('student.tickets.show', $ticket->id)
            ->with('success', 'Tin nhắn đã được gửi.');
    }

    /**
     * UC05: Mở lại Ticket (Reopen)
     */
    public function reopen(Request $request, Ticket $ticket)
    {
        abort_if($ticket->requester_id !== Auth::id(), 403);
        abort_unless(in_array($ticket->status, ['RESOLVED', 'CLOSED']), 403, 'Chỉ được mở lại ticket đã xử lý xong.');

        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do mở lại sự cố (ít nhất 10 ký tự).',
            'reason.min'      => 'Lý do phải có ít nhất 10 ký tự.',
        ]);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'REOPENED']);

        // Ghi comment lý do mở lại
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'content'   => '⚠️ **Yêu cầu mở lại sự cố:** ' . $request->reason,
        ]);

        // Ghi log
        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => Auth::id(),
            'old_status'         => $oldStatus,
            'new_status'         => 'REOPENED',
        ]);

        return redirect()->route('student.tickets.show', $ticket->id)
            ->with('success', 'Sự cố đã được mở lại. Chúng tôi sẽ tiếp tục hỗ trợ bạn.');
    }

    /**
     * UC05: Gửi đánh giá 5 sao (Satisfaction Survey)
     */
    public function survey(Request $request, Ticket $ticket)
    {
        abort_if($ticket->requester_id !== Auth::id(), 403);
        abort_unless($ticket->status === 'RESOLVED', 403, 'Chỉ được đánh giá ticket đã được khắc phục.');

        $request->validate([
            'rating_stars' => ['required', 'integer', 'between:1,5'],
            'comment'      => ['nullable', 'string', 'max:500'],
        ], [
            'rating_stars.required' => 'Vui lòng chọn mức đánh giá (1-5 sao).',
            'rating_stars.between'  => 'Mức đánh giá phải từ 1 đến 5 sao.',
        ]);

        SatisfactionSurvey::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'rating_stars' => $request->rating_stars,
                'comment'      => $request->comment,
            ]
        );

        // Tự động đóng ticket sau khi đánh giá
        $ticket->update([
            'status'    => 'CLOSED',
            'closed_at' => now(),
        ]);

        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => Auth::id(),
            'old_status'         => 'RESOLVED',
            'new_status'         => 'CLOSED',
        ]);

        return redirect()->route('student.tickets.show', $ticket->id)
            ->with('success', 'Cảm ơn bạn đã đánh giá! Phiếu sự cố đã được đóng lại.');
    }
}
