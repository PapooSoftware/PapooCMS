/**
 * Created by andreas on 10.09.15.
 */

jQuery(document).submit(
    function(e)
    {
        var form = jQuery(e.target);
        if(form.is(".reinstall_form_noreload"))
        {
            e.preventDefault();

            var formname = form.context.name;

            jQuery.ajax(
                {
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    success: function(data)
                    {
                        $("#" + formname).addClass("reinstall-button-reinstalled");

                        setInterval(function() {
                            $(".reinstall-button-reinstalled").removeClass("reinstall-button-reinstalled");
                        }, 3000);
                    }
                });
        }
    }
);
