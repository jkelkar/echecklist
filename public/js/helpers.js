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
  var rht = ht - 69;
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
    $('.node,.nodeSel').click(function(e){
        if (changed==true && !confirm("Do you want to continue without saving changes?")) {
            d.closeAll();
            d.openTo(oldloc, true);
            e.stopPropagation()
            return false;
        } else {
            return true;
        }
    });
});

function fmtint(val, wid) {
    var out = '00000000';
    //out = out.substr(0, wid);
    var outval = val.toString();
    if (outval.length > wid) {
        out = outval;
    } else {
        outval = out.substr(0, wid - outval.length) + outval;
    }
    return outval;
}

function count_radio_yna(section, maxct) {
    var yesct = 0,
        noct = 0, 
        nact= 0,
        unset = 0;
    var i, val;
    for (i = 1; i < maxct+1; i++) { 
        val = $('input[name='+section+fmtint(i, 2)+'_yna]:checked').val();
        switch(val) {
        case 'YES': yesct++; break;
        case 'NO': noct++; break;
        case 'N/A': nact++; break;
        default: unset++; 
        }
    }
    return [yesct, noct, nact, unset];
}
