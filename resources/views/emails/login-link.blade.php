<x-mail::message>
@if ($isNewAccount)
# Confirm your account

Hi {{ $user->name }},

One click and you're in — no password to remember.
@else
# Log in to {{ config('site.name') }}

Hi{{ $user->name ? ' '.$user->name : '' }},

Here's your one-click login link.
@endif

<x-mail::button :url="$url">
{{ $isNewAccount ? 'Confirm & continue' : 'Log in' }}
</x-mail::button>

This link expires in 30 minutes. If you didn't request this, you can safely ignore this email.

Thanks,<br>
The {{ config('site.name') }} team
</x-mail::message>
