// Sidebar toggle function
const sidebarToggle = () => {
  document.body.classList.toggle("sidebar-open");
};

// Sidebar trigger
const sidebarTrigger = document.getElementById("main-header__sidebar-toggle");

// Add event listener
sidebarTrigger.addEventListener("click", sidebarToggle);

// Sidebar collapse function
const sidebarCollapse = () => {
  document.body.classList.toggle("sidebar-collapsed");
};

// Sidebar trigger
const sidebarCollapseTrigger = document.getElementById("sidebar__collapse");

// Add event listener
sidebarCollapseTrigger.addEventListener("click", sidebarCollapse);

// Theme switcher function
const switchTheme = () => {
  // Get root element and data-theme value
  const rootElem = document.documentElement;
  let dataTheme = rootElem.getAttribute("data-theme"),
    newTheme;

  newTheme = dataTheme === "light" ? "dark" : "light";

  // Set the new HTML attribute
  rootElem.setAttribute("data-theme", newTheme);

  // Set the new local storage item
  localStorage.setItem("theme", newTheme);
};

// Add the event listener for the theme switcher
document
  .querySelector("#sidebar__theme-switcher")
  .addEventListener("click", switchTheme);

// menu dropdown
$(document).ready(function () {
  // jQuery pour dérouler et replier le menu latéral
  $(".sub-btn").click(function () {
    // Fermer tous les autres dropdowns
    $(".sub-menu").not($(this).next(".sub-menu")).slideUp();
    $(".sub-btn").not($(this)).find(".dropdown").removeClass("active");

    // Ouvrir le dropdown cliqué
    $(this).next(".sub-menu").slideToggle();
    $(this).find(".dropdown").toggleClass("active");
  });
});

// Écouter les événements de saisie dans le champ d'adresse
document.querySelector("#adresse").addEventListener("input", function (e) {
  let input = e.target.value.trim(); // Récupérer la valeur saisie dans le champ d'adresse

  // Si l'entrée est vide, cacher la liste des suggestions
  if (input === "") {
    document.getElementById("address-suggestions").style.display = "none";
    return;
  }

  // Requête à l'API pour obtenir des suggestions d'adresses
  let xhr = new XMLHttpRequest();
  xhr.open("get", "https://api-adresse.data.gouv.fr/search/?q=" + input);
  xhr.send();
  xhr.addEventListener("readystatechange", function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let data = JSON.parse(xhr.responseText);
      let select = document.querySelector("#address-suggestions");
      select.innerHTML = ""; // Nettoyer les anciennes suggestions

      // Peupler le select avec les suggestions d'adresses
      if (data.features.length > 0) {
        data.features.forEach(function (address) {
          let option = document.createElement("option");
          option.value = address.properties.label;
          option.textContent = address.properties.label;
          select.appendChild(option);
        });
        // Afficher la liste des suggestions
        document.getElementById("address-suggestions").style.display = "block";
      } else {
        // S'il n'y a pas de suggestions, cacher le select
        document.getElementById("address-suggestions").style.display = "none";
      }
    }
  });
});

// Placer l'adresse sélectionnée dans l'input text
document
  .querySelector("#address-suggestions")
  .addEventListener("change", function (e) {
    let selectedAddress = e.target.value;
    document.getElementById("adresse").value = selectedAddress;
    document.getElementById("address-suggestions").style.display = "none"; // Cacher la liste des suggestions
  });

function clearInput() {
  document.getElementById("adresse").value = "";
}

// function testInput() {
//   var inputValue = document.getElementById("myInput").value;
//   alert("Texte saisi : " + inputValue);
// }
