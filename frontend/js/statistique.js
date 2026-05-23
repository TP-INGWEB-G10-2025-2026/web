const listeStats =
document.getElementById("listeStats");

const prets =
JSON.parse(localStorage.getItem("prets")) || [];

function afficherStatistiques(){

    listeStats.innerHTML = "";

    let compteur = {};

    prets.forEach(function(pret){

        if(compteur[pret.materiel]){

            compteur[pret.materiel]++;

        }else{

            compteur[pret.materiel] = 1;

        }

    });

    for(let materiel in compteur){

        const li =
        document.createElement("li");

        li.innerHTML =
        materiel +
        " utilisé " +
        compteur[materiel] +
        " fois";

        listeStats.appendChild(li);

    }

}

afficherStatistiques();