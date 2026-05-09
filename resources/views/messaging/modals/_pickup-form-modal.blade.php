<div class="messaging-modal" id="modal-pickup-form">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Send Pickup Schedule</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-pickup-form')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.send-pickup-details', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>Buyers see the schedule the moment you submit. Phone numbers, emails and links inside notes will be blocked.</span>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="messaging-modal-field">
                        <label>Pickup Date *</label>
                        <input type="date" name="pickup_date" min="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="messaging-modal-field">
                        <label>Pickup Time *</label>
                        <input type="time" name="pickup_time" required>
                    </div>
                </div>
                <div class="messaging-modal-field">
                    <label>Location / Address *</label>
                    <input type="text" name="street_address" maxlength="255" required placeholder="e.g. 122 Prince Charles Drive, Nassau">
                </div>
                <div class="messaging-modal-field">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="directions_notes" maxlength="500" data-charcount="charcount-sched" placeholder="Directions, gate codes, what to bring, etc."></textarea>
                    <div class="char-count" id="charcount-sched">0 / 500</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-pickup-form')">Cancel</button>
                <button type="submit" class="btn-submit">Send Schedule to Buyer</button>
            </div>
        </form>
    </div>
</div>
