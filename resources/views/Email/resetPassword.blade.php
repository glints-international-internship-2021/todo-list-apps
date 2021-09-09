@component('mail::message')
# Reset Password Verification

Hi <strong>{{$user}}</strong>!<br>
You have requested to <strong>reset your password</strong>.<br>
If you think this is a mistake, please ignore this message!

@component('mail::button', ['url' => 'http://localhost:8000/api/v1/user/verification?token=' . $token ])
Reset Password
@endcomponent

Thanks,<br>
N-S-W
@endcomponent
