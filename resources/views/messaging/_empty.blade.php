<div class="empty-state">
    <span class="material-icons-round">inbox</span>
    @if ($threads->isEmpty())
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #475569; margin-bottom: 6px;">No transactions yet</h3>
        <p style="font-size: 0.875rem; color: #94a3b8; max-width: 360px;">When you win an auction or someone wins yours, the Messaging Center for that transaction will appear here.</p>
    @else
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #475569; margin-bottom: 6px;">Pick a transaction</h3>
        <p style="font-size: 0.875rem; color: #94a3b8; max-width: 360px;">Choose a car on the left to open its Messaging Center.</p>
    @endif
</div>
