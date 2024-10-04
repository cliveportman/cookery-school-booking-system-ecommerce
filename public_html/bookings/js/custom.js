$(document).ready(function(){


function saveMessage($message) {
  console.log($message);
}

$('#full-courses-toggle').click(function(e) {
  $('a.full').toggle();
    $(this).text(function(i, text){
        return text === "Hide full courses" ? "Show all courses" : "Hide full courses";
    });
    $(this).hasClass('pink') ? $(this).removeClass('pink') : $(this).addClass('pink');

});

$('.ui.accordion')
  .accordion();


$('.menu .item').tab();

/*
############################
CHECKBOXES
############################
*/

function initCheckboxes() {


  $('.ui.checkbox').checkbox();
      
  // parents
  $('.master.checkbox')
    .checkbox({
      // check all children
      onChecked: function() {
        var $childCheckbox  = $(this).parents('thead').siblings('tbody').find('.checkbox');
        $childCheckbox.checkbox('check');
        saveMessage('saved');
        exitMessage('bind');
      },
      // uncheck all children
      onUnchecked: function() {
        var $childCheckbox  = $(this).parents('thead').siblings('tbody').find('.checkbox');
        $childCheckbox.checkbox('uncheck');
        saveMessage('saved');
        exitMessage('bind');
      }
    });
  // children
  $('.child.checkbox')
    .checkbox({
      // Fire on load to set parent value
      // fireOnInit : true,
      // Change parent state on each child checkbox change
      onChange   : function() {
        var
          $checkboxGroup      = $(this).parents('tbody').find('.checkbox'),
          $parentCheckbox = $(this).parents('tbody').siblings('thead').find('.checkbox'),
          allChecked      = true,
          allUnchecked    = true
        ;
        $checkboxGroup.each(function() {
          if( $(this).checkbox('is checked') ) {
            allUnchecked = false;
          }
          else {
            allChecked = false;
          }
        });
        // set parent checkbox state, but don't trigger its onChange callback
        if(allChecked) {
          $parentCheckbox.checkbox('set checked');
        }
        else if(allUnchecked) {
          $parentCheckbox.checkbox('set unchecked');
        }
        else {
          $parentCheckbox.checkbox('set indeterminate');
        }
        $(this).parents('form').find('.updateregisterbutton').removeClass('disabled').addClass('pink');
        //saveMessage('saved');
        exitMessage('bind');
      }
    });
}
/*
############################
INIT ATTENDENCE FORM
############################
*/
function initAttendenceForm() {
  $('.attendenceform').submit( function(e) {

      // STOP FORM SUBMITTING
      e.preventDefault();      
      
      // ASSIGN THE FORM TO A VAR WE CAN ACCESS LATER
      $form = $(this);
      $form.find('.updateregisterbutton').addClass('loading');

      // PREP THE DATA IN THE FORM FOR SUBMISSION USING A POST REQUEST
      var data = $form.serialize();

      // MAKE THE POST REQUEST
      $.post('/', data, function(response) {
        
        // SUCCESS REDIRECT IN THE FORM IS A PAGE SAYING 'success'
        if (response == "success") {

          // WITH THE DATA SAVED WE CAN DISABLE THE SAVE BUTTON AGAIN
          $form.find('.updateregisterbutton').removeClass('pink loading').addClass('disabled');

          // AND IF THERE ARE NO OTHER UNSAVED FORMS ON THE PAGE WE CAN UNBIND THE EXIT MESSAGE
          if (!$('body form .updateregisterbutton.pink').length) exitMessage('unbind');
            
        } else {
            
            // WE COULD SHOW AN ERROR MESSAGE HERE

        }

      });
  });
}

/*
############################
INIT ORDER UPDATE FORM
############################
*/
function initUpdateOrderForm() {
  $('.updateorderform').submit( function(e) {
      // Prevent the form from actually submitting
      e.preventDefault();
      $form = $(this);
      $names = [];
      $lineId = $form.data('lineid');
      $notes = $(this).find('input.notes-' + $lineId).val();
      $(this).find('input.names-' + $lineId).each(function(){
        $names.push($(this).val());
      });
      console.log($names);
      // Get the post data
      var data = $(this).serialize();

      // Send it to the server
      $.post('/', data, function(response) {
        
        if (response == "success") {
              $form.addClass('success');
              
              setTimeout(function() {
                $('.ui.modal').modal('hide');
                console.log($form.data('lineid'));
                $i = 0;
                console.log('i: ' + $i);
                $('#'+ $form.data('lineid') +' td.names').find('label').each(function(){
                  $(this).text($names[$i]);
                  console.log('i: ' + $i);
                  $i++;
                });
                $('#'+ $form.data('lineid') +' td.notes').text($notes);
                //initShowOrderModals();
              }, 750);
              
          } else {
              $form.addClass('error');
          }
      });
  });
}

/*
############################
INIT TRANSACTION UPDATE FORM
############################
*/
function initUpdateTransactionForm() {
  $('.transactionupdateform').submit( function(e) {
      // Prevent the form from actually submitting
      e.preventDefault();
      $(this).find('button').addClass('loading');
      var data = $(this).serialize();
      $form = $(this);
      // Send it to the server
      $.post('/', data, function(response) {
        console.log('data sent');
        console.log(response);
        if (response == "success") {
          $form.find('button').removeClass('loading').addClass('active').text('PAID');
          $('td#' + $form.data('shortnumber') + ' span.red').addClass('green').removeClass('red').text('PAID');
              /*
              setTimeout(function() {
                //$('.ui.modal').modal('hide');
                console.log($form.data('lineid'));
                $i = 0;
                console.log('i: ' + $i);
                $('#'+ $form.data('lineid') +' td.names').find('label').each(function(){
                  $(this).text($names[$i]);
                  console.log('i: ' + $i);
                  $i++;
                });
                $('#'+ $form.data('lineid') +' td.notes').text($notes);
                //initShowOrderModals();
              }, 750);
              */
          } else {
              $form.addClass('error');
          }
      });
  });
}



/*
############################
INIT CANCELLATION UPDATE FORM
############################
*/
function initOrderCancellationForm() {
  $('.ordercancellationform').submit( function(e) {

      // Prevent the form from actually submitting
      e.preventDefault();
      var data = $(this).serialize();
      $form = $(this);

      // show a browser dialog asking to confirm
      $dialog = confirm("Are you sure you want to cancel this order?");
      if ($dialog == true) {

        $form.find('button').addClass('loading');    

        // Submit the form
        $.post('/', data, function(response) {
          console.log('data sent');
          console.log(response.success);
          if (response.success === true) {
            console.log('CANCELLED');
            $form.find('button').removeClass('loading');     
            $form.slideUp();
            $form.addClass('success');
            
            setTimeout(function() {
              $('.ordercancellationmessage').removeClass('hidden');
              $('tr#' + $form.data('shortnumber')).addClass('cancelled');
              $('.ui.modal').modal('hide');
            }, 750);
            
          } else {
              $form.addClass('error');
          }

        });
      }
  });
}

/*
############################
INIT CANCELLATION UPDATE FORM
############################
*/
function initLineItemQuantityForm() {
  $('.lineitemquantityform').submit( function(e) {

      // Prevent the form from actually submitting
      e.preventDefault();
      var data = $(this).serialize();
      $form = $(this);

      $form.find('button').addClass('loading');    
      // Submit the form
      $.post('/', data, function(response) {
        if (response == "success") {
          $form.find('button').removeClass('loading');
          $form.find('.success').removeClass('hidden');         
          setTimeout(function() {
            location.reload();
          }, 750);          
        } else {
          $form.find('button').removeClass('loading');
          $form.find('.error').removeClass('hidden');
        }

      });
  });
}


/*
###########################
ORDER MODALS
USED ON THE DAY VIEW TO DISPLAY ORDER DETAILS WHEN
USERS CLICK ON THE ORDER NUMBER
###########################
*/
function initShowOrderModals() {
  $('a.showOrderModal').click(function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).data('url'),
      type: 'POST',
      dataType: 'html'
    }).done(function(response) {
      $(response).modal('show');
      initUpdateOrderForm();
      initUpdateTransactionForm();
      initOrderCancellationForm();
      initLineItemQuantityForm();
    });

  });
}


