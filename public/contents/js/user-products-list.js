$(document).ready(function () {
  $(".add-to-cart-model-v1__Btn").click(function () {
    $(".add-to-cart-model").toggle(10);
  });
});

//************** */

const categories = document.querySelector(".shop-items__categories-container");
firstImg = document.querySelectorAll(".shop-items__one-category-container")[0];
arrowIcons = document.querySelectorAll(".wrapper i");

let isDragStart = false,
  prevPageX,
  prevScrollLeft;
let firstImgWidth = firstImg.clientWidth + 17;

arrowIcons.forEach((icon) => {
  icon.addEventListener("click", () => {
    //console.log(arrowIcons);

    categories.scrollLeft += icon.id == "left" ? -firstImgWidth : firstImgWidth;
  });
});
const dragStart = (e) => {
  isDragStart = true;
  prevPageX = e.pageX;
  prevScrollLeft = categories.scrollLeft;
};

const dragging = (e) => {
  if (!isDragStart) return;
  e.preventDefault();
  categories.classList.add("dragging");
  let positionDiff = e.pageX - prevPageX;
  categories.scrollLeft = prevScrollLeft - positionDiff;
};

const dragStop = () => {
  isDragStart = false;
  categories.classList.remove("dragging");
};
categories.addEventListener("mousedown", dragStart);
categories.addEventListener("mousemove", dragging);
categories.addEventListener("mouseup", dragStop);

/******************************************************** */
// 1. Sélectionnez tous les boutons sur la page
const buttons = document.querySelectorAll(
  ".shop-items__one-category-container"
);

// 2. Ajouter un écouteur d'événement "click" à chaque bouton
for (let i = 0; i < buttons.length; i++) {
  buttons[i].addEventListener("click", function () {
    // Ajouter la classe "active" au bouton cliqué
    this.classList.add("activeCategory");

    // Supprimer la classe "active" des autres boutons
    for (let j = 0; j < buttons.length; j++) {
      if (j !== i) {
        buttons[j].classList.remove("activeCategory");
      }
    }
  });
}

/************************************************************ */
