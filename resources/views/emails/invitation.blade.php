<!DOCTYPE html>
<html>
<head>
    <title>Convite para TechnologySolutions</title>
</head>
<body>
    <h1>Olá!</h1>
    <p>Você foi convidado para finalizar seu cadastro em nossa plataforma.</p>
    <p>Por favor, clique no link abaixo para continuar. Este link é válido por 24 horas.</p>
    
    {{-- link que o usuário irá clicar --}}
    <a href="{{ url('http://localhost:4200/cadastro/' . $invitation->token) }}">Finalizar Cadastro</a>
    
    <p>Se você não solicitou este convite, por favor, ignore este e-mail.</p>
    <p>Obrigado,<br>Equipe TechnologySolutions</p>
</body>
</html>