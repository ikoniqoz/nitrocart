
/*
 * Copy the values of the inner 
 * to the outer values
 */
$(document).on('click', '.boxduplicate', function(event) {
      $("input[name='outer_height']").ncUseVal("input[name='height']");
      $("input[name='outer_width']").ncUseVal("input[name='width']");
      $("input[name='outer_length']").ncUseVal("input[name='length']");
      event.preventDefault();
});