<div class="messaging-modal" id="modal-change">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Propose Date / Time Change</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-change')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.request-change', $activeThread->id) }}">
            @csrf
            <input type="hidden" name="pickup_detail_id" value="{{ $activeThread->latestPickupDetail?->id }}">
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>Provide a new date or time. The pickup location will stay the same — use Request New Location to change it.</span>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="messaging-modal-field">
                        <label>New Pickup Date</label>
                        <input type="date" name="requested_pickup_date" min="{{ now()->toDateString() }}">
                    </div>
                    <div class="messaging-modal-field">
                        <label>New Pickup Time</label>
                        <input type="time" name="requested_pickup_time">
                    </div>
                </div>
                <div class="messaging-modal-field">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="additional_notes" maxlength="250" data-charcount="charcount-chg" placeholder="Enter any additional information..."></textarea>
                    <div class="char-count" id="charcount-chg">0 / 250</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-change')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
