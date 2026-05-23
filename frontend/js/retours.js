const retourForm =
document.getElementById("retourForm");

const listeRetours =
document.getElementById("listeRetours");

let retours =
JSON.parse(localStorage.getItem("retours")) || [];

function afficherRetours(){

    listeRetours.innerHTML = "";

    retours.forEach(function(retour,index){

        const li =
        document.createElement("li");

        li.innerHTML =
        retour.materiel +
        " - " +
        retour.etat;

        const supprimer =
        document.createElement("button");

        supprimer.innerHTML = "Supprimer";

        supprimer.className = "action-btn";

        supprimer.onclick = function(){

            retours.splice(index,1);

            localStorage.setItem(
                "retours",
                JSON.stringify(retours)
            );

            afficherRetours();

        };

        li.appendChild(supprimer);

        listeRetours.appendChild(li);

    });

}

afficherRetours();

retourForm.addEventListener("submit",function(e){

    e.preventDefault();

    const materiel =
    document.getElementById("materielRetour").value;

    const etat =
    document.getElementById("etatRetour").value;

    if(materiel === ""){

        alert("Veuillez saisir le matériel");

        return;

    }

    const retour = {

        materiel,
        etat

    };

    retours.push(retour);

    localStorage.setItem(
        "retours",
        JSON.stringify(retours)
    );

    afficherRetours();

    retourForm.reset();

});