// JavaScript Document

// var actionUrl = 'scripts/survey_form_ajax.php';
var actionUrl = 'scripts/ajax_upcldf.php';
	
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
  // fill hidden fields (for form submission)
  $("#latitude").val(position.coords.latitude);
  $("#longitude").val(position.coords.longitude);

  // fill visible disabled fields
  $("#lat").val(position.coords.latitude);
  $("#long").val(position.coords.longitude);

  // update Google Maps iframe smoothly
  const url = "https://maps.google.com/maps?q=" + position.coords.latitude + "," + position.coords.longitude + "&hl=en&z=13&output=embed";
  $("#googlemap").fadeOut(200, function() {
    $(this).attr("src", url).fadeIn(300);
  });

  // optional toast feedback
//   $.notify({
//     message: "लोकेशन सफलतापूर्वक अपडेट हो गया ✅"
//   },{
//     type: 'success',
//     timer: 1000
//   });
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

function verify_otp_upcldf(val){
	var data = {"term":"b", "id":"verify_otp_upcldf", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_upcldf.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_uprnss(val){
	var data = {"term":"b", "id":"verify_otp_uprnss", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_uprnss.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_pcu(val){
	var data = {"term":"b", "id":"verify_otp_pcu", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_pcu.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_pcf(val){
	var data = {"term":"b", "id":"verify_otp_pcf", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_pcf.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_ldb(val){
	var data = {"term":"b", "id":"verify_otp_ldb", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_ldb.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_jefed(val){
	var data = {"term":"b", "id":"verify_otp_jefed", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_jefed.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}
function verify_otp_upss(val){
	var data = {"term":"b", "id":"verify_otp_upss", "val":val};
	$.ajax({
        type: "POST",
        url: 'scripts/ajax_upss.php',
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
			}else if(data.status=='pre_completed'){
				alert("अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
			}
			else if(data.status=='verified'){
				alert(data.msg);
				$("#otp_form").submit();
			}
        }
    });
}

function form_validate(){
	var msg=[];
	if($("#lat").val()=='' || $("#long").val()==''){
		msg.push('लोकेशन की जानकारी उपलब्ध नहीं है');
	}
	if($("#society_registration_no").val()==''){
		msg.push('समिति पंजीकरण संख्या दर्ज करें');
	}
	if($("#society_photo").val()=='' && !document.getElementById("society_photo_uploaded")){
		msg.push('समिति कि फोटो संलग्न करें');
	}
	if($("#sec_1_profit_loss").val()==''){
		msg.push('गत वित्तीय वर्ष में लाभ/हानि कि स्थिति दर्ज करें');
	}
	if($("#sec_1_profit_loss_amount").val()==''){
		msg.push('लाभ/हानि (धनराशि रुपये में) दर्ज करें');
	}
	if($("#sec_3_ownership").val()==''){
		msg.push('समिति भवन का स्वामित्व कि स्थिति दर्ज करें');
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
		$('#send_otp_button3').css('display', 'flex');
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