/*
###########################
EMAIL MODALS
USED ON THE DAY VIEW TO SEND AN EMAIL TO ALL OR ONE CUSTOMER FROM A CLASS
###########################
*/
function initShowEmailModals() {
  $('a.showEmailModal').click(function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).data('url'),
      type: 'POST',
      dataType: 'html'
    }).done(function(response) {
      $(response).modal('show');
      initSendEmailForm();
      $('.ui.modal').modal('refresh');
    });

  });
}
function initSendEmailForm() {
  $('.emailmessageredactor').redactor({
      buttons: ['bold', 'italic', 'link']
  });
  $('.sendemailform').submit( function(e) {

      // Prevent the form from actually submitting
      e.preventDefault();
      $(this).find('button.pink').addClass('loading');
      var data = $(this).serialize();
      $form = $(this);

      // Send it to the server
      $.post('/', data, function(response) {
        console.log(response);
        if (response == "success") {      
          $form.find('div.field, div.actions').slideUp();
          $form.addClass('success');
          } else {
              $form.addClass('error');
          }
      });

  });
}
initShowEmailModals();





/*
###########################
CLASS MODALS
USED ON THE MONTH VIEW TO DISPLAY CLASS DETAILS WHEN
USERS CLICK ON THE CLASS TITLE
###########################
*/
function initShowClassModals() {
  $('a.showClassModal').click(function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).data('url'),
      type: 'POST',
      dataType: 'html'
    }).done(function(response) {
      $(response).modal('show');
      //initUpdateOrderForm();
      initShowOrderModals();
      initShowEmailModals();
    });

  });
}
initShowClassModals();




function initModals() {
  $('a.modal-trigger, button.modal-trigger').click(function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).data('url'),
      type: 'POST',
      dataType: 'html'
    }).done(function(response) {
      //console.log(response);
      $(response).modal('show');
      initCheckboxes();
      $('textarea').redactor({
          buttons: ['bold', 'italic', 'link']
      });
    });

  });
}




