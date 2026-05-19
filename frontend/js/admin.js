// ===============================
// VERIFICATION ADMIN CONNECTE
// ===============================

const user = JSON.parse(localStorage.getItem("user"));

if (!user || user.role !== "admin") {
    window.location.href = "login.html";
}

// ===============================
// RECUPERATION DES ENSEIGNANTS
// ===============================

let teachers = JSON.parse(localStorage.getItem("teachers")) || [];

// ===============================
// SAUVEGARDE LOCALSTORAGE
// ===============================

function saveData() {
    localStorage.setItem("teachers", JSON.stringify(teachers));
}

// ===============================
// AJOUT ENSEIGNANT
// ===============================

function addTeacher() {

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("teacherEmail").value.trim();

    let password = prompt(
        "Entrer le mot de passe de l'enseignant"
    );

    if (!name || !email || !password) {
        alert("Veuillez remplir tous les champs");
        return;
    }

    // verifier email existant
    let teacherExists = teachers.find(
        t => t.email === email
    );

    if (teacherExists) {
        alert("Cet email existe déjà");
        return;
    }

    // ajout
    teachers.push({
        name: name,
        email: email,
        password: password,
        blocked: false
    });

    saveData();

    displayTeachers();

    // vider champs
    document.getElementById("name").value = "";
    document.getElementById("teacherEmail").value = "";

    alert("Enseignant ajouté avec succès");
}

// ===============================
// AFFICHAGE DES ENSEIGNANTS
// ===============================

function displayTeachers() {

    let content = "";

    if (teachers.length === 0) {

        content = `
        <tr>
            <td colspan="4">
                Aucun enseignant trouvé
            </td>
        </tr>
        `;
    }

    teachers.forEach((teacher, index) => {

        content += `
        <tr>

            <td>${teacher.name}</td>

            <td>${teacher.email}</td>

            <td>
                ${teacher.blocked ? "🔴 Bloqué" : "🟢 Actif"}
            </td>

            <td>

                <button class="action-btn"
                onclick="editTeacher(${index})">
                    Modifier
                </button>

                <button class="action-btn"
                onclick="toggleBlock(${index})">
                    ${teacher.blocked
                        ? "Débloquer"
                        : "Bloquer"}
                </button>

                <button class="action-btn"
                onclick="deleteTeacher(${index})">
                    Supprimer
                </button>

            </td>

        </tr>
        `;
    });

    document.getElementById(
        "teacherList"
    ).innerHTML = content;
}

// ===============================
// MODIFIER ENSEIGNANT
// ===============================

function editTeacher(index) {

    // récupérer enseignant
    let teacher = teachers[index];

    // informations
    let info = `
========== INFORMATIONS ==========
Nom : ${teacher.name}

Email : ${teacher.email}

Mot de passe : ${teacher.password}

Statut : ${teacher.blocked ? "Bloqué" : "Actif"}
==================================
`;

    // afficher infos
    alert(info);

    // confirmation
    let choice = confirm(
        "Voulez-vous modifier cet enseignant ?"
    );

    if (!choice) {
        return;
    }

    // =========================
    // NOM
    // =========================

    let newName = prompt(
        "Modifier le nom :",
        teacher.name
    );

    if (newName === null) {
        return;
    }

    newName = newName.trim();

    if (newName === "") {
        alert("Le nom est obligatoire");
        return;
    }

    // =========================
    // EMAIL
    // =========================

    let newEmail = prompt(
        "Modifier l'email :",
        teacher.email
    );

    if (newEmail === null) {
        return;
    }

    newEmail = newEmail.trim();

    if (newEmail === "") {
        alert("L'email est obligatoire");
        return;
    }

    // verifier email existant
    let emailExists = teachers.find((t, i) => {

        return (
            t.email === newEmail &&
            i !== index
        );

    });

    if (emailExists) {
        alert("Cet email est déjà utilisé");
        return;
    }

    // =========================
    // MOT DE PASSE
    // =========================

    let newPassword = prompt(
        "Modifier le mot de passe :",
        teacher.password
    );

    if (newPassword === null) {
        return;
    }

    newPassword = newPassword.trim();

    if (newPassword === "") {
        alert(
            "Le mot de passe est obligatoire"
        );
        return;
    }

    // confirmation finale
    let confirmSave = confirm(
        "Confirmer les modifications ?"
    );

    if (!confirmSave) {
        return;
    }

    // sauvegarde
    teacher.name = newName;
    teacher.email = newEmail;
    teacher.password = newPassword;

    saveData();

    displayTeachers();

    alert(
        "Informations modifiées avec succès"
    );
}

// ===============================
// BLOQUER / DEBLOQUER
// ===============================

function toggleBlock(index) {

    teachers[index].blocked =
        !teachers[index].blocked;

    saveData();

    displayTeachers();

    if (teachers[index].blocked) {
        alert("Enseignant bloqué");
    } else {
        alert("Enseignant débloqué");
    }
}

// ===============================
// SUPPRIMER ENSEIGNANT
// ===============================

function deleteTeacher(index) {

    let confirmDelete = confirm(
        "Voulez-vous supprimer cet enseignant ?"
    );

    if (!confirmDelete) {
        return;
    }

    teachers.splice(index, 1);

    saveData();

    displayTeachers();

    alert("Enseignant supprimé");
}

// ===============================
// DECONNEXION
// ===============================

function logout() {

    localStorage.removeItem("user");

    window.location.replace("login.html");
}

// ===============================
// INITIALISATION
// ===============================

displayTeachers();