@extends('layouts.app')

@section('title', 'Tạo phiếu báo sự cố mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Tạo Yêu cầu Hỗ trợ Sự cố mới</h4>
        </div>

        <div class="row g-4">
            <!-- Form Card (Left) -->
            <div class="col-md-8">
                <div class="card card-custom p-4 bg-white">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Tiêu đề sự cố <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" placeholder="VD: Máy chiếu phòng 302 C5 không lên hình..." value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">Loại sự cố <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn danh mục lỗi --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }} (SLA: {{ $cat->sla_hours }}h)
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-semibold">Mức độ ưu tiên <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="LOW" {{ old('priority') === 'LOW' ? 'selected' : '' }}>Thấp (Báo trước/Ít ảnh hưởng)</option>
                                    <option value="MEDIUM" {{ old('priority') === 'MEDIUM' || !old('priority') ? 'selected' : '' }}>Trung bình (Ảnh hưởng cá nhân)</option>
                                    <option value="HIGH" {{ old('priority') === 'HIGH' ? 'selected' : '' }}>Cao (Dừng tiết học/Dừng thi)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Vị trí xảy ra sự cố</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="VD: Phòng 302 - Nhà C5..." value="{{ old('location') }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Mô tả chi tiết hiện trạng lỗi <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Mô tả cụ thể hiện tượng lỗi, thông báo hiển thị trên màn hình..." required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tải ảnh minh chứng (Tối đa 5 file, <= 5MB/file)</label>
                            <div class="border border-2 border-dashed rounded p-4 text-center bg-light">
                                <i class="bi bi-cloud-arrow-up display-5 text-primary mb-2"></i>
                                <p class="mb-1 small fw-semibold">Kéo & thả hình ảnh minh chứng vào đây</p>
                                <span class="text-muted extra-small">Chấp nhận định dạng PNG, JPG, JPEG, PDF</span>
                                <input type="file" name="attachments[]" class="form-control form-control-sm mt-2 w-75 mx-auto" multiple accept="image/*,.pdf">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('home') }}" class="btn btn-light border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-send-fill me-1"></i> Gửi yêu cầu hỗ trợ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guidelines Sidebar (Right) -->
            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white border-start border-4 border-info">
                    <h5 class="fw-bold text-info mb-3"><i class="bi bi-lightbulb me-2"></i> Hướng dẫn báo sự cố</h5>
                    <ul class="small text-muted ps-3 mb-0 d-flex flex-column gap-2">
                        <li>Vui lòng ghi rõ <strong>Vị trí cụ thể</strong> (Tòa nhà, số phòng) để Kỹ thuật viên nhanh chóng tiếp cận.</li>
                        <li>Đính kèm <strong>Hình ảnh hiện trạng lỗi</strong> sẽ giúp KTV chẩn đoán và chuẩn bị sẵn linh kiện phù hợp trước khi di chuyển.</li>
                        <li>Thời gian cam kết khắc phục <strong>SLA</strong> bắt đầu đếm ngược ngay sau khi phiếu được tạo thành công.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
