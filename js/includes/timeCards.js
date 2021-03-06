jQuery(function() {
	
	/** ******************************
	 * Accordions
	 ****************************** **/
    var allPanels = jQuery('.accordion > dd').hide();

    jQuery('.accordion > dt > a').click(function () {
        $target = jQuery(this).parent().next();
        if (!$target.hasClass('active')) {
            allPanels.removeClass('active').slideUp();
            $target.addClass('active').slideDown();
			jQuery('.accordion > dt > a').find("i").removeClass('fa-angle-down');
			jQuery('.accordion > dt > a').find("i").addClass('fa-angle-right');
        } else {
            $target.removeClass('active').slideUp();
        }
		jQuery(this).find("i").toggleClass("fa-angle-right fa-angle-down");
        return false;
    });
	
});
