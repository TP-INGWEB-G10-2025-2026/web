const loginForm =
document.getElementById("loginForm");

loginForm.addEventListener("submit",function(e){

    e.preventDefault();

    const email =
    document.getElementById("email").value;

    const password =
    document.getElementById("password").value;

    if(
        email === "admin@gmail.com" &&
        password === "admin123"
    ){

        localStorage.setItem(
            "connecte",
            "oui"
        );

        localStorage.setItem(
            "user",
            JSON.stringify({
                email: email,
                role: "admin"
            })
        );

        window.location.href =
        "dashboard.html";

    }else{

        alert("Email ou mot de passe incorrect");

    }

});