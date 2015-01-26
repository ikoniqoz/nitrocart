function tooltip_reset()
{
	$('.tooltip-s').tipsy();
}

/*
 * Simple swap function
 * Can swap any 2 elements in Jquery
 * Using a.swap(b);
 */
$.fn.swap = function(other) {
    $(this).replaceWith($(other).after($(this).clone(true)));
};


/*
 * use the value of another
 * Use: $(".item").ncUseVal(".of_another");
 */
$.fn.ncUseVal = function(other) {
    $(this).val($(other).val());
};

/*
 * Simple swap function
 * Using a.ncRemoveLine(2000);
 */
$.fn.ncRemoveLine = function(delay) {
	var pre_delay = delay * 0.75;
	pre_delay = (pre_delay < delay)?pre_delay: delay;
	$(this).fadeTo("slow", 0.04);
	var line = $(this);
	setTimeout(function() {
	  	line.delay(delay).remove();
	}, pre_delay);
};

/*
 * Only for links
 */
$.fn.ncUpdateLink = function(buttonText,link,classes) {
	$(this).text(buttonText);
	$(this).attr('href', link );
	$(this).attr('class',classes);
};	

