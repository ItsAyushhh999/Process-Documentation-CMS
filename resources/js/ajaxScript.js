// import Quill from 'quill';
import $ from "jquery";
// import "./tiptap";

console.log('ajex script caled')
var forms = document.querySelectorAll("form");
Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener(
        "submit",
        function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            event.submitter.innerHTML =
                '<span class="flex space-x-2 items-center"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>         <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Processing...</span></span>';
        },
        false
    );
});

// modal
$(document).ready(function () {
    $("body").on("click", '[data-modal="modal"]', function () {
        var overlay = $(this).data("target");
        // var overlay = document.getElementById('modal_overlay')
        var modal = $(".modal");
        // console.log(modal)
        $(`${overlay}`).removeClass("hidden");
        setTimeout(() => {
            modal.removeClass("opacity-0 scale-90");
        }, 100);
        $('[data-dismiss="modal"]').on("click", function () {
            // console.log("modal 1",modal)
            $(this).parents(overlay).addClass("hidden");
            modal.addClass("opacity-0 scale-90");
        });
    });
});


$(document).on("click", function (e) {
    // console.log("e.targer", e.target)
    if (e.target != undefined && e.target.classList.contains("trigger_model")) {
        var modal = $(".modal");
        modal.addClass("opacity-0 scale-90");
        $(e.target).addClass("hidden");
    }
    if (e.target != undefined && e.target.classList.contains("trigger_sider")) {
        var sider = $(".sider");
        sider.addClass("translate-x-full");
        $(e.target).addClass("hidden");
    }
    if (e.target != undefined && e.target.classList.contains("trigger_slider_modal")) {
        var modal = $(".modal-drawer");
        modal.addClass("opacity-0 translate-y-full");
        setTimeout(() => {
        $(e.target).addClass("hidden");
        }, 100)
    }
});


