<div class="messaging-modal" id="modal-location">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Request New Location</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-location')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.request-location', $activeThread->id) }}">
            @csrf
            <input type="hidden" name="pickup_detail_id" value="{{ $activeThread->latestPickupDetail?->id }}">
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>The seller must approve any new pickup location. Date and time are not changed by this form — use Propose Change for that.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Proposed New Location *</label>
                    <input type="text" name="requested_location" maxlength="255" required placeholder="Enter proposed address or location">
                </div>
                <div class="messaging-modal-field">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="additional_notes" maxlength="250" data-charcount="charcount-loc" placeholder="Enter any additional information..."></textarea>
                    <div class="char-count" id="charcount-loc">0 / 250</div>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-location')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
