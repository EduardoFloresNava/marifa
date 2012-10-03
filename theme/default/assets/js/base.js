// Manejo acciones del buscador.
(function ($) {
    $('#search .search-input button').click(function (e) {
        e.preventDefault();
        var usuario = $('#search .search-options input[name=usuario]').val(),
            categoria = $('#search .search-options select[name=categoria]').val()
            query = $('#search .search-input .query').val();

        if (usuario != '')
        {
            window.location.href = '/buscador/q/'+encodeURIComponent(query)+'/1/'+encodeURIComponent(categoria)+'/'+encodeURIComponent(usuario);
        }
        else if(categoria != '' && categoria != 'todos')
        {
            window.location.href = '/buscador/q/'+encodeURIComponent(query)+'/1/'+encodeURIComponent(categoria);
        }
        else
        {
            window.location.href = '/buscador/q/'+encodeURIComponent(query);
        }
    });
} ($));

(function ($) {
    $('div.fotos ul.thumbnails').masonry({
        itemSelector: 'li.span4'
    });
} ($));