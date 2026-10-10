<x-mail::message>
# Confirm your subscription

Hello@if($subscriber->name) {{ $subscriber->name }}@endif,

Please confirm your email address to join the **Sharzhukov's Laboratory** newsletter. You'll receive monthly updates — new projects, blog posts, and behind-the-scenes notes. No spam.

<x-mail::button :url="$confirmUrl" color="primary">
Confirm subscription
</x-mail::button>

If you didn't subscribe, you can safely ignore this email.

Thanks,<br>
Sharzhukov's Laboratory
</x-mail::message>