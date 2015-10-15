$(document).ready(function(){		
	$('ul.sortable').nestedSortable({
        handle: 'div',
		items: 'li',
		forcePlaceholderSize: true,
		helper:	'clone',
		maxlevels: 2,
		opacity: .6,
		revert: 250,
		tabSize: 25,
		placeholder: 'placeholder',
		tolerance: 'pointer',
		toleranceElement: '> div',
		listType: 'ul'
    });
    $('#submit-list').click(function(event){
    		event.preventDefault();			
    		var serialized = $('ul.sortable').nestedSortable('serialize');
			var list_s = $('#list-form').serialize();
			alert(serialized + ' \n' +list_s);
			$.get('/secretariaat/index.php/lijsten/opslaan', serialized+'&'+list_s, function(json){
				
			});
		})
	$('#oorzaken-list ul li input').focus(function(event){
		$(this).parent('div').addClass('editabel').css('color', '#fff');
	});
	$('#oorzaken-list ul li input').focusout(function(event){
		$(this).parent('div').removeClass('editabel').css('color', '#EEE');
	});
		
	$('#serialize').click(function(){
		serialized = $('ul.sortable').nestedSortable('serialize');
		$('#serializeOutput').text(serialized+'\n\n');
	})

	$('#toHierarchy').click(function(e){
		hiered = $('ul.sortable').nestedSortable('toHierarchy', {startDepthCount: 1});
		hiered = dump(hiered);
		(typeof($('#toHierarchyOutput')[0].textContent) != 'undefined') ?
		$('#toHierarchyOutput')[0].textContent = hiered : $('#toHierarchyOutput')[0].innerText = hiered;
	})

	$('#toArray').click(function(e){
		arraied = $('ul.sortable').nestedSortable('toArray', {startDepthCount: 0});
		arraied = dump(arraied);
		(typeof($('#toArrayOutput')[0].textContent) != 'undefined') ?
		$('#toArrayOutput')[0].textContent = arraied : $('#toArrayOutput')[0].innerText = arraied;
	})
});	

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Strings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}