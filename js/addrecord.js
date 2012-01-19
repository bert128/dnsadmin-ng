$(document).ready(function() {


    $('#ddtype').change(function(e){
	$("#help-a").hide();
	if ($("#ddtype").val() == "A") { $("#help-a").show(); }

	$("#help-aaaa").hide();
	if ($("#ddtype").val() == "AAAA") { $("#help-aaaa").show(); }

	$("#help-cname").hide();
	if ($("#ddtype").val() == "CNAME") { $("#help-cname").show(); }

	$("#help-hinfo").hide();
	if ($("#ddtype").val() == "HINFO") { $("#help-hinfo").show(); }

	$("#help-mx").hide();
	if ($("#ddtype").val() == "MX") { $("#help-mx").show(); }

	$("#help-mboxfw").hide();
	if ($("#ddtype").val() == "MBOXFW") { $("#help-mboxfw").show(); }

	$("#help-naptr").hide();
	if ($("#ddtype").val() == "NAPTR") { $("#help-naptr").show(); }

	$("#help-ns").hide();
	if ($("#ddtype").val() == "NS") { $("#help-ns").show(); }

	$("#help-ptr").hide();
	if ($("#ddtype").val() == "PTR") { $("#help-ptr").show(); }

	$("#help-soa").hide();
	if ($("#ddtype").val() == "SOA") { $("#help-soa").show(); }

	$("#help-srv").hide();
	if ($("#ddtype").val() == "SRV") { $("#help-srv").show(); }

	$("#help-txt").hide();
	if ($("#ddtype").val() == "TXT") { $("#help-txt").show(); }

	$("#help-url").hide();
	if ($("#ddtype").val() == "URL") { $("#help-url").show(); }

    });

    // And now fire change event when the DOM is ready
    $('#ddtype').trigger('change');


});
