/*global $, console, window*/

var app = {

    assign_nav: function (hook) {
        "use strict";
        var target = hook.attr("data-target");
        hook.click(function () {
            app.scroll_to_id(target);
        });
    },

    scroll_to_id: function (id) {
        "use strict";
        $('html, body').animate({
            scrollTop: $("#" + id).offset().top
        }, 500);
        setTimeout(function () {
            $("#" + id).find("input").first().focus();
        }, 450);
    },

    init_nav: function () {
        "use strict";
        var hook = $("div[data-type=nav]"),
            i = 0;
        for (i = 0; i < hook.length; i += 1) {
            app.assign_nav(hook.eq(i));
        }
    },

    assign_slot: function (hook) {
        "use strict";
        var slot_val = hook.attr("data-workshop");
        hook.click(function () {
            $("input[name=Workshop]").val(slot_val);
            hook.children("img").addClass("selected");
            app.render_slot_availability(slot_val);
            setTimeout(function () {
                hook.children("img").removeClass("selected");
            }, 1000);
        });
    },

    init_slot: function () {
        "use strict";
        var hook = $("li[data-type=slot]"),
            i = 0;
        for (i = 0; i < hook.length; i += 1) {
            app.assign_slot(hook.eq(i));
        }
    },

    tab_return_handle: function (hook) {
        "use strict";
        var target = hook.attr("data-tab");
        hook.on('keydown', function (e) {
            if (e.keyCode === 13 || e.keyCode === 9) {
                if (target) {
                    e.preventDefault();
                    app.scroll_to_id(target);
                    return;
                }
            }
            if (e.keyCode === 13) {
                e.preventDefault();
            }
            if ((e.shiftKey && e.keyCode === 9) ||
                (e.shiftKey && e.keyCode === 13)) {
                e.preventDefault();
            }
        });
    },

    process_input_properties: function () {
        "use strict";
        // Find all data-tab elements.
        var hook = $("input"),
            i = 0;
        for (i = 0; i < hook.length; i += 1) {
            app.tab_return_handle(hook.eq(i));
        }
    },

    init_notice: function () {
        "use strict";
        $("#notice").click(function (el) {
            if ($("#notice").attr("data-click")) {
                setTimeout(function () {
                    app.scroll_to_id($("#notice").attr("data-click"));
                }, 200);
            }
        });

    },

    render_slot_availability: function (id) {
        "use strict";
        $("#notice").text("Please Wait");
        $.get("/api/count/" + id, function (data) {
            if (data.registrations_accepted) {
                $("#notice").html(data.remains + " Seats are remaining in this Slot. <span>Click Here to Proceed to Registration.</span>");
                $("#notice").attr("data-click", "two");
            } else {
                $("#notice").text("Sorry, this slot is not accepting registrations anymore. Please choose another slot.");
                $("#notice").attr("data-click", "NaN");
            }
        });
    }
};

app.scroll_to_id("one");
app.init_notice();
app.init_nav();
app.init_slot();
app.process_input_properties();