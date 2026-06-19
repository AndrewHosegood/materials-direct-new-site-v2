jQuery(document).ready(function($){

    if (sessionStorage.getItem('xmasClosed') === 'true') {
        $(".newsletter-signup").css({ display: 'none', visibility: 'hidden' });
    }
	
    $(document).on('click', '.newsletter-signup__icon, .newsletter-signup__close', function() {
        const modalBg = $(".newsletter-signup");
        const modal = $(".newsletter-signup__modal");
        const closure = $(".newsletter-signup");
        
        modal.css({
            transition: "transform 0.3s ease",
            transform: "scale(1.05)"
        });

        setTimeout(function() {
            modalBg.css({ display: 'none', visibility: 'invisible' });
            //modal.addClass("animate");
            //modal.hide();
            closure.hide();
            // Store a flag in sessionStorage to prevent it from showing again during the session
            sessionStorage.setItem('xmasClosed', 'true');
        }, 300); // Match the transition duration
    });

    $(document).on('click', '.newsletter-signup__chat', function(event) { 
        event.preventDefault(); 
		window.HubSpotConversations.widget.open(); 
    });


    // toggle scale the technical article image on the home page
	$('.home-technical-bulletin__btn').hover(
    function() {
      // Mouse enter
      $('.home-technical-bulletin__img').css({
        'transform': 'scale(1.03)',
        'box-shadow': '0px 0px 16px rgba(239, 144, 3, 0.9)'
      });
    },
    function() {
      // Mouse leave
      $('.home-technical-bulletin__img').css({
        'transform': 'scale(1)',
        'box-shadow': 'none'
      });
    }
  	);
	// toggle scale the technical article image on the home page
	

	// // display modal if download PDF is clicked
	$(".home-technical-bulletin__btn").click(function(event){
		event.preventDefault();
	  	$(".newsletter-signup__modal").addClass("zoom-in");
		$(".newsletter-signup").css({ display: 'block', visibility: 'visible' });
	});
	// display modal if download PDF is clicked

}); // document ready