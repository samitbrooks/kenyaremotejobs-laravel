<x-mail::message>
{!! nl2br(e($body)) !!}

Thanks,<br>
The {{ config('site.name') }} team

<x-slot:subcopy>
You're receiving this because you have an account at {{ config('site.name') }}.
[Unsubscribe]({{ $unsubscribeUrl }}) at any time.
</x-slot:subcopy>
</x-mail::message>
