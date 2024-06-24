document.addEventListener('DOMContentLoaded', function() { 

    window.addEventListener('load', function () {
        const popup = document.getElementById('popup');
        const closeButton = document.getElementById('close-button');
        const urgentButton = document.getElementById('urgentButton');
        const importantButton = document.getElementById('importantButton');
        const normalButton = document.getElementById('normalButton');

        function showMessages(priority) {
            const messages = document.querySelectorAll('.popup-messages li');
            messages.forEach(function (message) {
                if (message.dataset.priority === priority) {
                    message.style.display = 'block';
                } else {
                    message.style.display = 'none';
                }
            });
        }
    
        function resetDisplay() {
            const messages = document.querySelectorAll('.popup-messages li');
            messages.forEach(function (message) {
                message.style.display = 'block';
            });
        }
    
        function closePopup() {
            resetDisplay();
            popup.style.display = 'none';
            const expirationDate = new Date();
            expirationDate.setHours(expirationDate.getHours() + 1); // set expiration to 1 hour from now
            document.cookie = 'popupDisplayed=true; expires=' + expirationDate.toUTCString();
        }

        if (closeButton) {
            closeButton.addEventListener('click', closePopup);
        }

    
        window.addEventListener('click', function (event) {
            if (event.target === popup) {
                closePopup();
            }
        });

        if (urgentButton) {
            urgentButton.addEventListener('click', function () {
            showMessages('urgent');
            });
        }

        if (importantButton) {
            importantButton.addEventListener('click', function () {
                showMessages('important');
            });
        }

        if (normalButton) {
            normalButton.addEventListener('click', function () {
                showMessages('normal');
            });
        }
    
        // Check if the cookie exists
        if (document.cookie.indexOf('popupDisplayed=true') === -1) {
            // If the cookie doesn't exist, display the popup
            if(popup) {
                popup.style.display = 'block';
            }

            if(closeButton) {
                // Add an event to the close button
                closeButton.addEventListener('click', function () {
                    resetDisplay();
        
                    // Hide the popup
                    popup.style.display = 'none';
                    // Set a cookie to indicate that the popup has been displayed
                    const expirationDate = new Date();
                    expirationDate.setHours(expirationDate.getHours() + 1); // set expiration to 1 hour from now
                    document.cookie = 'popupDisplayed=true; expires=' + expirationDate.toUTCString();
                });
            }
    
            // Hide the popup after a delay of 4 seconds
            setTimeout(function () {
                popup.style.display = 'none';
                // Set a cookie to indicate that the popup has been displayed
                const expirationDate = new Date();
                expirationDate.setHours(expirationDate.getHours() + 1); // set expiration to 1 hour from now
                document.cookie = 'popupDisplayed=true; expires=' + expirationDate.toUTCString();
            }, 3600000); // 3600000 milliseconds = 1 hour
        }
    });
    











    function openModal(info) {

        const traitementId = info.event.id;
        const startDateTraitement = info.event.start;
        const endDateTraitement = info.event.end;

        const commentaireCloture = info.event.extendedProps.clotureComment;
        const commentaireError = info.event.extendedProps.errorComment;


        const generalModal = document.getElementById("generalModal");
        const deleteButton = document.getElementById("deleteButton");
        const cloturerButton = document.getElementById("closeButton");
        const errorButton = document.getElementById("errorButton");
        const editButton = document.getElementById("editButton");
        const currentDate = moment();

        // deplacer et fermer le modal Erreur
        const errorModal = document.getElementById("errorModal");
        const closeErrorModal = document.getElementById("closeError");

        closeErrorModal.addEventListener("click", () => {
            errorModal.style.display = "none"; 
            info.event.setProp('backgroundColor', '');
        });

        window.addEventListener('click', function (event) {
            if (event.target === errorModal) {
                errorModal.style.display = "none"; 
                info.event.setProp('backgroundColor', '');
            }
        });

        errorButton.addEventListener("click", () => {
            generalModal.style.display = "none"; 

            const errorValid = document.getElementById("validError");
            const deleteError = document.getElementById("deleteError");

            const formattedEndDateTraitement = moment(endDateTraitement).format("YYYY-MM-DDTHH:mm");
            document.getElementById("dateFinErreur").value = formattedEndDateTraitement;


            document.getElementById("commentaireError").value = commentaireError;

            deleteError.addEventListener("click", () => {
                const confirmation = confirm("Êtes-vous sûr de vouloir supprimer l'erreur ?");
                if (confirmation) {
                    $.ajax({
                        url: '/delete_error/' + traitementId,
                        method: 'POST', 
                        success: function () {
                            window.location.reload();
                        },
                        error: function () {
                            alert("Erreur dans la requête");
                            window.location.reload();
                        }
                    });

                    modal.style.display = "none"; 
                }
            });

            const modal = document.getElementById("errorModal");
            modal.style.display = "block"; 

            errorValid.addEventListener("click", (event) => {

                if (!validateError(startDateTraitement, endDateTraitement)) {
                    event.preventDefault();
                }
                else {
                    const commentaireValue = $('#commentaireError').val();
                    const dateFinErreur = $('#dateFinErreur').val();
                    const dataToSend = { commentaire: commentaireValue, dateFinErreur: dateFinErreur  };

                    const confirmation = confirm("Êtes-vous sûr de vouloir mettre un message d'erreur ?");

                    if (confirmation) {
                        $.ajax({
                            url: '/traitment_erreur/' + traitementId,
                            method: 'POST', 
                            data: dataToSend,
                            success: function () {
                                window.location.reload();
                            },                                                                   
                            error: function () {
                                alert("Erreur dans la requête");
                                
                            }
                        });
        
                        modal.style.display = "none"; 
                    }
                }
            });
        });






        // deplacer et fermer le modal cloture
        const clotureModal = document.getElementById("clotureModal");
        const closeClotureModal = document.getElementById("closeCloture");

        closeClotureModal.addEventListener("click", () => {
            clotureModal.style.display = "none"; 
            info.event.setProp('backgroundColor', '');
        });

        window.addEventListener('click', function (event) {
            if (event.target === clotureModal) {
                clotureModal.style.display = "none"; 
                info.event.setProp('backgroundColor', '');
            }
        });

        cloturerButton.addEventListener("click", () => {
            generalModal.style.display = "none";
            
            const clotureValid = document.getElementById("validCloture");
            const deleteCloture = document.getElementById("deleteCloture");

            const formattedEndDateTraitement = moment(endDateTraitement).format("YYYY-MM-DDTHH:mm");
            document.getElementById("dateFinCloture").value = formattedEndDateTraitement;

            document.getElementById("commentaireCloture").value = commentaireCloture;

            deleteCloture.addEventListener("click", () => {
                const confirmation = confirm("Êtes-vous sûr de vouloir supprimer la clotûre ?");
                if (confirmation) {
                    $.ajax({
                        url: '/delete_cloture/' + traitementId,
                        method: 'POST', 
                        success: function () {
                            window.location.reload();
                        },
                        error: function () {
                            alert("Erreur dans la requête");
                            window.location.reload();
                        }
                    });

                    modal.style.display = "none"; 
                }
            });

            const modal = document.getElementById("clotureModal");

            if (moment(startDateTraitement).isAfter(currentDate)) {
                alert("Ce traitement n\'est pas encore lancé.");
                modal.style.display = "none"; 
            } else {
                modal.style.display = "block"; 
            }
            
           clotureValid.addEventListener("click", (event) => {

                if (!validateCloture(startDateTraitement, endDateTraitement)) {
                    event.preventDefault();
                }
                else {
                    const commentaireValue = $('#commentaireCloture').val();
                    const dateFinCloture = $('#dateFinCloture').val();
                    const dataToSend = { commentaire: commentaireValue, dateFinCloture: dateFinCloture };

                    const confirmation = confirm("Êtes-vous sûr de vouloir clôturer ce traitement ?");

                    if (confirmation) {
                        $.ajax({
                            url: '/traitment_cloture/' + traitementId,
                            method: 'POST', 
                            data: dataToSend,
                            success: function () {
                                window.location.reload();
                            },
                            error: function () {
                                alert("Erreur dans la requête");
                                window.location.reload();
                            }
                        });

                        modal.style.display = "none"; 
                    }
                }
            });

        });

        

        // deplacer et fermer le modal Edit traitement
        const editModal = document.getElementById("editModal");
        const closeEdit = document.getElementById("closeEdit");

        closeEdit.addEventListener("click", () => {
            editModal.style.display = "none"; 
            info.event.setProp('backgroundColor', '');
        });

        window.addEventListener('click', function (event) {
            if (event.target === editModal) {
                editModal.style.display = "none"; 
                info.event.setProp('backgroundColor', '');
            }
        });


        editButton.addEventListener("click", () => {
            generalModal.style.display = "none";


            const modal = document.getElementById("editModal");
            const etapeSelect = document.getElementById("new_etape");
            const caisseExploitSelect = document.getElementById("new_caisse_exploit");
            const caisseSelect = document.getElementById("new_caisse");
            const datedebutInput = document.getElementById("new_datedebut");
            const datefinInput = document.getElementById("new_datefin");
            const nbreDossierInput = document.getElementById("new_nbredossier");
            const processContainers = document.querySelectorAll(".process-container");
            const vmContainers = document.querySelectorAll(".vm-container");
            const scenarioSelect = document.getElementById("new_scenario");
            const scenarioLabel = document.getElementById("new_scenarioLabel");
            const poolSelect = document.getElementById("new_pool");
            const poolSelect1 = document.getElementById("new_pool1");
            const VmList = document.getElementById("new_VmList");
            const processList = document.getElementById("new_processList");
            const secondPool = document.getElementById("new_secondPool");
            const addPool = document.getElementById("new_addPool");
            const deletePool = document.getElementById("new_deletePool");
            const tempsTraitementContainer = document.getElementById("new_tempsTraitementContainer");
            const checkboxPool = document.getElementById("new_checkboxPool");
            const checkboxPool1 = document.getElementById("new_checkboxPool1");
            const vmCheckboxes = document.querySelectorAll('.vm-checkbox1');
            const processCheckboxes = document.querySelectorAll('.process-checkbox1');

            const selectAllCheckbox = document.getElementById('new_selectAllCheckbox');
            const selectAllVmCheckbox = document.getElementById('new_selectAllVm');

            selectAllCheckbox.checked = false;
            selectAllVmCheckbox.checked = false;
            
            // RECUPERER LES VALEURS ET LES INJECTER DANS LE FORM
            const selectedEvent = info.event.extendedProps;

            if (selectedEvent) {
                modal.style.display = "block";

                const caisseExploitValue = selectedEvent.caisseExploitId;
                const ifExistCaisseExploit = caisseExploitSelect.querySelector(`option[value="${caisseExploitValue}"]`);

                if (ifExistCaisseExploit) {
                    // Sélectionner l'option
                    ifExistCaisseExploit.selected = true;

                    // Désactiver le champ de sélection
                    caisseExploitSelect.disabled = true;
                }


                const caisseValue = selectedEvent.caisseId;
                const ifExistCaisse = caisseSelect.querySelector(`option[value="${caisseValue}"]`);

                if (ifExistCaisse) {
                    // Sélectionner l'option
                    ifExistCaisse.selected = true;

                    // Désactiver le champ de sélection
                    caisseSelect.disabled = true;
                }


                // Récupérer les IDs des pools du traitement depuis resourceId
                const poolId = selectedEvent['poolId'];

                // Afficher le premier ID de poolIds dans le premier select
                const firstPoolId = poolId[0];
                if (poolId.length > 0) {
                    const ifExistFirstPool = poolSelect.querySelector(`option[value="${firstPoolId}"]`);
                    if (ifExistFirstPool) {
                        ifExistFirstPool.selected = true;
                    }

                }

                // Afficher le deuxième ID de poolIds dans le deuxième select
                const secondPoolId = poolId[1];
                if (secondPoolId) {
                    if (poolId.length > 1) {
                        const ifExistSecondPool = poolSelect1.querySelector(`option[value="${secondPoolId}"]`);
                        if (ifExistSecondPool) {
                            ifExistSecondPool.selected = true;
                        }
                    }
                } else {
                    const ifExistSecondPool = poolSelect1.querySelector(`option[value="Tous"]`);
                    if (ifExistSecondPool) {
                        ifExistSecondPool.selected = true;
                    }
                }



                const scenarioId = selectedEvent.scenarioId;
                const ifScenarioExist = scenarioSelect.querySelector(`option[value="${scenarioId}"]`);

                if (ifScenarioExist) {
                    // Sélectionner l'option
                    ifScenarioExist.selected = true;
                }

                
                const processIds = selectedEvent['processId'];
                const processCheckboxes1 = document.querySelectorAll(`[data-scenario="${scenarioId}"] .process-checkbox1`);
                
                processCheckboxes1.forEach(checkbox => {
                    const checkboxValue = checkbox.value.toString();
                    for (let i = 0; i < processIds.length; i++) {
                        const processIdsValue = processIds[i].toString();

                        if (processIdsValue === checkboxValue) {
                            checkbox.checked = true;
                            break;
                        }
                    }
                });
                
                const vmIds = selectedEvent['VmId'];
                const vmCheckboxes1 = document.querySelectorAll(`[data-pool="${firstPoolId}"] .vm-checkbox1, [data-second-pool="${secondPoolId}"] .vm-checkbox1`);
                
                vmCheckboxes1.forEach(checkbox => {
                    const checkboxValue = checkbox.value.toString();;
                    for (let i = 0; i < vmIds.length; i++) {
                        const vmIdsValue = vmIds[i].toString();;

                        if (vmIdsValue === checkboxValue) {
                            checkbox.checked = true;
                            break; 
                        }
                    }
                });
                
                
                


                const etapeValue = selectedEvent.etape;
                const ifExistEtape = etapeSelect.querySelector(`option[value="${etapeValue}"]`);

                if (ifExistEtape) {
                    // Sélectionner l'option
                    ifExistEtape.selected = true;

                    // Désactiver le champ de sélection
                    etapeSelect.disabled = true;
                }



                // Récupérer la valeur du nombre de dossiers du traitement
                const nbreDossierValue = selectedEvent.nbrDossier;

                // Injecter la valeur dans le champ input
                nbreDossierInput.value = nbreDossierValue;


                const TraitementStart = info.event.start;
                const TraitementEnd = info.event.end;
                // Récupérer les valeurs des dates de début et de fin du traitement
                const datedebutValue = moment(TraitementStart).format("YYYY-MM-DDTHH:mm");
                const datefinValue = moment(TraitementEnd).format("YYYY-MM-DDTHH:mm");

                // Injecter les valeurs dans les champs input
                datedebutInput.value = datedebutValue;
                datefinInput.value = datefinValue;

            } else {
                console.error('Traitement non trouvé.');
            }

            if (poolSelect1.value !== "Tous") {
                addPool.style.display = "none";
                secondPool.style.display = "";
                deletePool.style.display = "";
            } else {
                addPool.style.display = "";
                secondPool.style.display = "none";
                deletePool.style.display = "none";
            }


            if(poolSelect.value !== "Tous") {
                scenarioLabel.style.display = "";
                scenarioSelect.style.display = "";

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
            }

            if(scenarioSelect.value !== "Tous") {
                processList.style.display = "";
                VmList.style.display = "";

                const selectedProcess = scenarioSelect.value;

                processContainers.forEach(function(container) {
                    const scenarioId = container.getAttribute("data-scenario");
        
                    if (selectedProcess === scenarioId) {
                        container.style.display = "block";
                    } else {
                        container.style.display = "none";
                    }
                });

                    const etapeLabel = document.getElementById("new_etapeLabel");
                    const selecteLabel = document.getElementById("new_etape");
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

            // EVENEMENTS DE CHANGEMENT

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
        
                nbreDossierInput.value = '';
        
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

                    var WeekendsAndHolidays = document.getElementById("new_WeekendsAndHolidays");
                    var vmMax = document.getElementById("vmMax");

                    if (vmMax) {
                        vmMax.innerHTML = "";
                    }

                    if (WeekendsAndHolidays) {
                        WeekendsAndHolidays.innerHTML = "";
                    }

                    // si le scénario contient puma alors afficher :
                    const etapeLabel = document.getElementById("new_etapeLabel");
                    const selecteLabel = document.getElementById("new_etape");
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





            // Cocher toutes les checkboxes et vms
            var selectAllProcessCheckbox = document.getElementById("new_selectAllCheckbox");

            selectAllProcessCheckbox.addEventListener("change", function (event) {
                var checkbox = event.target;

                var selectedProcess = scenarioSelect.value;
                var processCheckboxes = document.querySelectorAll(`[data-scenario="${selectedProcess}"] .process-checkbox1`);

                processCheckboxes.forEach(function (processCheckbox) {
                    processCheckbox.checked = checkbox.checked;
                });

                if (!checkbox.checked) {

                    processCheckboxes.forEach(function (processCheckbox) {
                        processCheckbox.checked = false;
                    });
                }
            });


            var selectAllVmcheckbox = document.getElementById("new_selectAllVm");



            selectAllVmcheckbox.addEventListener("change", function (event) {
                var checkbox = event.target;
                var selectedVm = poolSelect.value;
                var vmCheckboxes;
            
                if (poolSelect1.value !== "Tous") {
                    var selectedVm1 = poolSelect1.value;
                    vmCheckboxes = document.querySelectorAll(`[data-pool="${selectedVm}"] .vm-checkbox1, [data-second-pool="${selectedVm1}"] .vm-checkbox1`);
                } else {
                    vmCheckboxes = document.querySelectorAll(`[data-pool="${selectedVm}"] .vm-checkbox1`);
                }

                vmCheckboxes.forEach(function (vmCheckbox) {
                    vmCheckbox.checked = checkbox.checked;
                });

                if (!checkbox.checked) {
                    // Décocher toutes les VM si la case "Select All" est décochée
                    vmCheckboxes.forEach(function (vmCheckbox) {
                        vmCheckbox.checked = false;
                    });
                }
            });

        });

        const saveButtonEdit = document.getElementById("saveButtonEdit");    

        saveButtonEdit.addEventListener("click", (event) => {
            if (!validateForm()) {
                event.preventDefault();
            } else {
                sendRequest();
            }
        });

        function sendRequest() {
            const newCaisse = document.getElementById('new_caisse').value;
            const newCaisseExploit = document.getElementById('new_caisse_exploit').value;
            const newScenario = document.getElementById('new_scenario').value;
            const newNbredossier = document.getElementById('new_nbredossier').value;
            const newDatedebut = document.getElementById('new_datedebut').value;
            const newDatefin = document.getElementById('new_datefin').value;
            const newEtape = document.getElementById('new_etape').value;
        
            const selectedPoolValues = [];
            const select1 = document.getElementById("new_pool");
            const select2 = document.getElementById("new_pool1");
            
            selectedPoolValues.push(select1.value);
            selectedPoolValues.push(select2.value);
            
            // Récupérer les valeurs des cases cochées pour les VMs et les processus
            const newVm = Array.from(document.querySelectorAll('input[name="new_vm[]"]:checked')).map(checkbox => checkbox.value);
            const newProcess = Array.from(document.querySelectorAll('input[name="new_process[]"]:checked')).map(checkbox => checkbox.value);
        
            const dataToSend = {
                newCaisse: newCaisse,
                newCaisseExploit: newCaisseExploit,
                newPool: selectedPoolValues,
                newScenario: newScenario,
                newNbredossier: newNbredossier,
                newDatedebut: newDatedebut,
                newDatefin: newDatefin,
                newEtape: newEtape,
                newVm: newVm,
                newProcess: newProcess
            };
        
            $.ajax({
                url: '/traitement_edit/' + traitementId,
                method: 'POST', 
                data: dataToSend,
                success: function () {
                    window.location.reload();
                },
                error: function () {
                    sendRequest();
                }
            });
        }
        
        function validateForm() {
            var valid = true;
    
            const caisse = document.getElementById("new_caisse");
            if (caisse.value === "Tous") {
                alert("Veuillez choisir une caisse de traitement.");
                valid = false;
            }

            const caisse_exploit = document.getElementById("new_caisse_exploit");
            if (caisse_exploit.value === "Tous") {
                alert("Veuillez choisir une caisse exploitante.");
                valid = false;
            }

            const pool = document.getElementById("new_pool");
            if (pool.value === "Tous") {
                alert("Veuillez choisir une pool.");
                valid = false;
            }

            const scenario = document.getElementById("new_scenario");
            if (scenario.value === "Tous") {
                alert("Veuillez choisir un scénario.");
                valid = false;
            }

            const processCheckboxes = document.querySelectorAll('.process-checkbox1');
            var isProcessChecked = Array.from(processCheckboxes).some(checkbox => checkbox.checked);
    
            if (isProcessChecked === false) {
                alert("Vous devez sélectionner au moins un process.");
                valid = false;
            }

            const vmCheckboxes = document.querySelectorAll('.vm-checkbox1');
            var isVmChecked = Array.from(vmCheckboxes).some(checkbox => checkbox.checked);
    
            if (isVmChecked === false) {
                alert("Vous devez sélectionner au moins une VM.");
                valid = false;
            }
    
            var secondPoolCheckboxes = document.querySelectorAll('.vm-container[data-second-pool] .vm-checkbox1');
            var isChecked = Array.from(secondPoolCheckboxes).some(function (checkbox) {
                return checkbox.checked;
            });

            const poolSelect1 = document.getElementById("new_pool1");
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
    
            const dateDebut = document.getElementById("new_datedebut");
            const dateFin = document.getElementById("new_datefin");
    
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
   
            return valid; 
        }





        deleteButton.addEventListener("click", () => {
            generalModal.style.display = "none";

            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer ce(s) traitement(s) ? ");

            if (confirmation) {
                $.ajax({
                    url: '/traitement_delete/' + traitementId,
                    method: 'GET', 
                    success: function () {
                        window.location.reload();
                    },
                    error: function () {
                        alert("Erreur dans la requête");
                    }
                });
            }
        });    

    }
    
    function validateError(startDateTraitement, endDateTraitement) {
        const commentaireError = document.getElementById("commentaireError");
        const dateFinErreur = document.getElementById("dateFinErreur");

        var valid = true;

        if (commentaireError.value.trim() === '' || commentaireError.value.length === 0 || commentaireError.value.length > 300
        ) {
            alert("Veuillez écrire un commentaire entre 1 et 300 caractères.");
            valid = false;
        }

        function isValidDateTime(dateTimeString) {
            // Format de date et heure local (ISO 8601)
            const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        
            // Vérifier si la chaîne de date correspond au format
            return dateTimeRegex.test(dateTimeString);
        }

        if (new Date(dateFinErreur.value) <= startDateTraitement) {
            alert("Veuillez entrer une date supérieur à celle du début du traitement.");
            valid = false;
        }

        if (!isValidDateTime(dateFinErreur.value)) {
            alert("Veuillez entrer une date de fin valide.");
            valid = false;
        }

        return valid;
    }

    function validateCloture(startDateTraitement, endDateTraitement) {
        const commentaireCloture = document.getElementById("commentaireCloture");
        const dateFinCloture = document.getElementById("dateFinCloture");

        var valid = true;

        if (commentaireCloture.value.trim() === '' || commentaireCloture.value.length === 0 || commentaireCloture.value.length > 300
        ) {
            alert("Veuillez écrire un commentaire entre 1 et 300 caractères.");
            valid = false;
        }

        if (new Date(dateFinErreur.value) <= startDateTraitement) {
            alert("Veuillez entrer une date supérieur à celle du début du traitement.");
            valid = false;
        }


        function isValidDateTime(dateTimeString) {
            // Format de date et heure local (ISO 8601)
            const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        
            // Vérifier si la chaîne de date correspond au format
            return dateTimeRegex.test(dateTimeString);
        }
    
        if (!isValidDateTime(dateFinCloture.value)) {
            alert("Veuillez entrer une date de fin valide.");
            valid = false;
        }

        return valid;
    }
    
    
































































































    const calendarEl = document.getElementById('calendar');
    
    // definir une variable qui va recupérer la dernière vue utilisé
    var lastView = localStorage.getItem("fcDefaultView") || 'resourceTimelineDay';
    var lastDate = localStorage.getItem("fcLastDate") || new Date().toISOString().slice(0, 10);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        timeZone: 'local',
        initialDate: lastDate,
        headerToolbar: {
            left: 'today prev,next traitmentButton annoncesButton',
            center: 'title',
            right: 'resourceTimelineDay resourceTimelineWeek resourceTimelineYear listWeek'
        },
        initialView: lastView,
        filterResourcesWithEvents: true,
        aspectRatio: 1.6,
        views: {

            listWeek: {
                buttonText: 'Liste',
            },

            resourceTimeGridYear: {
                type: 'resourceTimeGrid',
                duration: { year: 1 },
                buttonText: 'Année',
                slotDuration: { months: 1 },
                slotLabelInterval: { months: 1 },
                slotLabelFormat: { month: 'long' }, 
                allDaySlot: false,
            },
            
            resourceTimelineDay: {
                buttonText: 'Jour',
                scrollTime: '00:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    meridiem: false,
                },
            },

            resourceTimelineWeek: {
                slotDuration: { days: 1 },
                firstDay: 1,
                slotLabelFormat: { weekday: 'long', day: '2-digit' }
            }, 
        
            
            resourceTimelineYear: {
                slotLabelFormat: { day: 'numeric', month: 'numeric' }, 
            }                
        },
        editable: false,
        resourceAreaHeaderContent: 'Ressource(s)',
        customButtons: {
            traitmentButton: {
                text: 'Traitement',
                click: function () {
                    fetch('/verify_role')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur lors de la requête');
                        }
                        return response.json(); 
                    })
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById("myModal");
                            const closeButton = document.getElementById("closeCreate");
                            modal.style.display = "block";

                            closeButton.addEventListener("click", () => {
                                modal.style.display = "none";
                            });
                
                            window.addEventListener('click', function (event) {
                                if (event.target === modal) {
                                    modal.style.display = "none";
                                }
                            });
                        } else {
                            console.error(data.message); 
                            alert("Vous ne pouvez pas accéder à cette fonctionnalité.");
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });                
                },
            },
            
            annoncesButton: {
                text: 'Annonces',
                click: function() {
                    const popup = document.getElementById("popup");
                    const closeButton = document.getElementById('close-button');

                    if(popup) {
                        popup.style.display = "block";

                        closeButton.addEventListener("click", () => {
                            popup.style.display = "none"; 
                        });
                    } else {
                        alert("Aucune annonce disponible.");
                    }
                
                },
            },

            exportCSV: {
                text: 'Export',
                click: function() {
                    exportCSV();
                },
            }
        },



        eventDidMount: function(info) {
            // Ajouter un titre (tooltip) à chaque case du jour férié
            info.el.setAttribute('title', info.event.title);
        },




        eventClick: function(info) {
            // Empêcher les jours fériés d'être cliquable
            if (!info.event.end) {
                return;
            }
            
            fetch('/verify_role')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la requête');
                }
                return response.json(); 
            })
            .then(data => {
                if (data.success) {
                    if (info.event.extendedProps.priority) {

                        const modal = document.getElementById("annoncesModal");
                        const closeButton = document.getElementById("closeAnnonces");
                        modal.style.display = "block";
            
                        const title = document.getElementById("titleAnnonces");
                        title.innerHTML = "<h4>Gérer l'annonce</h4>";

                        closeButton.addEventListener("click", () => {
                            modal.style.display = "none"; 
                        });

                        window.addEventListener('click', function (event) {
                            if (event.target === modal) {
                                modal.style.display = "none"; 
                            }
                        });

                        const editButtonAnnonces = document.getElementById("editButtonAnnonces");
                        const deleteButtonAnnonces = document.getElementById("deleteButtonAnnonces");

                        editButtonAnnonces.addEventListener("click", () => {
                            $.ajax({
                                url: '/message/edit/' + info.event.id,
                                method: 'GET',
                                success: function () {
                                    window.location.href = 'message/edit/' + info.event.id;
                                },
                                error: function () {
                                    alert("Erreur dans la requête");
                                }
                            });
                        });
                

                        deleteButtonAnnonces.addEventListener("click", () => {
                            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette annonce ? ");
                
                            if (confirmation) {
                                $.ajax({
                                    url: '/message/delete/' + info.event.id,
                                    method: 'GET', 
                                    success: function () {
                                        window.location.reload();
                                        alert("Annonce supprimée.");
                                    },
                                    error: function () {
                                        alert("Erreur dans la requête");
                                    }
                                });
                            }
                        });  

                    } else {
                        info.event.setProp('backgroundColor', '#FF6F00');

                        const modal = document.getElementById("generalModal");
                        const closeButton = document.getElementById("closeGeneral");
                        modal.style.display = "block";
            
                        const title = document.getElementById("title");
                        title.innerHTML = "<h4>Traitement n°" + info.event.id + "</h4>";

                        closeButton.addEventListener("click", () => {
                            modal.style.display = "none"; 
                            info.event.setProp('backgroundColor', '');
                        });
            
                        window.addEventListener('click', function (event) {
                            if (event.target === modal) {
                                modal.style.display = "none"; 
                                info.event.setProp('backgroundColor', '');
                            }
                        });
                        
                        openModal(info);
                    }
                } else {
                    console.error(data.message); 
                    alert("Vous ne pouvez pas accéder à cette fonctionnalité.");
                }
            })
            .catch(error => {
                console.error(error);
            });                
        },



        eventMouseEnter: function (info) {

            var currentView = info.view.type;
            if (currentView === 'listWeek') {
                return;
            }

            info.el.style.cursor = "pointer";

            var event = info.event;

            info.event.setProp('backgroundColor', '#FF6F00');

            if (event.title != null) {
                var content = '<b style= "color:#81BDEC;">' + event.title + '</b><br><br>'; 
            }

            if (event.extendedProps.etape != null) {
                content += '<b style="color:#DF3E3E; text-transform: uppercase;">' + event.extendedProps.etape + '</b><br>';
            }

            if (event.id != null && event.extendedProps.poolName != null) {
                content += '<b>Traitement n°' + event.id + '</b><br>';
            }

            if (event.start != null) {
                content += '<b>Début</b> : ' + event.start.toLocaleString() + '<br>';
            }

            if (event.end != null) {
                content += '<b>Fin</b> : ' + event.end.toLocaleString() + '<br>';
            }

            if (event.extendedProps.priority != null) {
                content += '<br><b>Priorité</b> : ' + event.extendedProps.priority + '<br>';
            }            

            if (event.extendedProps.content != null) {
                content += '<br><b>Contenu</b> : ' + event.extendedProps.content + '<br>';
            }

            if (event.extendedProps.created != null) {
                content += '<br><b>Ajouté le</b> : ' + event.extendedProps.created + '<br>';
            }

            if (event.extendedProps.autor != null) {
                content += '<br><b>Auteur</b> : ' + event.extendedProps.autor + '<br>';
            }

            if (event.extendedProps.poolName != null) {
                content += '<b>Pool(s)</b> : ' + event.extendedProps.poolName + '<br>';
            }

            if (event.extendedProps.VmName != null) {
                content += '<b>VM(s)</b> : ' + event.extendedProps.VmName + '<br>';
            }

            if (event.extendedProps.caisseName != null) {
                content += '<b>Caisse de traitement</b> : ' + event.extendedProps.caisseName + ' (' + event.extendedProps.caisseNum + ')<br>';
            }

            if (event.extendedProps.caisseExploitName != null) {
                content += '<b>Caisse exploitante</b> : ' + event.extendedProps.caisseExploitName + ' (' + event.extendedProps.caisseExploitNum + ')<br>';
            }

            if (event.extendedProps.scenarioName != null) {
                content += '<b>Scénario</b> : ' + event.extendedProps.scenarioName + '<br>';
            }

            if (event.extendedProps.processName != null) {
                content += '<b>Process</b> : ' + event.extendedProps.processName + '<br>';
            }

            if (event.extendedProps.nbrDossier != null) {
                content += '<b>Nbre de dossier(s)</b> : ' + event.extendedProps.nbrDossier + '<br>';
            }

            if (event.extendedProps.periodicity != null) {
                content += '<b>Périodicité</b> : ' + event.extendedProps.periodicity + '<br>';
            }

            if (event.extendedProps.etat != null) {
                content += '<b>État</b> : ' + '' + '<b style= "color:#ECCC81;">' + event.extendedProps.etat + '</b><br>';
            }

            if (event.extendedProps.errorComment != null) {
                content += '<b>Commentaire d\'erreur</b> : ' + event.extendedProps.errorComment + '<br>';
            }

            if (event.extendedProps.dateFinError != null) {
                content += '<b>Mise en erreur le</b> : ' + event.extendedProps.dateFinError + '<br>';
            }

            if (event.extendedProps.clotureComment != null) {
                content += '<b>Commentaire de clôture</b> : ' + event.extendedProps.clotureComment + '<br>';
            }

            if (event.extendedProps.dateFinCloture != null) {
                content += '<b>Mise en clôture le</b> : ' + event.extendedProps.dateFinCloture + '<br>';
            }

            // Utilisez tippy pour afficher l'infobulle

            var tooltip = tippy(info.el, {
                content: content,
                allowHTML: true,
            });

            tooltip.show();
        },
        



        eventMouseLeave: function (info) {
            // Masquer les informations de l'événement lorsque la souris quitte
            var tooltip = info.el._tippy;
            if (tooltip) {
                info.event.setProp('backgroundColor', '');
                tooltip.destroy();
            }
        },






        eventContent: function(info) {

            const event = info.event;
            const etat = event.extendedProps.etat;
            const multiplesPools = event.extendedProps.poolName;

            const eventElement = document.createElement('div');
            eventElement.className = 'calendar-event';

            // Si le traitement a plus d'une pool, alors mettre un code couleur
            if (multiplesPools && multiplesPools.length >= 2) {
                eventElement.style.border = "2px dashed #073077";
                eventElement.style.backgroundColor = getColorForCaisseExploitName(etat);
            } else {
                eventElement.style.backgroundColor = getColorForCaisseExploitName(etat);
            }

            const annonces = event.extendedProps.priority;
            if (annonces) {
                eventElement.style.backgroundColor = getColorForAnnouncement(annonces);
            }

            const content = document.createElement('div');
            content.className = 'event-content';
            content.innerHTML = '<b>' + event.title + '</b>';

            eventElement.appendChild(content);

            return { domNodes: [eventElement] };
        },

















        datesSet: function (dateInfo) {

            var titleElement = calendarEl.querySelector('.fc-toolbar-title');
            var selectDateModal = document.getElementById('selectDateModal');
            var closeDateModal = document.getElementById('closeDateModal');
            var selectDateCalendar = document.getElementById('selectDateCalendar');

            titleElement.addEventListener('mouseover', function () {
                titleElement.setAttribute('title', 'Sélectionner une date');
                titleElement.style.cursor = 'pointer';
            });            

            titleElement.addEventListener('click', function () {
                selectDateModal.style.display = "block"; 

                var currentDate = calendar.getDate(); // Obtenir la date actuelle affichée dans le calendrier
                var formattedDate = moment(currentDate).format('YYYY-MM-DD');
        
                selectDateCalendar.value = formattedDate;

                closeDateModal.addEventListener('click', function () { 
                    selectDateModal.style.display = "none"; 
                });

                window.addEventListener('click', function (event) {
                    if (event.target === selectDateModal) {
                        selectDateModal.style.display = "none"; 
                    }
                });


            });

            if (selectDateCalendar) {
                selectDateCalendar.addEventListener('change', function () {
                    var selectedDate = selectDateCalendar.value;
                    selectDateModal.style.display = "none";
        
                    calendar.gotoDate(selectedDate);
                });
            }

                
            var currentView = dateInfo.view.type;
            var currentDate = dateInfo.startStr;
            var endDate = dateInfo.endStr; 

            // Vérifier si le calendrier n'est pas en mode 'resourceTimelineYear'
            if (currentView === 'resourceTimelineYear') {
                // Supprimer les événements de jours fériés si la vue est 'resourceTimelineYear'
                var holidaysEvents = calendar.getEvents().filter(function(event) {
                    return event.backgroundColor === '#8E0B0B'; // Retourne true si la couleur de fond est celle des jours fériés
                });
                holidaysEvents.forEach(function(event) {
                    event.remove(); // Supprimer chaque événement de jours fériés
                });

            } else {
                updateEvents();
            }

            localStorage.setItem("fcDefaultView", currentView);
            localStorage.setItem("fcLastDate", currentDate);

            const statistiques = document.getElementById("statistiques");
            const processUtilizationChart = document.getElementById("processUtilizationChart");
            const vmUtilizationChart = document.getElementById("vmUtilizationChart");

            let isProcessChartVisible = true;
            
            // AFFICHAGE CHART POOL SINON CHART PROCESS
            if (sessionStorage.getItem("isProcessChartVisible") === "false") { 

                vmUtilizationChart.style.display = 'none';
                processUtilizationChart.style.display = 'block';
                getProcessPercentage(currentView, currentDate, endDate);
            }
            else 
            {
                processUtilizationChart.style.display = 'none';
                vmUtilizationChart.style.display = 'block';
                getPoolPercentage(currentView, currentDate, endDate);
            }

            // Fonction quand on veut changer la vue avec le bouton Statistiques
            function changeChart() {
                if(isProcessChartVisible) {

                    vmUtilizationChart.style.display = 'none';
                    document.getElementById('prevPageButton1').style.display = "none";
                    document.getElementById('nextPageButton1').style.display = "none";
                    
                    processUtilizationChart.style.display = 'block';
                    getProcessPercentage(currentView, currentDate, endDate);

                    isProcessChartVisible = !isProcessChartVisible;
                    sessionStorage.setItem("isProcessChartVisible", isProcessChartVisible.toString());
                } else {
                    isProcessChartVisible = !isProcessChartVisible;
                    sessionStorage.setItem("isProcessChartVisible", isProcessChartVisible.toString());
                    window.location.reload();
                }
            }

            // On recupère les évenements au click du bouton
            statistiques.addEventListener("click", changeChart);
        },
    });






























































    // Récupérer l'URL actuelle
    var url = window.location.href;

    // Diviser l'URL en parties en utilisant le caractère '?'
    var parts = url.split('?');

    // Initialiser des variables pour stocker l'ID et la date
    var id;
    var dateDebut;

    // Vérifier s'il y a des paramètres d'URL après le caractère '?'
    if (parts.length > 1) {
        // Diviser les paramètres en paires clé-valeur en utilisant le caractère '&'
        var params = parts[1].split('&');

        // Parcourir les paramètres pour trouver ceux contenant 'id' et 'dateDebut'
        for (var i = 0; i < params.length; i++) {
            var param = params[i].split('=');
            // Vérifier si le paramètre est 'id'
            if (param[0] === 'id') {
                // Récupérer et stocker la valeur de l'ID
                id = param[1];
            }
            // Vérifier si le paramètre est 'dateDebut'
            else if (param[0] === 'dateDebut') {
                // Récupérer et stocker la valeur de la dateDebut
                dateDebut = decodeURIComponent(param[1]);
            }
        }
    }

    function fetchEventData(id, dateDebut) {
        if (id && dateDebut) {
            $.ajax({
                type: 'GET',
                url: url,
                success: function() {
                    calendar.changeView('resourceTimelineDay');
                    calendar.gotoDate(dateDebut);
                    var event = calendar.getEventById(id);
                    if (event) {
                        calendar.trigger('eventClick', { event: event });

                        var baseUrl = window.location.origin;
                        window.history.replaceState(null, null, baseUrl);

                    } else {
                        // Relancez la requête AJAX pour récupérer les données mises à jour
                        fetchEventData(id, dateDebut);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }

    // Utilisation de la fonction
    fetchEventData(id, dateDebut);











    var ajaxRequestInProgress = false;
    var ajaxRequestInProgress1 = false;

    // Fonction pour effectuer la requête Ajax et obtenir les vms
    function getPoolPercentage(currentView, currentDate, endDate) {

        if (ajaxRequestInProgress1) {
            return;
        }
    
        ajaxRequestInProgress1 = true;

        $.ajax({
            type: "GET",
            url: "/get_all_pools",
            data: {
                currentView: currentView,
                currentDate: currentDate,
                endDate: endDate,
            },
            success: function (poolData) {
                // Créez un tableau pour stocker les noms des pools
                var poolsNames = [];
                 // Créez un tableau pour stocker les vms disponibles
                var vmsNamesAvailable = [];
                // Créez un tableau pour stocker les vms utilisées
                var vmsNamesUsed = [];
                // Créez un tableau pour stocker les données pour le graphique
                var percentageData = [];

                var hoursData = [];

                // Parcourez les données des processus
                poolData.forEach(function (pool) { 

                        poolsNames.push(pool.nom_pool);

                        vmsNamesAvailable.push(pool.vm_available);
                        vmsNamesUsed.push(pool.vm_used);
    
                        switch (currentView) {
                            case 'resourceTimelineDay':
                                hoursData.push(pool.totalDurationDayByPoolInHours);
                                percentageData.push(pool.percentageDay);
                                break;
                            case 'resourceTimelineWeek':
                            case 'listWeek':
                                hoursData.push(pool.totalDurationWeekByPoolInHours);
                                percentageData.push(pool.percentageWeek);
                                break;
                            case 'dayGridMonth':
                                hoursData.push(pool.totalDurationMonthByPoolInHours);
                                percentageData.push(pool.percentageMonth);
                                break;
                            case 'resourceTimelineYear':
                                hoursData.push(pool.totalDurationYearByPoolInHours);
                                percentageData.push(pool.percentageYear);
                                break;
                            default:
                                hoursData.push(0);
                                percentageData.push(0);
                                break;
                        }
                });

                // Appelez la fonction pour créer le graphique avec les noms des processus
                createChartPools(poolsNames, percentageData, currentView, hoursData, vmsNamesUsed, vmsNamesAvailable);
                ajaxRequestInProgress1 = false;
            },
            error: function (error) {
                console.error(error);
                ajaxRequestInProgress1 = false;

            }
        });
    }

    // Fonction pour effectuer la requête Ajax et obtenir les processus
    function getProcessPercentage(currentView, currentDate, endDate) {

        if (ajaxRequestInProgress) {
            return;
        }
    
        ajaxRequestInProgress = true;

        $.ajax({
            type: "GET",
            url: "/get_all_processes",
            data: {
                currentView: currentView,
                currentDate: currentDate,
                endDate: endDate,
            },
            success: function (processData) {
                // Créez un tableau pour stocker les noms des processus
                var processNames = [];
                // Créez un tableau pour stocker les % pour le graphique
                var percentageData  = [];
                // Créez un tableau pour stocker les heures pour le graphique
                var hoursData  = [];
                
                // Parcourez les données des processus
                processData.forEach(function (process) { 

                    processNames.push(process.nom_process);

                    // Ajoutez le pourcentage correspondant à la vue actuelle
                    switch (currentView) {
                        case 'resourceTimelineDay':
                            hoursData.push(process.totalDurationDayByProcessInHours);
                            percentageData.push(process.percentageDay);
                            break;
                        case 'resourceTimelineWeek':
                        case 'listWeek':
                            hoursData.push(process.totalDurationWeekByProcessInHours);
                            percentageData.push(process.percentageWeek);
                            break;
                        case 'dayGridMonth':
                            hoursData.push(process.totalDurationMonthByProcessInHours);
                            percentageData.push(process.percentageMonth);
                            break;
                        case 'resourceTimelineYear':
                            hoursData.push(process.totalDurationYearByProcessInHours);
                            percentageData.push(process.percentageYear);
                            break;
                        default:
                            hoursData.push(0);
                            percentageData.push(0);
                            break;
                    }

                });

                // Appelez la fonction pour créer le graphique avec les noms des processus
                createChartProcess(processNames, percentageData, currentView, hoursData);
                ajaxRequestInProgress = false;
            },
            error: function (error) {
                console.error(error);
                ajaxRequestInProgress = false;

            }
        });
    }










    // Déclarer une variable pour stocker l'instance du graphique actuel
    var myChart;

    // Déclarer une variable pour suivre la page actuelle
    var currentPage = 0;

    // Fonction pour créer le graphique avec les noms des processus
    function createChartProcess(processNames, percentageData, currentView, hoursData) {

        // Récupérer l'élément canvas
        var canvas = document.getElementById('processUtilizationChart');

        if (!canvas) {
            console.error("Diagramme non trouvé.");
            return;
        }

        Chart.defaults.color = '#fff';

        // Détruire le graphique actuel s'il existe
        if (myChart) {
            myChart.destroy();
        }

        // Nombre de labels à afficher par page
        const labelsPerPage = 1;

        var messageParagraph = document.getElementById('messageParagraph');

        if (!processNames || processNames.length === 0) {
            messageParagraph.textContent = "Aucun process trouv\u00e9.";
            return; // Ne pas créer de graphique si aucun processus n'est trouvé
        }

        // Calculer le nombre total de pages
        const totalPages = Math.ceil(processNames.length / labelsPerPage);

        // Vérifier si le nombre total de processus est inférieur ou égal à 1
        if (processNames.length <= labelsPerPage) {
            // S'il y a 1 processus ou moins, cacher les boutons de navigation
            document.getElementById('nextPageButton').style.display = 'none';
            document.getElementById('prevPageButton').style.display = 'none';
        } else {
            // S'il y a plus de 1 processus, afficher les boutons de navigation
            document.getElementById('nextPageButton').style.display = 'block';
        }

        function truncateLabel(label, maxLength, percentage) {
            if (label.length > maxLength) {
                label = label.substring(0, maxLength) + '...';
            }
            return label + ' (' + percentage.toFixed(2) + ' %)';
        }
        

        // Calculer l'index de début et de fin pour la page actuelle
        const startIndex = currentPage * labelsPerPage;
        const endIndex = Math.min(startIndex + labelsPerPage, processNames.length);

        // Utiliser uniquement les pourcentages pour la page actuelle
        const pagePercentageData = percentageData.slice(startIndex, endIndex);

        // Utiliser uniquement les processus pour la page actuelle
        const pageProcessNames = processNames.slice(startIndex, endIndex);

        // Calculer le total des pourcentages pour la page actuelle
        const totalPercentage = pagePercentageData.reduce((acc, percent) => acc + percent, 0);

        if (totalPercentage < 100) {
            const cutoutPercentage = 100 - totalPercentage;

            pagePercentageData.push(cutoutPercentage);
            pageProcessNames.push("Espace libre"); 
        }

        // Utiliser Chart.js pour créer un graphique à secteurs (pie chart)
        var ctx = canvas.getContext('2d');

        // Définir le titre en fonction de la vue actuelle
        var titleText = getTitleTextProcess(currentView);

        // Configurer les options du graphique
        var chartOptions = {
            type: 'pie',
            data: {
                labels: pageProcessNames.map((label, index) => truncateLabel(label, 30, pagePercentageData[index])),
                datasets: [{
                    data: pagePercentageData,
                    hoursData: hoursData.slice(startIndex, endIndex),
                    backgroundColor: ['rgba(255, 99, 132, 0.8)', 'rgba(255, 255, 255, 1)'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 11,
                                weight: 'bold',
                                color: 'white'
                            },
                            boxWidth: 25,
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                        },
                        onClick: null, 
                    },
                    title: {
                        display: true,
                        text: titleText,
                        font: {
                            size: 20,
                        }
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                var hours = context.dataset.hoursData[context.dataIndex];
                                if (totalPercentage < 100 && totalPercentage > 0) {
                                    if (hours !== undefined) {
                                        var days = Math.floor(hours / 24);
                                        var remainingHours = hours % 24;
                                        var hoursString = Math.floor(remainingHours);
                                        var minutesString = Math.round((remainingHours - Math.floor(remainingHours)) * 60);
                                        return 'Temps utilisé : ' + days + ' jour(s), ' + hoursString + ' heure(s) et ' + minutesString + ' minute(s)';
                                    } else {
                                        return "Certains créneaux horaires sont libres.";
                                    }
                                } else if (totalPercentage == 100) {
                                    return "Ce process est occupé à 100%.";
                                }
                                else {
                                    return "Ce process est libre."
                                } 
                            },
                        },
                    },
                },
            },
        };

        // Créer un nouveau graphique
        myChart = new Chart(ctx, chartOptions);

        updateChartAndNavigation(processNames, percentageData, currentView, totalPages, hoursData);
    }

























    // Déclarer une variable pour stocker l'instance du graphique actuel
    var myChartPools;

    // Déclarer une variable pour suivre la page actuelle
    var currentPage = 0;

    // Fonction pour créer le graphique avec les noms des pools
    function createChartPools(poolsNames, percentageData, currentView, hoursData, vmsNamesUsed, vmsNamesAvailable) {

        // Récupérer l'élément canvas
        var canvas = document.getElementById('vmUtilizationChart');

        if (!canvas) {
            console.error("Diagramme non trouvé.");
            return;
        }

        Chart.defaults.color = '#fff';

        // Détruire le graphique actuel s'il existe
        if (myChartPools) {
            myChartPools.destroy();
        }

        // Nombre de labels à afficher par page
        const labelsPerPage = 1;

        var messageParagraph = document.getElementById('messageParagraph');

        if (!poolsNames || poolsNames.length === 0) {
            messageParagraph.textContent = "Aucune pool trouv\u00e9.";
            return; // Ne pas créer de graphique si aucune pool n'est trouvé
        }

        // Calculer le nombre total de pages
        const totalPages = Math.ceil(poolsNames.length / labelsPerPage);

        // Vérifier si le nombre total de pools est inférieur ou égal à 1
        if (poolsNames.length <= labelsPerPage) {
            // S'il y a 1 pools ou moins, cacher les boutons de navigation
            document.getElementById('nextPageButton1').style.display = 'none';
            document.getElementById('prevPageButton1').style.display = 'none';
        } else {
            // S'il y a plus de 1 pools, afficher les boutons de navigation
            document.getElementById('nextPageButton1').style.display = 'block';
        }

        function truncateLabel(label, maxLength, percentage) {
            if (label.length > maxLength) {
                label = label.substring(0, maxLength) + '...';
            }
            return label + ' (' + percentage.toFixed(2) + ' %)';
        }

        // Calculer l'index de début et de fin pour la page actuelle
        const startIndex = currentPage * labelsPerPage;
        const endIndex = Math.min(startIndex + labelsPerPage, poolsNames.length);

        // Utiliser uniquement les pourcentages pour la page actuelle
        const pagePercentageData = percentageData.slice(startIndex, endIndex);

        // Utiliser uniquement les pools pour la page actuelle
        const pagePoolsNames = poolsNames.slice(startIndex, endIndex);

        // Calculer le total des pourcentages pour la page actuelle
        const totalPercentage = pagePercentageData.reduce((acc, percent) => acc + percent, 0);
        const cutoutPercentage = 100 - totalPercentage;

        if (totalPercentage < 100) {
            pagePercentageData.push(cutoutPercentage);
            pagePoolsNames.push("Espace libre"); 
        }

        // Utiliser Chart.js pour créer un graphique à secteurs (pie chart)
        var ctx = canvas.getContext('2d');
        
        canvas.classList.add('chart-canvas');

        // Définir le titre en fonction de la vue actuelle
        var titleText = getTitleTextPool(currentView);

        // Configurer les options du graphique
        var chartOptions = {
            type: 'pie',
            data: {
                labels: pagePoolsNames.map((label, index) => truncateLabel(label, 30, pagePercentageData[index])),
                datasets: [{
                    data: pagePercentageData,
                    hoursData: hoursData.slice(startIndex, endIndex),
                    vmsNamesAvailable: vmsNamesAvailable.slice(startIndex, endIndex),
                    vmsNamesUsed: vmsNamesUsed.slice(startIndex, endIndex),
                    backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(255, 255, 255, 1)'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 11,
                                weight: 'bold',
                                color: 'white'
                            },
                            boxWidth: 25,
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                        },
                        onClick: null, 
                    },
                    title: {
                        display: true,
                        text: titleText,
                        font: {
                            size: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                var hours = context.dataset.hoursData[context.dataIndex];
                            
                                if (totalPercentage < 100 && totalPercentage > 0) {
                                    if (hours !== undefined) {
                                        var days = Math.floor(hours / 24);
                                        var remainingHours = hours % 24;
                                        var hoursString = Math.floor(remainingHours);
                                        var minutesString = Math.round((remainingHours - Math.floor(remainingHours)) * 60);
                                        return 'Temps utilisé : ' + days + ' jour(s), ' + hoursString + ' heure(s) et ' + minutesString + ' minute(s)';
                                    } else {
                                        return "Certains créneaux horaires sont libres.";
                                    }
                                } else if (totalPercentage == 100) {
                                    return "Cette pool est occupée à 100%.";
                                }
                                else {
                                    return "Cette pool est libre."
                                } 

                            },
                        },
                    },
                },
            },
        };

        // Créer un nouveau graphique
        myChartPools = new Chart(ctx, chartOptions);

        updateChartAndNavigation1(poolsNames, percentageData, currentView, totalPages, hoursData, vmsNamesUsed, vmsNamesAvailable);
    }
        























    function updateChartAndNavigation(processNames, percentageData, currentView, totalPages, hoursData) {
        // Récupérez les boutons existants
        const prevButton = document.getElementById('prevPageButton');
        const nextButton = document.getElementById('nextPageButton');

        prevButton.innerHTML = '←';
        nextButton.innerHTML = '→';

        prevButton.style.display = currentPage === 0 ? 'none' : 'block';
        nextButton.style.display = currentPage === totalPages - 1 ? 'none' : 'block';
    
        // Retirer les écouteurs d'événements pour éviter les problèmes de liaison multiple
        document.getElementById('nextPageButton').removeEventListener('click', goToNextPage);
        document.getElementById('prevPageButton').removeEventListener('click', goToPrevPage);
    
        // Gérer le clic sur la flèche pour passer à la page suivante
        document.getElementById('nextPageButton').onclick = goToNextPage;

        // Gérer le clic sur la flèche pour revenir à la page précédente
        document.getElementById('prevPageButton').onclick = goToPrevPage;

    
        function goToNextPage() {
            currentPage = Math.min(currentPage + 1, totalPages - 1);
            createChartProcess(processNames, percentageData, currentView, hoursData);
        }
        
        function goToPrevPage() {
            currentPage = Math.max(currentPage - 1, 0);
            createChartProcess(processNames, percentageData, currentView, hoursData);
        }
        
    }

    function updateChartAndNavigation1(poolsNames, percentageData, currentView, totalPages, hoursData, vmsNamesUsed, vmsNamesAvailable) {
        // Récupérez les boutons existants
        const prevButton1 = document.getElementById('prevPageButton1');
        const nextButton1 = document.getElementById('nextPageButton1');

        prevButton1.innerHTML = '←';
        nextButton1.innerHTML = '→';

        prevButton1.style.display = currentPage === 0 ? 'none' : 'block';
        nextButton1.style.display = currentPage === totalPages - 1 ? 'none' : 'block';
    
        // Retirer les écouteurs d'événements pour éviter les problèmes de liaison multiple
        document.getElementById('nextPageButton1').removeEventListener('click', goToNextPage);
        document.getElementById('prevPageButton1').removeEventListener('click', goToPrevPage);
    
        // Gérer le clic sur la flèche pour passer à la page suivante
        document.getElementById('nextPageButton1').onclick = goToNextPage;

        // Gérer le clic sur la flèche pour revenir à la page précédente
        document.getElementById('prevPageButton1').onclick = goToPrevPage;

    
        function goToNextPage() {
            currentPage = Math.min(currentPage + 1, totalPages - 1);
            createChartPools(poolsNames, percentageData, currentView, hoursData, vmsNamesUsed, vmsNamesAvailable);
        }
        
        function goToPrevPage() {
            currentPage = Math.max(currentPage - 1, 0);
            createChartPools(poolsNames, percentageData, currentView, hoursData, vmsNamesUsed, vmsNamesAvailable);
        }
        
    }










    // Fonction pour obtenir le texte du titre en fonction de la vue actuelle
    function getTitleTextPool(currentView) {
        switch (currentView) {
            case 'resourceTimelineDay':
                return 'Utilisation des pools par jour';
            case 'resourceTimelineWeek':
                return 'Utilisation des pools par semaine';
            case 'dayGridMonth':
                return 'Utilisation des pools par mois';
            case 'resourceTimelineYear':
                return 'Utilisation des pools par ann\u00e9e';
            case 'listWeek':
                return 'Utilisation des pools par semaine';
            default:
                return '';
        }
    }








    // Fonction pour obtenir le texte du titre en fonction de la vue actuelle
    function getTitleTextProcess(currentView) {
        switch (currentView) {
            case 'resourceTimelineDay':
                return 'Utilisation des process par jour';
            case 'resourceTimelineWeek':
                return 'Utilisation des process par semaine';
            case 'dayGridMonth':
                return 'Utilisation des process par mois';
            case 'resourceTimelineYear':
                return 'Utilisation des process par ann\u00e9e';
            case 'listWeek':
                return 'Utilisation des process par semaine';
            default:
                return '';
        }
    }

    
    function getColorForAnnouncement(annonces) {
        switch (annonces) {
            case 'urgent':
                return '#990000';
            case 'important':
                return '#994C00';
            case 'normal':
                return '#009900';
        }
    }


    function getColorForCaisseExploitName(etat) {
        switch (etat) {
            case 'En cours':
                return '#6698CA';
            case 'Clôturé':
                return '#B2B0B0';
            case 'Erreur':
                return '#CC0000';
        }
    }






















































    $.ajax({
        url: '/get_pool_vm_resources',
        method: 'GET',
        success: function (data) {
            const resources = data.map(function (ressources) {
                return {
                    id: ressources.id,
                    title: ressources.title
                };
            });
            
            const resourceOrder = resources.map(resource => resource.title);
    
            calendar.setOption('resources', resources);
            calendar.setOption('resourceOrder', resourceOrder);
        }
    });
    



    





    

    // FILTRER PAR NOM
    var rechercheTraitement = document.getElementById('rechercheTraitement');
    rechercheTraitement.addEventListener('input', function() {
        var rechercheTexte = this.value.toLowerCase(); // Convertir le texte en minuscules pour une comparaison insensible à la casse
        filtrerParNom(rechercheTexte);
        chargerAnnonces();
    });


    // Variables pour stocker les états des cases à cocher modifiées
    var caisseExploitChanged = false;
    var poolVmChanged = false;
    var scenarioChanged = false;

    // Gestionnaire d'événement pour les cases à cocher des caisses d'exploitation
    var caisseExploitCheckboxes = document.querySelectorAll('#caisseExploitCheckboxes input[type="checkbox"]');
    caisseExploitCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            caisseExploitChanged = true;
            updateEvents(); 
        });
    });

    // Gestionnaire d'événement pour les cases à cocher des pools VM
    var poolVmCheckboxes = document.querySelectorAll('#poolVmCheckboxes input[type="checkbox"]');
    poolVmCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            poolVmChanged = true;
            updateEvents();
        });
    });

    // Gestionnaire d'événement pour les cases à cocher des scénarios
    var scenarioCheckboxes = document.querySelectorAll('#scenarioCheckboxes input[type="checkbox"]');
    scenarioCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            scenarioChanged = true;
            updateEvents();
        });
    });

    chargerEvenements().then(filtrerEvenements);

    function updateEvents() {
        // Appeler la requête AJAX pour récupérer les jours fériés
        $.ajax({
            url: '/holidays',
            method: "GET",
            success: function(response) {
                var joursFeries = convertToEvents(response.data);
                calendar.removeAllEventSources(); // Supprimer les événements précédents
                calendar.addEventSource(joursFeries); // Ajouter les nouveaux événements
                calendar.render(); // Rendre à nouveau le calendrier
                if (caisseExploitChanged || poolVmChanged || scenarioChanged) {

                    caisseExploitChanged = false;
                    poolVmChanged = false;
                    scenarioChanged = false;
            
                    filtrerEvenements(); 
                    chargerAnnonces();
                }
            },
            error: function(error) {
                console.error('Erreur lors de la récupération des jours fériés :', error);
            }
        });
    }

    function convertToEvents(joursFeries) {
        var events = [];
        // Vérifier si le calendrier actuel n'est pas 'resourceTimelineYear'
        if (calendar.view.type !== 'resourceTimelineYear') {
            // Convertir les jours fériés en format d'événement accepté par FullCalendar
            Object.keys(joursFeries).forEach(function (date) {
                events.push({
                    title: joursFeries[date],
                    start: date,
                    end: date,
                    allDay: true,
                    backgroundColor: '#8E0B0B',
                    display: 'background'
                });
            });
        }
        return events;
    }


    function chargerEvenements() {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: '/get_traitement_events',
                method: 'GET',
                success: function(responseData) {
                    data = responseData;
                    afficherEvenements(data);
                    chargerAnnonces();
                    resolve(); // Résoudre la promesse une fois le chargement terminé
                },
                error: function(error) {
                    reject(error); // Rejeter la promesse en cas d'erreur
                }
            });
        });
    }


    function afficherEvenements(data) {
        data.forEach(function(traitement) {

        const endDate = moment(traitement.end);
        const formattedEndDate = endDate.format('DD/MM/YYYY');

        let title = `${traitement.numSemaine} - ${traitement.caisseName} (${traitement.caisseNum}) - ${traitement.poolName} - ${traitement.scenarioName} -  ${traitement.etat} - Fin : ${formattedEndDate}`;

        // Vérifier si etape est null avant de l'inclure dans le titre
        if (traitement.etape !== null) {
            title += ` - ${traitement.etape}`;
        }

            const event = {
                id: traitement.id,
                title: title,
                end: traitement.end,
                poolId: traitement.poolId,
                poolName: traitement.poolName,
                VmId: traitement.VmId,
                VmName: traitement.VmName,
                caisseId: traitement.caisseId,
                caisseName: traitement.caisseName,
                caisseNum: traitement.caisseNum,
                caisseExploitId: traitement.caisseExploitId,
                caisseExploitName: traitement.caisseExploitName,
                caisseExploitNum: traitement.caisseExploitNum,
                scenarioId: traitement.scenarioId,
                scenarioName: traitement.scenarioName,
                processId: traitement.processId,
                processName: traitement.processName,
                nbrDossier: traitement.nbrDossier,
                etat: traitement.etat,
                clotureComment: traitement.clotureComment,
                dateFinCloture: traitement.dateFinCloture,
                errorComment: traitement.errorComment,
                dateFinError: traitement.dateFinError,
                etape: traitement.etape,
                periodicity: traitement.periodicity,
            };

            if (Array.isArray(traitement.resourceId) && traitement.resourceId.length > 0) {
                event.resourceIds = traitement.resourceId;
            } else {
                event.resourceId = traitement.resourceId;
            }

            if(traitement.dateFinError) {
                event.dateFinError = traitement.dateFinError;
            }

            if(traitement.dateFinCloture) {
                event.dateFinCloture = traitement.dateFinCloture;
            }

            calendar.addEvent(event);
        });
    
        calendar.render();
    }


    
    function chargerAnnonces() {
        $.ajax({
            url: '/get_annonces_events',
            method: 'GET',
            success: function (data) {
                data.forEach(function (ressources) {
                    const event = {
                        id: ressources.id,
                        title: ressources.title,
                        start: ressources.start,
                        end: ressources.end,
                        priority: ressources.priority,
                        content: ressources.content,
                        autor: ressources.autor,
                        created: ressources.created,
                        resourceId: ressources.ressourceId
                    };
        
                    calendar.addEvent(event);
                });
        
                calendar.render();
            }
        });
    }
    
    























    function filtrerParNom(rechercheTexte) {
        // Effacer les événements actuels du calendrier
        calendar.removeAllEvents();
    
        // Créer une expression régulière à partir du texte de recherche
        const regex = new RegExp(rechercheTexte.toLowerCase());
    
        // Filtrer les événements en fonction du texte de recherche
        for (let i = 0; i < data.length; i++) {
            const traitement = data[i];
            const titreEvenement = (
                `${traitement.caisseName} (${traitement.caisseNum}) - ${traitement.poolName} - ${traitement.scenarioName} - ${traitement.numSemaine}`
            ).toLowerCase();
    
            if (regex.test(titreEvenement)) {
                const endDate = moment(traitement.end);
                const formattedEndDate = endDate.format('DD/MM/YYYY');
                let title = `${traitement.numSemaine} - ${traitement.caisseName} (${traitement.caisseNum}) - ${traitement.poolName} - ${traitement.scenarioName} -  ${traitement.etat} - Fin : ${formattedEndDate}`;

                // Vérifier si etape est null avant de l'inclure dans le titre
                if (traitement.etape !== null) {
                    title += ` - ${traitement.etape}`;
                }
                const event = {
                    id: traitement.id,
                    title: title,
                    start: traitement.start,
                    end: traitement.end,
                    poolId: traitement.poolId,
                    poolName: traitement.poolName,
                    VmId: traitement.VmId,
                    VmName: traitement.VmName,
                    caisseId: traitement.caisseId,
                    caisseName: traitement.caisseName,
                    caisseNum: traitement.caisseNum,
                    caisseExploitId: traitement.caisseExploitId,
                    caisseExploitName: traitement.caisseExploitName,
                    caisseExploitNum: traitement.caisseExploitNum,
                    scenarioId: traitement.scenarioId,
                    scenarioName: traitement.scenarioName,
                    processId: traitement.processId,
                    processName: traitement.processName,
                    nbrDossier: traitement.nbrDossier,
                    etat: traitement.etat,
                    clotureComment: traitement.clotureComment,
                    dateFinCloture: traitement.dateFinCloture,
                    errorComment: traitement.errorComment,
                    dateFinError: traitement.dateFinError,
                    etape: traitement.etape,
                    periodicity: traitement.periodicity,
                };
    
                if (Array.isArray(traitement.resourceId) && traitement.resourceId.length > 0) {
                    event.resourceIds = traitement.resourceId;
                } else {
                    event.resourceId = traitement.resourceId;
                }

                if(traitement.dateFinError) {
                    event.dateFinError = traitement.dateFinError;
                }
    
                if(traitement.dateFinCloture) {
                    event.dateFinCloture = traitement.dateFinCloture;
                }

                // Ajouter l'événement filtré au calendrier
                calendar.addEvent(event);
            }
        }
    
        // Rendre à nouveau le calendrier
        calendar.render();
    }



    function filtrerEvenements() {
        // Effacer les événements actuels du calendrier
        calendar.removeAllEvents();
    
        // Récupérer les caisses d'exploitation sélectionnées
        var selectedCaisseExploits = [];
        var caisseExploitCheckboxes = document.querySelectorAll('#caisseExploitCheckboxes input[type="checkbox"]:checked');
        caisseExploitCheckboxes.forEach(function(checkbox) {
            selectedCaisseExploits.push(checkbox.nextElementSibling.textContent.trim());
        });
    
        // Récupérer les pools VM sélectionnés
        var selectedPools = [];
        var poolVmCheckboxes = document.querySelectorAll('#poolVmCheckboxes input[type="checkbox"]:checked');
        poolVmCheckboxes.forEach(function(checkbox) {
            selectedPools.push(checkbox.nextElementSibling.textContent.trim());
        });
    
        // Récupérer les scénarios sélectionnés
        var selectedScenarios = [];
        var scenarioCheckboxes = document.querySelectorAll('#scenarioCheckboxes input[type="checkbox"]:checked');
        scenarioCheckboxes.forEach(function(checkbox) {
            selectedScenarios.push(checkbox.nextElementSibling.textContent.trim());
        });
    
        // Filtrer les événements en fonction des sélections
        data.forEach(function(traitement) {
            var poolsInEvent = traitement.poolName;

            if ((selectedCaisseExploits.length === 0 || selectedCaisseExploits.includes(traitement.caisseExploitName)) &&
                (selectedPools.length === 0 || selectedPools.some(pool => poolsInEvent.includes(pool))) &&
                (selectedScenarios.length === 0 || selectedScenarios.includes(traitement.scenarioName))) 
                
                {

                const endDate = moment(traitement.end);
                const formattedEndDate = endDate.format('DD/MM/YYYY');
                let title = `${traitement.numSemaine} - ${traitement.caisseName} (${traitement.caisseNum}) - ${traitement.poolName} - ${traitement.scenarioName} -  ${traitement.etat} - Fin : ${formattedEndDate}`;

                // Vérifier si etape est null avant de l'inclure dans le titre
                if (traitement.etape !== null) {
                    title += ` - ${traitement.etape}`;
                }

                const event = {
                    id: traitement.id,
                    title: title,
                    start: traitement.start,
                    end: traitement.end,
                    poolId: traitement.poolId,
                    poolName: traitement.poolName,
                    VmId: traitement.VmId,
                    VmName: traitement.VmName,
                    caisseId: traitement.caisseId,
                    caisseName: traitement.caisseName,
                    caisseNum: traitement.caisseNum,
                    caisseExploitId: traitement.caisseExploitId,
                    caisseExploitName: traitement.caisseExploitName,
                    caisseExploitNum: traitement.caisseExploitNum,
                    scenarioId: traitement.scenarioId,
                    scenarioName: traitement.scenarioName,
                    processId: traitement.processId,
                    processName: traitement.processName,
                    nbrDossier: traitement.nbrDossier,
                    etat: traitement.etat,
                    clotureComment: traitement.clotureComment,
                    dateFinCloture: traitement.dateFinCloture,
                    errorComment: traitement.errorComment,
                    dateFinError: traitement.dateFinError,
                    etape: traitement.etape,
                    periodicity: traitement.periodicity,
                };
    
                if (Array.isArray(traitement.resourceId) && traitement.resourceId.length > 0) {
                    event.resourceIds = traitement.resourceId;
                } else {
                    event.resourceId = traitement.resourceId;
                }
                
                if(traitement.dateFinError) {
                    event.dateFinError = traitement.dateFinError;
                }
    
                if(traitement.dateFinCloture) {
                    event.dateFinCloture = traitement.dateFinCloture;
                }
                
                // Ajouter l'événement filtré au calendrier
                calendar.addEvent(event);
            }
        });

        // Rendre à nouveau le calendrier
        calendar.render();
    }
});
