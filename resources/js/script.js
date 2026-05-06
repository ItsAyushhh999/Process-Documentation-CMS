// import Quill from 'quill';
import $ from "jquery";
import "./tiptap";
// import flatpicker from "flatpickr"
import "./datatable";

// window.flatpicker = flatpicker;

// // import './datetimepicker'
var elem = document.getElementById("dateTimePicker");
if (elem != null) {
    flatpickr(elem, {
        enableTime: true,
        defaultHour: "17",
        defaultMinute: "30",
        dateFormat: "m/d/Y h:i K",
    });
}

const elements = document.querySelectorAll("[datepicker]");
    let currentDate = new Date();
    let threeMonthBackDates = new Date(currentDate);
    let pastDates =  threeMonthBackDates.setMonth(currentDate.getMonth() - 3);
elements.forEach((el) => {
    flatpickr(el, {
        enableTime: true,
        defaultHour: "17",
        defaultMinute: "30",
        dateFormat: "m/d/Y",
        defaultDate: pastDates,
    });
});

const element = document.querySelectorAll("[datepicker2]");

    console.log(pastDates,currentDate);
    element.forEach((el) => {
    flatpickr(el, {
        enableTime: true,
        defaultHour: "17",
        defaultMinute: "30",
        dateFormat: "m/d/Y",
        defaultDate: currentDate,
    });
})

$(".print").click(function () {
    console.log("clicked");
});
// $(function () {
$(document).ready(function () {
    $(".htmlToPdf").on("click", function () {
        console.log("clicked");
        var divToPrint = $("#printArea");
        let newWindow = window.open("");
        newWindow.document.write(
            "<html><head><title>" + document.title + "</title>"
        );
        newWindow.document.write("</head><body >");
        newWindow.document.write(divToPrint.html());
        newWindow.document.write("</body></html>");
        newWindow.document.close(); // necessary for IE >= 10
        newWindow.focus(); // necessary for IE >= 10*/

        newWindow.print();
        newWindow.close();
    });
    var sideBar = $("#docSideBar");
    $(".toggleSideBar").on("click", function () {
        console.log("toggled");
        if (sideBar.hasClass("-translate-x-full")) {
            sideBar.removeClass("-translate-x-full");
        } else {
            sideBar.addClass("-translate-x-full");
        }
    });
});

$("#view-more").click(function () {
    $(".viewless").slideToggle();
    if ($("#view-more").text() == "View Less") {
        $(this).text("View More");
    } else {
        $(this).text("View Less");
    }
});
// Navigation Bar
$(".nav-btn").click(function () {
    $("#navbar-default").slideToggle();
    this.classList.toggle("close");
});

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
$(".fade").ready(function () {
    $(".fade").fadeOut(4000);
});
const quillElement = document.getElementById("editor-container");
if (quillElement) {
    var quill = new Quill("#editor-container", {
        modules: {
            toolbar: [
                ["bold", "italic"],
                ["link", "blockquote", "code-block", "image"],
                [{ list: "ordered" }, { list: "bullet" }],
            ],
        },
        placeholder: "Compose an epic...",
        theme: "snow",
    });
    const form = document.querySelector("form");
    form.onsubmit = function (e) {
        const input = document.querySelector("input[name=description]");

        input.value = quill.root.innerHTML;
    };
}

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

    // modal drawer
    $("body").on("click", '[data-modal="modal-drawer"]', function () {
        var overlay = $(this).data("target");
        const id = $(this).data('id')
        console.log('id', id)
        var modal = $(".modal-drawer");
        $(`${overlay}`).removeClass("hidden");
        setTimeout(() => {
            modal.removeClass("opacity-0 translate-y-full");
        }, 100);

        $.ajax({
            url: '/tasks/' + id + '/editV2',
            type: 'GET',
            success: function(data) {
                // console.log('potato', data)
                $('#modal_drawer').empty().html(data);
                $('#content-placeholder').find('script').each(function() {
                    eval($(this).text());
                });
            },
        })

        $('[data-dismiss="modal-drawer"]').on("click", function () {
            // console.log("modal 1",clicked)
            modal.addClass("opacity-0 translate-y-full");
            setTimeout(() => {
                $(this).parents(overlay).addClass("hidden");
            }, 150)
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

$(document).ready(function () {
    $("body").on("click", '[data-modal="sider"]', function () {
        var overlay = $(this).data("target");
        var sider = $(".sider");
        $(`${overlay}`).removeClass("hidden");
        setTimeout(() => {
            sider.removeClass("opacity-0 translate-x-full");
        }, 100);
        $('[data-dismiss="sider"]').on("click", function () {
            // console.log("modal 1")
            sider.addClass("translate-x-full");
            setTimeout(() => {
                sider.addClass("opacity-0");
                $(this).parents(overlay).addClass("hidden");
            }, 100);
        });
    });
});
// const commentForm = document.querySelector("#commentForm");
// console.log("fdsf")
// commentForm.addEventListener("submit", (e) => {
//   e.preventDefault();
//   console.log("Hello")

//   // handle submit
// });


// tabs
$(document).ready(function () {
    $("body").on("click", '[data-toggle="tab"]', function () {
        // remove all active class
        $('[data-toggle="tab"]').removeClass('tab-active')
        var overlay = $(this).data("target");
        console.log('clicked', overlay)
        $(this).addClass('tab-active')
        

        // $(`${overlay}`).removeClass("hidden");

        // var sider = $(".sider");
        // $(`${overlay}`).removeClass("hidden");
        // setTimeout(() => {
        //     sider.removeClass("opacity-0 translate-x-full");
        // }, 100);
        // $('[data-dismiss="sider"]').on("click", function () {
        //     // console.log("modal 1")
        //     sider.addClass("translate-x-full");
        //     setTimeout(() => {
        //         sider.addClass("opacity-0");
        //         $(this).parents(overlay).addClass("hidden");
        //     }, 100);
        // });
    });
});


