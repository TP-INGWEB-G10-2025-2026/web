// compte admin fixe
const ADMIN = {
    email: "admin@gmail.com",
    password: "admin123"
};

let teachers = JSON.parse(localStorage.getItem("teachers")) || [];

function login() {

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value;

    if (!email || !password) {
        alert("Remplir tous les champs");
        return;
    }

    if (role === "admin") {

        if (email === ADMIN.email && password === ADMIN.password) {

            localStorage.setItem("user", JSON.stringify({
                role: "admin",
                email: email
            }));

            window.location.href = "admin.html";
        } 
        else {
            alert("Email ou mot de passe admin incorrect");
        }

        return;
    }

    if (role === "teacher") {

        const teacher = teachers.find(t =>
    t.email === email &&
    t.password === password
        );

        if (teacher) {

            localStorage.setItem("user", JSON.stringify({
                role: "teacher",
                email: email
            }));

            window.location.href = "teacher.html";

        } else {
            alert("Enseignant introuvable");
        }
    }
}
