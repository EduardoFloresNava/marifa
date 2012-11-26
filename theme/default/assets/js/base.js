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
} (jQuery));

// Acomodar automáticamente donde van ubicadas las fotos de la vista de fotos.
(function ($) {
    $('div.fotos ul.thumbnails').masonry({
        itemSelector: 'li.span4'
    });
} (jQuery));

// Tooptips en los elementos.
(function ($) {
    $('a[rel="tooltip"],span[rel="tooltip"]').tooltip();
} (jQuery));

// Grupos predefinidos de los permisos de rangos.
(function ($) {
    $('#permiso-tipo-usuario').click(function (e) {
        $('form[name=permisos] input[type=checkbox]').each(function(key, value) {
            if ($.inArray($(value).attr('id'), ["4", "20", "21", "40", "41", "60", "62"]) >= 0)
            {
                $(value).attr('checked', true);
            }
            else
            {
                $(value).attr('checked', false);
            }
        });

        e.preventDefault();
    });

    $('#permiso-tipo-moderador').click(function (e) {
        $('form[name=permisos] input[type=checkbox]').each(function(key, value) {
            if ($.inArray(this.id, ["0", "1", "2", "3", "20", "21", "22", "23", "24", "25", "26", "27", "28", "40", "41", "42", "43", "44", "45", "46", "47", "60", "61", "62", "63", "64", "65", "66"]) >= 0)
            {
                $(value).attr('checked', true);
            }
            else
            {
                $(value).attr('checked', false);
            }
        });

        e.preventDefault();
    });

    $('#permiso-tipo-administrador').click(function (e) {
        $('form[name=permisos] input[type=checkbox]').each(function(key, value) {
            if (this.id == 4)
            {
                $(value).attr('checked', false);
            }
            else
            {
                $(value).attr('checked', true);
            }
        });

        e.preventDefault();
    });
} (jQuery));

(function ($) {
    if (/#c\-[0-9]+/.test(window.location.hash))
    {
        $(window.location.hash).addClass('highlight');
    }
} (jQuery));