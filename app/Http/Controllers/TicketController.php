<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->select('tickets.*', 'ticket_categories.name as category_name');

        if ($user->role === 'REQUESTER') {
            $query->where('tickets.requester_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('tickets.status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('tickets.title', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->orderBy('tickets.created_at', 'desc')->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = DB::table('ticket_categories')->get();
        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'description' => 'required|string',
            'location' => 'nullable|string|max:150',
        ]);

        $ticketId = DB::table('tickets')->insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'category_id' => $request->category_id,
            'requester_id' => Auth::id(),
            'status' => 'OPEN',
            'priority' => $request->priority,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tickets.show', $ticketId)->with('success', 'Đã tạo yêu cầu hỗ trợ sự cố thành công!');
    }

    public function show($id)
    {
        $ticket = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->join('users as requester', 'tickets.requester_id', '=', 'requester.id')
            ->leftJoin('users as assignee', 'tickets.current_assignee_id', '=', 'assignee.id')
            ->select('tickets.*', 'ticket_categories.name as category_name', 'requester.name as requester_name', 'assignee.name as assignee_name')
            ->where('tickets.id', $id)
            ->first();

        if (!$ticket) {
            abort(404, 'Không tìm thấy phiếu sự cố.');
        }

        $comments = DB::table('ticket_comments')
            ->join('users', 'ticket_comments.user_id', '=', 'users.id')
            ->select('ticket_comments.*', 'users.name as user_name', 'users.role as user_role')
            ->where('ticket_comments.ticket_id', $id)
            ->orderBy('ticket_comments.created_at', 'asc')
            ->get();

        $survey = DB::table('satisfaction_surveys')->where('ticket_id', $id)->first();

        return view('tickets.show', compact('ticket', 'comments', 'survey'));
    }
}
