<div class="messaging-modal" id="modal-third-party">
    <div class="messaging-modal-card">
        <div class="messaging-modal-header">
            <h3>Third-Party / Tow Truck Pickup</h3>
            <button type="button" class="close-btn" onclick="closeMessagingModal('modal-third-party')">&times;</button>
        </div>
        <form method="POST" action="{{ route('messaging.thread.authorize-third-party', $activeThread->id) }}">
            @csrf
            <div class="messaging-modal-body">
                <div class="messaging-modal-notice">
                    <span class="material-icons-round">info</span>
                    <span>You are responsible for arranging and paying for any third-party or towing services. CayMark will not handle scheduling on their behalf.</span>
                </div>
                <div class="messaging-modal-field">
                    <label>Pickup Type *</label>
                    <div style="display:flex; gap: 12px; flex-wrap: wrap;">
                        <label style="display:flex; align-items:center; gap:6px; font-weight:500; font-size:0.85rem;">
                            <input type="radio" name="pickup_type" value="individual" required> Third-Party / Individual
                        </label>
                        <label style="display:flex; align-items:center; gap:6px; font-weight:500; font-size:0.85rem;">
                            <input type="radio" name="pickup_type" value="tow_company"> Tow Truck / Towing Company
                        </label>
                    </div>
                </div>
                <div class="messaging-modal-field">
                    <label>Name of Person or Company *</label>
                    <input type="text" name="authorized_name" maxlength="255" required placeholder="Enter name of person or company">
                </div>
                <div class="messaging-modal-field">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="additional_notes" maxlength="250" data-charcount="charcount-tp" placeholder="Enter any additional information..."></textarea>
                    <div class="char-count" id="charcount-tp">0 / 250</div>
                </div>
                <div class="messaging-modal-notice" style="background:#fef3c7; border-color:#fbbf24; color:#92400e;">
                    <span class="material-icons-round" style="color:#d97706;">warning_amber</span>
                    <span>You are fully responsible for ensuring the third party is available at the on-site delivery location to receive the vehicle. You must securely give them the pickup code so they can present it at collection.</span>
                </div>
            </div>
            <div class="messaging-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMessagingModal('modal-third-party')">Cancel</button>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
