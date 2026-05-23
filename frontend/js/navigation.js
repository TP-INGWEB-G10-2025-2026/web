if(localStorage.getItem("connecte") !== "oui"){
    window.location.href = "login.html";
}

function logout(){

    localStorage.removeItem("connecte");

    window.location.href = "login.html";

}