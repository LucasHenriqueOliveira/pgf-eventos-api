@component('mail::message')
# Change password Request

Clique no botão abaixo para trocar sua senha

@component('mail::button', ['url' => 'https://pfe-eventos.herokuapp.com/api/users/response-password-reset/'.$token])
Nova senha
@endcomponent

Obrigado,<br>
PFE - Eventos
@endcomponent