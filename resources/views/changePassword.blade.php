<html>
    <head>
        <title>PFE - INSS</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <div style="margin-top:40px; padding: 30px;">

            <h1>Alteração de senha</h1>
            @if ($email)
                <form name="form" role="form" class="form">
                    
                    <div class="form-group">
                        <label for="novo-password" class="">Nova senha</label>  
                        <input type="password" class="form-control" name="password" id="password" placeholder="Nova senha">
                    </div>

                    <div class="form-group">
                        <label for="confirmar-novo-password" class="">Confirmar nova senha</label>  
                        <input type="password" class="form-control" id="confirmar-novo-password" placeholder="Confirmar nova senha">
                    </div>

                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="form-group">
                        <button type="button" id="submit" class="btn btn-primary">Nova senha</button>
                    </div>
                </form>
            @else
                <h4>{{ $message }}</h4>

                <br>
                Obrigado,<br>
                PFE - Eventos
            @endif

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
            </script>
            <script>
                $(function() {
                    //twitter bootstrap script
                    $("button#submit").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "https://pfe-eventos.herokuapp.com/api/users/change-password",
                            dataType: "json",
                            data: $('form.form').serialize(),
                            success: function(msg){
                                alert("Nova senha alterada com sucesso. Favor fazer login com a nova senha no aplicativo.");
                            },
                            error: function(){
                                alert("Ocorreu um erro na troca de senha.");
                            }
                       });

                        $('form.form').trigger("reset");
                    });
                });
            </script>
        </div>
    </body>
</html>