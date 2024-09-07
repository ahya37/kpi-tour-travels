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

	var email 	= $("#email").val();
	var pass 	= $("#password").val();

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
	})
});

// localStorage.clear()
// localStorage.setItem('profile_pict', '');

// BUAT HASIL JSON PARSE MENJADI ARRAY
var dataStorage 	= JSON.parse(localStorage.getItem('items'))[0];
console.log(JSON.parse(localStorage.getItem('items')));

if(dataStorage['email'] != '') {
	$("#remember_me").prop('checked', true);
	$("#email").val(dataStorage['email']);
	$("#password").focus();
} else {
	$("#remember_me").prop('checked', false);
	$("#email").val('');
}

// if() {
// 	$("#remember_me").prop('checked', true);
// 	$("#email").val(localStorage.getItem('email'));
// } else {
// 	$("#remember_me").prop('checked', false);
// 	$("#email").val('');
// }

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