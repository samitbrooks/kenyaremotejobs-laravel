<x-mail::message>
# Welcome to {{ config('site.name') }}

Hi there,

Your account is ready — you can now unlock listings, save your match profile, and build a CV, all from the same login.

<x-mail::button :url="url('/jobs')">
Browse open jobs
</x-mail::button>

A couple of things worth knowing:

<x-mail::panel>
New listings don't stay up forever — unlock the ones you want with credits (or go unlimited with a subscription) before they disappear.
</x-mail::panel>

Titles, companies, and full descriptions are always free to read. What's gated while a listing is new is just the employer's name and the apply link.

Thanks,<br>
The {{ config('site.name') }} team
</x-mail::message>
