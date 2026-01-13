let myChartInstance = null; 

// --- Fonction 1 : Gère l'upload du fichier JSON ---
document.getElementById("fileInput").addEventListener("change", function(e) {
 const fichierInput = this;
const fichier = e.target.files[0];
 if (!fichier) return;
 // Blocage essentiel contre la double-requête/duplication 
 fichierInput.disabled = true; 
 const formData = new FormData();
 formData.append('fichier_json', fichier); 
 fetch('upload.php', {
 method: 'POST',
 body: formData 
 })
 .then(response => response.json())
 .then(data => {
 if (data.success) {
 console.log(`Fichier ${data.nom_fichier} intégré.`);
 // Mise à jour du tableau après upload
 fetchFichiersEtMettreAJourTableau(); 
 } else {
 alert(`Erreur d'upload : ${data.message}`);
 }
 })
 .catch(error => {
 console.error('Erreur réseau ou du serveur:', error);
 alert("Erreur lors de l'envoi du fichier.");
 })
 .finally(() => {
 // Réactivation
 fichierInput.disabled = false;
 fichierInput.value = ''; 
 });
});


// --- Fonction 2 : Récupère TOUS les fichiers et remplit le tableau ---
function fetchFichiersEtMettreAJourTableau() {
 fetch('fetch_fichiers.php') 
 .then(response => response.json())
 .then(fichiers => {
const tbody = document.querySelector('.table-hover tbody');
 tbody.innerHTML = ''; 
       // Affichage de TOUTES les entrées 
 // ...
fichiers.forEach(fichier => {
const row = tbody.insertRow();

row.insertCell().textContent = fichier.nom_fichier;
row.insertCell().textContent = fichier.date_integration ? fichier.date_integration.substring(0, 10) : 'N/A';

// ===================================================
// 🚨 NOUVEAU BLOC : REND LA DESCRIPTION MODIFIABLE
// ===================================================
const descCell = row.insertCell();
descCell.innerHTML = fichier.description || 'Cliquer pour ajouter...';
descCell.contentEditable = true; // Rendre la cellule modifiable
descCell.classList.add('editable-description'); // Optionnel, pour le style (curseur, etc.)

// Événement 'blur' : se déclenche lorsque l'utilisateur quitte la cellule (sauvegarde)
descCell.addEventListener('blur', function() {
    const nouvelleDescription = this.innerText;
    
    // Vérification simple : si le texte est différent de la valeur initiale stockée
    if (nouvelleDescription.trim() !== (fichier.description || '').trim()) {
        // Sauvegarde dans la base de données via la fonction AJAX
        sauvegarderDescription(fichier.id, nouvelleDescription);
        
        // Mise à jour de l'objet local pour éviter qu'il ne redéclenche la sauvegarde
        fichier.description = nouvelleDescription; 
    }
});
// ===================================================

const dashboardCell = row.insertCell();
// ... la suite des boutons (boutonVoir, boutonSupprimer, etc.)
 // Partie de la Fonction 2 : dans fetchFichiersEtMettreAJourTableau()
// ...

// Bouton "Voir Graphique"
const boutonVoir = document.createElement('button');
boutonVoir.textContent = 'Voir Graphique';
boutonVoir.className = 'btn-voir-graphique'; // ⬅️ UTILISATION DE LA CLASSE CSS
// boutonVoir.style.cssText a été supprimé
boutonVoir.onclick = () => chargerGraphique(fichier.chemin_stockage); 
dashboardCell.appendChild(boutonVoir);
 
// Bouton de suppression
const boutonSupprimer = document.createElement('button');
boutonSupprimer.textContent = 'Supprimer';
boutonSupprimer.className = 'btn-supprimer'; // ⬅️ UTILISATION DE LA CLASSE CSS
// boutonSupprimer.style.cssText a été supprimé
boutonSupprimer.onclick = () => supprimerFichier(fichier.id);
dashboardCell.appendChild(boutonSupprimer);
// ...
 });
})
 .catch(error => console.error("Erreur lors du chargement des fichiers:", error));
}


