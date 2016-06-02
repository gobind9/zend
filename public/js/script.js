$(document).ready(function(){
	$('.form-control').change(function(){
		calculateSum();
	});
	$("input[name='pid[]']").change(function(){
		calculateSum();
	});
	calculateSum();
	
	
});

function calculateSum(){
	var total = 0;
	$("input[name^='qty_in_stock_']").each(function(i,v){
		var pid 	= $(this).attr('id');
		var value 	= $(this).val();
		var price   = $('#qty_' + pid).text();
		
	    v = parseFloat(value);
	   	if (!isNaN(v)){
	   		//if($('#pid_' + pid).is(':checked')){
	   			total = total + (v*price);
	   		//}
	    }	  
	})

	$('#totalamt').html('<strong>'+total+'</strong>');
}
