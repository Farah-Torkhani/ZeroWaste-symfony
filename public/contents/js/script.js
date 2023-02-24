/*$(function () {
  $("#prouct-add-form-image__file-btn").change(function (event) {
    console.log("event");
    var x = URL.createObjectURL(event.target.files[0]);
    $("#prouct-add-form-image__img").attr("src", x);
    $("#prouct-add-form-image__img").show(0);
    $(".bx-cloud-upload").hide(0);

    console.log(event);
  });
});*/

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

//*************************************************** */
/*
function show(data) {
  document.querySelector(".product-add-form__input-category").value = data;
  option.classList.toggle("activee");
  chevronDown.classList.toggle("rotate");
}

let dropdown = document.querySelector(".product-add-form__input-category");
let option = document.querySelector(".product-category__option");
let chevronDown = document.querySelector(".bx-chevron-down");

dropdown.onclick = function () {
  option.classList.toggle("activee");
  chevronDown.classList.toggle("rotate");
};

/*
$(document).ready(function () {
  $(".product-add-form__input-category").click(function () {
    $(".product-category__option").slideToggle(500);
  });
});
*/
//************************************************************ */
/* $(document).ready(function () {
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
*/
//*************************************************************** */

/*
let input = document.getElementById("prouct-add-form-image__file-btn");
let image = document.getElementById("prouct-add-form-image__img");

input.addEventListener("change", function () {
  console.log("rrrrrrrr");
  if (this.files && this.files[0]) {
    let reader = new FileReader();

    reader.onload = function (e) {
      image.src = e.target.result;
      image.style.display = "block";
    };
    console.log("rrrrrrrr");

    reader.readAsDataURL(this.files[0]);
  }
});*/

//*******************************dash commands **************************************************/

/*
let command = document.querySelector(".product-card-commands-content");
let commandInfo = document.querySelector(
  ".product-card-commands-content__information-details"
);
let chevronDownC = document.querySelector(
  ".product-card-commands-chevron-icon"
);

command.onclick = function () {
  commandInfo.classList.toggle("showC");
  chevronDownC.classList.toggle("rotate");
};
*/

/*
$(document).ready(function () {
  $(".product-card-commands-content").click(function () {
    $(".product-card-commands-content__information-details").slideToggle(700);
    $(".up-cmd").toggle(0);
    $(".down-cmd").toggle(0);
  });
});
*/
function toggle_commande_dash(x) {
  $("#product-card-commands-content__information-details" + x).slideToggle(700);
  $("#chevron-down" + x).toggle(0);
  $("#chevron-up" + x).toggle(0);
}

$(function () {
  $("#categorie_produit_imageCategorie").change(function (event) {
    var x = URL.createObjectURL(event.target.files[0]);
    $("#category-image-add-container__img").attr("src", x);
    $("#category-image-add-container__img").show(0);
    $(".bx-image-add").hide(0);
  });
});

$(document).ready(function () {
  $("#add-btn-rightSide").mouseenter(function () {
    $("#add-btn-hover-container").show(0);
  });

  $("#add-btn-rightSide").mouseleave(function () {
    $("#add-btn-hover-container").hide(0);
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
/*
$(document).ready(function () {
  $("#add-new-category__id").click(function () {
    $("#add-category__form-id").slideToggle(300); 
  });
});
*/

// Get the button and the div
const button = document.getElementById("add-new-category__id");
const div = document.getElementById("add-category__form-id");

// Initialize the visibility state from localStorage, if it exists
const visibilityState = localStorage.getItem("divVisibility");
if (visibilityState === "hidden") {
  div.style.display = "none";
}

// Add a click event listener to the button
button.addEventListener("click", function () {
  // Toggle the visibility of the div
  if (div.style.display === "none") {
    div.style.display = "block";
    localStorage.setItem("divVisibility", "visible");
  } else {
    div.style.display = "none";
    localStorage.setItem("divVisibility", "hidden");
  }
});

//************************************************************************ */
