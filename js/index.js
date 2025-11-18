

		function send_otp(number){
			var user_role = $("#user_role").val();
			if(!user_role){
				var data = {"term":"b", "id":"otp", "val":'', "number":number};	
			}
			else{
				var data = {"term":"b", "id":"otp", "val":'', "number":number, "user_role":user_role};	
			}
			
			$.ajax({
				type: "POST",
				url: 'scripts/ajax.php',
				data: data, // serializes the form's elements.
				success: function(data){
					data = JSON.parse(data);
					data = data[0];
					if(data.status=='error'){
						alert(data.msg);
					}
					else if(data.status=='multi'){
						$("#mobile_number").attr("readonly", "readonly");
						txt = '<select name="user_role" id="user_role" class="form-control">';
						$.each(data['opt_val'], function(key, value){
							txt += '<option value="'+key+'">'+value+'</option>';
						});
						$("#mobile_number").after(txt)
						alert(data.msg);
						
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
		
		function verify_otp(otp){
		var data = {"term":"b", "id":"verifyotp", "val":'', "otp":otp};
		$.ajax({
			type: "POST",
			url: 'scripts/ajax.php',
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