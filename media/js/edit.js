$(function() {

    /**
     * удаление
     */
    $(".deleteStruct").click(function(e) {

        if (!confirm("Вы уверены, что хотите удалить узел?")) {
            return false;
        }

        window.location = $(this).find("a").prop("href");

    });

    $(".addStruct").click(function() {
        window.location = $(this).find("a").prop("href");
    });

    /**
     * настройки
     */
    $("#showSettings").click(function() {
        var settings = $("#settings");

        if (settings.css('display') == 'none') {
            settings.show();
        }
        else {
            settings.hide();
        }
    });

    /**
     * Перемещение
     */
    $(".elementMenu").draggable({
        "zIndex": 100,
        "drag": function(event, ui) {
//            var element = $(this);
//            element.css('z-index', -1);
        },
        "stop": function(event, ui) {

            var nom2;
            var element = $(this);
            var elements = $(".elementMenu");
            elements.each(function(index, val) {

                var curElem = $(val);
                if (curElem.attr("nom") != element.attr("nom")) {
                    var curCoord = curElem.offset();

//                    console.log(curCoord);

                    var elemCoord = element.offset();

                    var curHeight = curElem.height();
                    var curWidth = curElem.width();

//                    console.log("elem");
//                    console.log(elemCoord);

                    if (curCoord.top <= elemCoord.top &&
                            curCoord.left <= elemCoord.left &&
                            elemCoord.top <= curCoord.top + curHeight &&
                            elemCoord.left <= curCoord.left + curWidth)
                    {
//                        console.log(curElem.attr("nom"));

                        nom2 = curElem.attr("nom");
                    }
                }

            });

            nom = element.attr('nom');

            if (nom && nom2) {
                var reqUrl = '/structure/move/' + nom + '/' + nom2;
//                console.log(reqUrl);

                $.ajax({
                    type: "POST",
                    url: reqUrl,
                    data: "{}",
                    success: function(data) {
//                        console.log("success");
//                        console.log(data);

                        refresh();
                    },
                    error: function(data) {
//                        console.log("error");
//                        console.log("error" + data.responseText);
//                        console.log("error" + data);

                        refresh();
                    }

                });
            } else {
                refresh();
            }
        }
    });

    function refresh()
    {
        window.location = window.location;
    }

    /**
     * баннер?
     */
    $("#allBaners").change(function() {
        location.href = '/other/baner/' + $("#allBaners :selected").val();
    });

    $('textarea.tinymce').tinymce({
        // General options
        theme: "modern",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,ibrowser",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "ibrowser,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        extended_valid_elements: "article[*],div[*]",
        // Replace values for the template plugin
        template_replace_values: {
            username: "Some User",
            staffid: "991234"
        }
    });

});