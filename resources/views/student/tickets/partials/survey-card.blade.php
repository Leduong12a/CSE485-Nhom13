{{-- partial: survey-card.blade.php --}}
{{-- Card đánh giá 5 sao (Satisfaction Survey) - hiển thị khi ticket = RESOLVED và chưa có survey --}}

<div class="detail-card mb-4" style="border: 2px solid #fbbf24; background: linear-gradient(135deg, #fffbeb, #fef9c3);">
    <div class="detail-card-body" style="padding: 1.5rem;">

        {{-- Header --}}
        <div class="text-center mb-3">
            <div style="font-size:2rem; margin-bottom:6px;">🎉</div>
            <h3 style="font-size:1.05rem; font-weight:700; color:#1e293b; margin:0;">Sự cố đã được khắc phục!</h3>
            <p style="font-size:0.82rem; color:#92400e; margin:4px 0 0;">
                Hãy đánh giá chất lượng phục vụ để giúp chúng tôi cải thiện nhé.
            </p>
        </div>

        <form method="POST" action="{{ route('student.tickets.survey', $ticket->id) }}" id="surveyForm">
            @csrf

            {{-- Star Rating --}}
            <div class="text-center mb-3">
                <div class="star-rating" id="starRating">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating_stars" value="{{ $i }}"
                               {{ old('rating_stars') == $i ? 'checked' : '' }}>
                        <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                    @endfor
                </div>
                @error('rating_stars')
                    <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                @enderror
                <div id="ratingText" style="font-size:0.78rem; color:#92400e; margin-top:6px; min-height:1.2em;"></div>
            </div>

            {{-- Comment --}}
            <div class="mb-3">
                <textarea name="comment" rows="3"
                    class="form-control"
                    placeholder="Chia sẻ cảm nhận của bạn hoặc góp ý cho chúng tôi... (tùy chọn)"
                    style="border-radius:10px; border:1.5px solid #fde68a; background:rgba(255,255,255,0.7); font-size:0.85rem; resize:none;">{{ old('comment') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="btnSurvey"
                    class="btn flex-grow-1"
                    style="border-radius:10px; font-weight:700; padding:0.65rem; background:linear-gradient(90deg,#f59e0b,#fbbf24); border:none; color:white; box-shadow:0 4px 14px rgba(245,158,11,0.35);">
                    <span id="surveyText"><i class="bi bi-star-fill me-2"></i>Gửi đánh giá</span>
                    <span id="surveySpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...
                    </span>
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 4px;
    }

    .star-rating input[type="radio"] { display: none; }

    .star-rating label {
        font-size: 2.2rem;
        color: #d1d5db;
        cursor: pointer;
        transition: color 0.15s, transform 0.12s;
        line-height: 1;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #f59e0b;
    }

    .star-rating label:hover { transform: scale(1.15); }
</style>

<script>
    const ratingLabels = {
        1: '😞 Rất không hài lòng',
        2: '😕 Không hài lòng',
        3: '😐 Bình thường',
        4: '😊 Hài lòng',
        5: '🤩 Rất hài lòng!',
    };

    document.querySelectorAll('.star-rating input').forEach(input => {
        input.addEventListener('change', () => {
            document.getElementById('ratingText').textContent = ratingLabels[input.value] ?? '';
        });
    });

    document.getElementById('surveyForm').addEventListener('submit', () => {
        document.getElementById('surveyText').classList.add('d-none');
        document.getElementById('surveySpinner').classList.remove('d-none');
        document.getElementById('btnSurvey').disabled = true;
    });
</script>
