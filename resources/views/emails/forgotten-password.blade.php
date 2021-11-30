@component('mail::message')
# todolist/QTodo app - Forgotten password

Hello. You are receiving this mail because you started the reset password process.
Here is your new password.

@component('mail::panel')
    {{ $pwd }}
@endcomponent

## Don't forget to change it after the login

Thanks,<br>
todolist/QTodo
@endcomponent
