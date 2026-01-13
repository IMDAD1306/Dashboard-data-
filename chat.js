const historiqueChat = document.getElementById('historique-chat');
const inputQuestion = document.getElementById('question');
const boutonEnvoyer = document.getElementById('envoyer');

let messageChargementElement = null; // Variable globale pour suivre le message de chargement

/**
 * Ajoute ou met à jour un message dans l'historique du chat.
 * @param {string} contenu - Le contenu (texte brut ou HTML formaté).
 * @param {string} role - 'user' ou 'ai'.
 * @param {boolean} estMarkdown - Indique si le contenu doit être converti (pour l'IA).
 * @returns {HTMLElement} L'élément div créé.
 */
function ajouterMessage(contenu, role, estMarkdown = false) {
    const divMessage = document.createElement('div');
    divMessage.className = `message ${role}-message`;
    
    if (role === 'ai' && estMarkdown && typeof marked !== 'undefined') {
        // CONVERSION MARKDOWN -> HTML (Utilisation de marked.parse)
        divMessage.innerHTML = marked.parse(contenu); 
    } else {
        // Traitement simple du texte pour les messages utilisateur ou si marked n'est pas dispo
        divMessage.innerHTML = contenu.replace(/\n/g, '<br>'); 
    }
    
    historiqueChat.appendChild(divMessage);
    historiqueChat.scrollTop = historiqueChat.scrollHeight;
    
    return divMessage;
}

// ----------------------------------------------------
// ÉCOUTEURS D'ÉVÉNEMENTS
// ----------------------------------------------------
// ----------------------------------------------------
// ÉCOUTEURS D'ÉVÉNEMENTS
// ----------------------------------------------------
boutonEnvoyer.addEventListener('click', envoyerQuestion);
inputQuestion.addEventListener('keypress', (e) => {
 if (e.key === 'Enter') {
 e.preventDefault(); // Empêche le comportement par défaut (si existant)
 resumerTexte(); // <-- Appel de la fonction Résumer
 }
});
// ----------------------------------------------------
// ----------------------------------------------------


function envoyerQuestion() {
    const question = inputQuestion.value.trim();
    if (!question) return;

    // 1. Désactiver l'interface pendant le chargement
    boutonEnvoyer.disabled = true;
    inputQuestion.disabled = true;
    inputQuestion.value = '';

    // 2. Afficher la question de l'utilisateur
    ajouterMessage(question, 'user');

    // 3. Ajouter un message de chargement pour l'IA et le suivre
    messageChargementElement = ajouterMessage('...', 'ai'); 
    
    // 4. Envoi de la requête
    fetch("../IA/gemini.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: question })
    })
    .then(response => response.json())
    .then(data => {
        
        // =========================================================
        // 🚨 BLOC AJOUTÉ : GESTION DU 429 🚨
        // =========================================================
        if (data.error && data.error.includes("Code HTTP 429")) {
            // Supprimer le message de chargement
            if (messageChargementElement) {
                historiqueChat.removeChild(messageChargementElement);
                messageChargementElement = null; 
            }
            
            // Bloquer l'interface et lancer le compte à rebours (60s)
            bloquerInterface429(); 
            return; // ARRÊT : Ceci empêche le code de réponse normale de s'exécuter
        }
        // =========================================================
        
        let answerText = "Désolé, je n'ai pas pu générer de réponse.";
        
        // Logique de traitement de la réponse (Votre code original)
        if (data.candidates && 
            data.candidates[0] && 
            data.candidates[0].content && 
            data.candidates[0].content.parts && 
            data.candidates[0].content.parts[0] &&
            data.candidates[0].content.parts[0].text) 
        {
            answerText = data.candidates[0].content.parts[0].text;
        } else if (data.error) {
            answerText = `Erreur de communication : ${data.error}`;
        } else if (data.debug_input) {
            answerText = `Erreur : Le serveur n'a pas reçu la question. (Debug: ${JSON.stringify(data)})`;
        }
        
        // 5. Mettre à jour le message de chargement avec la réponse finale
        if (messageChargementElement) {
            // Suppression du message de chargement
            historiqueChat.removeChild(messageChargementElement);
            messageChargementElement = null; // Réinitialisation de la variable
        }

        // 6. Afficher la réponse de l'IA avec conversion Markdown
        // Nous utilisons 'true' pour indiquer que c'est du Markdown
        ajouterMessage(answerText, 'ai', true); 
    })
    .catch(err => {
        console.error(err);
        
        // Gérer l'erreur sur le message de chargement
        if (messageChargementElement) {
             historiqueChat.removeChild(messageChargementElement);
             messageChargementElement = null;
        }

        ajouterMessage("Erreur réseau lors de la communication avec le serveur.", 'ai');
    })
    .finally(() => {
        // 7. Réactiver l'interface (ce bloc s'exécute uniquement si le 'return;' du 429 n'a pas été appelé)
        boutonEnvoyer.disabled = false;
        inputQuestion.disabled = false;
        inputQuestion.focus();
    });
}
// ====================================================
// FONCTION MANQUANTE : GÉRER LE RÉSUMÉ (resumerTexte)
// ====================================================
// Définir la limite maximale en mots (à placer en haut de chat.js)
const LIMITE_MOTS_RESUME_MAX = 2000; 

