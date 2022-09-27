var page = {
  loginAttempt: function(){
    $form = $('#login-form');
    $form.find('#form-message').html('&nbsp;');
    $.ajax({
      method: 'POST',
      url: $form.attr('action'),
      data: $form.serialize(),
      dataType: 'json'
    }).done(function(d){
      window.location.href = "/polisy";
    }).fail(function(jqXHR, message){
      $('#form-message').text('Wrong data. No entry.');
      $form.find('.form-control').val('');
    });
  }
};

$(document).ready(function() {
  $('#login-form').keyup(function(e){
    if(e.keyCode == '13'){
      page.loginAttempt();
    }
  })
});
