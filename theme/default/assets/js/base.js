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
    $('a[rel="tooltip"],span[rel="tooltip"],.show-tooltip').tooltip();
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

// Resaltado de comentarios por HASH.
(function ($) {
    if (/#c\-[0-9]+/.test(window.location.hash))
    {
        $(window.location.hash).addClass('highlight');
    }
} (jQuery));

/**
 * Notificaciones AJAX.
 */
(function ($) {
    // Función para actualiza la cantidad de notificaciones pendientes.
    function cantidad_notificaciones(cantidad) {
        if (cantidad <= 0)
        {
            $("#suceso-dropdown-button span.badge").remove();
        }
        else
        {
            if ($("#suceso-dropdown-button span.badge").length <=0)
            {
                $('#suceso-dropdown-button').append('<span class="badge badge-important event">'+cantidad+'</span>');
            }
            else
            {
                $("#suceso-dropdown-button span.badge").html(cantidad);
            }
        }
    }

    // Actualización de listado de sucesos y notificación de nuevos.
    if ($("#suceso-dropdown-button").length > 0)
    {
        setInterval(function () {
            // Borro sucesos viejos.
            $(".pop-notification div.notification").remove();

            $.ajax({
                url: '/notificaciones/sin_desplegar',
                dataType: 'json',
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                },
                success: function (data, textStatus, jqXHR) {
                    // Cargo los elementos.
                    $.each(data, function(index, value) {
                        if ($("#suceso-dropdown li#suceso-"+value['id']).length <= 0)
                        {
                            // Borro elemento si no existe.
                            $("#suceso-dropdown .alert").remove();

                            // Agrego a la lista de notificaciones.
                            $("#suceso-dropdown").prepend('<li data-desplegado="" id="suceso-'+value['id']+'">'+value['html']+'</li>');

                            // Notifico en listado.
                            $(".pop-notification").prepend('<div style="display: none;" class="notification" id="suceso-'+value['id']+'">'+value['html']+'<a class="close" data-dismiss="alert">×</a></div>');
                        }
                    });

                    // Muestro los nuevos.
                    $(".pop-notification div.notification").slideDown();

                    // Recalculo indice.
                    cantidad_notificaciones($("#suceso-dropdown li").map(function () {
                        if ($(this).attr('data-desplegado') != 1)
                        {
                            return ($(this).attr('id').toString().split('-')[1])*1;
                        }
                        else
                        {
                            return null;
                        }
                    }).get().length);
                }
            });
        }, 20000);
    }
} (jQuery));

$('a[data-dismiss="alert"]').click(function (e) {
    if ($(this).parent().parent().is('.alert-container'))
    {
        if ($(this).parent().parent().children('div.alert-item').length == 1)
        {
            $(this).parent().parent().remove();
        }
    }

    $(this).parent().remove();
    e.preventDefault();
});