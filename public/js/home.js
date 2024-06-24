document.addEventListener('DOMContentLoaded', function () {
    // deplacer le modal List
    const listModal = document.getElementById("listModal");

    window.addEventListener('click', function (event) {
        if (event.target === listModal) {
            listModal.style.display = "none"; 
        }
    });

    // Script pour la recherche des caisse dans la home page
    const inputRecherche = document.getElementById('recherche');
    const caisseOptions = document.querySelectorAll('.caisse-option');
    const noCaisseFoundMessage = document.getElementById('noCaisseFoundMessage');

    inputRecherche.addEventListener('input', function () {
        const rechercheValue = inputRecherche.value.toLowerCase();
        let caisseFound = false;

        caisseOptions.forEach(function (option) {
            const texteOption = option.textContent.toLowerCase();
            if (texteOption.includes(rechercheValue)) {
                option.style.display = 'block';
                caisseFound = true;
            } else {
                option.style.display = 'none';
            }
        });

        if (!caisseFound) {
            noCaisseFoundMessage.style.display = 'block';
        } else {
            noCaisseFoundMessage.style.display = 'none';
        }
    });

    window.addEventListener('beforeunload', function () {
        resetForm();
    });




    
    function resetForm() {
        const rechercheTraitement = document.getElementById('rechercheTraitement');
        const recherche = document.getElementById('recherche');
        const commentaireCloture = document.getElementById('commentaireCloture');
        const commentaireError = document.getElementById('commentaireError');
        const dateFinErreur = document.getElementById('dateFinErreur');
        const dateFinCloture = document.getElementById('dateFinCloture');
        
        rechercheTraitement.value = '';
        recherche.value = '';
        commentaireCloture.value = '';
        commentaireError.value = '';

        dateFinErreur.value = '';
        dateFinCloture.value = '';
    }



    // Script pour les options de filtre
    const scenarioBtn = document.getElementById('scenarioBtn');
    const caisseExploitBtn = document.getElementById('caisseExploitBtn');
    const poolBtn = document.getElementById('poolBtn');

    const scenarioCheckboxes = document.getElementById('scenarioCheckboxes');
    const caisseExploitCheckboxes = document.getElementById('caisseExploitCheckboxes');
    const poolVmCheckboxes = document.getElementById('poolVmCheckboxes');

    caisseExploitCheckboxes.style.display = 'block';

    scenarioBtn.addEventListener('click', function () {
        scenarioCheckboxes.style.display = (scenarioCheckboxes.style.display === 'block') ? 'block' : 'block';
        caisseExploitCheckboxes.style.display = 'none';
        poolVmCheckboxes.style.display = 'none';
    });

    // Vous pouvez ajouter des événements similaires pour les autres boutons si nécessaire
    caisseExploitBtn.addEventListener('click', function () {
        caisseExploitCheckboxes.style.display = (caisseExploitCheckboxes.style.display === 'block') ? 'block' : 'block';
        scenarioCheckboxes.style.display = 'none';
        poolVmCheckboxes.style.display = 'none';
    });

    poolBtn.addEventListener('click', function () {
        poolVmCheckboxes.style.display = (poolVmCheckboxes.style.display === 'block') ? 'block' : 'block';
        scenarioCheckboxes.style.display = 'none';
        caisseExploitCheckboxes.style.display = 'none';
    });











    
    document.getElementById('arrow').addEventListener('click', function() {
        var container = document.getElementById('caisseScrollContainer');
        var itemHeight = container.scrollHeight / container.children.length;
        container.scrollTop += itemHeight;
    });

    var scrollInterval;

    document.getElementById('arrow').addEventListener('mousedown', function() {
        scrollInterval = setInterval(function() {
            var container = document.getElementById('caisseScrollContainer');
            var itemHeight = container.scrollHeight / container.children.length;
            container.scrollTop += itemHeight;
        }, 100); // Répéter toutes les 100 millisecondes (ajustez selon vos besoins)
    });
    
    document.getElementById('arrow').addEventListener('mouseup', function() {
        clearInterval(scrollInterval);
    });
    
    document.getElementById('arrow').addEventListener('mouseleave', function() {
        clearInterval(scrollInterval);
    });






    // Gestion du code couleur
    const legendColor = document.getElementById("legendColor");
    const modalLegendColor = document.getElementById("modalLegendColor");
    const closeModalLegendColor = document.getElementById("closeModalLegendColor");

    window.addEventListener('click', function (event) {
        if (event.target === modalLegendColor) {
            modalLegendColor.style.display = "none"; 
        }
    });

    legendColor.addEventListener('click', function () {
        modalLegendColor.style.display = "block";
    });

    closeModalLegendColor.addEventListener('click', function () {
        modalLegendColor.style.display = "none";
    });




    // ZOOM DU CALENDRIER
    const zoom = document.getElementById('zoom');
    const options = document.getElementById('options');
    const container = document.querySelector('.col-md-9');

    const getCookie = (name) => {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    };

    const setCookie = (name, value, hours) => {
        const expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + (hours * 60 * 60 * 1000 * 24));
        document.cookie = `${name}=${value};expires=${expirationDate.toUTCString()};path=/`;
    };

    const isZoomed = getCookie('zoomed') === 'true';

    const applyZoomState = (zoomed) => {
        if (zoomed) {
            zoom.setAttribute("title", "Dézoom");
            options.style.display = "none";
            container.style.width = '100%';
            container.style.marginRight = '20px';
            setCookie('zoomed', 'true', 1);
        } else {
            zoom.setAttribute("title", "Zoom");
            options.style.display = "block";
            container.style.width = '';
            container.style.marginRight = '';
            setCookie('zoomed', '', -1);
        }
    };

    applyZoomState(isZoomed);

    zoom.addEventListener("click", () => {
        const currentZoomState = getCookie('zoomed') === 'true';
        applyZoomState(!currentZoomState);
    });

});
