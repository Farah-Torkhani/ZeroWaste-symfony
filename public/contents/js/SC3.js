const dash = document.querySelector(".dash");
const sidebar = document.querySelector(".dash__side-bar");
const toggle = document.querySelector(".toggle");
const modeSwitch = document.querySelector(".toggle-switch");
const modeText = document.querySelector(".mode-text");
const logo = document.querySelector(".logo");
const logoDark = document.querySelector(".logo-dark");
const moon = document.querySelector(".bx-moon");
const sun = document.querySelector(".bx-sun");

let getMode = localStorage.getItem("mode");
let getMode2 = localStorage.getItem("mode2");
let getMode3 = localStorage.getItem("mode3");

if (getMode && getMode === "dark") {
  dash.classList.toggle("dark");
  logo.classList.toggle("hidden");
  logoDark.classList.toggle("hidden");
  moon.classList.toggle("sun");
  sun.classList.toggle("sun");
}
if (getMode2 && getMode2 === "close") {
  sidebar.classList.toggle("close");
  logo.classList.toggle("logo-toggled-width");
}

modeSwitch.addEventListener("click", () => {
  dash.classList.toggle("dark");
  logo.classList.toggle("hidden");
  logoDark.classList.toggle("hidden");
  moon.classList.toggle("sun");
  sun.classList.toggle("sun");

  if (dash.classList.contains("dark")) {
    modeText.innerText = "Light Mode";
    localStorage.setItem("mode", "dark");
  } else {
    modeText.innerText = "Dark Mode";
    localStorage.setItem("mode", "light");
  }
});

toggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
  logo.classList.toggle("logo-toggled-width");

  if (sidebar.classList.contains("close")) {
    localStorage.setItem("mode3", "logo-toggled-width");
    localStorage.setItem("mode2", "close");
  } else {
    localStorage.setItem("mode2", "open");
    localStorage.setItem("mode3", "");
  }
});

//************************************************************ */
$(document).ready(function () {
  $("#add-event-btn").click(function () {
    $(".event-forum").show(0);
    $(".event-forum-model").css("right", "0%");
  });
});

$(document).ready(function () {
  $("#event-forum-model__header-closeBtn").click(function () {
    $(".event-forum").hide(0);
    $(".event-forum-model").css("right", "-50%");
  });
});


$(function () {
  $("#prouct-add-form-image__file-btn").change(function (event) {
    var x = URL.createObjectURL(event.target.files[0]);
    $("#prouct-add-form-image__img").attr("src", x);
    $("#prouct-add-form-image__img").show(0);
    $(".bx-cloud-upload").hide(0);

    console.log(event);
  });
});



/*-----------------------------------------------------------------------*/
/*-----------------------------------------------------------------------*/

function toggle_commande_dash(x) {
  $("#product-card-commands-content__information-details" + x).slideToggle(700);
  $("#chevron-down" + x).toggle(0);
  $("#chevron-up" + x).toggle(0);
}



$(document).ready(function () {
  $("#add-event-btn").mouseenter(function () {
    $("#add-btn-hover-container").show(0);
  });

  $("#add-event-btn").mouseleave(function () {
    $("#add-btn-hover-container").hide(0);
  });

  $("#add-participant").mouseenter(function () {
    $("#add-btn-hover-container2").show(0);
  });

  $("#add-participant").mouseleave(function () {
    $("#add-btn-hover-container2").hide(0);
  });


  $("#category-btn-rightSide").mouseenter(function () {
    $("#category-btn-hover-container").show(0);
  });

  $("#category-btn-rightSide").mouseleave(function () {
    $("#category-btn-hover-container").hide(0);
  });
});

$(document).ready(function () {
  $("#close-category-model").click(function () {
    $("#product-categories-model").hide(0);
  });
});

$(document).ready(function () {
  $("#category-btn-rightSide").click(function () {
    $("#product-categories-model").show(0);
  });
});

//

$(document).ready(function () {
  $("#add-new-category__id").click(function () {
    $("#add-category__form-id").slideToggle(300);
  });
});

/*-----------------------------------------------------------------------*/
/*-----------------------------------------------------------------------*/

