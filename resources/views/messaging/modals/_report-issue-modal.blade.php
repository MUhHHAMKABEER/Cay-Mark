@php
    use App\Models\SupportTicket;
    use App\Models\User;
    $reportCategories = $isSeller
        ? SupportTicket::CATEGORY_OPTIONS_SELLER
        : SupportTicket::CATEGORY_OPTIONS_BUYER;
@endphp
<div class="messaging-modal" id="modal-report">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Report Issue</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-report')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.report-issue', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>This opens a CayMark support ticket linked to this transaction. Our team will reach out via your account.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="">Select issue type…</option>
                        @foreach ($reportCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="messaging-modal-field">
                    <label>Describe the issue *</label>
                    <textarea name="body" minlength="10" maxlength="800" required data-charcount="charcount-report" placeholder="Please share what happened (10–800 characters)..."></textarea>
                    <div class="char-count" id="charcount-report">0 / 800</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-report')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
