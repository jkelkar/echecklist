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
    switch($(this).val()) {
      case 'Y':
        out = score;
        break;
      case 'P':
        out = 1;
        break;
      case 'N':
        out = 0;
        break;
      default:
        out = 99;
    };
    $('#'+name+'_score').val(out.toString());
  });
}

function clear() {
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
  