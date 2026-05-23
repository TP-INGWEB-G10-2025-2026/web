const categorieForm =
document.getElementById("categorieForm");

const listeCategories =
document.getElementById("listeCategories");

let categories =
JSON.parse(localStorage.getItem("categories")) || [];

function afficherCategories(){

    listeCategories.innerHTML = "";

    categories.forEach(function(categorie,index){

        const li = document.createElement("li");

        li.innerHTML = categorie.nom;

        const supprimer =
        document.createElement("button");

        supprimer.innerHTML = "Supprimer";

        supprimer.className = "action-btn";

        supprimer.onclick = function(){

            categories.splice(index,1);

            localStorage.setItem(
                "categories",
                JSON.stringify(categories)
            );

            afficherCategories();

        };

        li.appendChild(supprimer);

        listeCategories.appendChild(li);

    });

}

afficherCategories();

categorieForm.addEventListener("submit",function(e){

    e.preventDefault();

    const categorie = {

        nom: document.getElementById("categorieNom").value

    };

    categories.push(categorie);

    localStorage.setItem(
        "categories",
        JSON.stringify(categories)
    );

    afficherCategories();

    categorieForm.reset();

});