<script>


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

initCheckboxes();
initModals();

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
      },
      // uncheck all children
      onUnchecked: function() {
        var $childCheckbox  = $(this).parents('thead').siblings('tbody').find('.checkbox');
        $childCheckbox.checkbox('uncheck');
        saveMessage('saved');
      }
    });
  // children
  $('.child.checkbox')
    .checkbox({
      // Fire on load to set parent value
      //fireOnInit : true,
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
        saveMessage('saved');
      }
    });
}


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





});

</script>

</body>
</html>