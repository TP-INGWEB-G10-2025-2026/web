if(localStorage.getItem("connecte") !== "oui"){

    window.location.href = "login.html";

}

function logout(){

    const confirmation =
    confirm("Voulez-vous vous déconnecter ?");

    if(confirmation){

        localStorage.removeItem("connecte");

        localStorage.removeItem("user");

        window.location.href = "login.html";

    }

}