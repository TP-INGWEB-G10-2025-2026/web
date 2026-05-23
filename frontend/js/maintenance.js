const listeMaintenance =
document.getElementById("listeMaintenance");

const materiels =
JSON.parse(localStorage.getItem("materiels")) || [];

function afficherMaintenance(){

    listeMaintenance.innerHTML = "";

    materiels.forEach(function(materiel){

        if(
            materiel.etat === "En panne" ||
            materiel.etat === "En maintenance"
        ){

            const li = document.createElement("li");

            li.innerHTML =
            materiel.nom + " - " +
            materiel.etat;

            listeMaintenance.appendChild(li);

        }

    });

}

afficherMaintenance();