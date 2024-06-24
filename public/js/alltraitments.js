document.addEventListener('DOMContentLoaded', function() { 

    // Show/hide content panels based on button clicks
    const buttons = document.querySelectorAll('.buttons button');
    const contentPanels = document.querySelectorAll('.content-panel');

   // Hide all panels initially
    contentPanels.forEach(panel => {
        panel.style.display = 'none';
    });

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-id');
            // Hide all panels initially
            contentPanels.forEach(panel => {
                panel.style.display = 'none';
            });
            // Show the targeted panel
            const targetPanel = document.getElementById(targetId);
            targetPanel.style.display = 'block';

            // Remove 'active' class from all buttons
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            // Add 'active' class to the clicked button
            this.classList.add('active');
        });
    });

    window.addEventListener('beforeunload', function() {
        resetForm();
    });

    function resetForm() {
        const caisse_exploit = document.getElementById('caisse_exploit');
        const date = document.getElementById('date');
        const pool = document.getElementById("pool");
        const day = document.getElementById("day");
        const hour = document.getElementById("hour");
        const min = document.getElementById("min");
        const deleteOption = document.getElementById('deleteOption');
        const filterValueCaisse = document.getElementById('filterValueCaisse');
        const filterValueScenario = document.getElementById('filterValueScenario');
        const filterValuePeriod = document.getElementById('filterValuePeriod');
        const filterValuePool = document.getElementById('filterValuePool');

        caisse_exploit.value = 'Tous';
        pool.value = 'Tous';
        date.value = '';
        day.value = '';
        hour.value = '';
        min.value = '';
        deleteOption.value = 'Tous';
        filterValueCaisse.value = 'Tous';
        filterValueScenario.value = 'Tous';
        filterValuePeriod.value = 'Tous';
        filterValuePool.value = 'Tous';
    }

    const sendBtn = document.getElementById('sendBtn');

    sendBtn.addEventListener("click", (event) => {
        if (!validateForm()) {
            event.preventDefault();
        } else {
            if(!confirmModify()) {
                event.preventDefault();
            }
        }
    });

    function validateForm() {
        var valid = true;

        const caisse_exploit = document.getElementById('caisse_exploit');
        if (caisse_exploit.value === "Tous") {
            alert("Veuillez choisir une caisse exploitante.");
            valid = false;
        }

        const pool = document.getElementById('pool');
        if (pool.value === "Tous") {
            pool.value = 0;
        }   

        const date = document.getElementById('date');
        if (date.value.trim() !== "") {
            if (!isValidDateTime(date.value)) {
                alert("Veuillez entrer une date valide.");
                valid = false;
            }
        }

        function isValidDateTime(dateTimeString) {
            // Format de date et heure local (ISO 8601)
            const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        
            // Vérifier si la chaîne de date correspond au format
            return dateTimeRegex.test(dateTimeString);
        }

        const day = document.getElementById("day");
        const hour = document.getElementById("hour");
        const min = document.getElementById("min");


        if (day.value === "" && hour.value === "" && min.value === "") {
            alert("Veuillez entrer au moins une valeur parmi les 3 champs.");
            valid = false;
        }

        return valid;
    }

    function confirmModify() {
        return confirm("Êtes-vous sûr de vouloir modifier tous les traitements ?");
    }

    document.getElementById("day").addEventListener("input", function () {
        const day = this.value;
        if (day < -365 || day > 365) {
            alert("Veuillez entrer une valeur -365 et 365");
            this.value = "";
        }
    });

    document.getElementById("hour").addEventListener("input", function () {
        const hour = this.value;
        if (hour < -24 || hour > 24) {
            alert("Veuillez entrer une valeur -24 et 24");
            this.value = "";
        }
    });

    document.getElementById("min").addEventListener("input", function () {
        const min = this.value;
        if (min < -60 || min > 60) {
            alert("Veuillez entrer une valeur -60 et 60");
            this.value = "";
        }
    });

    const caisseSelect = document.getElementById("caisse_exploit");
    const poolSelect = document.getElementById("pool");
    const poolOptions = document.querySelectorAll(".pool-option");  
    
    caisseSelect.addEventListener("change", function() {
        const selectedCaisseId = caisseSelect.value;
        poolSelect.value = 'Tous';

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







    const deleteOption = document.getElementById('deleteOption');
    const filterModalCaisse = document.getElementById('filterModalCaisse');
    const filterModalPool = document.getElementById('filterModalPool');
    const filterModalScenario = document.getElementById('filterModalScenario');
    const filterModalPeriod = document.getElementById('filterModalPeriod');

    const filterValueCaisse = document.getElementById('filterValueCaisse');
    const filterValuePool = document.getElementById('filterValuePool');
    const filterValueScenario = document.getElementById('filterValueScenario');
    const filterValuePeriod = document.getElementById('filterValuePeriod');

    const sendBtn1 = document.getElementById('sendBtn1');

    sendBtn1.addEventListener("click", (event) => {
        if (!validateForm1()) {
            event.preventDefault();
        } else {
            if(!confirmDelete()) {
                event.preventDefault();
            }
        }
    });

    function validateForm1() {
        var valid = true;
        if (deleteOption.value === "Tous") {
            alert("Veuillez choisir une option.");
            valid = false;
        }
        if (filterValueCaisse.value === "Tous" && filterValuePool.value === "Tous" && filterValueScenario.value === "Tous" && filterValuePeriod.value === "Tous") {
            alert("Veuillez choisir un filtre.");
            valid = false;
        }

        const date = document.getElementById('date1');
        if (date.value.trim() !== "") {
            if (!isValidDateTime(date.value)) {
                alert("Veuillez entrer une date valide.");
                valid = false;
            }
        }

        function isValidDateTime(dateTimeString) {
            // Format de date et heure local (ISO 8601)
            const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        
            // Vérifier si la chaîne de date correspond au format
            return dateTimeRegex.test(dateTimeString);
        }

        return valid;
    }

    function confirmDelete() {
        return confirm("Êtes-vous sûr de vouloir supprimer tous les traitements ?");
    }

    deleteOption.addEventListener('change', function() {
        filterModalCaisse.style.display = (deleteOption.value === "caisse") ? "block" : "none";
        filterModalPool.style.display = (deleteOption.value === "pool") ? "block" : "none";
        filterModalScenario.style.display = (deleteOption.value === "scenario") ? "block" : "none";
        filterModalPeriod.style.display = (deleteOption.value === "periodique") ? "block" : "none";
    });
    

});