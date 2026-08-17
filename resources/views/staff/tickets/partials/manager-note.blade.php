{{-- partial: manager-note.blade.php --}}
{{-- Khung hiển thị Ghi chú & Chỉ đạo chuyên môn từ Trưởng bộ phận (Manager) --}}

@if ($latestAssignment && $latestAssignment->note)
<div class="card mb-4 border-primary border-opacity-25" style="background:#eff6ff; border-radius:14px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-2 mb-1" style="font-size:0.85rem; font-weight:700; color:#1d4ed8;">
            <i class="bi bi-person-workspace text-primary"></i>
            <span>Ghi chú Chỉ đạo từ Trưởng bộ phận: {{ $latestAssignment->assignedByUser?->name ?? 'Manager' }}</span>
            <span class="ms-auto text-muted fw-normal" style="font-size:0.75rem;">
                {{ $latestAssignment->assigned_at->format('H:i d/m/Y') }}
            </span>
        </div>
        <div class="p-2 bg-white rounded-3 border text-dark" style="font-size:0.85rem; line-height:1.5; font-style:italic;">
            "{{ $latestAssignment->note }}"
        </div>
    </div>
</div>
@endif
