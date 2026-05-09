<div class="messaging-modal" id="modal-other">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Other Request</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-other')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.other-request', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>Use this for anything not covered by the other actions. Phone numbers, emails and external links will be blocked.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Subject *</label>
                    <input type="text" name="subject" maxlength="120" required placeholder="What's this about?">
                </div>
                <div class="messaging-modal-field">
                    <label>Message *</label>
                    <textarea name="body" minlength="5" maxlength="500" required data-charcount="charcount-other" placeholder="Describe your request..."></textarea>
                    <div class="char-count" id="charcount-other">0 / 500</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-other')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
