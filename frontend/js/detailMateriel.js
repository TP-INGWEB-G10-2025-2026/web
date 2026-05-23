const detailMateriel =
document.getElementById("detailMateriel");

const materiels =
JSON.parse(localStorage.getItem("materiels")) || [];

if(materiels.length > 0){

    const materiel = materiels[0];

    detailMateriel.innerHTML =
    "Nom : " + materiel.nom + "<br>" +
    "Catégorie : " + materiel.categorie + "<br>" +
    "État : " + materiel.etat;

}else{

    detailMateriel.innerHTML =
    "Aucun matériel disponible";

}