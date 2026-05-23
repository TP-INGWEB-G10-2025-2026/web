const listeEtats = document.getElementById("listeEtats");

const prets = JSON.parse(localStorage.getItem("prets"));

prets.forEach(function(pret){

    listeEtats.innerHTML += `
        <tr>
            <td>${pret.materiel}</td>
            <td>${pret.etat}</td>
        </tr>
    `;

});