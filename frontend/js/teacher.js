// teacher.js

// récupérer utilisateur connecté
const user = JSON.parse(localStorage.getItem("user"));

// vérifier connexion
if (!user || user.role !== "teacher") {
    window.location.href = "login.html";
}

// récupérer enseignants
let teachers = JSON.parse(localStorage.getItem("teachers")) || [];

// retrouver enseignant connecté
let currentTeacher = teachers.find(t =>
    t.email.trim().toLowerCase() ===
    user.email.trim().toLowerCase()
);

// enseignant introuvable
if (!currentTeacher) {
    alert("Enseignant introuvable");
    window.location.href = "login.html";
}

// afficher informations
document.getElementById("teacherName").value =
currentTeacher.name;

document.getElementById("teacherEmail").value =
currentTeacher.email;

// sauvegarder données
function saveTeachers() {

    localStorage.setItem(
        "teachers",
        JSON.stringify(teachers)
    );
}

// afficher / masquer mot de passe
function togglePassword() {

    let oldPass =
    document.getElementById("oldPassword");

    let newPass =
    document.getElementById("teacherPassword");

    let confirmPass =
    document.getElementById("confirmPassword");

    if (oldPass.type === "password") {

        oldPass.type = "text";
        newPass.type = "text";
        confirmPass.type = "text";

    } else {

        oldPass.type = "password";
        newPass.type = "password";
        confirmPass.type = "password";
    }
}

// modifier profil
function saveProfile() {

    let name =
    document.getElementById("teacherName")
    .value.trim();

    let email =
    document.getElementById("teacherEmail")
    .value.trim();

    let oldPassword =
    document.getElementById("oldPassword")
    .value.trim();

    let newPassword =
    document.getElementById("teacherPassword")
    .value.trim();

    let confirmPassword =
    document.getElementById("confirmPassword")
    .value.trim();

    // vérification champs
    if (!name || !email) {
        alert("Remplir tous les champs");
        return;
    }

    // vérifier email déjà utilisé
    let emailExist = teachers.find(t =>
        t.email.toLowerCase() ===
        email.toLowerCase()
        && t !== currentTeacher
    );

    if (emailExist) {
        alert("Email déjà utilisé");
        return;
    }

    // changement mot de passe
    if (newPassword !== "") {

        // vérifier ancien mot de passe
        if (oldPassword !== currentTeacher.password) {
            alert("Ancien mot de passe incorrect");
            return;
        }

        // confirmation
        if (newPassword !== confirmPassword) {
            alert("Confirmation incorrecte");
            return;
        }

        // modifier mot de passe
        currentTeacher.password = newPassword;
    }

    // modifier informations
    currentTeacher.name = name;
    currentTeacher.email = email;

    // sauvegarder
    saveTeachers();


    // mettre à jour session
    localStorage.setItem("user", JSON.stringify({
        role: "teacher",
        email: email
    }));

    alert("Informations modifiées avec succès");
}

// déconnexion
function logout() {

    localStorage.removeItem("user");

    window.location.href = "login.html";
}



