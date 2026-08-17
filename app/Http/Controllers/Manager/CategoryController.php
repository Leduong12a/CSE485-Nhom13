<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * UC11: Trang Cấu hình Danh mục Sự cố & SLA
     */
    public function index()
    {
        $categories = TicketCategory::withCount('tickets')
            ->orderBy('name')
            ->get();

        return view('manager.categories.index', compact('categories'));
    }

    /**
     * Tạo mới Danh mục sự cố
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:ticket_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sla_hours'   => ['required', 'integer', 'min:1', 'max:168'],
        ], [
            'name.required' => 'Vui lòng nhập tên loại sự cố.',
            'name.unique'   => 'Tên loại sự cố này đã tồn tại.',
            'sla_hours.required' => 'Vui lòng nhập số giờ SLA cam kết.',
            'sla_hours.min' => 'Thời gian SLA phải ít nhất 1 giờ.',
        ]);

        TicketCategory::create($validated);

        return redirect()->route('manager.categories.index')
            ->with('success', 'Đã tạo danh mục sự cố mới thành công!');
    }

    /**
     * Cập nhật Danh mục sự cố & Thời gian SLA
     */
    public function update(Request $request, TicketCategory $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:ticket_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'sla_hours'   => ['required', 'integer', 'min:1', 'max:168'],
        ]);

        $category->update($validated);

        return redirect()->route('manager.categories.index')
            ->with('success', "Đã cập nhật danh mục '{$category->name}' thành công!");
    }

    /**
     * Xóa Danh mục sự cố
     */
    public function destroy(TicketCategory $category)
    {
        if ($category->tickets()->exists()) {
            return redirect()->back()
                ->with('error', "Không thể xóa danh mục '{$category->name}' vì đang có phiếu sự cố thuộc danh mục này.");
        }

        $category->delete();

        return redirect()->route('manager.categories.index')
            ->with('success', 'Đã xóa danh mục sự cố.');
    }
}
