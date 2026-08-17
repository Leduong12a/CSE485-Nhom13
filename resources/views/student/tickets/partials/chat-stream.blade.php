{{-- partial: chat-stream.blade.php --}}
{{-- Khung hội thoại trao đổi 2 chiều giữa Sinh viên & Kỹ thuật viên --}}

<div class="detail-card" style="position:sticky; top:76px;">
    <div class="detail-card-header">
        <span><i class="bi bi-chat-dots me-2 text-primary"></i>Trao đổi với Kỹ thuật viên</span>
        <span style="font-size:0.75rem; color:#94a3b8;">{{ $ticket->comments->count() }} tin nhắn</span>
    </div>

    {{-- Message Stream --}}
    <div id="chatStream" style="
        max-height: 420px;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f8fafc;
        scroll-behavior: smooth;
    ">
        @forelse ($ticket->comments as $comment)
            @php $isMe = $comment->user_id === Auth::id(); @endphp

            <div class="d-flex gap-2 {{ $isMe ? 'flex-row-reverse' : '' }}" style="align-items: flex-end;">

                {{-- Avatar --}}
                <div style="
                    width: 32px; height: 32px;
                    border-radius: 8px;
                    background: {{ $isMe ? '#0d6efd' : '#22c55e' }};
                    color: white;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 0.8rem; font-weight: 700;
                    flex-shrink: 0;
                ">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>

                {{-- Bubble --}}
                <div style="max-width: 78%;">
                    <div style="
                        background: {{ $isMe ? '#0d6efd' : 'white' }};
                        color: {{ $isMe ? 'white' : '#1e293b' }};
                        border-radius: {{ $isMe ? '12px 12px 4px 12px' : '12px 12px 12px 4px' }};
                        padding: 0.6rem 0.9rem;
                        font-size: 0.85rem;
                        line-height: 1.55;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
                        white-space: pre-wrap;
                        word-break: break-word;
                    ">{{ $comment->content }}</div>

                    {{-- Attachments trong chat --}}
                    @if ($comment->attachments->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mt-1 {{ $isMe ? 'justify-content-end' : '' }}">
                            @foreach ($comment->attachments as $att)
                                @if (str_starts_with($att->file_type ?? '', 'image/'))
                                    <img src="{{ $att->url }}"
                                         alt="Ảnh đính kèm"
                                         style="width:70px; height:70px; border-radius:8px; object-fit:cover; cursor:pointer; border:1.5px solid #e5e7eb;"
                                         onclick="document.getElementById('lightboxImg').src=this.src; new bootstrap.Modal(document.getElementById('lightboxModal')).show()">
                                @else
                                    <a href="{{ $att->url }}" target="_blank"
                                       style="display:flex; align-items:center; gap:4px; background:rgba(0,0,0,0.06); border-radius:7px; padding:4px 8px; font-size:0.72rem; color:inherit; text-decoration:none;">
                                        <i class="bi bi-paperclip"></i> {{ basename($att->file_path) }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div style="font-size:0.68rem; color:#94a3b8; margin-top:3px; {{ $isMe ? 'text-align:right;' : '' }}">
                        {{ $comment->user->name }} · {{ $comment->created_at->format('H:i d/m') }}
                    </div>
                </div>
            </div>

        @empty
            <div style="text-align:center; padding:2.5rem 1rem;">
                <i class="bi bi-chat-square-text" style="font-size:2.5rem; color:#cbd5e1; display:block; margin-bottom:0.75rem;"></i>
                <p style="font-size:0.82rem; color:#94a3b8; margin:0;">Chưa có tin nhắn nào.<br>Gửi tin nhắn để trao đổi với Kỹ thuật viên.</p>
            </div>
        @endforelse
    </div>

    {{-- Input gửi tin nhắn --}}
    <div style="padding: 0.75rem; border-top: 1px solid #f1f5f9; background: white;">
        @if ($ticket->status === 'CLOSED')
            <div class="p-3 text-center rounded-3 bg-light text-muted" style="font-size:0.82rem; border: 1px dashed #cbd5e1;">
                <i class="bi bi-lock-fill me-1 text-secondary"></i> Sự cố đã đóng hoàn toàn (Khung chat đã khóa).
            </div>
        @else
            <form method="POST" action="{{ route('student.tickets.comments.store', $ticket->id) }}"
                  enctype="multipart/form-data" id="chatForm">
                @csrf

                <div style="display:flex; flex-direction:column; gap:8px;">

                    <textarea name="content" id="chatInput" rows="2"
                        placeholder="Nhập tin nhắn trao đổi..."
                        style="
                            border-radius: 10px;
                            border: 1.5px solid #e5e7eb;
                            padding: 0.55rem 0.85rem;
                            font-size: 0.85rem;
                            resize: none;
                            transition: border-color 0.2s;
                            font-family: 'Inter', sans-serif;
                        "
                        onkeydown="if(event.ctrlKey&&event.key==='Enter') document.getElementById('chatForm').submit();"
                        required></textarea>

                    {{-- Preview files đính kèm trong chat --}}
                    <div id="chatFilesPreview" class="d-flex flex-wrap gap-1" style="display:none !important;"></div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex gap-1">
                            {{-- Nút Chụp ảnh trực tiếp từ Camera điện thoại --}}
                            <label for="chatCamera" style="
                                display: inline-flex; align-items: center; gap: 5px;
                                cursor: pointer; font-size: 0.8rem; color: #0d6efd;
                                padding: 0.35rem 0.7rem; border-radius: 8px;
                                border: 1.5px solid #bfdbfe; background:#eff6ff;
                                transition: border-color 0.15s, color 0.15s;
                            " class="btn-attach" title="Chụp ảnh từ Camera">
                                <i class="bi bi-camera-fill"></i> Chụp ảnh
                                <input type="file" id="chatCamera" name="attachments[]"
                                       accept="image/*" capture="environment"
                                       style="display:none;"
                                       onchange="previewChatFiles(this)">
                            </label>

                            {{-- Nút đính kèm tệp từ thư viện --}}
                            <label for="chatAttachments" style="
                                display: inline-flex; align-items: center; gap: 5px;
                                cursor: pointer; font-size: 0.8rem; color: #64748b;
                                padding: 0.35rem 0.7rem; border-radius: 8px;
                                border: 1.5px solid #e5e7eb;
                                transition: border-color 0.15s, color 0.15s;
                            " class="btn-attach" title="Chọn từ Thư viện">
                                <i class="bi bi-images"></i> Thư viện
                                <input type="file" id="chatAttachments" name="attachments[]"
                                       accept=".jpg,.jpeg,.png,.pdf" multiple
                                       style="display:none;"
                                       onchange="previewChatFiles(this)">
                            </label>
                        </div>

                        {{-- Nút gửi --}}
                        <button type="submit" id="btnSend"
                            style="
                                background: var(--tlu-primary);
                                border: none;
                                border-radius: 9px;
                                padding: 0.4rem 1rem;
                                color: white;
                                font-size: 0.83rem;
                                font-weight: 600;
                                display: flex; align-items: center; gap: 6px;
                                transition: background 0.15s;
                            ">
                            <i class="bi bi-send-fill"></i> Gửi
                            <span style="font-size:0.67rem; opacity:0.7;">(Ctrl+↵)</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
    // Auto scroll to bottom of chat
    (function() {
        const cs = document.getElementById('chatStream');
        if (cs) cs.scrollTop = cs.scrollHeight;
    })();

    function previewChatFiles(input) {
        const preview = document.getElementById('chatFilesPreview');
        preview.innerHTML = '';
        preview.style.display = input.files.length ? 'flex' : 'none';
        Array.from(input.files).forEach(f => {
            const span = document.createElement('span');
            span.style.cssText = 'font-size:0.72rem; background:#f1f5f9; border-radius:6px; padding:2px 8px; color:#374151; display:flex; align-items:center; gap:4px;';
            span.innerHTML = `<i class="bi bi-paperclip"></i> ${f.name}`;
            preview.appendChild(span);
        });
    }
</script>
