const listeDemandes =
document.getElementById("listeDemandes");

let reservations =
JSON.parse(localStorage.getItem("reservations")) || [];

function afficherDemandes(){

    listeDemandes.innerHTML = "";

    reservations.forEach(function(reservation,index){

        const li = document.createElement("li");

        li.innerHTML =
        reservation.user + " - " +
        reservation.materiel + " ";

        const valider =
        document.createElement("button");

        valider.innerHTML = "Valider";

        valider.className = "action-btn";

        valider.onclick = function(){

            reservation.statut = "Validée";

            localStorage.setItem(
                "reservations",
                JSON.stringify(reservations)
            );

            afficherDemandes();

        };

        const rejeter =
        document.createElement("button");

        rejeter.innerHTML = "Rejeter";

        rejeter.className = "action-btn";

        rejeter.onclick = function(){

            reservation.statut = "Rejetée";

            localStorage.setItem(
                "reservations",
                JSON.stringify(reservations)
            );

            afficherDemandes();

        };

        li.appendChild(valider);

        li.appendChild(rejeter);

        if(reservation.statut){

            li.innerHTML +=
            " - " + reservation.statut;

        }

        listeDemandes.appendChild(li);

    });

}

afficherDemandes();