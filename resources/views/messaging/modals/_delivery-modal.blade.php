<div class="messaging-modal" id="modal-delivery">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Request Delivery</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-delivery')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.request-delivery', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>You are responsible for arranging and paying for delivery. CayMark only routes the request to the seller for approval.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Delivery Address *</label>
                    <input type="text" name="delivery_address" maxlength="255" required placeholder="Enter complete delivery address">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="messaging-modal-field">
                        <label>Preferred Delivery Date</label>
                        <input type="date" name="preferred_date" min="{{ now()->toDateString() }}">
                    </div>
                    <div class="messaging-modal-field">
                        <label>Preferred Delivery Time</label>
                        <input type="time" name="preferred_time">
                    </div>
                </div>
                <div class="messaging-modal-field">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="additional_notes" maxlength="250" data-charcount="charcount-del" placeholder="Enter any additional information..."></textarea>
                    <div class="char-count" id="charcount-del">0 / 250</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-delivery')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
