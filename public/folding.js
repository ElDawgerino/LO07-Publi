var Folding = {
    toggleFolder: function(titleObject){
        var folded = titleObject.dataset.folded; //La liste était-elle repliée ?

        //Trouver la div group-publication-list contenant la liste des publications d'un groupe
        var groupPublicationsList = titleObject.parentNode.getElementsByClassName("group-publication-list")[0];
        var arrowSpan = titleObject.getElementsByClassName("arrow")[0];

        if(!folded && folded !== "false"){
            folded = "false";

            //On stocke la taille à la première utilisation
            titleObject.dataset.originalHeight = groupPublicationsList.clientHeight;
            groupPublicationsList.style.height = titleObject.dataset.originalHeight; //Et on l'affecte pour la future transition
        }

        groupPublicationsList.style.height = (folded === "true" ? titleObject.dataset.originalHeight : "0");
        arrowSpan.innerHTML = (folded === "true" ? "⏷" : "⏵");

        titleObject.dataset.folded = (folded === "true" ? "false" : "true");
    }
}
