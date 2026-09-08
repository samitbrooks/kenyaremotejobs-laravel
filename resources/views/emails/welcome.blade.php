<x-mail::message>
# Welcome to {{ config('site.name') }}

Hi there,

Your account is ready — you can now unlock listings, save your match profile, and build a CV, all from the same login.

<x-mail::button :url="url('/jobs')">
Browse open jobs
</x-mail::button>

A couple of things worth knowing:

<x-mail::panel>
Every listing opens up **completely free** {{ config('jobs.premium_window_days') }} days after it's posted — no account needed for that part, credits or not.
</x-mail::panel>

Titles, companies, and full descriptions are always free to read. What's gated while a listing is new is just the employer's name and the apply link.

Thanks,<br>
The {{ config('site.name') }} team
</x-mail::message>
