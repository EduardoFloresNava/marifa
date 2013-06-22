// Genero cadena aleatoria para las cookie's.
$('#make_random_cookie_secret').click(function ()
{
    // Armo cadena aleatoria.
    var text = "";
    var possible = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ+-*#&@!?";

    for (var i = 0; i < 20; i++)
    {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    $('#cookie_secret').val(text);
});