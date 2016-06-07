(function($) {  // Avoid conflicts with other libraries

"use strict";
$("#selectall").change(function() {
    if(this.checked) {
        $(".ads_id_list").prop("checked", true);
    }else{
        $(".ads_id_list").prop("checked", false);
	}
});

$("#ads_member").change(function() {
    //$('#submit').click();
});

})(jQuery); // Avoid conflicts with other libraries