function resumerTexte() {
    // Récupérer le texte de l'utilisateur
    const texteUtilisateur = inputQuestion.value.trim(); 
    
    // Vérification de base (Le texte n'est pas vide)
    if (!texteUtilisateur) {
        ajouterMessage("Veuillez saisir le texte que vous souhaitez résumer.", 'ai');
        return;
    }

    // ----------------------------------------------------
    // 💡 1. LOGIQUE DE PRÉVENTION (LIMITE DE MOTS)
    // ----------------------------------------------------
    const mots = texteUtilisateur.split(/\s+/).filter(word => word.length > 0);
    
    if (mots.length > LIMITE_MOTS_RESUME_MAX) {
        const messageErreur = `
            ⚠️ **Texte trop long.** Votre texte (${mots.length} mots) 
            dépasse la limite autorisée de ${LIMITE_MOTS_RESUME_MAX} mots. 
            Veuillez le raccourcir.
        `;
        ajouterMessage(messageErreur, 'ai', true); 
        
        boutonEnvoyer.disabled = false;
        inputQuestion.disabled = false;
        inputQuestion.focus();
        
        return; 
    }
    // ----------------------------------------------------

    // Le PROMPT SECRET
    const promptSecret = "Résume de manière concise le texte suivant en français, en ne conservant que les points essentiels : \n\n--- TEXTE À RÉSUMER ---\n";
    const messageFinal = promptSecret + texteUtilisateur;
    
    // 1. Désactiver l'interface
    boutonEnvoyer.disabled = true; 
    inputQuestion.disabled = true;
    
    // =================================================================
    // 💥 NOUVELLE LOGIQUE POUR L'AFFICHAGE DU MESSAGE UTILISATEUR 💥
    // =================================================================
    
    // A. Préparer le contenu avec l'aperçu et le bouton "Voir tout"
    const contenuApercu = "Demande de résumé : " + texteUtilisateur;
    // On utilise la fonction utilitaire créée précédemment
    const messageHTML = creerMessageLong(contenuApercu, 150); 
    
    // B. Créer l'élément DIV du message utilisateur manuellement (pour pouvoir injecter du HTML)
    const divMessageUtilisateur = document.createElement('div');
    divMessageUtilisateur.classList.add('message', 'user-message');
    divMessageUtilisateur.innerHTML = messageHTML; // Injection du HTML avec aperçu/bouton
    historiqueChat.appendChild(divMessageUtilisateur); 
    
    // Nettoyer la zone de saisie
    inputQuestion.value = '';

    // 3. Message de chargement
    // (Utilisation de la variable globale messageChargementElement comme dans votre code original)
    messageChargementElement = ajouterMessage('...', 'ai'); 
    
    // Scroll pour voir le nouveau message
    historiqueChat.scrollTop = historiqueChat.scrollHeight;
    
    // =================================================================

    // 4. Envoi de la requête à l'IA avec le prompt secret
    fetch("../IA/gemini.php", {
        method: "POST",
        headers: { 'Content-Type': "application/json" },
        body: JSON.stringify({ prompt: messageFinal })
    })
    .then(response => response.json())
    .then(data => {
        
        // ----------------------------------------------------
        // 💡 2. LOGIQUE DE RÉACTION (GESTION DU 429)
        // ----------------------------------------------------
        if (data.error && data.error.includes("Code HTTP 429")) {
            // Supprimer le message de chargement
            if (messageChargementElement) {
                historiqueChat.removeChild(messageChargementElement);
                messageChargementElement = null; 
            }
            
            // Bloquer l'interface et lancer le compte à rebours
            bloquerInterface429(); 
            return; 
        }
        // ----------------------------------------------------
        
        let answerText = "Désolé, je n'ai pas pu générer de réponse de résumé.";
        
        // Logique de traitement de la réponse
        if (data.candidates && 
            data.candidates[0] && 
            data.candidates[0].content && 
            data.candidates[0].content.parts && 
            data.candidates[0].content.parts[0] &&
            data.candidates[0].content.parts[0].text) 
        {
             answerText = data.candidates[0].content.parts[0].text;
        } else if (data.error) {
             answerText = `Erreur de communication : ${data.error}`;
        }
        
        // 5. Mettre à jour le message de chargement (supprimer l'ancien)
        if (messageChargementElement) {
            historiqueChat.removeChild(messageChargementElement);
            messageChargementElement = null; 
        }

        // 6. Afficher la réponse
        ajouterMessage(answerText, 'ai', true); 
    })
    .catch(err => {
        console.error(err);
        
        if (messageChargementElement) {
            historiqueChat.removeChild(messageChargementElement);
            messageChargementElement = null;
        }
        ajouterMessage("Erreur réseau lors de la communication pour le résumé.", 'ai');
    })
    .finally(() => {
        // 7. Réactiver l'interface
        boutonEnvoyer.disabled = false;
        inputQuestion.disabled = false;
        inputQuestion.focus();
    });
}

