@component('mail::message')
# {{ $notification->title }}

{{ $notification->body }}

@if($notification->link)
@component('mail::button', ['url' => $notification->link])
Learn More
@endcomponent
@endif

Thanks,<br>
{{ $organisation->name }}

@component('mail::subcopy')
You're receiving this email because you're subscribed to {{ $organisation->name }} on Notifi.
@endcomponent
@endcomponent
