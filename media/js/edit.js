$(function() {
    
    $("#showSettings").click(function(){
        if($("#settings").css('display')=='none')
            $("#settings").show();
        else
            $("#settings").hide();
    });
    
  var selectMenu;
  //Перемещение
  $( ".elementMenu").draggable({
    
    'drag':function(event,ui){
       var element = $(this);
       div = ui.helper.find(".elementMenu");
       element.css('z-index',-1);
    },
    'stop':function(event,ui){
        var element = $(this);
        
        nom = element.attr('nom');
        nom2 = selectMenu;
        
        if(nom && nom2){
            $.ajax({
                type: "POST",
                    url: '/admin/structure/move/'+nom+'/'+nom2,
                    data: "{}",

                    success: function(data) {
                        window.location = window.location;
                    },
                    error: function(data){
                        
                        window.location = window.location;
                    }

                });
        }else{
            window.location = window.location;
        }
    }
  });
  
  $( ".elementMenu" ).mouseover(function(){
      selectMenu = $(this).attr('nom');
  });
  
  $( ".elementMenu" ).mouseout(function(){
      selectMenu = 0;
  });
  
  $("#allBaners").change(function(){
      location.href = '/admin/other/baner/'+$("#allBaners :selected").val();
  });
  

  
  $('textarea.tinymce').tinymce({
			// Location of TinyMCE script
        script_url : '/../media/js/tiny_mce/tiny_mce.js',
        
        // General options
        theme : "advanced",
        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,ibrowser",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "ibrowser,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        extended_valid_elements:"article[*],div[*]",
        // Example content CSS (should be your site CSS)
        content_css : "css/content.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});

  
});