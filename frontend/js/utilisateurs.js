const userForm =
document.getElementById("userForm");

const listeUsers =
document.getElementById("listeUsers");

let utilisateurs =
JSON.parse(localStorage.getItem("utilisateurs")) || [];

function afficherUsers(){

    listeUsers.innerHTML = "";

    utilisateurs.forEach(function(user,index){

        const tr =
        document.createElement("tr");

        tr.innerHTML =
        "<td>" + user.nom + "</td>" +
        "<td>" + user.email + "</td>";

        const td =
        document.createElement("td");

        const modifier =
        document.createElement("button");

        modifier.innerHTML = "Modifier";

        modifier.className = "action-btn";

        modifier.onclick = function(){

            const nouveauNom =
            prompt(
                "Modifier nom",
                user.nom
            );

            if(nouveauNom){

                user.nom = nouveauNom;

                localStorage.setItem(
                    "utilisateurs",
                    JSON.stringify(utilisateurs)
                );

                afficherUsers();

            }

        };

        const supprimer =
        document.createElement("button");

        supprimer.innerHTML = "Supprimer";

        supprimer.className = "action-btn";

        supprimer.onclick = function(){

            const confirmation =
            confirm(
                "Supprimer utilisateur ?"
            );

            if(confirmation){

                utilisateurs.splice(index,1);

                localStorage.setItem(
                    "utilisateurs",
                    JSON.stringify(utilisateurs)
                );

                afficherUsers();

            }

        };

        td.appendChild(modifier);

        td.appendChild(supprimer);

        tr.appendChild(td);

        listeUsers.appendChild(tr);

    });

}

afficherUsers();

userForm.addEventListener("submit",function(e){

    e.preventDefault();

    const nom =
    document.getElementById("nom").value;

    const email =
    document.getElementById("email").value;

    if(
        nom === "" ||
        email === ""
    ){

        alert("Veuillez remplir tous les champs");

        return;

    }

    const utilisateur = {

        nom,
        email

    };

    utilisateurs.push(utilisateur);

    localStorage.setItem(
        "utilisateurs",
        JSON.stringify(utilisateurs)
    );

    afficherUsers();

    userForm.reset();

});