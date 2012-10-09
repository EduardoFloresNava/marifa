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

    $('.bbcode-bar .btn-list-sorted').click(function (e){ e.preventDefault(); $.markItUp({ target: $(this).closest('.bbcode-bar').parent().find('textarea'), name: 'Bulleted list', openWith: '[list]\n', closeWith: '\n[/ulist]'}); });
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

/*





    myBbcodeSettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "~/sets/bbcode/preview.php",
  markupSet: [
      {name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
      {name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
      {name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
      {separator:'---------------' },

      {separator:'---------------' },
      {name:'Colors', openWith:'[color=[![Color]!]]', closeWith:'[/color]', dropMenu: [
          {name:'Yellow', openWith:'[color=yellow]', closeWith:'[/color]', className:"col1-1" },
          {name:'Orange', openWith:'[color=orange]', closeWith:'[/color]', className:"col1-2" },
          {name:'Red', openWith:'[color=red]', closeWith:'[/color]', className:"col1-3" },
          {name:'Blue', openWith:'[color=blue]', closeWith:'[/color]', className:"col2-1" },
          {name:'Purple', openWith:'[color=purple]', closeWith:'[/color]', className:"col2-2" },
          {name:'Green', openWith:'[color=green]', closeWith:'[/color]', className:"col2-3" },
          {name:'White', openWith:'[color=white]', closeWith:'[/color]', className:"col3-1" },
          {name:'Gray', openWith:'[color=gray]', closeWith:'[/color]', className:"col3-2" },
          {name:'Black', openWith:'[color=black]', closeWith:'[/color]', className:"col3-3" }
      ]},
      {name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]', dropMenu :[
          {name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
          {name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
          {name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
      ]},
      {separator:'---------------' },
      {name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]'},
      {name:'Numeric list', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'},
      {name:'List item', openWith:'[*] '},
      {separator:'---------------' },
      {name:'Quotes', openWith:'[quote]', closeWith:'[/quote]'},
      {name:'Code', openWith:'[code]', closeWith:'[/code]'},
      {separator:'---------------' },
      {name:'Clean', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") } },
      {name:'Preview', className:"preview", call:'preview' }
   ]
}
*/














    /*
    $('#markitup-h1').click(function (){ $.markItUp({placeHolder:'Tu título...', closeWith:function(markItUp) { return markdownTitle(markItUp, '=') }}); });
    $('#markitup-h2').click(function (){ $.markItUp({closeWith:function(markItUp) { return markdownTitle(markItUp, '-') }}); });
    $('#markitup-h3').click(function (){ $.markItUp({placeHolder:'Tu título...', openWith:'### '}); });
    $('#markitup-h4').click(function (){ $.markItUp({placeHolder:'Tu título...', openWith:'#### '}); });
    $('#markitup-h5').click(function (){ $.markItUp({placeHolder:'Tu título...', openWith:'##### '}); });
    $('#markitup-h6').click(function (){ $.markItUp({placeHolder:'Tu título...', openWith:'###### '}); });

    $('#markitup-bold').click(function (){ $.markItUp({openWith:'**', closeWith:'**'}); });
    $('#markitup-italic').click(function (){ $.markItUp({openWith:'_', closeWith:'_'}); });

    $('#markitup-list-sorted').click(function (){ $.markItUp({openWith:function(markItUp) { return markItUp.line+'. ';}}); });
    $('#markitup-list-unsorted').click(function (){ $.markItUp({openWith:'- '}); });

    $('#markitup-picture').click(function (){ $.markItUp({replaceWith:'![[![Texto alternativo]!]]([![Url:!:http://]!] "[![Título]!]")'}); });
    $('#markitup-link').click(function (){ $.markItUp({openWith:'[', closeWith:']([![Url:!:http://]!] "[![Título]!]")', placeHolder:'Tu texto aquí...'}); });

    $('#markitup-quote').click(function (){ $.markItUp({openWith:'> '}); });
    $('#markitup-code').click(function (){ $.markItUp({openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'}); });
*/

}(jQuery));
/* FIN INTERFACE COMENTARIOS REVISION */