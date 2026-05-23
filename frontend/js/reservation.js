const reservationForm = document.getElementById("reservationForm");

const listeReservations = document.getElementById("listeReservations");

let reservations =
JSON.parse(localStorage.getItem("reservations")) || [];

function afficherReservations(){

    listeReservations.innerHTML = "";

    reservations.forEach(function(reservation){

        const li = document.createElement("li");

        li.innerHTML =
        reservation.user + " - " +
        reservation.materiel + " - " +
        reservation.debut + " - " +
        reservation.fin;

        listeReservations.appendChild(li);

    });

}

afficherReservations();

reservationForm.addEventListener("submit", function(e){

    e.preventDefault();

    const reservation = {

        user: document.getElementById("reservationUser").value,

        materiel:
        document.getElementById("reservationMateriel").value,

        debut:
        document.getElementById("reservationDebut").value,

        fin:
        document.getElementById("reservationFin").value

    };

    reservations.push(reservation);

    localStorage.setItem(
        "reservations",
        JSON.stringify(reservations)
    );

    afficherReservations();

    reservationForm.reset();

});