function convertToParsableDateString(string) {
    // works with DD/MM/YYYY
    var parts = string.split("/");
    return new Date(parts[2], parts[1] - 1, parts[0]);
}




/*
############################
INIT ADD PRODUCT FORM
############################
*/
function initAddProductForm () {

  // init vars
  $class = $('#classselect').children(':selected').text();
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  if ($('#dateinput').val()) {
    $existingDate = convertToParsableDateString($('#dateinput').val());
    $friendlyDate = $existingDate.getDate() + ' ' + monthNames[$existingDate.getMonth()] + ' ' + $existingDate.getFullYear();
  } else {
    $friendlyDate = '';
  }

  // function for auto-creation of product title from class and date
  function getTitle($class = '', $date = '') {
    ($class == '') ? $class = '[No class selected] ' : $class += ' on ';
    if ($date == '') $date = '[No date selected]';
    $('#producttitle').val($class + $date);
  }

  // init other search dropdowns than the classselect
  $('#room, #startTime, #finishTime, #teacher').dropdown({ fullTextSearch: true });

  // calls the getTitle function when the class select is changed
  $('#classselect').dropdown({
    fullTextSearch: true,
    onChange: function () {
      $class = $(this).children(':selected').text();
      getTitle($class, $friendlyDate);
    },
  });

  // inits the calendar field and calls the getTitle function when the date is changed
  $('#date').calendar({
    type: 'date',
    onChange: function (date, text) {
      $formattedDate = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
      $('#dateinput').val($formattedDate);
      $friendlyDate = date.getDate() + ' ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();
      getTitle($class, $friendlyDate);
      //console.log($('#dateinput').val());    
    },
  });

  
  $('#teacherselect').dropdown({
    onChange: function () {
      ($(this).val() == "nA") ? $(this).attr('name', 'fields[teacher]') : $(this).attr('name', 'fields[teacher][]');
    },
  });
  

  $('#locationselect').dropdown({
    onChange: function () {
      ($(this).val() == "nA") ? $(this).attr('name', 'fields[location]') : $(this).attr('name', 'fields[location][]');
    },
  });
  
  // handles the validation
  $('#addproductform').form({
    fields: {
      title: {
        identifier: 'producttitle',
        rules: [{type: 'empty'}]
      },
      class: {
        identifier: 'classselect',
        rules: [{type: 'empty'}]
      },
      date: {
        identifier: 'dateinput-text',
        rules: [{type: 'empty'}]
      },
      starttime: {
        identifier: 'starttime',
        rules: [{type: 'empty'}]
      },
      finishtime: {
        identifier: 'finishtime',
        rules: [{type: 'empty'}]
      },
      price: {
        identifier: 'priceinput',
        rules: [{type: 'empty'}]
      },
      places: {
        identifier: 'placesinput',
        rules: [{type: 'empty'}]
      }
    }
  });

  $('#addproductbutton-another').click(function(e) {
    e.preventDefault();
    $('#redirectinput').val('products/add/{id}');
    console.log($('#redirectinput').val());
    $('#addproductform').submit();
  });
  
} // end of the initAddProductForm function

/*
############################
INIT LOGIN FORM
############################
*/
function initLoginForm () {
  $('#loginform').form({
    fields: {
      username: {
        identifier: 'username',
        rules: [{type: 'empty'}]
      },
      password: {
        identifier: 'password',
        rules: [{type: 'empty'}]
      }
    }
  });
}



$('.message .close').on('click', function() {
  $(this).closest('.message').transition('fade');
});


/*
############################
INIT ADD ENTRY FORM
############################
*/
function initAddEntryForm () {

  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  if ($('#dateinput').val()) {
    $existingDate = convertToParsableDateString($('#dateinput').val());
    $friendlyDate = $existingDate.getDate() + ' ' + monthNames[$existingDate.getMonth()] + ' ' + $existingDate.getFullYear();
  } else {
    $friendlyDate = '';
  }

  // inits the calendar field and calls the getTitle function when the date is changed
  $('#date').calendar({
    type: 'date',
    onChange: function (date, text) {
      $formattedDate = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
      $('#dateinput').val($formattedDate);
      $friendlyDate = date.getDate() + ' ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();
      //console.log($('#dateinput').val());    
    },
  });
  
  // handles the validation
  $('#addentryform').form({
    fields: {
      title: {
        identifier: 'producttitle',
        rules: [{type: 'empty'}]
      },
      date: {
        identifier: 'dateinput-text',
        rules: [{type: 'empty'}]
      },
    }
  });
  
} // end of the initA

function exitMessage($action = 'bind') {

  if ($action == 'bind') {
    window.onbeforeunload = function() {
      return 'You have unsaved changes. Are you sure you want to leave this page?' ;
    }
  } else {
    window.onbeforeunload = null;   
  }

}

initAddProductForm();
initAttendenceForm();
//initAddEntryForm();
initLoginForm();
initCheckboxes();
initModals();
initShowOrderModals()

$('#additionaldescription').redactor({
    buttons: ['bold', 'italic']
});



}); // end of document ready function