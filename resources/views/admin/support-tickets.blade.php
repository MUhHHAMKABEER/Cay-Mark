@extends('layouts.admin')

@section('title', 'Support Tickets - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Support Tickets</h1>
        <p class="text-gray-600 mt-2">View and manage user support requests</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Tickets</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Open</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">In Progress</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Resolved</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $ticket->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $ticket->user->email ?? '' }}</div>
                            <div class="text-xs text-gray-400">{{ ucfirst($ticket->user->role ?? 'guest') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ticket->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ Str::limit($ticket->message, 80) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ticket->created_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button type="button" onclick="openReplyModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}', '{{ addslashes($ticket->message) }}')" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-ticket-alt text-4xl mb-3 text-gray-300"></i>
                            <p>No support tickets</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>

<div id="replyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <form id="replyForm" method="POST">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Reply to Ticket</h3>
            <p class="text-sm text-gray-600 mb-1"><strong id="replyTicketTitle"></strong></p>
            <p class="text-sm text-gray-500 mb-4" id="replyTicketMessage"></p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Reply</label>
                <textarea name="admin_reply" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Type your reply..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeReplyModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Send Reply</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReplyModal(id, title, message) {
    document.getElementById('replyForm').action = "{{ url('admin/support-tickets') }}/" + id + "/reply";
    document.getElementById('replyTicketTitle').textContent = title;
    document.getElementById('replyTicketMessage').textContent = message;
    document.getElementById('replyModal').classList.remove('hidden');
}
function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
}
window.onclick = function(e) {
    var modal = document.getElementById('replyModal');
    if (e.target == modal) closeReplyModal();
};
</script>
@endsection
