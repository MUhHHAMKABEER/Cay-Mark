<div class="messaging-modal" id="modal-pin">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Confirm Pickup with PIN</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-pin')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.confirm-pickup', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>The buyer will give you a 4-digit PIN at pickup. Enter it here to mark the transaction complete and trigger your payout.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Pickup PIN *</label>
                    <input type="text" name="pickup_pin" required maxlength="4" pattern="[0-9]{4}" placeholder="1234" style="text-align:center; font-size: 1.5rem; font-family: 'Courier New', monospace; letter-spacing: 0.5em;">
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-pin')">Cancel</button>
                <button type="submit" class="btn-submit" style="background:#10b981;">Confirm Pickup</button>
            </div>
        </form>
    </div>
</div>
