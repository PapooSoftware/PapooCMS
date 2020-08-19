/**
 * TinyLimiter - scriptiny.com/tinylimiter
 * License: GNU GPL v3.0 - scriptiny.com/license
 */

(function($) {
    $.fn.extend( {
        limiter: function(limit, elem, meta) {
            $(this).on("keyup focus", function() {
                setCount(this, elem);
            });
            function setCount(src, elem) {
                var chars = src.value.length;
                if (meta!="meta")
                {
                    if (chars > limit) {
                        src.value = src.value.substr(0, limit);
                        chars = limit;
                    }
                    elem.html( limit - chars );
                }
                else
                {
                    elem.html( limit + chars );
                }


            }
            setCount($(this)[0], elem);
        }
    });
})(jQuery);