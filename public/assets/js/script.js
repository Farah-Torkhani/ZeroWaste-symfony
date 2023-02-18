//*************************************************************** */
$(function () {
  $("#update_user_ProfilePic").change(function (event) {
    var x = URL.createObjectURL(event.target.files[0]);
    $("#prouct-add-form-image__img").attr("src", x);
    $("#prouct-add-form-image__img").show(0);
    $(".bx-cloud-upload").hide(0);

    console.log(event);
  });
});
//*************************************************************** */

const updateModalBtn = document.querySelector(".changePassBtn");
const closeModalBtn = document.querySelector(
  ".dash-user-profile__update__card__btn-close"
);
const modal = document.querySelector(".dash-user-profile__update__card");
const bg = document.querySelector(".t");

updateModalBtn.addEventListener("click", function () {
  modal.classList.add("dash-user-profile__update__card__open");
  bg.classList.add("t__open");
  console.log("object");
});

closeModalBtn.addEventListener("click", function () {
  modal.classList.remove("dash-user-profile__update__card__open");
  bg.classList.remove("t__open");
});
