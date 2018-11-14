@component('mail::message')
# Change password Request

Clique no botão abaixo para trocar sua senha

@component('mail::button', ['url' => 'http://localhost:4200/response-password-reset?token='.$token])
Nova senha
@endcomponent

Obrigado,<br>
PFE - Eventos
@endcomponent