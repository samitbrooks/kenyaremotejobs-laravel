@props(['count' => null])

<div class="border-b border-slate-200 bg-[#f8f9fd] px-4 py-2 text-xs text-slate-600">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2">
        <p>Remote opportunities for people working from Kenya @if ($count !== null) <span class="ml-2 text-slate-500">{{ number_format($count) }} current listings</span> @endif</p>
        <a href="{{ config('site.telegram_url') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-[#3444da] hover:underline">Job updates on Telegram &rarr;</a>
    </div>
</div>
