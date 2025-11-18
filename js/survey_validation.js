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
			var i=0;
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.tehseel_name+'</option>';
				i++;
				
			});
			$("#tehseel_name").html(txt);
		    
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
			var i=0;
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.block_name+'</option>';
				i++;
				
			});
          	$("#block_name").html(txt);
      	    $('#block_name[multiple]').multiselect();
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
				txt += '<option value="'+value.id+'">'+value.type_name+'</option>';
				
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
          	$("#society_name_dis").html(txt);
        }
    });
}

x = document.getElementById("map_container");
function getLocation() {
	console.log('test');
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
	console.log(url);
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
function verify_otp(val, number, otp){
	var data = {"term":"b", "id":"verify_otp", "val":val, "number":number, "otp":otp};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			data = JSON.parse(data);
			data = data[0];
			console.log(data);
			if(data.status=='invalid'){
				alert("Invalid OTP please retry");
			}
			else if(data.status=='notfound'){
				alert("Data not found");
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
			console.log(cols);
			var text = '';
			for(var i=1; i<=val; i++){
				text += '<div class="col-sm-'+cols+' form-group"><label>भुजा '+i+' की लम्बाई</label><input type="text" name="sec_3_a_side_'+i+'" id="sec_3_a_'+i+'"  class="form-control"></div>';	
			}
			$("#sides_display").html(text);
			$("#sides_display").css("display", "flex");
			
		}
		
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