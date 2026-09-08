@props([])

<form method="POST" action="{{ url('/logout') }}">
    @csrf
    <button type="submit" {{ $attributes->merge(['class' => 'btn-pop rounded-full border border-black/10 px-5 py-2 text-sm font-semibold transition hover:bg-black/5']) }}>
        Log out
    </button>
</form>
