<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Thống kê nhanh ticket cá nhân
        $myTicketsCount = DB::table('tickets')->where('requester_id', $user->id)->count();
        $inProgressCount = DB::table('tickets')->where('requester_id', $user->id)->where('status', 'IN_PROGRESS')->count();
        $resolvedCount = DB::table('tickets')->where('requester_id', $user->id)->where('status', 'RESOLVED')->count();

        // Danh sách ticket gần đây
        $recentTickets = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->select('tickets.*', 'ticket_categories.name as category_name')
            ->where('tickets.requester_id', $user->id)
            ->orderBy('tickets.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('home', compact('myTicketsCount', 'inProgressCount', 'resolvedCount', 'recentTickets'));
    }
}
