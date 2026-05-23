const listeNotifications =
document.getElementById("listeNotifications");

const reservations =
JSON.parse(localStorage.getItem("reservations")) || [];

function afficherNotifications(){

    listeNotifications.innerHTML = "";

    reservations.forEach(function(reservation){

        const li = document.createElement("li");

        li.innerHTML =
        "Nouvelle réservation : " +
        reservation.user + " - " +
        reservation.materiel;

        listeNotifications.appendChild(li);

    });

}

afficherNotifications();