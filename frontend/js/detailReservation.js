const detailReservation =
document.getElementById("detailReservation");

const reservations =
JSON.parse(localStorage.getItem("reservations")) || [];

if(reservations.length > 0){

    const reservation = reservations[0];

    detailReservation.innerHTML =
    "Utilisateur : " + reservation.user + "<br>" +
    "Matériel : " + reservation.materiel + "<br>" +
    "Date début : " + reservation.debut + "<br>" +
    "Date fin : " + reservation.fin;

}else{

    detailReservation.innerHTML =
    "Aucune réservation disponible";

}