$(document).ready(function() {


    $('#domaintype').change(function(e){
	if ($("#domaintype").val() == "1") { $("#masterip").show(); $("#template").hide(); }
	if ($("#domaintype").val() == "0") { $("#masterip").hide(); $("#template").show(); }

    });

    // And now fire change event when the DOM is ready
    $('#ddtype').trigger('change');


});