function classifierTexte() {
    // 1. Récupérer le texte de l'utilisateur
    const texteUtilisateur = inputQuestion.value.trim(); 
    
    // Vérification de base
    if (!texteUtilisateur) {
        ajouterMessage("Veuillez saisir le texte que vous souhaitez classifier.", 'ai');
        return;
    }

    // 2. Logique de prévention (Limite de mots - on garde la même que le résumé)
    const mots = texteUtilisateur.split(/\s+/).filter(word => word.length > 0);
    if (mots.length > LIMITE_MOTS_RESUME_MAX) {
        const messageErreur = `⚠️ **Texte trop long.** Votre texte dépasse la limite autorisée.`;
        ajouterMessage(messageErreur, 'ai', true); 
        return; 
    }

    // 3. Le PROMPT SECRET de CLASSIFICATION
    const promptSecret = "Agis comme un expert en analyse de données. Classifie et organise de manière structurée le texte suivant en français : \n\n--- TEXTE À CLASSIFIER ---\n";
    const messageFinal = promptSecret + texteUtilisateur;
    
    // 4. Désactiver l'interface
    boutonEnvoyer.disabled = true; 
    inputQuestion.disabled = true;
    
    // 5. Affichage du message utilisateur avec aperçu
    const contenuApercu = "Demande de classification : " + texteUtilisateur;
    const messageHTML = creerMessageLong(contenuApercu, 150); 
    
    const divMessageUtilisateur = document.createElement('div');
    divMessageUtilisateur.classList.add('message', 'user-message');
    divMessageUtilisateur.innerHTML = messageHTML; 
    historiqueChat.appendChild(divMessageUtilisateur); 
    
    // Nettoyer la zone de saisie
    inputQuestion.value = '';

    // 6. Message de chargement
    messageChargementElement = ajouterMessage('...', 'ai'); 
    historiqueChat.scrollTop = historiqueChat.scrollHeight;
    
    // 7. Envoi de la requête à l'IA
    fetch("../IA/gemini.php", {
        method: "POST",
        headers: { 'Content-Type': "application/json" },
        body: JSON.stringify({ prompt: messageFinal })
    })
    .then(response => response.json())
    .then(data => {
        // Gestion de l'erreur 429 (Trop de requêtes)
        if (data.error && data.error.includes("Code HTTP 429")) {
            if (messageChargementElement) {
                historiqueChat.removeChild(messageChargementElement);
                messageChargementElement = null; 
            }
            bloquerInterface429(); 
            return; 
        }
        
        // Extraction de la réponse
        let answerText = "Désolé, je n'ai pas pu générer de classification.";
        if (data.candidates && data.candidates[0]?.content?.parts?.[0]?.text) {
             // On ajoute un petit titre en gras pour la réponse
             answerText = "**CLASSIFICATION :**\n\n" + data.candidates[0].content.parts[0].text;
        }
        
        // Supprimer message chargement
        if (messageChargementElement) {
            historiqueChat.removeChild(messageChargementElement);
            messageChargementElement = null; 
        }

        // Afficher la réponse
        ajouterMessage(answerText, 'ai', true); 
    })
    .catch(err => {
        console.error(err);
        if (messageChargementElement) {
            historiqueChat.removeChild(messageChargementElement);
            messageChargementElement = null;
        }
        ajouterMessage("Erreur réseau lors de la communication pour la classification.", 'ai');
    })
    .finally(() => {
        // 8. Réactiver l'interface
        boutonEnvoyer.disabled = false;
        inputQuestion.disabled = false;
        inputQuestion.focus();
    });
}
/**
 * Crée le HTML pour un message long avec un bouton "Voir plus".
 * @param {string} texteComplet - Le texte intégral à afficher/masquer.
 * @param {number} limite - Le nombre de caractères à afficher en aperçu.
 * @returns {string} Le code HTML à injecter.
 */
