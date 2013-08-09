/**
 * Utility code for eChecklist
 */
$("a.pd").click(function(event) {
  event.preventDefault();
});

function noteChange() {
  changed = true;
}

function setlab(id, lab_name, lab_id) {
  /**
   * sets the selected labid and labname
   */
  $('#'+id).val(lab_name);
  labname = lab_name;
  labid = lab_id;
  $(".ui-autocomplete").html('');
  //return false;
}

function log( message ) {
  $( "<div>" ).text( message ).prependTo( "#{$n}_results" );
  $( "#{$n}_results" ).scrollTop( 0 );
}

function ecAutocomplete(name, url, callback, callbackname){
  $('#'+name).autocomplete({
    minLength: 1,
    delay: 80,
    source: url,
    messages: {
      noResults: '',
      results: function() {}
    },
    select: function(event, ui) {
      $('#'+name).autocomplete('destroy');
      callback(name, ui.item.labname, ui.item.id);
      $("ui-autocomplete ul").html('');
    },
  })
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
    .html( '<div class="pd" style="color:green;font-size:22px;" ' +
        'onclick="'+callbackname+'(\''+name+'\',\''+item.labname+
        '\','+item.id+');" > '
        + item.id + ' ' + item.labname + "</div>" )
        .appendTo( ul );
  };
};

function watch_ynp(name, score) {
  var selector = "input[name='"+ name +"']";
  $(selector).change(function() {
    var out = 0;
    // alert($(this).val());
    switch($(this).val()) {
      case 'YES':
        out = score;
        break;
      case 'PARTIAL':
        out = 1;
        break;
      case 'NO':
        out = 0;
        break;
      default:
        out = 0;
    };
    $('#'+name+'_score').val(out.toString());
    $('#'+name+'_icon').remove();
    changed = true;
  });
}

function watch_radio(name) {
  var selector = "input[name="+ name +"]";
  $(selector).click(function() {
    //$('#'+name+'_icon').attr('src', '');
    $("#"+name+'_icon').remove();
    changed= true;
  });
}

function watch_select(name, baseurl) {
  var selector = "#"+name;
  $(selector).change(function() {
    switch($(this).val()) {
      case '-':
        $(this).after(' <img id="' + name + '_icon" ' + 'src="'+baseurl+'/cancel-on.png" />');
        changed = true;
        break;
      default:
        $("#"+name+'_icon').remove();
        changed= true;
    }
  });
}

function clear()  {
  var url = $(location).attr('href');
  //window.location.pathname;
  window.location= url;
}
/*
$('form').click(function(event) {
    //event.preventDefautlt();
  $(this).data('clicked',$(event.target))
});

function formSubmit() {
/ *  if ($('form input[name=sbsave]').data('clicked').is('[name=sbsave]'))
      alert('save');
  if ($('form input[name=sbsavec]').data('clicked').is('[name=sbsavec]'))
    alert('save&c');
 * /

}*/
/*$(document).ready(function() {
  $("form").submit(function() { 

    var val = $("input[type=submit][clicked=true]").val();

    switch (val) {
      case 'Cancel':
      case 'Save':
      case 'Save & Continue':
        alert(val);
        break;
      default:  
    }

  });
});

$("form input[type=submit]").click(function() {
  $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
  $(this).attr("clicked", "true");
});
 */ 

function save() {
  var url = $(location).attr('href');

}

function toggleNCBox(here) {
  var id = here.id;
  var name = '#'+id+'_nc';
  var divname = '#div'+id+'_nc';
  var state = $('#'+id).prop('checked');
  if (!!state) {
    $(name).val('T');
    $(divname).show(1000);
  } else {
    $(name).val('F');
    $(divname).hide(1000);
  }
}
// for template only - with id=rightpane
function resetSize() {
  var ht = $(window).height();
  var wt = $(window).width();
  var rht = ht - 34;
  $('#rightpane').css('height', rht+'px');
}

function getRadioClicked(name) {
  $("input:radio[name="+name+"]").click(function() {
    var value = $this.val();
  });
}
$(function() {
    $('.datepicker').datepicker();
    $('.bpad').mouseover( function() {
        $(this).css('background-color', '#ccffcc');
    }).mouseout(function() {
        $(this).css('background-color', '#ffffff');
    });
});


