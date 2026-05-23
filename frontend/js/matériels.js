const materielForm =
document.getElementById("materielForm");

const listeMateriels =
document.getElementById("listeMateriels");

let materiels =
JSON.parse(localStorage.getItem("materiels")) || [];

function afficherMateriels(){

    listeMateriels.innerHTML = "";

    materiels.forEach(function(materiel,index){

        const tr =
        document.createElement("tr");

        tr.innerHTML =
        "<td>" + materiel.nom + "</td>" +
        "<td>" + materiel.categorie + "</td>" +
        "<td>" + materiel.etat + "</td>";

        const td =
        document.createElement("td");

        const modifier =
        document.createElement("button");

        modifier.innerHTML = "Modifier";

        modifier.className = "action-btn";

        modifier.onclick = function(){

            const nouveauNom =
            prompt(
                "Nouveau nom",
                materiel.nom
            );

            if(nouveauNom){

                materiel.nom = nouveauNom;

                localStorage.setItem(
                    "materiels",
                    JSON.stringify(materiels)
                );

                afficherMateriels();

            }

        };

        const supprimer =
        document.createElement("button");

        supprimer.innerHTML = "Supprimer";

        supprimer.className = "action-btn";

        supprimer.onclick = function(){

            const confirmation =
            confirm(
                "Supprimer ce matériel ?"
            );

            if(confirmation){

                materiels.splice(index,1);

                localStorage.setItem(
                    "materiels",
                    JSON.stringify(materiels)
                );

                afficherMateriels();

            }

        };

        td.appendChild(modifier);

        td.appendChild(supprimer);

        tr.appendChild(td);

        listeMateriels.appendChild(tr);

    });

}

afficherMateriels();

materielForm.addEventListener("submit",function(e){

    e.preventDefault();

    const nom =
    document.getElementById("materielNom").value;

    const categorie =
    document.getElementById("materielCategorie").value;

    const etat =
    document.getElementById("materielEtat").value;

    if(
        nom === "" ||
        categorie === ""
    ){

        alert("Veuillez remplir tous les champs");

        return;

    }

    const materiel = {

        nom,
        categorie,
        etat

    };

    materiels.push(materiel);

    localStorage.setItem(
        "materiels",
        JSON.stringify(materiels)
    );

    afficherMateriels();

    materielForm.reset();

});