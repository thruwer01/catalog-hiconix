$(document).ready(function() {
  $('.btnPopup').on('click',function () {
	var link_attr = $(this).attr('data-product');
	if (typeof link_attr !== typeof undefined && link_attr !== false) {
		$('textarea#form-comment').val($(this).attr('data-product'));
	}
	  
    $('input,textarea').removeClass('noValid');
    $('select[name="request_goal"]').prop('selectedIndex',0);
    $('#form_input_tel').hide();
    $('#form_input_email').hide();
    $('input[type="radio"]').each((i,node) => {
      node.checked = false;
    });
  })
  $('.btnPopup').magnificPopup({
    type: 'inline',
  })
});


$('#form-comment').on('input', function () {
  if ($('#form-comment').val().length == 200) {
    $('span#comment-error').show();
  } else {
    $('span#comment-error').hide();
  }
})

$('.sendSubmit').click(function(e) {
    e.preventDefault();
    if ($('input#submit01').prop( "disabled") == true) {
      return false
    }
    $('input#submit01').prop( "disabled", true);
    var form, city, tel, email, city_val, tel_val, email_val, error1, error2, error3, error4, error5, error6, nameValid, telValid, emailValid, request_goal, comment, commentError;
    var contact_choise_nodes;
    form = $(this).closest('form');
    city = form.find('input[name="city"]');
    tel = form.find('input[type="tel"]');
    email = form.find('input[type="email"]');
    request_goal = form.find('select[name="request_goal"]')
    contact_choise_nodes = form.find('input[type="radio"]')
    comment = form.find('#form-comment');
    commentError = form.find('span#comment-error');

    city_val = city.val();
    tel_val = tel.val();
    email_val = email.val();
    error1 = false;
    error2 = false;
    error3 = false;
    error4 = false;
    error5 = false;
    error6 = false;

    if (comment.val() == "" || comment.val().length < 3) {
      error6 = true
      comment.addClass('noValid');
    } else {
      error6 = false
      comment.removeClass('noValid');
    }

    if (request_goal.val() == null) {
      error4 = true;
      request_goal.addClass('noValid');
    } else {
      error4 = false;
      request_goal.removeClass('noValid');
    }

    let tempVar = [];

    contact_choise_nodes.each((i,node) => {
      if (node.checked == false) {
        tempVar.push(0);
      } else {
        tempVar.push(1);
      }
    });

    if (Math.max.apply(null, tempVar) == 0) {
      error5 = true;
    } else {
      error5 = false;
    }

    if (city.attr('required')) {
      error1 = true;
      nameValid = /^([a-zA-Zа-яА-ЯЄєёЁіІїЇ_ -]{3,})+$/;
      if (nameValid.test(city_val)) {
        error1 = false;
        city.removeClass('noValid')
      } else {
        city.addClass('noValid');
      }
    }
    if (tel.attr('required')) {
      error2 = true;
      telValid = /^([0-9()-+. \\-]{6,})+$/;
      if (telValid.test(tel_val)) {
        error2 = false;
        tel.removeClass('noValid')
      } else {
        tel.addClass('noValid');
      }
    }
    if (email.attr('required')) {
      error3 = true;
      emailValid = /^[a-zA-Zа-яА-ЯёЁіІїЇ@0-9()-+. ]+$/;
      if (emailValid.test(email_val)) {
        error3 = false;
        email.removeClass('noValid')
      } else {
        email.addClass('noValid');
      }
    }
    $('input#submit01').prop( "disabled", false);
    if (error1 == false && error2 == false && error3 == false && error4 == false && error5 == false && error6 == false) {
      $('input#submit01').prop( "disabled", true);
      $.post("/callback/callback.php", $(form).serialize(), function(result) {
        console.log(result);
        if (result = 'send') {
          // document.location.href = 'thanks.html'
            $.magnificPopup.open({
              items: {
                type: 'inline',
                src: '#popup4',
              }
            })
          $('input[type="text"],input[type="email"],input[type="tel"],textarea').val('');
          $('input#submit01').prop( "disabled", false);
        } else {
          $.magnificPopup.open({
            items: {
              type: 'inline',
              src: '#popup5',
            }
          })
        }

      });
    }
});

$(function(){
  $('.form--close').click(function() {
    $.magnificPopup.close();
  });
});

$('.form--phone').mask('+7 (999) 999-99-99');

//Показываем или скрываем поля в поп-апе в зависимости от выбора способа связи

const inputRadioTel = document.querySelector('#contact_choise1');
const inputRadioEmail = document.querySelector('#contact_choise2');

[inputRadioTel,inputRadioEmail].forEach(inputRadio => {
  inputRadio.onchange = () => {
    if (inputRadio.value == "tel") {
      $('#form_input_tel').show();
      $('#form_input_tel input').prop('required',true);
      $('#form_input_email').hide();
      $('#form_input_email input').prop('required',false);
    }
    if (inputRadio.value == "msg") {
      $('#form_input_tel').hide();
      $('#form_input_tel input').prop('required',false);
      $('#form_input_email').show();
      $('#form_input_email input').prop('required',true);
    }
  }
})