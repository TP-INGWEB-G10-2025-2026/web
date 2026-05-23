const historiquePrets =
document.getElementById("historiquePrets");

const historiqueRetours =
document.getElementById("historiqueRetours");

const prets =
JSON.parse(localStorage.getItem("prets")) || [];

const retours =
JSON.parse(localStorage.getItem("retours")) || [];

function afficherHistorique(){

    historiquePrets.innerHTML = "";

    historiqueRetours.innerHTML = "";

    prets.forEach(function(pret){

        const li =
        document.createElement("li");

        li.innerHTML =
        pret.utilisateur +
        " a emprunté " +
        pret.materiel +
        " du " +
        pret.dateDebut +
        " au " +
        pret.dateRetour;

        historiquePrets.appendChild(li);

    });

    retours.forEach(function(retour){

        const li =
        document.createElement("li");

        li.innerHTML =
        retour.materiel +
        " retourné état : " +
        retour.etat;

        historiqueRetours.appendChild(li);

    });

}

afficherHistorique();