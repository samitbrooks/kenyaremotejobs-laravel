<x-mail::message>
@if ($isNewAccount)
# Welcome to {{ config('site.name') }}

Hi {{ $user->name }},

Thanks for signing up. {{ config('site.name') }} connects Kenyan and East African talent with international companies that specifically want to hire from here — no password required, just the one-click link below to confirm your email and get started.
@else
# Your login link

Hi{{ $user->name ? ' '.$user->name : '' }},

Someone (hopefully you) asked to log in to your {{ config('site.name') }} account. Click below to continue — no password needed.
@endif

<x-mail::button :url="$url">
{{ $isNewAccount ? 'Confirm & continue' : 'Log in' }}
</x-mail::button>

<x-mail::panel>
This link is valid for 30 minutes and can only be used to access your own account. If you didn't request it, no action is needed — your account is safe and no one can get in without this specific link.
</x-mail::panel>

Questions or something not working right? Just reply to this email — a real person reads it.

Thanks,<br>
The {{ config('site.name') }} team

<x-slot:subcopy>
Trouble with the button above? Copy and paste this URL into your browser: [{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>
