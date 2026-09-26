<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
<tr>
<td style="vertical-align: middle; padding-right: 10px;">
<img src="{{ url('images/logo-icon.png') }}" alt="KenyaRemoteJobs" width="32" height="32" style="width: 32px; height: 32px; display: block; border-radius: 6px; border: 0;" />
</td>
<td style="vertical-align: middle;">
<span style="font-size: 19px; font-weight: 800; letter-spacing: -0.5px; color:#12464c;">Kenya</span><span style="font-size: 19px; font-weight: 800; letter-spacing: -0.5px; color:#ff6b35;">Remote</span><span style="font-size: 19px; font-weight: 800; letter-spacing: -0.5px; color:#12464c;">Jobs</span>
</td>
</tr>
</table>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
