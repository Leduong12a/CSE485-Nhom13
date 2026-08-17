{{-- partial: ticket-info.blade.php --}}
{{-- Thông tin chi tiết sự cố + Gallery ảnh minh chứng gốc --}}

<div class="detail-card">
    <div class="detail-card-header">
        <span><i class="bi bi-file-text me-2 text-primary"></i>Nội dung sự cố</span>
        <div class="d-flex gap-2">
            <span class="badge badge-status badge-priority-{{ $ticket->priority }}" style="font-size:0.72rem;">
                @switch($ticket->priority)
                    @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i> Ưu tiên Cao      @break
                    @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i> Ưu tiên Trung bình @break
                    @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i> Ưu tiên Thấp     @break
                @endswitch
            </span>
        </div>
    </div>
    <div class="detail-card-body">

        {{-- Meta thông tin --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Danh mục</div>
                <div style="font-size:0.85rem; color:#374151; font-weight:500;">
                    <i class="bi bi-tag me-1 text-primary"></i>{{ $ticket->category?->name ?? '—' }}
                </div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Vị trí sự cố</div>
                <div style="font-size:0.85rem; color:#374151; font-weight:500;">
                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $ticket->location ?: 'Không xác định' }}
                </div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Ngày gửi</div>
                <div style="font-size:0.85rem; color:#374151;">
                    <i class="bi bi-calendar3 me-1"></i>{{ $ticket->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Kỹ thuật viên phụ trách</div>
                <div style="font-size:0.85rem; color:#374151; font-weight:500;">
                    @if ($ticket->currentAssignee)
                        <i class="bi bi-person-check me-1 text-success"></i>{{ $ticket->currentAssignee->name }}
                    @else
                        <i class="bi bi-hourglass-split me-1 text-warning"></i><span style="color:#94a3b8;">Chưa có KTV phụ trách</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Nội dung mô tả --}}
        <div class="mb-4">
            <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">Mô tả sự cố</div>
            <div style="background:#f8fafc; border-radius:10px; padding:1rem; font-size:0.875rem; color:#374151; line-height:1.7; white-space:pre-wrap;">{{ $ticket->description }}</div>
        </div>

        {{-- Gallery ảnh minh chứng gốc --}}
        @if ($ticket->attachments->isNotEmpty())
            <div>
                <div style="font-size:0.72rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">
                    Ảnh / Tệp minh chứng ({{ $ticket->attachments->count() }})
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($ticket->attachments as $i => $attachment)
                        @php $isImage = str_starts_with($attachment->file_type ?? '', 'image/'); @endphp

                        @if ($isImage)
                            <div class="attachment-thumb" data-lightbox="{{ $i }}"
                                 style="width:90px; height:90px; border-radius:10px; overflow:hidden; cursor:pointer; border:1.5px solid #e5e7eb; position:relative; flex-shrink:0;">
                                <img src="{{ $attachment->url }}"
                                     alt="Ảnh minh chứng {{ $i+1 }}"
                                     style="width:100%; height:100%; object-fit:cover;"
                                     loading="lazy">
                                <div style="position:absolute; inset:0; background:rgba(0,0,0,0); transition:background 0.2s; display:flex; align-items:center; justify-content:center; color:white;"
                                     class="thumb-overlay">
                                    <i class="bi bi-search" style="opacity:0;"></i>
                                </div>
                            </div>
                        @else
                            <a href="{{ $attachment->url }}" target="_blank"
                               style="width:90px; height:90px; border-radius:10px; border:1.5px solid #e5e7eb; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; text-decoration:none; background:#fafafa; flex-shrink:0; transition:border-color 0.15s, box-shadow 0.15s;"
                               class="pdf-thumb">
                                <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size:1.8rem;"></i>
                                <span style="font-size:0.65rem; color:#64748b; text-align:center; padding:0 4px; overflow:hidden; max-width:100%; white-space:nowrap; text-overflow:ellipsis;">
                                    {{ basename($attachment->file_path) }}
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Lightbox Modal --}}
            <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="background:rgba(0,0,0,0.9); border:none; border-radius:16px;">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body d-flex align-items-center justify-content-center p-4">
                            <img id="lightboxImg" src="" alt="Preview"
                                 style="max-width:100%; max-height:80vh; border-radius:10px; object-fit:contain;">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .thumb-overlay:hover { background: rgba(0,0,0,0.4) !important; }
    .thumb-overlay:hover i { opacity: 1 !important; }
    .pdf-thumb:hover { border-color: var(--tlu-primary) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
</style>

<script>
    document.querySelectorAll('[data-lightbox]').forEach(thumb => {
        thumb.addEventListener('click', () => {
            const img = thumb.querySelector('img');
            document.getElementById('lightboxImg').src = img.src;
            new bootstrap.Modal(document.getElementById('lightboxModal')).show();
        });
    });
</script>
