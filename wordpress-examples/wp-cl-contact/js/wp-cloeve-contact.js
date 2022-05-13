
function cloeveContactFormSubmit(){

    // for each form
    jQuery( ".cloeve-contact-form" ).each(function( index ) {

        // get data
        var currentForm = jQuery( this );
        var formData = currentForm.serializeArray();

        // if data
        if(formData !== undefined && jQuery.isArray(formData)){

            // debug
            //console.log(formData);

            // disable
            jQuery("#cloeve_contact_btn_submit").prop("disabled",true);

            // save
            jQuery.ajax({
                type: 'POST',
                url: '/wp-json/cloeve-contact/v1/submit',
                data: formData,
                success: function(response) {

                    // reset form
                    currentForm.each(function(){
                        this.reset();
                    });

                    // show message
                    jQuery(".cloeve-contact-message").css("margin-top", "10px");
                    jQuery(".cloeve-contact-message").css("max-height", "40px");

                    // hide message
                    setTimeout(function(){
                        jQuery(".cloeve-contact-message").css("margin-top", "0");
                        jQuery(".cloeve-contact-message").css("max-height", "0");
                    }, 5000);

                    // enable
                    jQuery("#cloeve_contact_btn_submit").prop("disabled",false);
                },
                error: function() {
                    alert("There was an error submitting your email, please try again.");

                    // enable
                    jQuery("#cloeve_contact_btn_submit").prop("disabled",false);
                }
            });

        }

    });



    return false;
}