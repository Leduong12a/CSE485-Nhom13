@extends('manager.layouts.app')

@section('title', 'Cấu hình Danh mục & SLA')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Cấu hình Danh mục Sự cố &amp; SLA</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Quản lý các loại lỗi kỹ thuật và thời gian cam kết khắc phục tối đa (SLA hours)</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-bold" style="font-size:0.85rem;" data-bs-toggle="modal" data-bs-target="#createCatModal">
        <i class="bi bi-plus-lg me-1"></i> Thêm loại sự cố mới
    </button>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                <tr>
                    <th>ID</th>
                    <th>Tên Loại sự cố</th>
                    <th>Mô tả phạm vi sự cố</th>
                    <th>Cam kết SLA (Giờ)</th>
                    <th>Số ticket thuộc danh mục</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                <tr>
                    <td class="font-monospace text-muted fw-bold">#{{ $cat->id }}</td>
                    <td class="fw-bold text-dark">{{ $cat->name }}</td>
                    <td style="max-width:300px;" class="text-muted">{{ $cat->description ?: '—' }}</td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size:0.8rem;">
                            <i class="bi bi-clock-history me-1"></i> {{ $cat->sla_hours }} giờ
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $cat->tickets_count }} ticket</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-2"
                                    onclick="openEditCatModal({{ json_encode($cat) }})">
                                <i class="bi bi-pencil"></i> Sửa
                            </button>

                            <form method="POST" action="{{ route('manager.categories.destroy', $cat->id) }}"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" {{ $cat->tickets_count > 0 ? 'disabled title="Không thể xóa khi có ticket thuộc danh mục"' : '' }}>
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Chưa có danh mục sự cố nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Thêm mới --}}
<div class="modal fade" id="createCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" style="color:#1e293b;"><i class="bi bi-plus-circle me-2 text-primary"></i>Thêm loại sự cố mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manager.categories.store') }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Tên loại sự cố <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="VD: Lỗi Wi-Fi TLU, Máy chiếu..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Mô tả phạm vi sự cố</label>
                        <textarea name="description" rows="3" class="form-control rounded-3" placeholder="Mô tả các biểu hiện của loại sự cố này..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Thời gian xử lý tối đa SLA (Số giờ) <span class="text-danger">*</span></label>
                        <input type="number" name="sla_hours" class="form-control rounded-3" value="24" min="1" max="168" required>
                        <small class="text-muted" style="font-size:0.75rem;">Ví dụ: 4 (4 giờ), 24 (1 ngày), 48 (2 ngày).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold rounded-3">Tạo danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Cập nhật --}}
<div class="modal fade" id="editCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" style="color:#1e293b;"><i class="bi bi-pencil-square me-2 text-primary"></i>Cập nhật loại sự cố</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="editCatForm">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Tên loại sự cố <span class="text-danger">*</span></label>
                        <input type="text" id="editName" name="name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Mô tả phạm vi sự cố</label>
                        <textarea id="editDescription" name="description" rows="3" class="form-control rounded-3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Thời gian xử lý tối đa SLA (Số giờ) <span class="text-danger">*</span></label>
                        <input type="number" id="editSlaHours" name="sla_hours" class="form-control rounded-3" min="1" max="168" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold rounded-3">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openEditCatModal(cat) {
        document.getElementById('editCatForm').action = `/manager/categories/${cat.id}`;
        document.getElementById('editName').value = cat.name;
        document.getElementById('editDescription').value = cat.description || '';
        document.getElementById('editSlaHours').value = cat.sla_hours;
        new bootstrap.Modal(document.getElementById('editCatModal')).show();
    }
</script>
@endpush