function chargerGraphique(chemin) {
    console.log("Tentative de chargement du fichier :", chemin);

    fetch(chemin)
    .then(response => {
        if (!response.ok) {
            throw new Error(`Le fichier n'existe pas à l'adresse : ${chemin}`);
        }
        return response.text(); 
    })
    .then(texteBrut => {
        try {
            const data = JSON.parse(texteBrut);
            
            // --- LOGIQUE DYNAMIQUE FILTRÉE ---
            //  gardeZ que les clés qui contiennent des TABLEAUX (Array)
            // Cela permet d'ignorer la clé "description" qui est une simple chaîne de caractères
            const clesDeDonnees = Object.keys(data).filter(cle => Array.isArray(data[cle]));

            if (clesDeDonnees.length < 2) {
                alert("Le JSON doit contenir au moins deux listes de données .");
                return;
            }

            // On prend les deux premières listes trouvées
            const nomX = clesDeDonnees[0];
            const nomY = clesDeDonnees[1];
            const labelsX = data[nomX];
            const valeursY = data[nomY];

            // --- Mise à jour de l'interface ---
            if (myChartInstance) { myChartInstance.destroy(); } 
            document.getElementById("texteGraphique").style.display = 'block';
            document.getElementById("myChart").style.display = 'block';

            let indexMax = valeursY.indexOf(Math.max(...valeursY));
            document.getElementById("texteGraphique").innerHTML = 
                `Analyse de <b>${nomY}</b> : Valeur max pour <b>${labelsX[indexMax]}</b> (${valeursY[indexMax]}).`;

            // --- Création du graphique ---
            let ctx = document.getElementById("myChart").getContext("2d");
            myChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsX,
                    datasets: [{
                        label: nomY,
                        data: valeursY,
                        backgroundColor: 'rgba(42, 129, 138, 0.2)',
                        borderColor: 'rgba(42, 129, 138, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { 
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });

        } catch (err) {
            // Si le JSON est corrompu par une erreur PHP, on le voit ici
            console.error("Erreur de parsing JSON. Contenu reçu du serveur :");
            console.log(texteBrut); 
            alert("Erreur : Le fichier contient des caractères invalides (Erreur PHP possible).");
        }
    })
    .catch(error => {
        alert("Erreur réseau : " + error.message);
    });
}


// --- Fonction 4 : Gère la suppression du fichier ---
function supprimerFichier(idFichier) {
if (!confirm("Êtes-vous sûr de vouloir supprimer définitivement cette ligne et le fichier associé ?")) {
return; 
 }
 const formData = new FormData();
 formData.append('id', idFichier); // Envoie l'ID à supprimer
 fetch('supprimer_fichier.php', { // <-- Appelle le script PHP nécessaire
 method: 'POST',
 body: formData 
 })
 .then(response => response.json())
 .then(data => {
 if (data.success) {
 alert(data.message);
 // Recharger le tableau
 fetchFichiersEtMettreAJourTableau(); 
 } else {
 alert(`Erreur de suppression : ${data.message}`);
 }
 })
 .catch(error => console.error('Erreur réseau ou serveur lors de la suppression:', error));
}

// APPEL INITIAL : Charger le tableau dès que la page est prête
window.onload = fetchFichiersEtMettreAJourTableau;

/**
 * Envoie la nouvelle description au serveur pour la mettre à jour en base de données.
 * @param {number} idFichier L'ID unique du fichier à mettre à jour.
 * @param {string} nouvelleDescription Le nouveau texte de description.
 */
function sauvegarderDescription(idFichier, nouvelleDescription) {
    const descriptionTrimmed = nouvelleDescription.trim();

    fetch('update_description.php', { // Cible le script PHP de sauvegarde
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${idFichier}&description=${encodeURIComponent(descriptionTrimmed)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Description mise à jour avec succès (ID: " + idFichier + ").");
            // Vous pouvez ajouter ici un retour visuel (flash de couleur verte sur la cellule, etc.)
        } else {
            console.error("Échec de la mise à jour:", data.message);
            alert("Erreur lors de la sauvegarde : " + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur réseau lors de la mise à jour:', error);
        alert("Erreur réseau. Impossible de sauvegarder la description.");
    });
}