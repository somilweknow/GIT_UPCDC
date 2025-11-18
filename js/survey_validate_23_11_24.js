// JavaScript Document

var actionUrl = 'scripts/survey_form_ajax.php';

function fill_district(val){
	var data = {"term":"b", "id":"dist", "val":val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.district_name+'</option>';
				
			});
          	$("#district_name").html(txt);
        }
    });
}

function fill_tehseel(val){
	var data = {term:"b", id:"tehseel", val:val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.tehseel_name+'</option>';
				
			});
			$("#tehseel_name").html(txt);
			$('#tehseel_name').multiselect();
        }
    });
}
	
function fill_block(val){
	var data = {term:"b", id:"block", val:val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.block_name+'</option>';
				
			});
          	$("#block_name").html(txt);
        }
    });
}

function fill_type(val){
	var data = {term:"b", id:"type", val:val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'" ';
				if(value.status=='0'){
					txt += ' disabled="disable" ';
				}
				txt += '>'+value.type_name+'</option>';
				
			});
          	$("#society_type").html(txt);
        }
    });
}
	
function fill_society(val){
	var data = {term:"b", id:"society", val:val, division:$("#division_name").val(), district:$("#district_name").val(), tehseel:$("#tehseel_name").val(), block:$("#block_name").val()};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.society_name+'</option>';
				
			});
          	$("#society_name").html(txt);
        }
    });
}
	
function fill_society_dis(val){
	var data = {term:"b", id:"dis_society", val:val, division:$("#division_name").val(), district:$("#district_name").val()};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.society_name+'</option>';
				
			});
          	$("#society_name").html(txt);
        }
    });
}

x = document.getElementById("map_container");
function getLocation() {
	//console.log('test');
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(showPosition, showError);
	} 
	else { 
		x.innerHTML = "Geolocation is not supported by this browser.";
	}
}	
	
function showPosition(position) {
	$("#latitude").val(position.coords.latitude);
	$("#longitude").val(position.coords.longitude);
	$("#lat").val(position.coords.latitude);
	$("#long").val(position.coords.longitude);
	var url = "https://maps.google.com/maps?q="+position.coords.latitude+","+position.coords.longitude+"&hl=en&z=13&output=embed";
	//console.log(url);
	$("#googlemap").attr("src", url);
	document.getElementById('googlemap').location.reload();
}	

function showError(error) {
	x = document.getElementById("map_container");
  switch(error.code) {
    case error.PERMISSION_DENIED:
      x.innerHTML = "User denied the request for Geolocation."
      break;
    case error.POSITION_UNAVAILABLE:
      x.innerHTML = "Location information is unavailable."
      break;
    case error.TIMEOUT:
      x.innerHTML = "The request to get user location timed out."
      break;
    case error.UNKNOWN_ERROR:
      x.innerHTML = "An unknown error occurred."
      break;
  }
} 

function send_otp(val, number){
	var data = {"term":"b", "id":"send_otp", "val":val, "number":number};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			data = JSON.parse(data);
			data = data[0];
			if(data.status=='error'){
				alert("Some error occurred please retry");
			}
			else if(data.status=='completed'){
				alert("You have already filled the survey");
			}
			else if(data.status=='otp_sent'){
				alert("OTP Sent to your mobile. "+data.msg);
				$("#send_otp_button").hide();
				$("#otp_verify").show();
			}
        }
    });
}