function creerMessageLong(texteComplet, limite = 150) {
    // Si le texte n'est pas long, on le renvoie tel quel.
    if (texteComplet.length <= limite) {
        return texteComplet.replace(/\n/g, '<br>');
    }

    const apercu = texteComplet.substring(0, limite);
    
    // Le contenu visible (jusqu'à la limite) et le bouton de bascule
    let html = `
        <div class="message-apercu">${apercu}...</div>
        <div class="message-cache" style="display:none;">${texteComplet.substring(limite).replace(/\n/g, '<br>')}</div>
        <button class="voir-plus-btn" onclick="toggleMessage(this)">
            <span class="texte-btn">Voir tout</span> 
            <span class="icone-fleche">▼</span>
        </button>
    `;
    return html;
}

/**
 * Fonction appelée par le bouton pour afficher/masquer le contenu.
 * @param {HTMLElement} bouton - Le bouton cliqué.
 */
function toggleMessage(bouton) {
    const parent = bouton.closest('.message'); // Remonte à l'élément message parent
    const apercu = parent.querySelector('.message-apercu');
    const cache = parent.querySelector('.message-cache');
    const texteBtn = bouton.querySelector('.texte-btn');
    const iconeFleche = bouton.querySelector('.icone-fleche');
    
    if (cache.style.display === 'none') {
        // Afficher le texte caché
        apercu.style.display = 'none';
        cache.style.display = 'block';
        texteBtn.textContent = 'Voir moins';
        iconeFleche.textContent = '▲';
    } else {
        // Masquer le texte et revenir à l'aperçu
        apercu.style.display = 'block';
        cache.style.display = 'none';
        texteBtn.textContent = 'Voir tout';
        iconeFleche.textContent = '▼';
    }
}

/**
 * Désactive l'interface et affiche un compte à rebours de 60 secondes après une erreur 429.
 * L'interface se réactive automatiquement à la fin du compte à rebours.
 */
/**
 * Désactive l'interface et affiche un compte à rebours de 60 secondes après une erreur 429.
 * L'interface se réactive automatiquement à la fin du compte à rebours.
 */
function bloquerInterface429() {
    const inputField = document.getElementById('question');
    const sendButton = document.getElementById('envoyer');
    
    const DELAI_ATTENTE = 60;
    let tempsRestant = DELAI_ATTENTE;
    let intervalId = null;

    // 1. Désactiver l'interface immédiatement
    if (inputField) inputField.disabled = true;
    if (sendButton) sendButton.disabled = true;

    // 2. Afficher le message initial dans le chat
    let messageElement = ajouterMessage(`
        ⚠️ **Erreur 429 : Limite de débit atteinte.** Le service de Google est temporairement bloqué. 
        **Réactivation dans : ${tempsRestant} secondes.**
    `, 'ai', true);

    // 3. Démarrer le compte à rebours
    intervalId = setInterval(() => {
        tempsRestant--; // Décrémenter le temps restant

        // Mise à jour de l'affichage (modification du message existant)
        if (messageElement) {
             // Utilisation de marked.parse pour mettre à jour la DIV complète avec la nouvelle valeur
             const nouveauContenu = `
                 ⚠️ **Erreur 429 : Limite de débit atteinte.** Le service de Google est temporairement bloqué. <br>
                 **Réactivation dans : ${tempsRestant} secondes.**
             `;
             // C'est la ligne corrigée qui assure la mise à jour du contenu Markdown
             messageElement.innerHTML = marked.parse(nouveauContenu); 
        }

        // 4. Fin du compte à rebours
        if (tempsRestant <= 0) {
            clearInterval(intervalId);
            
            // 5. Réactiver l'interface
            if (inputField) inputField.disabled = false;
            if (sendButton) sendButton.disabled = false;
            
            // 6. Confirmer le déblocage
            ajouterMessage("✅ **Interface débloquée.** La limite de débit est réinitialisée. Vous pouvez réessayer votre requête.", 'ai', true);
            if (inputField) inputField.focus();
        }
    }, 1000);
}