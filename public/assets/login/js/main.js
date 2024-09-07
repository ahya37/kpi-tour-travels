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
		if($("#password").val() == '') {
			$(".last").addClass('field--not-empty');
		}
	})

	$("#password").on('blur', () => {
		if($("#password").val() == '') {
			$(".last").removeClass('field--not-empty');
		}
	})
});

// localStorage.clear()
localStorage.setItem('profile_pict', '');

if(localStorage.getItem('email') != '') {
	$("#remember_me").prop('checked', true);
	$("#email").val(localStorage.getItem('email'));
} else {
	$("#remember_me").prop('checked', false);
	$("#email").val('');
}

function doIngatSaya() {
	// GET DATA USERNAME
	if($("#remember_me").is(':checked') === true) {
		var username  = $("#email").val();
		if(username != '') {
			localStorage.setItem('email', username);
		} else {
			localStorage.setItem('email', '');
		}
	} else {
		localStorage.setItem('email', '');
	}
}

console.log(localStorage);