<x-layouts.admin title="Admin: Users">
    <h1 class="text-2xl font-bold">Users</h1>
    <p class="mt-1 text-sm text-foreground/50">{{ $users->count() }} total.</p>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-black/5 bg-white shadow-sm">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-black/10 text-foreground/50">
                    <th class="p-4 font-medium">Email</th>
                    <th class="p-4 font-medium">Access</th>
                    <th class="p-4 font-medium">Joined</th>
                    <th class="p-4 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-black/5 last:border-0">
                        <td class="p-4">{{ $user->email }}</td>
                        <td class="p-4">
                            @if ($user->subscribed)
                                <span class="font-semibold text-emerald-700">Full access</span>
                            @else
                                <span class="text-foreground/50">Pay per job</span>
                            @endif
                        </td>
                        <td class="p-4 text-foreground/50">{{ \App\Support\Format::timeAgo($user->created_at) }}</td>
                        <td class="p-4 text-right">
                            <livewire:admin-subscription-toggle :user-id="$user->id" :subscribed="$user->subscribed" :key="'sub-'.$user->id" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-foreground/50">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
