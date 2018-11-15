<html>
    <head>
        <title>PFE - INSS</title>
    </head>
    <body>
        <div style="margin-top:40px; padding: 30px;">

            <h1>Alteração de senha</h1>
            @if ($email)
                <form action="https://pfe-eventos.herokuapp.com/api/users/change-password" method="POST" role="form" class="form">
                    
                    <div class="form-group">
                        <label for="novo-password" class="">Nova senha</label>  
                        <input type="password" class="form-control" id="novo-password" placeholder="Nova senha">
                    </div>

                    <div class="form-group">
                        <label for="confirmar-novo-password" class="">Confirmar nova senha</label>  
                        <input type="password" class="form-control" id="confirmar-novo-password" placeholder="Confirmar nova senha">
                    </div>
                    
                    <div class="form-group">
                        <button class="btn btn-primary">Nova senha</button>
                    </div>
                </form>
            @else
                <h4>{{ $message }}</h4>

                <br>
                Obrigado,<br>
                PFE - Eventos
            @endif

        </div>
    </body>
</html>