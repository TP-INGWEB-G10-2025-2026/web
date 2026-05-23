const infoUser =
document.getElementById("infoUser");

const user =
JSON.parse(localStorage.getItem("user"));

if(user){

    infoUser.innerHTML =
    "Connecté : " + user.email;

}