// Manejo acciones del buscador.
(function ($) {
    $('#search .search-input button').click(function (e) {
        e.preventDefault();
        var usuario = $('#search .search-options input[name=usuario]').val(),
            categoria = $('#search .search-options select[name=categoria]').val(),
            query = $('#search .search-input .query').val();

        if (usuario !== '')
        {
            window.location.href = window.site_url+'buscador/q/'+encodeURIComponent(query)+'/1/'+encodeURIComponent(categoria)+'/'+encodeURIComponent(usuario);
        }
        else if(categoria !== '' && categoria !== 'todos')
        {
            window.location.href = window.site_url+'buscador/q/'+encodeURIComponent(query)+'/1/'+encodeURIComponent(categoria);
        }
        else
        {
            window.location.href = window.site_url+'buscador/q/'+encodeURIComponent(query);
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
    $('a[rel="tooltip"],span[rel="tooltip"],.show-tooltip,[title]').tooltip();
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
            if (this.id === 4)
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

//
// Notificaciones AJAX.
//
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
        //
        // Cargamos las nuevas notificaciones.
        //
        setInterval(function () {
            // Borro sucesos viejos.
            $(".pop-notification div.notification").remove();

            $.ajax({
                url: window.site_url+'notificaciones/sin_desplegar',
                dataType: 'json',
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
                    cantidad_notificaciones($("#suceso-dropdown li").length);
                }
            });
        }, 20000);

        //
        // Marcamos como desplegados.
        //
        $('#suceso-dropdown-button').click(function(e) {
            // Cargo listado de sucesos nuevos.
            var sucesos = $("#suceso-dropdown li").map(function () {
                if ($(this).attr('data-desplegado') !== 1)
                {
                    return ($(this).attr('id').toString().split('-')[1])*1;
                }
                else
                {
                    return null;
                }
            }).get();

            // Verifico si hay que marcar como desplegados.
            if (sucesos.length > 0 && $("#suceso-dropdown").css('display') === 'none') {
                $.ajax({
                    url: window.site_url+'notificaciones/desplegadas',
                    type: 'POST',
                    data: {sucesos: sucesos},
                    dataType: 'json',
                    success: function (data) {
                        // Marco elementos como desplegados.
                        $.each(data, function(index, value) {
                            $("#suceso-dropdown li#suceso-"+value).attr('data-desplegado', 1);
                        });
                    }
                });
            }
        });
    }
} (jQuery));

//
// Limpieza de alertas anidadas de mensajes FLASH.
//
$('a[data-dismiss="alert"]').click(function (e) {
    if ($(this).parent().parent().is('.alert-container'))
    {
        if ($(this).parent().parent().children('div.alert-item').length === 1)
        {
            $(this).parent().parent().remove();
        }
    }

    $(this).parent().remove();
    e.preventDefault();
});

//
// Sugerencias de usuarios para citas(@usuario) en publicaciones a los perfiles.
//
(function ($) {
    /**
     * Helper para posición del cursor.
     */
    $.fn.getCursorPosition = function() {
        var el = $(this).get(0);
        var pos = 0;
        if('selectionStart' in el) {
            pos = el.selectionStart;
        } else if('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    };

    /**
     * Inicio autocompletado.
     */
    $("#publicacion").textext({
        plugins: 'autocomplete ajax filter',
        useSuggestionsToFilter: true,
        html: {
            wrap: '<div class="autocomplete-textext"></div>',
            dropdown: '<ul class="dropdown-menu"></ul>',
            hidden: ''
        },
        autocomplete: {
            dropdown: {
                maxHeight: 'none'
            }
        },
        ajax: {
            url: window.site_url+'perfil/usuarios_permitidos/',
            dataType: 'json',
            cacheResults : true
        },
        keys: {
            8   : 'backspace',
            9   : 'tab',
            13  : 'enter',
            27  : 'escape!',
            37  : 'left',
            38  : 'up',
            39  : 'right',
            40  : 'down',
            46  : 'delete',
            108 : 'numpadEnter'
        },
        ext: {
            autocomplete: {
                addDropdownItem: function (html) {
                    var self = this,
                        container = self.containerElement(),
                        node = $('<li class="text-suggestion"><a href="#" class="text-label"></a></li>');

                    node.find('.text-label').html(html);
                    node.find('.text-label').click(function (e) { e.preventDefault(); });
                    container.append(node);
                    return node;
                },
                clearItems: function () {
                    this.containerElement().find('li').remove();
                },
                //onHideDropdown: function () {
                //    this.hideDropdown();
                //    this.input().focus();
                //},
                onUpKeyDown: function (e) {
                    if (this.isDropdownVisible())
                    {
                        this.togglePreviousSuggestion();
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();
                },
                onDownKeyDown: function (e) {
                    if (this.isDropdownVisible())
                    {
                        this.toggleNextSuggestion();
                    }
                    else
                    {
                        this.getSuggestions();
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();
                },
                onUpKeyEnter: function (e) {
                    if (this.isDropdownVisible())
                    {
                        e.stopPropagation();
                        this.selectFromDropdown();
                    }
                },
                toggleNextSuggestion: function () {
                    var self = this,
                        selected = self.selectedSuggestionElement(),
                        next;

                    if(selected.length > 0) {
                        next = selected.next();

                        if(next.length > 0) {
                            selected.removeClass('active');
                        }
                    }
                    else
                    {
                        next = self.suggestionElements().first();
                    }

                    next.addClass('active');
                    self.scrollSuggestionIntoView(next);
                },
                togglePreviousSuggestion: function () {
                    var self = this,
                        selected = self.selectedSuggestionElement(),
                        prev = selected.prev();

                    if(prev.length === 0)
                        return;

                    self.clearSelected();
                    prev.addClass('active');
                    self.scrollSuggestionIntoView(prev);
                },
                clearSelected: function() {
                    this.suggestionElements().removeClass('active');
                },
                selectedSuggestionElement: function () {
                    return this.containerElement().find('li.active');
                },
                selectFromDropdown: function() {
                    var to_append = this.selectedSuggestionElement().text()+' ',
                        input = this.input(),
                        contenido = input.val();

                    // Obtengo última etiqueta.
                    var inicio = contenido.lastIndexOf('@');

                    // Verifico si hay más contenido.
                    if (inicio < 0)
                    {
                        input.val('@'.to_append);
                        return;
                    }

                    // Verifico si hay espacio luego final.
                    var fin = contenido.indexOf(' ', inicio);

                    // No tengo espacios.
                    if (fin < 0)
                    {
                        input.val(contenido.substr(0, inicio)+'@'+to_append);
                    }
                    else
                    {
                        input.val(contenido.substr(0, inicio)+'@'+to_append+contenido.substr(fin));
                    }

                    this.hideDropdown();
                    return;
                }
            },
            itemManager: {
                compareItems: function (item1, item2) {
                    return item1 === item2;
                },
                filter: function (list, query) {
                    // Obtengo linea actual.
                    var query_actual = query.substr(0, $("#publicacion").getCursorPosition()),
                        inicio_linea = query_actual.lastIndexOf('\n');

                    // Verifico inicio de linea correcto.
                    if (inicio_linea < 0)
                    {
                        inicio_linea = 0;
                    }

                    // Trunco a solo linea actual.
                    query_actual = query_actual.substr(inicio_linea);

                    // Obtengo inicial.
                    var ss = query_actual.lastIndexOf(" ");

                    // Verifico existencia.
                    if (ss < 0)
                    {
                        ss = 0;
                    }

                    // Dejo última palabra.
                    query_actual = query_actual.substr(ss).trim();

                    // Verifico si comienza con @.
                    if (query_actual[0] === '@') {
                        q = query_actual.substr(1);
                    }
                    else
                    {
                        return null;
                    }

                    // No proceso vacia, envio todas.
                    if (q === '')
                    {
                        return list;
                    }

                    // Devuelvo todas las que concuerden.
                    return $.map(list, function(value) {
                        if (value.substr(0, q.length) === q)
                        {
                            return value;
                        }
                        else
                        {
                            return null;
                        }
                    });
                },
                itemContains: function (item, needle) {
                    return item.indexOf(needle) > 0;
                },
                itemToString: function (item) {
                    return item;
                },
                stringToItem: function (str) {
                    return str;
                }
            }
        }
    });

    // Elimino estilo.
    $("#publicacion").removeAttr('style');

    // Vuelvo a agregar nombre.
    $("#publicacion").attr('name', 'publicacion');
}(jQuery));

//
// Publicación de links, fotos y videos.
//
(function ($) {
    $("#publicacion-tipo-texto, #publicacion-tipo-foto, #publicacion-tipo-enlace, #publicacion-tipo-video").click(function (e) {
        var tipo = $(this).attr('id').substr(17);
        if ($('#publicacion-contenido input[name="tipo"]').val() !== tipo)
        {
            // Actualizo valores de la barra.
            $('#publicacion-contenido input[name="tipo"]').val(tipo);
            $(this).closest('.nav').find('li.active').each(function () {
                $(this).removeClass('active');
                $(this), $(this).children('a').children('i').removeClass('icon-white').addClass('icon');
            });
            $(this).parent().addClass('active');
            $(this).children('i').removeClass('icon').addClass('icon-white');

            // Actualizo marcado.
            switch (tipo) {
                case 'texto':
                    $('#publicacion-contenido .url').hide();
                    break;
                case 'foto':
                    if ($('#publicacion-contenido .url').length <= 0)
                    {
                        // Agrego el elemento.
                        $('#publicacion-contenido').prepend('<input type="text" name="url" class="url span8" placeholder="URL de la imagen..." />');
                    }
                    else
                    {
                        $('#publicacion-contenido .url').attr('placeholder', 'URL de la imagen...').show();
                    }
                    break;
                case 'enlace':
                    if ($('#publicacion-contenido .url').length <= 0)
                    {
                        // Agrego el elemento.
                        $('#publicacion-contenido').prepend('<input type="text" name="url" class="url span8" placeholder="URL a publicar..." />');
                    }
                    else
                    {
                        $('#publicacion-contenido .url').attr('placeholder', 'URL a publicar...').show();
                    }
                    break;
                case 'video':
                    if ($('#publicacion-contenido .url').length <= 0)
                    {
                        // Agrego el elemento.
                        $('#publicacion-contenido').prepend('<input type="text" name="url" class="url span8" placeholder="URL del video a publicar..." />');
                    }
                    else
                    {
                        $('#publicacion-contenido .url').attr('placeholder', 'URL del video a publicar...').show();
                    }
                    break;
            }
        }
        e.preventDefault();
    });
} (jQuery));

//
// Generación automática de etiquetas en posts y fotos.
//
(function ($) {
    $('.generar-etiquetas').click(function (e) {
        e.preventDefault();

        // Verifico no sea doble click.
        if ($(this).attr('disabled'))
        {
            return false;
        }

        // Evito se envie de nuevo.
        $(this).attr('disabled', 'disabled');

        // Realizo la peticion.
        $.ajax({
            url: window.site_url+'post/etiquetas',
            type: 'POST',
            data: {contenido: $('#contenido').val()},
            error: function (jqXHR, textStatus, errorThrown) {
                $(this).removeAttr('disabled');
            },
            context: $(this),
            success: function (data, textStatus, jqXHR) {
                $(this).prev().val(data);
                $(this).removeAttr('disabled');
            }
        });
    });
} (jQuery));

//
// Selección categorías fotos y posts.
//
(function ($) {
    $('#foto-menu-categoria').change(function () {
        if ($(this).val() !== '')
        {
            location.href = window.site_url+'/foto/categoria/'+$(this).val();
        }
        else
        {
            location.href = window.site_url+'/foto/';
        }
    });
} (jQuery));