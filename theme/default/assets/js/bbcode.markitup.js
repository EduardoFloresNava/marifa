/* INTERFACE BBCODE */
!(function ($) {
    /* AGREGAMOS LOS TOOPTIPS */
    $('.bbcode-bar .btn').tooltip({placement: 'top'});

    /* INICIAMOS CON LAS CONFIGURACIONES */
    $('.bbcode-bar + textarea').markItUp({ previewParserPath: '', onShiftEnter: { keepDefault: false, openWith:'\n\n'}, markupSet:[] });

    /* LISTADO ETIQUETAS */
    $('.bbcode-bar .btn-bold').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bold', key: 'B', openWith:'[b]', closeWith: '[/b]'}); });
    $('.bbcode-bar .btn-italic').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bold', key: 'I', openWith:'[i]', closeWith: '[/i]'}); });
    $('.bbcode-bar .btn-underline').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bold', key: 'U', openWith:'[u]', closeWith: '[/u]'}); });
    $('.bbcode-bar .btn-strike').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bold', key: 'S', openWith:'[s]', closeWith: '[/s]'}); });

    $('.bbcode-bar .btn-align-left').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Align Left', openWith:'[align="left"]', closeWith: '[/align]'}); });
    $('.bbcode-bar .btn-align-right').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Align Right', openWith:'[align="right"]', closeWith: '[/align]'}); });
    $('.bbcode-bar .btn-align-center').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Align center', openWith:'[align="center"]', closeWith: '[/align]'}); });
    $('.bbcode-bar .btn-align-justify').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Align justify', openWith:'[align="justify"]', closeWith: '[/align]'}); });

    $('.bbcode-bar .btn-h1').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H1', openWith: '[h1]', closeWith: '[/h1]'}); });
    $('.bbcode-bar .btn-h2').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H2', openWith: '[h2]', closeWith: '[/h2]'}); });
    $('.bbcode-bar .btn-h3').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H3', openWith: '[h3]', closeWith: '[/h3]'}); });
    $('.bbcode-bar .btn-h4').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H4', openWith: '[h4]', closeWith: '[/h4]'}); });
    $('.bbcode-bar .btn-h5').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H5', openWith: '[h5]', closeWith: '[/h5]'}); });
    $('.bbcode-bar .btn-h6').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'H6', openWith: '[h6]', closeWith: '[/h6]'}); });

    $('.bbcode-bar .btn-list-sorted').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bulleted list', openWith: '[list]\n', closeWith: '\n[/list]'}); });
    $('.bbcode-bar .btn-list-unsorted').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Numeric list', openWith: '[olist]\n', closeWith: '\n[/olist]'}); });
    $('.bbcode-bar .btn-list-item').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'List Item', openWith: '[li]', closeWith: '[/li]'}); });

    $('.bbcode-bar .btn-picture').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Picture', key: 'P', replaceWith: '[img][![Url]!][/img]'}); });
    $('.bbcode-bar .btn-link').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Link', key: 'L', openWith: '[url=[![Url]!]]', closeWith: '[/url]', placeHolder: 'Tu texto aquí...'}); });

    $('.bbcode-bar .btn-spoiler').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Spoiler', openWith: '[spoiler]', closeWith: '[/spoiler]'}); });
    $('.bbcode-bar .btn-quote').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Quote', openWith: '[quote]', closeWith: '[/quote]'}); });
    $('.bbcode-bar .btn-code').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Code', openWith: '[code]', closeWith: '[/code]'}); });

    /* CITAR COMENTARIOS DE OTROS USUARIOS */
    $('.btn-quote-comment').click(function (e) {
        e.preventDefault();

        var attr = $(this).attr('data-user'),
            contenido = $('.comentario-body', $(this).closest('.comentario')).text();

        if (contenido)
        {
            if (attr)
            {
                $.markItUp({openWith: '[quote="'+attr+'"]'+contenido+'[/quote]'});
            }
            else
            {
                $.markItUp({openWith: '[quote]'+contenido+'[/quote]'});
            }
        }
    });
}(jQuery));
/* FIN INTERFACE COMENTARIOS REVISION */