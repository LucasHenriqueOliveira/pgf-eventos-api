@component('mail::message')
# Change password Request

Clique no botão abaixo para validar seu cadastro

@component('mail::button', ['url' => 'http://lucashenrique.me/pfe/api/users/validar/'.$token])
Validar o cadastro
@endcomponent

Obrigado,<br>
PFE - Eventos
@endcomponent