/* From web2py.js */
/*
function popup(url) {
  newwindow=window.open(url,'name','height=400,width=600');
  if (window.focus) newwindow.focus();
  return false;
}
function collapse(id) { jQuery('#'+id).slideToggle(); }
function fade(id,value) { if(value>0) jQuery('#'+id).hide().fadeIn('slow'); else jQuery('#'+id).show().fadeOut('slow'); }
function ajax(u,s,t) {
    var i;
    query = '';
    if (typeof s == "string") {
        d = jQuery(s).serialize();
        if(d){ query = d; }
    } else {
        pcs = [];
        if (s != null && s != undefined) for(i=0; i<s.length; i++) {
            q = jQuery("[name="+s[i]+"]").serialize();
            if(q){pcs.push(q);}
        }
        if (pcs.length>0){query = pcs.join("&");}
    }
    jQuery.ajax({type: "POST", url: u, data: query, success: function(msg) { if(t) { if(t==':eval') eval(msg); else if(typeof t=='string') jQuery("#"+t).html(msg); else t(msg); } } });
}

String.prototype.reverse = function () { return this.split('').reverse().join('');};
function web2py_ajax_fields(target) {
  var w2p_ajax_date_format = "%m/%d/%Y";
  var date_format = (typeof w2p_ajax_date_format != 'undefined') ? w2p_ajax_date_format : "%Y-%m-%d";
  var datetime_format = (typeof w2p_ajax_datetime_format != 'undefined') ? w2p_ajax_datetime_format : "%Y-%m-%d %H:%M:%S";
  jQuery("input.date",target).each(function() {Calendar.setup({inputField:this, ifFormat:date_format, showsTime:false });});
  jQuery("input.datetime",target).each(function() {Calendar.setup({inputField:this, ifFormat:datetime_format, showsTime: true, timeFormat: "24" });});
  jQuery("input.time",target).each(function(){jQuery(this).timeEntry();});

};

function web2py_ajax_init(target) {
  jQuery('.hidden', target).hide();
  jQuery('.error', target).hide().slideDown('slow');
  web2py_ajax_fields(target);
};

function web2py_event_handlers() {
  var doc = jQuery(document);
  doc.on('click', '.flash', function(e){jQuery(this).fadeOut('slow'); e.preventDefault();});
  doc.on('keyup', 'input.integer', function(){this.value=this.value.reverse().replace(/[^0-9\-]|\-(?=.)/g,'').reverse();});
  doc.on('keyup', 'input.double, input.decimal', function(){this.value=this.value.reverse().replace(/[^0-9\-\.,]|[\-](?=.)|[\.,](?=[0-9]*[\.,])/g,'').reverse();});
  var confirm_message = (typeof w2p_ajax_confirm_message != 'undefined') ? w2p_ajax_confirm_message : "Are you sure you want to delete this object?";
  doc.on('click', "input[type='checkbox'].delete", function(){if(this.checked) if(!confirm(confirm_message)) this.checked=false;});
};

jQuery(function() {
   var flash = jQuery('.flash');
   flash.hide();
   if(flash.html()) flash.slideDown();
   web2py_ajax_init(document);
   web2py_event_handlers();
});

function web2py_trap_form(action,target) {
   jQuery('#'+target+' form').each(function(i){
      var form=jQuery(this);
      if(!form.hasClass('no_trap'))
        form.submit(function(e){
         jQuery('.flash').hide().html('');
         web2py_ajax_page('post',action,form.serialize(),target);
     e.preventDefault();
      });
   });
}

function web2py_trap_link(target) {
    jQuery('#'+target+' a.w2p_trap').each(function(i){
        var link=jQuery(this);
        link.click(function(e) {
            jQuery('.flash').hide().html('');
            web2py_ajax_page('get',link.attr('href'),[],target);
            e.preventDefault();
        });
    });
}

function web2py_ajax_page(method, action, data, target) {
  jQuery.ajax({'type':method, 'url':action, 'data':data,
    'beforeSend':function(xhr) {
      xhr.setRequestHeader('web2py-component-location', document.location);
      xhr.setRequestHeader('web2py-component-element', target);},
    'complete':function(xhr,text){
      var html=xhr.responseText;
      var content=xhr.getResponseHeader('web2py-component-content');
      var command=xhr.getResponseHeader('web2py-component-command');
      var flash=xhr.getResponseHeader('web2py-component-flash');
      var t = jQuery('#'+target);
      if(content=='prepend') t.prepend(html);
      else if(content=='append') t.append(html);
      else if(content!='hide') t.html(html);
      web2py_trap_form(action,target);
      web2py_trap_link(target);
      web2py_ajax_init('#'+target);
      if(command)
        eval(decodeURIComponent(escape(command)));
      if(flash)
        jQuery('.flash').html(decodeURIComponent(escape(flash))).slideDown();
      }
    });
}

*/


