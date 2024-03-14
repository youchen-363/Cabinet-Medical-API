        // L'URL de base de l'API
        const baseUrl1 = 'https://chuckapi.alwaysdata.net';
        const baseUrl = 'http://localhost/S4/R401_API';
        const resource = '/ChuckAPI/v1/index.php';
        const urlAuth = '/authapi/authapi.php';

        // Méthode pour effectuer un appel API GET pour récupérer toutes les phrases
        function getAllPhrases() {
            fetch(baseUrl+resource)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    displayInfoResponse(document.getElementById('infoGetAllPhrases'),data);
                    displayData(data.data);
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code de récupération de toutes les phrases avec la méthode GET*/

            /**Lors du TP1 vous avez pu analyser la structure des données retournées (Voir Doc ChuckAPI).
             * => Les éléments de réponse (status, status_code, status_message) sont des informations de la réponse HTTP.
             * => Les données sont dans un tableau d'objets (id, phrase, vote, date_ajout, date_modif, faute, signalement).
             * 
             * Les éléments de réponses doivent être affichés dans la zone en dessous du bouton.
             * Exemple :
             * document.getElementById('infoGetAllPhrases') : permet d'obtenir un objet représentant la balise <div> avec l'id 'infoGetAllPhrases'
             * infoReponse représente les informations renvoyées par la requête d'API
             *      displayInfoResponse(document.getElementById('infoGetAllPhrases'),infoReponse);
             * 
             * Les données doivent être affichées dans la zone en bas de page.
             * Exemple :
             * tabData représente les données retournées par la requête d'API
             *      displayData(tabdata);
             * 
             * A vous d'implémenter le code pour récupérer les données et les afficher en vous appuyant sur les exemples ci-dessus,
             * la documentation de l'API et la document sur Fetch API.
             * A vous de jouer !
             */

            // Affichage d'un message dans une boîte de dialogue pour l'exemple
            alert('J\'affiche les informations de la réponse HTTP dans la zone en dessous du bouton \n et toutes les phrases dans la zone en bas de page');
        }

        // Méthode pour effectuer un appel API GET pour récupérer une seule phrase
        function getPhrase() {
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code de récupération d'une phrase avec la méthode GET*/
            // Récupérer la valeur d'une balise <input> identifiée avec l'id 'phraseID' : <input type="text" id="phraseID">
            var valeurDeLaBalise = document.getElementById('phraseID').value;
            
            // fetch(baseUrl+resource+'/'+valeurDeLaBalise)
            fetch(baseUrl+resource+'?id='+valeurDeLaBalise)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    displayInfoResponse(document.getElementById('infoGetAllPhrases'),data);
                    displayData(data.data);
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            //Construire le message à afficher
            var message = 'Le contenu de la balise est : ' + valeurDeLaBalise;
            // Afficher un message dans une boîte de dialogue pour l'exemple
            alert(message);
        }
        
        // Méthode pour créer une nouvelle phrase
        function addPhrase() {
            var valeurDeLaBalise = document.getElementById('newPhrase').value;
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code d'envoi d'une phrase avec la méthode POST*/
            const phraseData = {
                phrase: valeurDeLaBalise,
                auteur: "Dav"
               };
              
            const requestOptions = {
                method: 'POST', // Méthode HTTP
                headers: { 'Content-Type': 'application/json' }, // Type de contenu
                body: JSON.stringify(phraseData) // Corps de la requête
            };
            console.log(requestOptions);
            fetch(baseUrl+resource, requestOptions)
                .then(response => response.json()) // Convertir la réponse en JSON
                .then(data => {
                    displayInfoResponse(document.getElementById('infoGetAllPhrases'),data);
                    displayData(data.data);
                    console.log(data); //Afficher en console les données récupérées
                })
                .catch(error => console.error('Erreur Fetch:', error)); // Gérer les erreurs

            // Afficher un message dans une boîte de dialogue pour l'exemple
            alert('J\envoie une phrase pour être créée dans la base de données');
        }
        
        // Méthode pour mettre à jour une phrase
        function updatePhrase() {
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code de mise à jour d'une phrase avec la méthode PATCH puis PUT*/
            let majID = document.getElementById('updatePhraseID').value;
            let content = document.getElementById('updateContent').value;
            let vote = document.getElementById('updateVote').value; 
            let faute = document.getElementById('updateFaute').checked;
            let signalement = document.getElementById('updateSignalement').checked;
            let methodMAJ = document.querySelector('input[name="updateMethod"]:checked').value; // Sélectionne la méthode choisie
            const phraseData = {
                phrase: content,
                vote: vote,
                faute: faute,
                signalement: signalement
            };
            console.log(phraseData);
            const requestOptions = {
                method: methodMAJ, // Méthode HTTP
                headers: { 'Content-Type': 'application/json' }, // Type de contenu
                body: JSON.stringify(phraseData) // Corps de la requête
            };
            // fetch(baseUrl+resource+"/"+majID, requestOptions)
            fetch(baseUrl+resource+"?id="+majID, requestOptions)
            .then(response => response.json()) // Convertir la réponse en JSON
            .then(data => {
                displayInfoResponse(document.getElementById('infoGetAllPhrases'),data);
                displayData(data.data);
                console.log(data); //Afficher en console les données récupérées
            })
            .catch(error => console.error('Erreur Fetch:', error)); // Gérer les erreurs
        
            // Afficher un message dans une boîte de dialogue pour l'exemple
            alert('Je mets à jour une phrase avec PATCH puis PUT');
        }

        // Méthode pour supprimer une phrase
        function deletePhrase() {
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code de suppression d'une phrase avec la méthode DELETE*/
            var valeurDeLaBalise = document.getElementById('deletePhraseID').value;
            /*TODO : Remplacer/Adapter le code ci-dessous par votre code d'envoi d'une phrase avec la méthode POST*/
            const phraseData = {
                phrase: valeurDeLaBalise,
                auteur: "Dav"
            };
            console.log(phraseData);
            const requestOptions = {
                method: 'DELETE', // Méthode HTTP
                headers: { 'Content-Type': 'application/json' }, // Type de contenu
                body: JSON.stringify(phraseData) // Corps de la requête
            };
            fetch(baseUrl+resource+"?id="+valeurDeLaBalise, requestOptions)
            // fetch(baseUrl+resource+"/"+valeurDeLaBalise, requestOptions)
                .then(response => response.json()) // Convertir la réponse en JSON
                .then(data => {
                    displayInfoResponse(document.getElementById('infoGetAllPhrases'),data);
                    console.log(data); //Afficher en console les données récupérées
                })
                .catch(error => console.error('Erreur Fetch:', error)); // Gérer les erreurs
            // Afficher un message dans une boîte de dialogue pour l'exemple
            alert('Je supprime une phrase avec DELETE');
        }

        // Méthode pour afficher les données dans le tableau HTML
        function displayData(phrases) {
            const tableBody = document.getElementById('responseTableBody');
            tableBody.innerHTML = ''; // nettoie le tableau avant de le remplir
            const apiResponse = document.getElementById('apiResponse');
            apiResponse.style.display = phrases.length > 0 ? 'block' : 'none';

            phrases.forEach(phrase => {
                const row = tableBody.insertRow();
                row.insertCell(0).textContent = phrase.id;
                row.insertCell(1).textContent = phrase.phrase;
                row.insertCell(2).textContent = phrase.date_ajout;
                row.insertCell(3).textContent = phrase.date_modif;
                row.insertCell(4).textContent = phrase.vote;
                row.insertCell(5).textContent = phrase.faute;
                row.insertCell(6).textContent = phrase.signalement;
            });
        }

        // Mise à jour de la fonction pour afficher les informations de réponse
        function displayInfoResponse(baliseInfo,info) {
            if(info) {
                baliseInfo.textContent = `Statut: ${info.status}, Code: ${info.status_code}, Message: ${info.status_message}`;
                baliseInfo.style.display = 'block';
            } else {
                baliseInfo.style.display = 'none';
            }
        }
        
        // Attacher les événements aux boutons
        document.getElementById('getAllPhrases').addEventListener('click', getAllPhrases);
        document.getElementById('getPhrase').addEventListener('click', getPhrase);
        document.getElementById('addPhrase').addEventListener('click', addPhrase);
        document.getElementById('deletePhrase').addEventListener('click', deletePhrase);
        document.getElementById('updatePhrase').addEventListener('click', updatePhrase);
        