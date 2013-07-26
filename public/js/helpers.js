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
		/*function(request, response) {
        $.ajax({
            type: 'GET',
            url: '{$url}',
            data: {
                'term':request.term
            },
            ContentType: "application/json; chrarset=utf-8",
            dataType: "json",
            success:function(data){
                // response callback
                response(data);
            },
            error:function(message){
                //pass an empty array to close the meny
                response([]);
            }
        });
		 */
		select: function(event, ui) {
			$('#'+name).autocomplete('destroy');
			//$(d_id).html('');
			// $(d_id).dialog('destroy');
			// act.su(ui.item.id);
			// Set the id and name to something
			callback(name, ui.item.labname, ui.item.id);
			$("ui-autocomplete ul").html('');
		},
		//appendTo: "#{$n}_results",
		/*response: function(event, ui) {
        var i, il, line;
        line = '';
        il = ui.content.length;
        for (i=0; i< il; i++) {
            line += '<li class="limed"><a style="color:red;" href="" >'+ui.content[i].labname+'</a></li>';
        }
    		$("#{$n}_results").html(line);
    }*/
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.html( '<div class="pd" style="color:green;font-size:22px;" ' +
				'onclick="'+callbackname+'(\''+name+'\',\''+item.labname+'\','+item.id+');" > '
				+ item.id + ' ' + item.labname + "</div>" )
		.appendTo( ul );
	};
};