// JavaScript Document
function verify_otp(val){
	var data = {"term":"b", "id":"verify_otp", "val":val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			data = JSON.parse(data);
			data = data[0];
			console.log(data);
			if(data.status=='error'){
				alert("Some error occurred please retry");
			}
			else if(data.status=='notfound'){
				alert("Data not found");
			}
			else if(data.status=='completed'){
				alert("You have already filled the survey");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}

function seller(val){
	if(val=='yes'){
		$("#seller_count").css("display", "block");
	}
	else{
		$("#seller_count").css("display", "none");
	}
}

function support_staff(val){
	if(val=='yes'){
		$("#support_staff_count").css("display", "block");
	}
	else{
		$("#support_staff_count").css("display", "none");
	}
}

function guard(val){
	if(val=='yes'){
		$("#guard_count").css("display", "block");
	}
	else{
		$("#guard_count").css("display", "none");
	}
}

function computer_operator(val){
	if(val=='yes'){
		$("#computer_operator_count").css("display", "block");
	}
	else{
		$("#computer_operator_count").css("display", "none");
	}
}

function show_sides_of_land(val){
	val = parseFloat(val);
	if(!val){
		val = 0;
		alert("कृप्या भूखण्ड की भुजाओं कि संख्या लिखे");
		$("#sec_3_a_land_length").val(0);
		$("#sec_3_a_land_length").parent().addClass(" alert alert-danger");
		return;
	}
	else{
		//console.log(val);
		if(val>6){
			alert("भूखण्ड की भुजाओं कि अधिकतम सीमा 6 है । कृप्या 6 या इससे कम अंक लिखें");
			$("#sec_3_a_land_length").val(0);
			$("#sec_3_a_land_length").parent().addClass(" alert alert-danger");
			return;
		}
		else{
			var cols = Math.floor(12/val);
			$("#sec_3_a_land_length").parent().removeClass(" alert alert-danger");
			//console.log(cols);
			var text = '';
			for(var i=1; i<=val; i++){
				text += '<div class="col-sm-'+cols+' form-group"><label>भुजा '+i+' की लम्बाई (मीटर में)</label><input type="text" name="sec_3_a_side_'+i+'" id="sec_3_a_'+i+'"  class="form-control"></div>';	
			}
			$("#sides_display").html(text);
			$("#sides_display").css("display", "flex");
			
		}
		
	}
}

function hide_show(val, id, result){
	var disp=0;
	
	if(Array.isArray(result)){
		//console.log('Hurray');
		$.each(result, function(key, value){
			if(val==value){
				$(id).css("display", "block");
				disp=1;
			}
		});
		if(disp==0){
			$(id).css("display", "none");
		}
	}
	else{
		//console.log('Failed');
		if(val==result){
			$(id).css("display", "block");
		}
		else{
			$(id).css("display", "none");
		}
	}
	
}

function form_validate(){
	var msg=[];
	if($("#lat").val()=='' || $("#long").val()==''){
		msg.push('लोकेशन की जानकारी उपलब्ध नहीं है');
	}
	if($("#sec1_liquidation").val()==''){
		msg.push('परिसमापन कि स्थिति दर्ज करें');
	}
	if($("#sec_1_litigation").val()==''){
		msg.push('वाद कि स्थिति दर्ज करें');
	}
	if($("#society_registration_no").val()==''){
		msg.push('समिति पंजीकरण संख्या दर्ज करें');
	}
	if($("#society_photo").val()=='' && !document.getElementById("society_photo_uploaded")){
		msg.push('समिति कि फोटो संलग्न करें');
	}
	if($("#person_name").val()==''){
		msg.push('प्रपत्र भर रहे व्यक्ति का नाम दर्ज करें');
	}
	if($("#person_designation").val()==''){
		msg.push('प्रपत्र भर रहे व्यक्ति का पदनाम दर्ज करें');
	}
	if($("#person_aadhaar").val()==''){
		msg.push('प्रपत्र भर रहे व्यक्ति का आधार नम्बर दर्ज करें');
	}
	if($("#sec_1_members_active").val()==''){
		msg.push('सक्रिय सदस्य (संख्या) दर्ज करें');
	}
	if($("#sec_1_members_non_active").val()==''){
		msg.push('निष्क्रिय सदस्य (संख्या) दर्ज करें');
	}
	if($("#sec_1_investment").val()==''){
		msg.push('उर्वरक / कृषि निवेश (कुल टर्नोवर रुपये में) दर्ज करें');
	}
	if($("#sec_1_loan").val()==''){
		msg.push('ऋण (कुल टर्नोवर रुपये में) दर्ज करें');
	}
	if($("#sec_1_msp").val()=='' || $("#sec_1_msp_comm").val()==''){
		msg.push('मूल्य समर्थन दर्ज करें');
	}
	if($("#sec_1_subscriber").val()==''){
		msg.push('उपभोक्ता कि स्थिति दर्ज करें');
	}
	if($("#sec_1_pds").val()==''){
		msg.push('सार्वजनिक वितरण प्रणाली (PDS) कि स्थिति दर्ज करें');
	}
	if($("#sec_1_total_business").val()==''){
		msg.push('कुल व्यवसाय दर्ज करें');
	}
	if($("#sec_1_profit_loss").val()==''){
		msg.push('गत वित्तीय वर्ष में लाभ/हानि कि स्थिति दर्ज करें');
	}
	if($("#sec_1_profit_loss_amount").val()==''){
		msg.push('लाभ/हानि (धनराशि रुपये में) दर्ज करें');
	}
	if($("#sec_1_sequentially").val()==''){
		msg.push('क्रमिक लाभ/हानि कि स्थिति दर्ज करें');
	}
	if($("#sec_1_sequentially_amount").val()==''){
		msg.push('क्रमिक लाभ/हानि (धनराशि रुपये में) दर्ज करें');
	}
	// if($("#sec2_balance_sheet").val()==''){
	// 	msg.push('संतुलन पत्र कि स्थिति दर्ज करें');
	// }
	if($("#sec2_financial_audit").val()==''){
		msg.push('वित्तिय आडिट कि स्थिति दर्ज करें');
	}
	if($("#sec_1_1_2_msc").val()==''){
		msg.push('बहुसेवा केंद्र कि स्थिति दर्ज करें');
	}
	if($("#sec_2_secretary").val()==''){
		msg.push('सचिव कि स्थिति दर्ज करें');
	}
	// if($("#sec_2_accountant").val()==''){
	// 	msg.push('लेखाकार कि स्थिति दर्ज करें');
	// }
	if($("#sec_2_computer_operator").val()==''){
		msg.push('कंप्यूटर आपरेटर / कार्यालय सहायक कि स्थिति दर्ज करें');
	}
	if($("#sec_2_assistant_accountant").val()==''){
		msg.push('सहायक लेखाकार कि स्थिति दर्ज करें');
	}
	if($("#sec_2_seller").val()==''){
		msg.push('विक्रेता कि स्थिति दर्ज करें');
	}
	if($("#sec_2_support_staff").val()==''){
		msg.push('सहयोगी कि स्थिति दर्ज करें');
	}
	if($("#sec_2_guard").val()==''){
		msg.push('चौकीदार कि स्थिति दर्ज करें');
	}
	if($("#sec_2_govt_program").val()==''){
		msg.push('कंप्यूटरीकरण की कार्य योजना कि स्थिति दर्ज करें');
	}
	if($("#sec_3_ownership").val()==''){
		msg.push('समिति भवन का स्वामित्व कि स्थिति दर्ज करें');
	}
	if($("#sec_3_a_land_length").val()==''){
		msg.push('भूखण्ड में भुजा कि संख्या दर्ज करें');
	}
	if($("#sec_3_a_area").val()==''){
		msg.push('क्षेत्रफल (हेक्टेयर में) दर्ज करें');
	}
	if($("#sec_3_a_govt_records").val()==''){
		msg.push('राजस्व अभिलेख में दर्ज होने कि स्थिति दर्ज करें');
	}
	if($("#sec_3_a_gata").val()==''){
		msg.push('गाटा/खसरा संख्या दर्ज करें');
	}
	if($("#sec_3_a_image").val()=='' && !document.getElementById("sec_3_a_image_uploaded")){
		msg.push('समिति भूखण्ड फोटो संलग्न करें');
	}
	if($("#sec_3_a_land_chauhaddi_east").val()=='' || $("#sec_3_a_land_chauhaddi_west").val()=='' || $("#sec_3_a_land_chauhaddi_south").val()=='' || $("#sec_3_a_land_chauhaddi_north").val()==''){
		msg.push('चौहद्दी का विवरण दर्ज करें');
	}
	if($("#sec_3_a_land_on_road").val()==''){
		msg.push('सड़क पर भूमि कि लम्बाई दर्ज करें');
	}
	if($("#sec_3_d_boundry").val()==''){
		msg.push('चारदिवारी (बाऊण्डरी वाल) कि स्थिति दर्ज करें');
	}
	if($("#sec_3_d_main_gate").val()==''){
		msg.push('प्रमुख द्वार (मेन गेट) कि स्थिति दर्ज करें');
	}
	if($("#sec_4_micro_atm").val()==''){
		msg.push('माईक्रो ए.टी.एम. कि स्थिति दर्ज करें');
	}
	if($("#sec_4_drone").val()==''){
		msg.push('ड्रोन कि स्थिति दर्ज करें');
	}
	if($("#sec_4_chhanna").val()==''){
		msg.push('छलना कि स्थिति दर्ज करें');
	}
	if($("#sec_4_power_duster").val()==''){
		msg.push('पावर डस्टर कि स्थिति दर्ज करें');
	}
	if($("#sec_4_tractor").val()==''){
		msg.push('ट्रैक्टर कि स्थिति दर्ज करें');
	}
	if($("#sec_4_custom_hiring").val()==''){
		msg.push('कस्टम हाईरिंग सेंटर कि स्थिति दर्ज करें');
	}
	if($("#sec_4_chair").val()==''){
		msg.push('कुर्सी (संख्या) दर्ज करें');
	}
	if($("#sec_4_table").val()==''){
		msg.push('मेज (संख्या) दर्ज करें');
	}
	if($("#sec_4_almari").val()==''){
		msg.push('अलमारी (संख्या) दर्ज करें');
	}
	if($("#sec_5_built_building").val()==''){
		msg.push('निर्मित भवन की स्थिति स्थिति दर्ज करें');
	}
	// if($("#sec_6_access_road").val()==''){
	// 	msg.push('पहुंच मार्ग का विवरण दर्ज करें');
	// }
	if($("#sec_7_electrical_connection").val()==''){
		msg.push('विद्युत कनेक्शन कि स्थिति दर्ज करें');
	}
	if($("#sec_8_internet_connection").val()==''){
		msg.push('इण्टरनेट कनेक्शन कि स्थिति दर्ज करें');
	}
	if($("#sec_6_narrow_tubes").val()==''){
		msg.push('सरकारी नलके का पानी कि स्थिति दर्ज करें');
	}
	if($("#sec_6_water_tank").val()==''){
		msg.push('पानी कि टंकी कि स्थिति दर्ज करें');
	}
	if($("#sec_6_samarsabel").val()==''){
		msg.push('सबमर्सिबल कि स्थिति दर्ज करें');
	}
	if($("#sec_6_handpump").val()==''){
		msg.push('हैंड पंप कि स्थिति दर्ज करें');
	}
	if($("#error_status").val()==1){
		msg.push('सभी डाटा सही से भरे / प्रपत्र को एक भर फिर से जाच ले');
	}
	if(msg!=''){
		$.each(msg, function(key, value){
			$.notify({
				icon: 'pe-7s-gift',
				message: value

			},{
				type: 'danger',
				timer: 2000
			});
			
		});
		
	}
	else{
		// $('#send_otp_button1').css('display', 'flex');
		$('#send_otp_button2').css('display', 'flex');
	}
}

function hide_show(val, id, result){
	if(val==result){
		$(id).css("display", "block");
	}
	else{
		$(id).css("display", "none");
	}
}