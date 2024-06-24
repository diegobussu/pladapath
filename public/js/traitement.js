document.addEventListener("DOMContentLoaded", function() {
    // deplacer le modal Create
    const modal = document.getElementById("myModal");
    const saveButton = document.getElementById("saveButton");
    
    let isDragging = false;
    let offset = { x: 0, y: 0 };
    
    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            modal.style.cursor = "grab";
        }
    });
    
    modal.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - modal.getBoundingClientRect().left;
        offset.y = e.clientY - modal.getBoundingClientRect().top;
        modal.style.cursor = "grabbing";
    });
    
    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        modal.style.left = e.clientX - offset.x + "px";
        modal.style.top = e.clientY - offset.y + "px";
    });
    
    document.addEventListener("mouseup", () => {
        isDragging = false;
        modal.style.cursor = "grab";
    });
    
    // deplacer et fermer le modal Edit traitement
    const editModal = document.getElementById("editModal");
    const closeEdit = document.getElementById("closeEdit");

    if(closeEdit) {
        closeEdit.addEventListener("click", () => {
            editModal.style.display = "none"; 
        });
    }

    if(editModal) {
        document.addEventListener("mouseleave", () => {
            if (isDragging) {
                isDragging = false;
                editModal.style.cursor = "grab";
            }
        });

    
        editModal.addEventListener("mousedown", (e) => {
            isDragging = true;
            offset.x = e.clientX - editModal.getBoundingClientRect().left;
            offset.y = e.clientY - editModal.getBoundingClientRect().top;
            editModal.style.cursor = "grabbing";
        });

        editModal.addEventListener("mousedown", (e) => {
            isDragging = true;
            offset.x = e.clientX - editModal.getBoundingClientRect().left;
            offset.y = e.clientY - editModal.getBoundingClientRect().top;
            editModal.style.cursor = "grabbing";
        });

        document.addEventListener("mousemove", (e) => {
            if (!isDragging) return;
            editModal.style.left = e.clientX - offset.x + "px";
            editModal.style.top = e.clientY - offset.y + "px";
        });

        document.addEventListener("mouseup", () => {
            isDragging = false;
            editModal.style.cursor = "grab";
        });

        window.addEventListener('click', function (event) {
            if (event.target === editModal) {
                editModal.style.display = "none"; 
            }
        });
    }

    // deplacer et fermer le modal General
    const generalModal = document.getElementById("generalModal");
    const closeGeneralModal = document.getElementById("closeGeneral");

    closeGeneralModal.addEventListener("click", () => {
        generalModal.style.display = "none"; 
    });

    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            generalModal.style.cursor = "grab";
        }
    });

    generalModal.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - generalModal.getBoundingClientRect().left;
        offset.y = e.clientY - generalModal.getBoundingClientRect().top;
        generalModal.style.cursor = "grabbing";
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        generalModal.style.left = e.clientX - offset.x + "px";
        generalModal.style.top = e.clientY - offset.y + "px";
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        generalModal.style.cursor = "grab";
    });

    // deplacer et fermer le modal Erreur
    const errorModal = document.getElementById("errorModal");
    const closeErrorModal = document.getElementById("closeError");

    closeErrorModal.addEventListener("click", () => {
        errorModal.style.display = "none"; 
    });

    window.addEventListener('click', function (event) {
        if (event.target === errorModal) {
            errorModal.style.display = "none"; 
        }
    });

    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            generalModal.style.cursor = "grab";
        }
    });

    errorModal.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - errorModal.getBoundingClientRect().left;
        offset.y = e.clientY - errorModal.getBoundingClientRect().top;
        errorModal.style.cursor = "grabbing";
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        errorModal.style.left = e.clientX - offset.x + "px";
        errorModal.style.top = e.clientY - offset.y + "px";
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        errorModal.style.cursor = "grab";
    });

    // deplacer et fermer le modal cloture
    const clotureModal = document.getElementById("clotureModal");
    const closeClotureModal = document.getElementById("closeCloture");

    closeClotureModal.addEventListener("click", () => {
        clotureModal.style.display = "none"; 
    });

    window.addEventListener('click', function (event) {
        if (event.target === clotureModal) {
            clotureModal.style.display = "none"; 
        }
    });

    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            clotureModal.style.cursor = "grab";
        }
    });

    clotureModal.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - clotureModal.getBoundingClientRect().left;
        offset.y = e.clientY - clotureModal.getBoundingClientRect().top;
        clotureModal.style.cursor = "grabbing";
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        clotureModal.style.left = e.clientX - offset.x + "px";
        clotureModal.style.top = e.clientY - offset.y + "px";
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        clotureModal.style.cursor = "grab";
    });







    // deplacer et fermer le modal cloture
    const annoncesModal = document.getElementById("annoncesModal");

    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            annoncesModal.style.cursor = "grab";
        }
    });

    annoncesModal.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - annoncesModal.getBoundingClientRect().left;
        offset.y = e.clientY - annoncesModal.getBoundingClientRect().top;
        annoncesModal.style.cursor = "grabbing";
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        annoncesModal.style.left = e.clientX - offset.x + "px";
        annoncesModal.style.top = e.clientY - offset.y + "px";
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        annoncesModal.style.cursor = "grab";
    });





    // deplacer et fermer le modal code couleur

    const modalLegendColor = document.getElementById('modalLegendColor');

    document.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            modalLegendColor.style.cursor = "grab";
        }
    });

    modalLegendColor.addEventListener("mousedown", (e) => {
        isDragging = true;
        offset.x = e.clientX - modalLegendColor.getBoundingClientRect().left;
        offset.y = e.clientY - modalLegendColor.getBoundingClientRect().top;
        modalLegendColor.style.cursor = "grabbing";
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        modalLegendColor.style.left = e.clientX - offset.x + "px";
        modalLegendColor.style.top = e.clientY - offset.y + "px";
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        modalLegendColor.style.cursor = "grab";
    });

    saveButton.addEventListener("click", (event) => {
        if (!validateForm()) {
            event.preventDefault();
        } else {
            modal.style.display = "none"; 
        }
    });
    




    
    function validateForm() {
        var valid = true;

        const caisse = document.getElementById("caisse");
        if (caisse.value === "Tous") {
            alert("Veuillez choisir une caisse de traitement.");
            valid = false;
        }

        if (caisseSelect.value === "Tous") {
            alert("Veuillez choisir une caisse exploitante.");
            valid = false;
        }
        
        if (poolSelect.value === "Tous") {
            alert("Veuillez choisir une pool.");
            valid = false;
        }
        
        if (scenarioSelect.value === "Tous") {
            alert("Veuillez choisir un scénario.");
            valid = false;
        }

        var isProcessChecked = Array.from(processCheckboxes).some(checkbox => checkbox.checked);

        if (isProcessChecked === false) {
            alert("Vous devez sélectionner au moins un process.");
            valid = false;
        }

        var isVmChecked = Array.from(vmCheckboxes).some(checkbox => checkbox.checked);

        if (isVmChecked === false) {
            alert("Vous devez sélectionner au moins une VM.");
            valid = false;
        }

        var secondPoolCheckboxes = document.querySelectorAll('.vm-container[data-second-pool] .vm-checkbox');
        var isChecked = Array.from(secondPoolCheckboxes).some(function (checkbox) {
            return checkbox.checked;
        });

        if(!isChecked && poolSelect1.value !== "Tous") {
            alert("Aucune vm de la seconde pool a été cochée.");
            valid = false;
        }

        function isValidDateTime(dateTimeString) {
            // Format de date et heure local (ISO 8601)
            const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        
            // Vérifier si la chaîne de date correspond au format
            return dateTimeRegex.test(dateTimeString);
        }

        const dateDebut = document.getElementById("datedebut");
        const dateFin = document.getElementById("datefin");

        if (!isValidDateTime(dateDebut.value)) {
            alert("Veuillez entrer une date de début valide.");
            valid = false;
        }
    
        if (!isValidDateTime(dateFin.value)) {
            alert("Veuillez entrer une date de fin valide.");
            valid = false;
        }

        const startDate = new Date(dateDebut.value);
        const endDate = new Date(dateFin.value);

        if (endDate <= startDate) {
            alert("La date de fin doit être supérieure à la date de début.");
            valid = false;
        }

        if (startDate >= endDate) {
            alert("La date de début doit être inférieure à la date de fin.");
            valid = false;
        }

        const scenarioName = scenarioSelect.options[scenarioSelect.selectedIndex].text;
        const etape = document.getElementById("etape");
        // Vérifier si la valeur sélectionnée contient "puma"
        if (scenarioName.toLowerCase().includes("puma")) {
            if(etape.value === "Tous") {
                alert("Une étape doit être sélectionnée.");
                valid = false;
            }

            if(etape.value !== "Tous") {

                var dateDebutEtape2 = document.querySelector('#etape2 input[name="date_debut_etape_2"]').value;
                var dateDebutEtape3 = document.querySelector('#etape3 input[name="date_debut_etape_3"]').value;
                var dateDebutEtape4 = document.querySelector('#etape4 input[name="date_debut_etape_4"]').value;

                var dateFinEtape2 = document.querySelector('#etape2 input[name="date_fin_etape_2"]').value;
                var dateFinEtape3 = document.querySelector('#etape3 input[name="date_fin_etape_3"]').value;
                var dateFinEtape4 = document.querySelector('#etape4 input[name="date_fin_etape_4"]').value;

                if(etape.value === "Étape 1") {
                    if (!isValidDateTime(dateDebutEtape2)) {
                        alert("Veuillez entrer une date début étape 2 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateDebutEtape3)) {
                        alert("Veuillez entrer une date début étape 3 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateDebutEtape4)) {
                        alert("Veuillez entrer une date début étape 4 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape2)) {
                        alert("Veuillez entrer une date fin étape 2 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape3)) {
                        alert("Veuillez entrer une date fin étape 3 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape4)) {
                        alert("Veuillez entrer une date fin étape 4 valide.");
                        valid = false;
                    }
                }

                if(etape.value === "Étape 2") {
                    if (!isValidDateTime(dateDebutEtape3)) {
                        alert("Veuillez entrer une date début étape 3 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateDebutEtape4)) {
                        alert("Veuillez entrer une date début étape 4 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape3)) {
                        alert("Veuillez entrer une date fin étape 3 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape4)) {
                        alert("Veuillez entrer une date fin étape 4 valide.");
                        valid = false;
                    }
                }

                if(etape.value === "Étape 3") {
                    if (!isValidDateTime(dateDebutEtape4)) {
                        alert("Veuillez entrer une date début étape 4 valide.");
                        valid = false;
                    }
                    if (!isValidDateTime(dateFinEtape4)) {
                        alert("Veuillez entrer une date fin étape 4 valide.");
                        valid = false;
                    }
                }
            }
        } 

        return valid; 
    }

    document.getElementById("nbredossier").addEventListener("input", function () {
        const nbredossier = this.value;
        if (nbredossier < 0 || nbredossier > 100000) {
            alert("Veuillez entrer une valeur 0 et 100 000.");
            this.value = "";
        }
    });

    if(document.getElementById("new_nbredossier")) {
        document.getElementById("new_nbredossier").addEventListener("input", function () {
            const nbredossier = this.value;
            if (nbredossier < 0 || nbredossier > 100000) {
                alert("Veuillez entrer une valeur 0 et 100 000.");
                this.value = "";
            }
        });
    }




















    const refreshFormCreateTreatment = document.getElementById('refreshFormCreateTreatment');

    refreshFormCreateTreatment.addEventListener("click", () => {
        resetForm();
    });


    // RESET VALEURS FORMULAIRE CREATE ET MODIFIED
    /* window.addEventListener('beforeunload', function() {
        resetForm();
    }); */


    // Fonction pour réinitialiser les champs du formulaire
    function resetForm() {
        // Réinitialisez les champs du formulaire en affectant des valeurs vides ou par défaut
        const nbreDossierInput = document.getElementById('nbredossier');
        const scenarioSelect = document.getElementById('scenario');
        const datedebutInput = document.getElementById("datedebut");
        const datefinInput = document.getElementById("datefin");
        const caisse = document.getElementById("caisse");
        const pool = document.getElementById("pool");
        const vmCheckboxes = document.querySelectorAll('.vm-checkbox');
        const processCheckboxes = document.querySelectorAll('.process-checkbox');
        const commentaire = document.getElementById('commentaire');
        const checkboxPool = document.getElementById("checkboxPool");
        const selectAllCheckbox = document.getElementById("selectAllCheckbox");
        const selectAllVmcheckbox = document.getElementById("selectAllVm");
        const etape = document.getElementById("etape");
        const VmList = document.getElementById("VmList");
        const processList = document.getElementById("processList");
        const secondPool = document.getElementById("new_secondPool");
        const addPool = document.getElementById("new_addPool");

        addPool.style.display = '';
        secondPool.style.display = 'none';
        VmList.style.display = 'none';
        processList.style.display = 'none';
        nbreDossierInput.value = '';
        scenarioSelect.value = 'Tous';
        datedebutInput.value = '';
        datefinInput.value = '';
        caisse.value = 'Tous';
        pool.value = "Tous";
        etape.value = "Tous";
        processCheckboxes.forEach(checkbox => checkbox.checked = false);
        vmCheckboxes.forEach(checkbox => checkbox.checked = false);
        checkboxPool.checked = false;
        selectAllCheckbox.checked = false;
        selectAllVmcheckbox.checked = false;

        if (commentaire && commentaire.value != null) {
            commentaire.value = '';
        } 



        document.querySelector('#etape2 input[name="date_debut_etape_2"]').value = "";
        document.querySelector('#etape3 input[name="date_debut_etape_3"]').value = "";
        document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = "";

        document.querySelector('#etape2 input[name="date_fin_etape_2"]').value = "";
        document.querySelector('#etape3 input[name="date_fin_etape_3"]').value = "";
        document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = "";
    }





    $.ajax({
        url: '/verify_role',
        method: 'GET',
        success: function(response) {
            showCaisseAndPool(response);
        },
        error: function (error) {
            console.error('Erreur lors de l\'évènement', error);
        }
    });

    function showCaisseAndPool(data) {
        const idCaisse = data.idCaisse;
        const selectCaisse = document.getElementById("caisse_exploit");
        const caisse = selectCaisse.querySelector(`option[value="${idCaisse}"]`);
    
        if (caisse) {
            caisse.selected = true;
            filtrerPoolsEnFonctionDeLaCaisse(caisseSelect.value)
        }
    }




    function filtrerPoolsEnFonctionDeLaCaisse(selectedCaisseId) {
        poolOptions.forEach(function(option) {
            const poolCaisseId = option.getAttribute("data-caisse");

            if (selectedCaisseId === poolCaisseId) {
                option.style.display = "block"; 
            } else {
                option.style.display = "none";
            }
        });
    }









    const poolSelect = document.getElementById("pool");
    const poolSelect1 = document.getElementById("pool1");
    const scenarioLabel = document.getElementById("scenarioLabel");
    var processList = document.getElementById("processList");
    const processCheckboxes = document.querySelectorAll('.process-checkbox');
    const vmCheckboxes = document.querySelectorAll('.vm-checkbox');
    const caisseSelect = document.getElementById("caisse_exploit");
    const poolOptions = document.querySelectorAll(".pool-option");
    const vmContainers = document.querySelectorAll(".vm-container");
   

    // Ajoutez un gestionnaire d'événements de changement au sélecteur de caisse
    caisseSelect.addEventListener("change", function() {
        const selectedCaisseId = caisseSelect.value;
        poolSelect.value = 'Tous';
        poolSelect1.value = 'Tous';
        scenarioSelect.value = 'Tous';
        scenarioSelect.style.display = "none";
        processList.style.display = "none";

        // Parcourez les options de pool et affichez-les en fonction de la caisse sélectionnée
        poolOptions.forEach(function(option) {
            const poolCaisseId = option.getAttribute("data-caisse");
            
            if (selectedCaisseId === poolCaisseId) {
                option.style.display = "block"; 
            } else {
                option.style.display = "none";
            }
        });
    });



    // first pool choice
    poolSelect.addEventListener("change", function() {
        if (poolSelect.value === 'Tous' || (poolSelect1.value === poolSelect.value)) {
            scenarioSelect.value = 'Tous';
            scenarioSelect.style.display = "none";
            scenarioLabel.style.display = 'none';
            processList.style.display = "none";
        }
        else {
            scenarioSelect.value = 'Tous';
            scenarioSelect.style.display = '';
            scenarioLabel.style.display = '';
            processList.style.display = "none";
        }

        const selectedPoolId = poolSelect1.value;
        const selectedPoolId1 = poolSelect.value;

        // Parcourez les conteneurs VM et affichez-les en fonction des pools sélectionnés
        vmContainers.forEach(container => {
            const vmPoolId = container.getAttribute("data-pool");
            const vmPoolId1 = container.getAttribute("data-second-pool");

            if (selectedPoolId1 === vmPoolId || selectedPoolId === vmPoolId1) {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        });

        resetAllCheckboxes();

    });

    // second pool choice
    poolSelect1.addEventListener("change", function() {
        if (poolSelect1.value === 'Tous' || poolSelect.value === 'Tous' || (poolSelect1.value === poolSelect.value) ) {
            poolSelect1.value = 'Tous';
            scenarioSelect.value = 'Tous';
            scenarioSelect.style.display = "none";
            scenarioLabel.style.display = 'none';
            processList.style.display = "none";
        }
        else {
            scenarioSelect.value = 'Tous';
            scenarioSelect.style.display = '';
            scenarioLabel.style.display = '';
            processList.style.display = "none";
        }

        const selectedPoolId = poolSelect1.value;
        const selectedPoolId1 = poolSelect.value;

        // Parcourez les conteneurs VM et affichez-les en fonction des pools sélectionnés
        vmContainers.forEach(container => {
            const vmPoolId = container.getAttribute("data-pool");
            const vmPoolId1 = container.getAttribute("data-second-pool");

            if (selectedPoolId1 === vmPoolId || selectedPoolId === vmPoolId1) {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        });

        resetAllCheckboxes();
    });







    // Sélectionnez le sélecteur de scénario
    const scenarioSelect = document.getElementById("scenario");

    // Sélectionnez les conteneurs process
    const processContainers = document.querySelectorAll(".process-container");

    // Ajoutez un gestionnaire d'événements de changement au sélecteur de scénario
    scenarioSelect.addEventListener("change", function() {

        if (scenarioSelect.value === 'Tous') {
            processList.style.display = "none";
            VmList.style.display = "none";
        } else {
            const selectedProcess = scenarioSelect.value;

            VmList.style.display = "block";
            processList.style.display = "block";
            
            processContainers.forEach(function(container) {
                const scenarioId = container.getAttribute("data-scenario");
    
                if (selectedProcess === scenarioId) {
                    container.style.display = "block";
                } else {
                    container.style.display = "none";
                }
            });

            var WeekendsAndHolidays = document.getElementById("WeekendsAndHolidays");
            var vmMax = document.getElementById("vmMax");

            if (vmMax) {
                vmMax.innerHTML = "";
            }

            if (WeekendsAndHolidays) {
                WeekendsAndHolidays.innerHTML = "";
            }

            // si le scénario contient puma alors afficher :
            const etapeLabel = document.getElementById("etapeLabel");
            const selecteLabel = document.getElementById("etape");
            const scenarioName = scenarioSelect.options[scenarioSelect.selectedIndex].text;
    
            // Vérifier si la valeur sélectionnée contient "puma"
            if (scenarioName.toLowerCase().includes("puma")) {
                etapeLabel.style.display = '';
                selecteLabel.style.display = '';
            } else {
                etapeLabel.style.display = 'none';
                selecteLabel.style.display = 'none';
            }
        }
        resetAllCheckboxes();
    });



    function resetAllCheckboxes() {
        // Reset process checkboxes
        processCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
            checkbox.disabled = false;

        });
    
        // Reset VM checkboxes
        vmCheckboxes.forEach(function (vmCheckbox) {
            vmCheckbox.checked = false;
            vmCheckbox.disabled = false;
        });

        selectAllProcessCheckbox.checked = false;
        selectAllVmcheckbox.checked = false;
    }



    // Cocher toutes les checkboxes et vms
    var selectAllProcessCheckbox = document.getElementById("selectAllCheckbox");
    var VmList = document.getElementById("VmList");

    selectAllProcessCheckbox.addEventListener("change", function (event) {
        var checkbox = event.target;

        var selectedProcess = scenarioSelect.value;
        var processCheckboxes = document.querySelectorAll(`[data-scenario="${selectedProcess}"] .process-checkbox`);

        processCheckboxes.forEach(function (processCheckbox) {
            processCheckbox.checked = checkbox.checked;
        });

        if (!checkbox.checked) {

            processCheckboxes.forEach(function (processCheckbox) {
                processCheckbox.checked = false;
            });

            etapeSelect.value = "Tous";
            datesSelection.style.display = "none";
        }
    });


    var selectAllVmcheckbox = document.getElementById("selectAllVm");



    selectAllVmcheckbox.addEventListener("change", function (event) {
        var checkbox = event.target;
        var selectedVm = poolSelect.value;
        var vmCheckboxes;
    
        if (poolSelect1.value !== "Tous") {
            var selectedVm1 = poolSelect1.value;
            vmCheckboxes = document.querySelectorAll(`[data-pool="${selectedVm}"] .vm-checkbox, [data-second-pool="${selectedVm1}"] .vm-checkbox`);
        } else {
            vmCheckboxes = document.querySelectorAll(`[data-pool="${selectedVm}"] .vm-checkbox`);
        }

        vmCheckboxes.forEach(function (vmCheckbox) {
            vmCheckbox.checked = checkbox.checked;
        });

        if (!checkbox.checked) {
            // Décocher toutes les VM si la case "Select All" est décochée
            vmCheckboxes.forEach(function (vmCheckbox) {
                vmCheckbox.checked = false;
            });

            etapeSelect.value = "Tous";
            datesSelection.style.display = "none";
        }
    });













    // ajouter et supprimer la seconde pool
    var addPool = document.getElementById("addPool");
    var checkboxPool = document.getElementById("checkboxPool");
    var checkboxPool1 = document.getElementById("checkboxPool1");
    var deletePool = document.getElementById("deletePool");
    var secondPool = document.getElementById("secondPool");
    var datedebutInput = document.getElementById("datedebut");
    var datefinInput = document.getElementById("datefin");
    var tempsTraitementContainer = document.getElementById("tempsTraitementContainer");
    
    addPool.addEventListener("change", function() {
        addPool.style.display = "none";
        deletePool.style.display = "";
        checkboxPool1.checked = false;
        secondPool.style.display = "";

        scenarioSelect.value = 'Tous';
        processList.style.display = "none";
        VmList.style.display = "none";

        tempsTraitementContainer.style.display = "none";

        datedebutInput.value = '';
        datefinInput.value = '';

        etapeSelect.value = "Tous";
        datesSelection.style.display = "none";

        nbreDossierInput.value = '';

        resetAllCheckboxes();

    });

    deletePool.addEventListener("change", function() {
        addPool.style.display = "";
        checkboxPool.checked = false;
        deletePool.style.display = "none";
        secondPool.style.display = "none";
        
        poolSelect1.value = 'Tous';
        scenarioSelect.value = 'Tous';
        processList.style.display = "none";
        VmList.style.display = "none";

        tempsTraitementContainer.style.display = "none";

        datedebutInput.value = '';
        datefinInput.value = '';

        etapeSelect.value = "Tous";
        datesSelection.style.display = "none";

        nbreDossierInput.value = '';

        resetAllCheckboxes();
    });


    var dateFinInput = document.getElementById('datefin');

    dateFinInput.addEventListener("input", function () {
        etapeSelect.value = "Tous";
        datesSelection.style.display = "none";
    });




















    const dateDebutEtape2 = document.querySelector('#etape2 input[name="date_debut_etape_2"]');
    const dateFinEtape2 = document.querySelector('#etape2 input[name="date_fin_etape_2"]');

    dateDebutEtape2.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape2.value);

        if (datedebut >= new Date(dateDebutEtape3.value) ||  datedebut >= new Date(dateFinEtape3.value) || datedebut >= new Date(dateDebutEtape4.value) ||  datedebut >= new Date(dateFinEtape4.value)) {
            alert("Les dates l'étape 2 ne peuvent pas être supérieure aux dates de l'étape 3 et 4.");
            dateDebutEtape2.value = "";
        }

        if (datedebut <= new Date(dateDebutInput.value) ||  datedebut <= new Date(dateFinInput.value)) {
            alert("Les dates l'étape 3 ne peuvent pas être inférieure aux dates de l'étape 1.");
            dateDebutEtape2.value = "";
        }
    });

    dateFinEtape2.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape2.value);
        const datefin = new Date(dateFinEtape2.value);

        if (datefin <= datedebut) {
            alert("La date de fin de l'étape 2 ne peut pas être inférieure ou égale à la date de début de l'étape 2.");
            dateFinEtape2.value = "";
        }

        if (datefin >= new Date(dateDebutEtape3.value) ||  datefin >= new Date(dateFinEtape3.value) || datefin >= new Date(dateDebutEtape4.value) ||  datefin >= new Date(dateFinEtape4.value)) {
            alert("Les dates l'étape 2 ne peuvent pas être supérieure aux dates de l'étape 3 et 4.");
            dateFinEtape2.value = "";
        }

        
        if (datefin <= new Date(dateDebutInput.value) ||  datefin <= new Date(dateFinInput.value)) {
            alert("Les dates l'étape 2 ne peuvent pas être inférieure aux dates de l'étape 1.");
            dateFinEtape2.value = "";
        }
    });





    const dateDebutEtape3 = document.querySelector('#etape3 input[name="date_debut_etape_3"]');
    const dateFinEtape3 = document.querySelector('#etape3 input[name="date_fin_etape_3"]');

    dateDebutEtape3.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape3.value);
        const datefin = new Date(dateFinEtape3.value);

        if (datefin >= new Date(dateDebutEtape4.value) ||  datefin >= new Date(dateFinEtape4.value)) {
            alert("Les dates l'étape 3 ne peuvent pas être supérieure aux dates de l'étape 4.");
            dateDebutEtape3.value = "";
        }

        if (datedebut <= new Date(dateDebutInput.value) ||  datedebut <= new Date(dateFinInput.value) || datedebut <= new Date(dateDebutEtape2.value) ||  datedebut <= new Date(dateFinEtape2.value)) {
            alert("Les dates l'étape 3 ne peuvent pas être inférieure aux dates de l'étape 1 et 2.");
            dateDebutEtape3.value = "";
        }
    });

    dateFinEtape3.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape3.value);
        const datefin = new Date(dateFinEtape3.value);

        if (datedebut > datefin) {
            alert("La date de fin de l'étape 3 ne peut pas être inférieure à la date de début de l'étape 3.");
            dateFinEtape3.value = "";
        }

        if (datefin >= new Date(dateDebutEtape4.value) ||  datefin >= new Date(dateFinEtape4.value)) {
            alert("Les dates l'étape 3 ne peuvent pas être supérieure aux dates de l'étape 4.");
            dateFinEtape3.value = "";
        }

        if (datefin <= new Date(dateDebutInput.value) ||  datefin <= new Date(dateFinInput.value) || datefin <= new Date(dateDebutEtape2.value) ||  datefin <= new Date(dateFinEtape2.value)) {
            alert("Les dates l'étape 3 ne peuvent pas être inférieure aux dates de l'étape 1 et 2.");
            dateFinEtape3.value = "";
        }
    });





    const dateDebutEtape4 = document.querySelector('#etape4 input[name="date_debut_etape_4"]');
    const dateFinEtape4 = document.querySelector('#etape4 input[name="date_fin_etape_4"]');

    dateDebutEtape4.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape4.value);

        if (datedebut <= new Date(dateDebutInput.value) ||  datedebut <= new Date(dateFinInput.value) || datedebut <= new Date(dateDebutEtape2.value) ||  datedebut <= new Date(dateFinEtape2.value) || datedebut <= new Date(dateDebutEtape3.value) ||  datedebut <= new Date(dateFinEtape3.value)) {
            alert("Les dates l'étape 4 ne peuvent pas être inférieure aux dates de l'étape 1, 2 et 3.");
            dateDebutEtape4.value = "";
        }
    });

    dateFinEtape4.addEventListener("input", function () {
        const datedebut = new Date(dateDebutEtape4.value);
        const datefin = new Date(dateFinEtape4.value);

        if (datedebut > datefin) {
            alert("La date de fin de l'étape 4 ne peut pas être inférieure à la date de début de l'étape 4.");
            dateFinEtape4.value = "";
        }

        if (datefin <= new Date(dateDebutInput.value) ||  datefin <= new Date(dateFinInput.value) || datefin <= new Date(dateDebutEtape2.value) ||  datefin <= new Date(dateFinEtape2.value) || datefin <= new Date(dateDebutEtape3.value) ||  datefin <= new Date(dateFinEtape3.value)) {
            alert("Les dates l'étape 4 ne peuvent pas être inférieure aux dates de l'étape 1, 2 et 3.");
            dateFinEtape4.value = "";
        }
    });



    var etapeSelect = document.getElementById("etape");
    var datesSelection = document.getElementById("datesSelection");


    etapeSelect.addEventListener("change", function() {
        var selectedEtape = etapeSelect.value;

        if (selectedEtape === "Étape 1") {
            datesSelection.style.display = "block";
            document.getElementById("etape2").style.display = "block";
            document.getElementById("etape3").style.display = "block";
            document.getElementById("etape4").style.display = "block";
        } else if (selectedEtape === "Étape 2") {
            datesSelection.style.display = "block";
            document.getElementById("etape2").style.display = "block";
            document.getElementById("etape3").style.display = "block";
            document.getElementById("etape4").style.display = "block";
        } else if (selectedEtape === "Étape 3") {
            datesSelection.style.display = "block";
            document.getElementById("etape2").style.display = "none";
            document.getElementById("etape3").style.display = "block";
            document.getElementById("etape4").style.display = "block";
        } else {
            datesSelection.style.display = "none";
        }

    });







    const selectedCaisseId = caisseSelect.value;

    poolOptions.forEach(function(option) {
        const poolCaisseId = option.getAttribute("data-caisse");

        if (selectedCaisseId === poolCaisseId) {
            option.style.display = "block"; 
        } else {
            option.style.display = "none";
        }
    });


    if (poolSelect.value === "Tous") {
        secondPool.style.display = "none";
        addPool.style.display = "";
    }

    if (poolSelect1.value !== "Tous") {
        secondPool.style.display = "";
        addPool.style.display = "none";
        deletePool.style.display = "";
    } 
    
    if (poolSelect1.value === "Tous") {
        secondPool.style.display = "none";
        addPool.style.display = "";
    }




    const selectedPoolId = poolSelect.value;

    // Parcourez les conteneurs VM et affichez-les en fonction des pools sélectionnés
    vmContainers.forEach(container => {
        const vmPoolId = container.getAttribute("data-pool");

        if (selectedPoolId === vmPoolId) {
            container.style.display = "block";
        } else {
            container.style.display = "none";
        }
    });


    const selectedPoolId1 = poolSelect1.value;
    if(selectedPoolId1 !== "Tous") {
        vmContainers.forEach(container => {
            const vmPoolId = container.getAttribute("data-second-pool");
    
            if (selectedPoolId1 === vmPoolId) {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        });
    }




    if (scenarioSelect.value === 'Tous') {
        processList.style.display = "none";
        VmList.style.display = "none";
    } else {
        scenarioLabel.style.display = "";
        scenarioSelect.style.display = "";
        VmList.style.display = "block";
        processList.style.display = "block";


        const selectedProcess = scenarioSelect.value;
        
        processContainers.forEach(function(container) {
            const scenarioId = container.getAttribute("data-scenario");

            if (selectedProcess === scenarioId) {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        });

        // si le scénario contient puma alors afficher :
        const etapeLabel = document.getElementById("etapeLabel");
        const selecteLabel = document.getElementById("etape");
        const scenarioName = scenarioSelect.options[scenarioSelect.selectedIndex].text;

        // Vérifier si la valeur sélectionnée contient "puma"
        if (scenarioName.toLowerCase().includes("puma")) {
            etapeLabel.style.display = '';
            selecteLabel.style.display = '';
        } else {
            etapeLabel.style.display = 'none';
            selecteLabel.style.display = 'none';
        }
    }















































    etapeSelect.addEventListener("change", function() {
        var selectedEtape = etapeSelect.value;
        var scenarioId = document.getElementById('scenario').value;
        var nbreDossier = document.getElementById('nbredossier').value;

        EtapeSelect(selectedEtape, scenarioId, nbreDossier);
    });

    function EtapeSelect(selectedEtape, scenarioId, nbreDossier) {
    
        $.ajax({
            url: '/etape_select',
            method: 'GET',
            data: { selectedEtape: selectedEtape, scenarioId: scenarioId, nbreDossier: nbreDossier },
            success: function(response) {
                showEtape(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }




    function showEtape(response) {
        
        var selectedEtape = response.selectedEtape;

        var delai1 = parseInt(response.delai1);
        var delai2 = parseInt(response.delai2);
        var delai3 = parseInt(response.delai3);  

        var dateDebut = moment(document.getElementById('datedebut').value);
        var dateFin = moment(document.getElementById('datefin').value);

        if (dateDebut.isValid() && dateFin.isValid()) {

            var differenceEnMinutes = dateFin.diff(dateDebut, 'minutes');

            if (delai1 || delai2 || delai3) {

                if (selectedEtape === "Étape 1") {

                    datesSelection.style.display = "block";
                    document.getElementById("etape2").style.display = "block";
                    document.getElementById("etape3").style.display = "block";
                    document.getElementById("etape4").style.display = "block";

                    var dateDebutEtape2 = moment(dateFin).add(delai1, 'days');
                    var dateFinEtape2 = moment(dateDebutEtape2).add(differenceEnMinutes, 'minutes');
        
                    var dateDebutEtape3 = moment(dateFinEtape2).add(delai2, 'days');
                    var dateFinEtape3 = moment(dateDebutEtape3).add(differenceEnMinutes, 'minutes');
        
                    var dateDebutEtape4 = moment(dateFinEtape3).add(delai3, 'days');
                    var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');
                    
                    document.querySelector('#etape2 input[name="date_debut_etape_2"]').value = dateDebutEtape2.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape3 input[name="date_debut_etape_3"]').value = dateDebutEtape3.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = dateDebutEtape4.format('YYYY-MM-DDTHH:mm');

                    document.querySelector('#etape2 input[name="date_fin_etape_2"]').value = dateFinEtape2.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape3 input[name="date_fin_etape_3"]').value = dateFinEtape3.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');
                } else if (selectedEtape === "Étape 2") {

                    datesSelection.style.display = "block";
                    document.getElementById("etape2").style.display = "none";
                    document.getElementById("etape3").style.display = "block";
                    document.getElementById("etape4").style.display = "block";
        
                    var dateDebutEtape3 = moment(dateFin).add(delai2, 'days');
                    var dateFinEtape3 = moment(dateDebutEtape3).add(differenceEnMinutes, 'minutes');
        
                    var dateDebutEtape4 = moment(dateFinEtape3).add(delai3, 'days');
                    var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');

                    document.querySelector('#etape3 input[name="date_debut_etape_3"]').value = dateDebutEtape3.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = dateDebutEtape4.format('YYYY-MM-DDTHH:mm');

                    document.querySelector('#etape3 input[name="date_fin_etape_3"]').value = dateFinEtape3.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');

                } else if (selectedEtape === "Étape 3") {

                    datesSelection.style.display = "block";
                    document.getElementById("etape2").style.display = "none";
                    document.getElementById("etape3").style.display = "none";
                    document.getElementById("etape4").style.display = "block";
        
                    var dateDebutEtape4 = moment(dateFin).add(delai3, 'days');
                    var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');

                    document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = dateDebutEtape4.format('YYYY-MM-DDTHH:mm');
                    document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');
                } else {
                    datesSelection.style.display = "none";
                }
            } else {
                etapeSelect.value = "Tous";
                datesSelection.style.display = "none";
            }
        } else {
            etapeSelect.value = "Tous";
            datesSelection.style.display = "none";
        }
    }



































    function showCalcul(response) {
        var isProcessChecked = Array.from(processCheckboxes).some(checkbox => checkbox.checked);
        var isVmChecked = Array.from(vmCheckboxes).some(checkbox => checkbox.checked);
    
        var dateDebutInput = document.getElementById('datedebut');
        var dateFinInput = document.getElementById('datefin');

        if (isProcessChecked && isVmChecked && response && response.success) {
            
            document.getElementById("tempsTraitementContainer").style.display = "block";

            if(response.result1 === 0) {
                document.getElementById("result").textContent = "Aucun temps trouvé, vérifiez vos process.";
            } else {
                var formattedResult = response.result;
                document.getElementById("result").textContent = formattedResult;
            }
    
            var currentDate = moment();
            var startDateTime = moment(currentDate);
            startDateTime.set({ hour: 7, minute: 30 });
    
            var endDateTime0 = moment(currentDate);
            endDateTime0.set({ hour: 18, minute: 0 });

            // Vérifiez si un traitement existe pour la date actuelle dans la réponse
            var traitementsAssocies = response.traitementsAssocies;

            traitementsAssocies.forEach(function(traitement) {
                var traitementDateFin = moment(traitement.dateFin.date);
            
                if ((traitementDateFin.hour() === endDateTime0.hour() && traitementDateFin.minute() === endDateTime0.minute()) || traitementDateFin.isAfter(endDateTime0)) {
                    // Mettre startDateTime au lendemain à 07:30
                    startDateTime = traitementDateFin.clone().add(1, 'day').set({hour: 7, minute: 30});
                } else {
                    startDateTime = traitementDateFin.clone().add(1, 'minute');
                }
            });
    
            var formattedStartDate = startDateTime.format('YYYY-MM-DDTHH:mm');
            dateDebutInput.value = formattedStartDate;
    
            // Obtenez le nombre de minutes à ajouter (supposons que response.result1 contient le nombre de minutes)
            var resultMinute = response.result1;
    
            // Calculez la date de fin en ajoutant le nombre de minutes à la date de début
            var endDateTime = startDateTime.clone().add(resultMinute, 'minutes');
    
            // Formattez la date de fin
            var formattedEndDate = endDateTime.format('YYYY-MM-DDTHH:mm');
            dateFinInput.value = formattedEndDate;

        } else {
            document.getElementById("tempsTraitementContainer").style.display = "none";
            dateDebutInput.value = "";
            dateFinInput.value = "";
        }
    }
    

    function ProcessCheckboxChange(processId, nbredossier, checkedVMs, checkedProcessIdsString, checkedVmsIdsString) {
    
        $.ajax({
            url: '/process_checkbox',
            method: 'GET',
            data: { processId: processId, nbredossier: nbredossier, checkedVMs: checkedVMs, checkedProcessIdsString: checkedProcessIdsString, checkedVmsIdsString: checkedVmsIdsString },
            success: function(response) {
                showCalcul(response);
                showCalcul1(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }
    



    processCheckboxes.forEach(function(checkbox) {
        var processId = checkbox.value; 
        var nbreDossierInput = document.getElementById('nbredossier');

        checkbox.addEventListener('change', function() {
            var nbreDossier = nbreDossierInput.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            ProcessCheckboxChange(processId, nbreDossier, checkedVMs, checkedProcessIdsString, checkedVmsIdsString);

            // Calcul étape

            var selectedEtape = etapeSelect.value;
            var scenarioId = document.getElementById('scenario').value;

            EtapeSelect(selectedEtape, scenarioId, nbreDossier);

            WeekendsAndHolidays(checkedProcessIdsString);

            VmMax(checkedProcessIdsString);
        });
    });
 
    function VmCheckboxChange(vmId, nbredossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString) {
    
        $.ajax({
            url: '/vm_checkbox',
            method: 'GET',
            data: { vmId: vmId, nbredossier: nbredossier, checkedVMs: checkedVMs, checkedProcess: checkedProcess, checkedProcessIdsString: checkedProcessIdsString, checkedVmsIdsString: checkedVmsIdsString },
            success: function(response) {
                showCalcul(response);
                showCalcul1(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }




    vmCheckboxes.forEach(function(checkbox) {
        var vmId = checkbox.value; 
        var nbreDossierInput = document.getElementById('nbredossier');

        checkbox.addEventListener('change', function() {
            var nbreDossier = nbreDossierInput.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            VmCheckboxChange(vmId, nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

            var selectedEtape = etapeSelect.value;
            var scenarioId = document.getElementById('scenario').value;

            EtapeSelect(selectedEtape, scenarioId, nbreDossier);
        });
    });





    var nbreDossierInput = document.getElementById('nbredossier');

    nbreDossierInput.addEventListener("input", function () {
        
        var nbreDossier = nbreDossierInput.value;
        var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;
        var checkedProcess = document.querySelectorAll('.process-checkbox:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

        nbreDossierChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

        var selectedEtape = etapeSelect.value;
        var scenarioId = document.getElementById('scenario').value;

        EtapeSelect(selectedEtape, scenarioId, nbreDossier);

        WeekendsAndHolidays(checkedProcessIdsString);

        VmMax(checkedProcessIdsString);
    });

    function nbreDossierChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString) {
        $.ajax({
            url: '/nbreDossier_change',
            method: 'GET',
            data: { nbreDossier: nbreDossier, checkedVMs: checkedVMs, checkedProcess: checkedProcess, checkedProcessIdsString: checkedProcessIdsString, checkedVmsIdsString: checkedVmsIdsString},
            success: function(response) {
                showCalcul(response);
                showCalcul1(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }































    // DATE DEBUT CALCUL

    var dateDebutInput = document.getElementById('datedebut');

    dateDebutInput.addEventListener("input", function () {

        var nbreDossier = nbreDossierInput.value;
        var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;
        var checkedProcess = document.querySelectorAll('.process-checkbox:checked').length;

        var checkedVmsIds = [];

        document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
            checkedVmsIds.push(cb.value);
        });

        var checkedProcessIds = [];

        document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
            checkedProcessIds.push(cb.value);
        });

        var checkedVmsIdsString = checkedVmsIds.join(',');
        var checkedProcessIdsString = checkedProcessIds.join(',');

        dateDebutChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);


        var selectedEtape = etapeSelect.value;
        var scenarioId = document.getElementById('scenario').value;

        EtapeSelect(selectedEtape, scenarioId, nbreDossier);

        WeekendsAndHolidays(checkedProcessIdsString);

        VmMax(checkedProcessIdsString);
    });


    function dateDebutChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString) {
        $.ajax({
            url: '/dateDebut_change',
            method: 'GET',
            data: { nbreDossier: nbreDossier, checkedVMs: checkedVMs, checkedProcess: checkedProcess, checkedProcessIdsString: checkedProcessIdsString, checkedVmsIdsString: checkedVmsIdsString },
            success: function(response) {
                calculEndDate(response);
                calculEndDate1(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }



    function calculEndDate(response) {
        var isProcessChecked = Array.from(processCheckboxes).some(checkbox => checkbox.checked);
        var isVmChecked = Array.from(vmCheckboxes).some(checkbox => checkbox.checked);

        var dateDebutInput = document.getElementById('datedebut');
        var dateFinInput = document.getElementById('datefin');

        if (isProcessChecked && isVmChecked && response && response.success) {

            document.getElementById("tempsTraitementContainer").style.display = "block";

            var formattedResult = response.result;
            document.getElementById("result").textContent = formattedResult;

            var manuallyEnteredStartDate = document.getElementById('datedebut').value;
            dateDebutInput.value = manuallyEnteredStartDate;


            var resultMinute = response.result1;
            var endDate = new Date(new Date(manuallyEnteredStartDate).getTime() + resultMinute * 60000);
            var formattedEndDate = endDate.getFullYear() +
            '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) +
            '-' + ('0' + endDate.getDate()).slice(-2) +
            'T' + ('0' + endDate.getHours()).slice(-2) +
            ':' + ('0' + endDate.getMinutes()).slice(-2);
            dateFinInput.value = formattedEndDate;

        }
    }



















    // cocher toutes les vm
    
    var selectAllVmInput = document.getElementById("selectAllVm");

    selectAllVmInput.addEventListener("change", function () {
        
        var nbreDossier = nbreDossierInput.value;
        var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;
        var checkedProcess = document.querySelectorAll('.process-checkbox:checked').length;

        var checkedVmsIds = [];

        document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
            checkedVmsIds.push(cb.value);
        });

        var checkedProcessIds = [];

        document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
            checkedProcessIds.push(cb.value);
        });

        var checkedVmsIdsString = checkedVmsIds.join(',');
        var checkedProcessIdsString = checkedProcessIds.join(',');

        selectAllCheckbox(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);
    });




   // cocher tous les process
    
   var selectAllProcessInput = document.getElementById("selectAllCheckbox");

   selectAllProcessInput.addEventListener("change", function () {
       
       var nbreDossier = nbreDossierInput.value;
       var checkedVMs = document.querySelectorAll('.vm-checkbox:checked').length;
       var checkedProcess = document.querySelectorAll('.process-checkbox:checked').length;

       var checkedVmsIds = [];

       document.querySelectorAll('.vm-checkbox:checked').forEach(function(cb) {
           checkedVmsIds.push(cb.value);
       });

       var checkedProcessIds = [];

       document.querySelectorAll('.process-checkbox:checked').forEach(function(cb) {
           checkedProcessIds.push(cb.value);
       });

       var checkedVmsIdsString = checkedVmsIds.join(',');
       var checkedProcessIdsString = checkedProcessIds.join(',');

       selectAllCheckbox(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

       WeekendsAndHolidays(checkedProcessIdsString);

       VmMax(checkedProcessIdsString);
   });


   function selectAllCheckbox(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString) {
       $.ajax({
           url: '/selectAllCheckbox',
           method: 'GET',
           data: { nbreDossier: nbreDossier, checkedVMs: checkedVMs, checkedProcess: checkedProcess, checkedProcessIdsString: checkedProcessIdsString, checkedVmsIdsString: checkedVmsIdsString },
           success: function(response) {
               showCalcul(response);
               showCalcul1(response);
           },
           error: function (error) {
               console.error('Erreur lors de l\'évènement', error);
           }
       });
   }






   // WEEKDS ENDS & JOURS FERIES

   function WeekendsAndHolidays(checkedProcessIdsString) {
    
        $.ajax({
            url: '/WeekendsAndHolidays',
            method: 'GET',
            data: { checkedProcessIdsString: checkedProcessIdsString },
            success: function(response) {
                showMessage(response);
                showMessage1(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }



    function showMessage(response) {

        // Afficher les noms des processus avec week-ends et jours fériés
        var processNames = response.processIdsWithWeekendsAndHolidays;
        var weekendsAndHolidaysLabel = document.getElementById("WeekendsAndHolidays");
        var weekendsAndHolidaysLabel1 = document.getElementById("WeekendsAndHolidays1");

        if(processNames && processNames.length > 0) {
            weekendsAndHolidaysLabel.textContent = processNames.join(", ") + " ne tournent pas les week ends et jours fériés. Reprogrammation automatique.";
            weekendsAndHolidaysLabel1.textContent = "Si un jour tombe un week end ou un jour férié, reprogrammation au prochain jour ouvrable.";
        } else {
            weekendsAndHolidaysLabel.textContent = "";
            weekendsAndHolidaysLabel1.textContent = "";
        }
    }


   // PERIODICITE

   var selectedScenario = document.getElementById("scenario");

   selectedScenario.addEventListener("change", function () {
       Periodicity(selectedScenario.value);
   });
   
   function Periodicity(selectedScenarioId) {
       $.ajax({
           url: '/periodicity',
           method: 'GET',
           data: { selectedScenarioId: selectedScenarioId },
           success: function(response) {
               showPeriodicity(response);
           },
           error: function (error) {
               console.error('Erreur lors de l\'évènement', error);
           }
       });
   }
   
   function showPeriodicity(response) {
       var periodicityLabel = document.getElementById("periodicity");
   
       if(response.success && response.periodicity && response.periodicity_until) {
            periodicityLabel.textContent = "La périodicité de ce scénario est " + response.periodicity + " sur une période de " + response.periodicity_until + " an(s)";
       } else {
         periodicityLabel.textContent = "";
       }
   }
   




    
    // VM MAX (LIMITE DE UNE VM)

    function VmMax(checkedProcessIdsString) {

        $.ajax({
            url: '/vm_max',
            method: 'GET',
            data: {checkedProcessIdsString: checkedProcessIdsString},
            success: function(response) {
                vm_max(response);
                vm_max1(response);

            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }



    function vm_max(response) {
        var vmMax = document.getElementById("vmMax");

        if (response.success) {
            var processNames = [];
    
            // Parcourir les processus et leurs valeurs vm_max
            response.checkedProcessIds.forEach(function(processId, index) {
                var vm_max_value = response.vm_max[index];
                var processName = response.processNames[index];
    
                if (vm_max_value === true) {
                    // Récupérer les noms des processus
                    processNames.push(processName);
                }
            });

            if (processNames.length > 0) {
                var processNamesString = processNames.join(', ');
                vmMax.innerHTML = processNamesString + ' peuvent contenir qu\'une seule VM.';
            } else {
                vmMax.innerHTML = "";
            }
        } else {
            vmMax.innerHTML = "";
        }
    }  

































































    // POUR LE TRAITEMENT EDIT MODAL

    function showCalcul1(response) {
        var isProcessChecked = Array.from(processCheckboxes1).some(checkbox => checkbox.checked);
        var isVmChecked = Array.from(vmCheckboxes1).some(checkbox => checkbox.checked);
    
        var dateDebutInput = document.getElementById('new_datedebut');
        var dateFinInput = document.getElementById('new_datefin');

        if (isProcessChecked && isVmChecked && response && response.success) {
            
            document.getElementById("new_tempsTraitementContainer").style.display = "block";

            if(response.result1 === 0) {
                document.getElementById("new_result").textContent = "Aucun temps trouvé, vérifiez vos process.";
            } else {
                var formattedResult = response.result;
                document.getElementById("new_result").textContent = formattedResult;
            }
    
            var currentDate = moment();
            var startDateTime = moment(currentDate);
            startDateTime.set({ hour: 7, minute: 30 });
    
            var endDateTime0 = moment(currentDate);
            endDateTime0.set({ hour: 18, minute: 0 });

            // Vérifiez si un traitement existe pour la date actuelle dans la réponse
            var traitementsAssocies = response.traitementsAssocies;

            traitementsAssocies.forEach(function(traitement) {
                var traitementDateFin = moment(traitement.dateFin.date);
            
                if ((traitementDateFin.hour() === endDateTime0.hour() && traitementDateFin.minute() === endDateTime0.minute()) || traitementDateFin.isAfter(endDateTime0)) {
                    // Mettre startDateTime au lendemain à 07:30
                    startDateTime = traitementDateFin.clone().add(1, 'day').set({hour: 7, minute: 30});
                } else {
                    startDateTime = traitementDateFin.clone().add(1, 'minute');
                }
            });
    
            var formattedStartDate = startDateTime.format('YYYY-MM-DDTHH:mm');
            dateDebutInput.value = formattedStartDate;
    
            // Obtenez le nombre de minutes à ajouter (supposons que response.result1 contient le nombre de minutes)
            var resultMinute = response.result1;
    
            // Calculez la date de fin en ajoutant le nombre de minutes à la date de début
            var endDateTime = startDateTime.clone().add(resultMinute, 'minutes');
    
            // Formattez la date de fin
            var formattedEndDate = endDateTime.format('YYYY-MM-DDTHH:mm');
            dateFinInput.value = formattedEndDate;

        } else {
            document.getElementById("new_tempsTraitementContainer").style.display = "none";
            dateDebutInput.value = "";
            dateFinInput.value = "";
        }
    }

    function showMessage1(response) {

        // Afficher les noms des processus avec week-ends et jours fériés
        var processNames = response.processIdsWithWeekendsAndHolidays;
        var weekendsAndHolidaysLabel = document.getElementById("new_WeekendsAndHolidays");

        if(processNames && processNames.length > 0) {
            weekendsAndHolidaysLabel.textContent = processNames.join(", ") + " ne tournent pas les week ends et jours fériés. Reprogrammation automatique.";
        } else {
            weekendsAndHolidaysLabel.textContent = "";
        }
    }

    function calculEndDate1(response) {
        var isProcessChecked = Array.from(processCheckboxes1).some(checkbox => checkbox.checked);
        var isVmChecked = Array.from(vmCheckboxes1).some(checkbox => checkbox.checked);

        var dateDebutInput = document.getElementById('new_datedebut');
        var dateFinInput = document.getElementById('new_datefin');

        if (isProcessChecked && isVmChecked && response && response.success) {

            document.getElementById("new_tempsTraitementContainer").style.display = "block";

            var formattedResult = response.result;
            document.getElementById("new_result").textContent = formattedResult;

            var manuallyEnteredStartDate = document.getElementById('new_datedebut').value;
            dateDebutInput.value = manuallyEnteredStartDate;


            var resultMinute = response.result1;
            var endDate = new Date(new Date(manuallyEnteredStartDate).getTime() + resultMinute * 60000);
            var formattedEndDate = endDate.getFullYear() +
            '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) +
            '-' + ('0' + endDate.getDate()).slice(-2) +
            'T' + ('0' + endDate.getHours()).slice(-2) +
            ':' + ('0' + endDate.getMinutes()).slice(-2);
            dateFinInput.value = formattedEndDate;

        }
    }

    function vm_max1(response) {
        var vmMax = document.getElementById("new_vmMax");

        if (response.success) {
            var processNames = [];
    
            // Parcourir les processus et leurs valeurs vm_max
            response.checkedProcessIds.forEach(function(processId, index) {
                var vm_max_value = response.vm_max[index];
                var processName = response.processNames[index];
    
                if (vm_max_value === true) {
                    // Récupérer les noms des processus
                    processNames.push(processName);
                }
            });

            if (processNames.length > 0) {
                var processNamesString = processNames.join(', ');
                vmMax.innerHTML = processNamesString + ' peuvent contenir qu\'une seule VM.';
            } else {
                vmMax.innerHTML = "";
            }
        } else {
            vmMax.innerHTML = "";
        }
    }  





















    const vmCheckboxes1 = document.querySelectorAll('.vm-checkbox1');

    vmCheckboxes1.forEach(function(checkbox) {
        var vmId = checkbox.value; 
        var nbreDossierInput = document.getElementById('new_nbredossier');

        checkbox.addEventListener('change', function() {
            var nbreDossier = nbreDossierInput.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox1:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            VmCheckboxChange(vmId, nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);
        });
    });

    const processCheckboxes1 = document.querySelectorAll('.process-checkbox1');

    processCheckboxes1.forEach(function(checkbox) {
        var processId = checkbox.value; 
        var nbreDossierInput1 = document.getElementById('new_nbredossier');

        checkbox.addEventListener('change', function() {
            var nbreDossier = nbreDossierInput1.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            ProcessCheckboxChange(processId, nbreDossier, checkedVMs, checkedProcessIdsString, checkedVmsIdsString);

            WeekendsAndHolidays(checkedProcessIdsString);

            VmMax(checkedProcessIdsString);
        });
    });


    var nbreDossierInput1 = document.getElementById('new_nbredossier');
    
    if(nbreDossierInput1) {
        nbreDossierInput1.addEventListener("input", function () {
            
            var nbreDossier = nbreDossierInput1.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox1:checked').length;

                var checkedVmsIds = [];

                document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                    checkedVmsIds.push(cb.value);
                });

                var checkedProcessIds = [];

                document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                    checkedProcessIds.push(cb.value);
                });

                var checkedVmsIdsString = checkedVmsIds.join(',');
                var checkedProcessIdsString = checkedProcessIds.join(',');

            nbreDossierChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

            WeekendsAndHolidays(checkedProcessIdsString);

            VmMax(checkedProcessIdsString);
        });
    }



    // DATE DEBUT CALCUL

    var dateDebutInput = document.getElementById('new_datedebut');
    if(dateDebutInput) {
        dateDebutInput.addEventListener("input", function () {

            var nbreDossier = nbreDossierInput1.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox1:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            dateDebutChange(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

            WeekendsAndHolidays(checkedProcessIdsString);

            VmMax(checkedProcessIdsString);
        });
    }



    // cocher toutes les vm
    
    var selectAllVmInput = document.getElementById("new_selectAllVm");
    if(selectAllVmInput) {
        selectAllVmInput.addEventListener("change", function () {
            
            var nbreDossier = nbreDossierInput1.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox1:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            selectAllCheckbox(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);
        });
    }




   // cocher tous les process
    
   var selectAllProcessInput = document.getElementById("new_selectAllCheckbox");

   if(selectAllProcessInput) {
        selectAllProcessInput.addEventListener("change", function () {
            
            var nbreDossier = nbreDossierInput1.value;
            var checkedVMs = document.querySelectorAll('.vm-checkbox1:checked').length;
            var checkedProcess = document.querySelectorAll('.process-checkbox1:checked').length;

            var checkedVmsIds = [];

            document.querySelectorAll('.vm-checkbox1:checked').forEach(function(cb) {
                checkedVmsIds.push(cb.value);
            });

            var checkedProcessIds = [];

            document.querySelectorAll('.process-checkbox1:checked').forEach(function(cb) {
                checkedProcessIds.push(cb.value);
            });

            var checkedVmsIdsString = checkedVmsIds.join(',');
            var checkedProcessIdsString = checkedProcessIds.join(',');

            selectAllCheckbox(nbreDossier, checkedVMs, checkedProcess, checkedProcessIdsString, checkedVmsIdsString);

            WeekendsAndHolidays(checkedProcessIdsString);

            VmMax(checkedProcessIdsString);
        });
    }

});
