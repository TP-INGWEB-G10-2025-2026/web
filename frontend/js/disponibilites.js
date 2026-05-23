const listeDisponibles =
document.getElementById("listeDisponibles");

const listeIndisponibles =
document.getElementById("listeIndisponibles");

const materiels =
JSON.parse(localStorage.getItem("materiels")) || [];

function afficherDisponibilites(){

    listeDisponibles.innerHTML = "";

    listeIndisponibles.innerHTML = "";

    materiels.forEach(function(materiel){

        const li = document.createElement("li");

        li.innerHTML =
        materiel.nom + " - " +
        materiel.etat;

        if(materiel.etat === "Disponible"){

            listeDisponibles.appendChild(li);

        }else{

            listeIndisponibles.appendChild(li);

        }

    });

}

afficherDisponibilites();