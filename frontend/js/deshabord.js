const utilisateur =
JSON.parse(localStorage.getItem("user"));

if(!utilisateur){

    window.location.href = "login.html";

}

function ouvrirPage(page){

    window.location.href = page;

}