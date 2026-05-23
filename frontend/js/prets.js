const pretForm =
document.getElementById("pretForm");

const listePrets =
document.getElementById("listePrets");

let prets =
JSON.parse(localStorage.getItem("prets")) || [];

let materiels =
JSON.parse(localStorage.getItem("materiels")) || [];

function afficherPrets(){

    listePrets.innerHTML = "";

    prets.forEach(function(pret,index){

        const li =
        document.createElement("li");

        li.innerHTML =
        pret.utilisateur +
        " - " +
        pret.materiel +
        " - " +
        pret.dateDebut +
        " - " +
        pret.dateRetour;

        const supprimer =
        document.createElement("button");

        supprimer.innerHTML = "Supprimer";

        supprimer.className = "action-btn";

        supprimer.onclick = function(){

            prets.splice(index,1);

            localStorage.setItem(
                "prets",
                JSON.stringify(prets)
            );

            afficherPrets();

        };

        li.appendChild(supprimer);

        listePrets.appendChild(li);

    });

}

afficherPrets();

pretForm.addEventListener("submit",function(e){

    e.preventDefault();

    const utilisateur =
    document.getElementById("utilisateur").value;

    const materiel =
    document.getElementById("materiel").value;

    const dateDebut =
    document.getElementById("dateDebut").value;

    const dateRetour =
    document.getElementById("dateRetour").value;

    if(
        utilisateur === "" ||
        materiel === "" ||
        dateDebut === "" ||
        dateRetour === ""
    ){

        alert("Veuillez remplir tous les champs");

        return;

    }

    const pret = {

        utilisateur,
        materiel,
        dateDebut,
        dateRetour

    };

    prets.push(pret);

    localStorage.setItem(
        "prets",
        JSON.stringify(prets)
    );

    afficherPrets();

    pretForm.reset();

});