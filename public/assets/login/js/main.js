var local_data 	= [];
$(function() {
	// 'use strict';

//   $('.form-control').on('input', function() {
// 	  var $field = $(this).closest('.form-group');
// 	  if (this.value) {
// 	    $field.addClass('field--not-empty');
// 	  } else {
// 	    $field.removeClass('field--not-empty');
// 	  }
// 	});

	console.log('Hey, You Found Me!');
	let email 	= $("#email").val();
	let pass 	= $("#password").val();

	$("#email").focus();

	setTimeout(()	=> {
		$(".first").addClass('field--not-empty');
	}, 10);

	if(email != '') {
		$(".first").addClass('field--not-empty');
	} else {
		$(".first").removeClass('field--not-empty');
	}

	$("#email").on('click', () => {
		$(".first").addClass('field--not-empty');
	});

	$("#email").on('keyup', () => {
		if($("#email").val() == '') {
			$(".first").addClass('field--not-empty');
		}
	});

	$("#email").on('blur', () => {
		if($("#email").val() == '') {
			$(".first").removeClass('field--not-empty');
		}
	});

	if(pass != '') {
		$(".last").addClass('field--not-empty');
	} else {
		$(".last").removeClass('field--not-empty');
	}

	$("#password").on('click', () => {
		$(".last").addClass('field--not-empty');
	});

	$("#password").on('keyup', () => {
		$(".last").addClass('field--not-empty');
	})

	$("#password").on('blur', () => {
		if($("#password").val() == '') {
			$(".last").removeClass('field--not-empty');
		}
	});

	$("#form_login").on('submit', () => {
		if($("#email").val() == '') {
			$("#error_msg").html('Email Tidak Boleh Kosong');
			$("#email").focus();
			return false;
		} else if($("#password").val() == '') {
			$("#error_msg").html('Password Tidak Boleh Kosong');
			$("#password").focus();
			return false;
		} else {
			$("#error_msg").html('');
			return true;
		}
	})
});

// localStorage.clear()
// localStorage.setItem('profile_pict', '');

// BUAT HASIL JSON PARSE MENJADI ARRAY
var dataStorage 	= JSON.parse(localStorage.getItem('items'))[0];

// HAPUS PROFILE PICT DARI LOCAL STORAGE
const sendData 	= {
	'email'			: dataStorage['email'],
	'profile_pict'	: ''
};
local_data.push(sendData);
localStorage.setItem('items', JSON.stringify(local_data));

if(dataStorage['email'] != '') {
	$("#remember_me").prop('checked', true);
	$("#email").val(dataStorage['email']);
	$("#password").focus();
} else {
	$("#remember_me").prop('checked', false);
	$("#email").val('');
}

function doIngatSaya() {
	// GET DATA USERNAME
	if($("#remember_me").is(':checked') === true) {
		var username  = $("#email").val();
		if(username != '') {
			const sendData 	= {
				"email"			: username,
				"profile_pict"	: "",
			};
			local_data.push(sendData);
			localStorage.setItem('items', JSON.stringify(local_data));
		} else {
			const sendData	 = {
				"email"			: "",
				"profile_pict"	: "",	
			};
			local_data.push(sendData);
			localStorage.setItem('items', JSON.stringify(local_data));
		}
	} else {
		const sendData 	= {
			"email"			: "",
			"profile_pict"	: "",
		};
		local_data.push(sendData);
		localStorage.setItem('items', JSON.stringify(local_data));
	}
}