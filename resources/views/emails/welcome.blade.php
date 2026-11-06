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

Job titles, descriptions, and requirements are free to browse. Application links require Pro access, an active trial or referral pass, or an individual job unlock. Membership does not guarantee an interview or employment.

Thanks,<br>
Daisy from Kenya Remote Jobs
</x-mail::message>
