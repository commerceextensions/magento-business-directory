jQuery(document).ready(function(){	

  jQuery(".columns .form-list td.value").css("width","500px");
  jQuery(document).on("mouseover","#customerGrid_table tbody tr",function(){
	jQuery(this).css("cursor","pointer");
  });
  
  jQuery(function() {
    jQuery("#customerGrid").dialog({
      autoOpen      : false,
	  modal         : true,
	  width         : window.innerWidth*.65,
	  height        : "auto",
	  closeOnEscape : true,
	  draggable     : true,
	  resizable     : true,
	  title         : "Click on a Customer To Change the Listing Owner",
	  show: {
		effect  : "fadeIn",
		duration: 250
	  },
	  hide: {
		effect  : "fadeOut",
		duration: 250
	  }
    });
 
    jQuery("#find-customer").click(function() {
      jQuery("#customerGrid").dialog("open");	  
    });
  });  
});

function openGridRow(grid, event){
  jQuery(document).on("click","#customerGrid_table tbody tr",function(){
	var customer_id = jQuery(this).children("td:first-child").text().trim();
	var customer_name = jQuery(this).children("td:nth-child(2)").text().trim();
	jQuery("#customer_id").val(customer_id)	  
	jQuery("#customer_name").text(customer_name);
	jQuery("#customerGrid").dialog("close");	
	
	// trigger the little data change notification(disc icon on tab) 
	varienElementMethods.setHasChanges(document.getElementById('customer_id'));
  });
  return false;
} 