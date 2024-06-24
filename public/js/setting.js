document.addEventListener("DOMContentLoaded", function() {
    $(document).ready(function() {
        // Function to set a cookie
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        // Function to get a cookie
        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        // Initially hide all panels
        $(".content-panel").hide();
        
        // Check if there's a stored panelID in the cookie
        var lastPanelID = getCookie("lastPanelID");
        var activeButton = getCookie("activeButton");
        if (lastPanelID) {
            $("#" + lastPanelID).show();
            $(".buttons .button[data-id='" + activeButton + "']").addClass("active");
        } else {
            $("#mypoolvm").show(); // Show a default panel if no cookie is found
            $(".buttons .button[data-id='mypoolvm']").addClass("active"); // Set default button as active
        }

        // When a button in the nav is clicked
        $(".buttons .button").click(function() {
            var panelID = $(this).data("id");

            // Hide all panels
            $(".content-panel").hide();

            // Show the selected panel
            $("#" + panelID).show();

            // Store the selected panelID and active button in cookies
            setCookie("lastPanelID", panelID, 30); // Cookie expires in 30 days
            setCookie("activeButton", panelID, 30); // Cookie expires in 30 days

            // Remove "active" class from all buttons and add it to the clicked button
            $(".buttons .button").removeClass("active");
            $(this).addClass("active");
        });
    });

    const switches = document.querySelectorAll('.form-check-input');

    switches.forEach(switchElem => {
        switchElem.addEventListener('change', function() {
            if (this.checked) {
                switches.forEach(innerSwitch => {
                    if (innerSwitch !== this) {
                        innerSwitch.checked = false;
                    }
                });
            }
        });
    });
    
    // popup delete traitment
    const deleteLinks = document.querySelectorAll('.delete-link');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const traitementId = this.getAttribute('data-id');
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce champs ?')) {
                window.location.href = this.getAttribute('href');
            }
        });
    });


    // FILTRES DES DIFFERENTS PANELS DE PARAMETRE 
    //CAISSE
    // Function to filter the table rows based on the input value

    function filterTableCaisse(inputId, tableId) {
        var input = document.getElementById(inputId);
        var filter = input.value.toLowerCase();
        var table = document.getElementById(tableId);
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var tdName = rows[i].getElementsByTagName("td")[1]; 
            var tdNumber = rows[i].getElementsByTagName("td")[0];
            if (tdName && tdNumber) {
                var name = tdName.textContent || tdName.innerText;
                var number = tdNumber.textContent || tdNumber.innerText;
                if (name.toLowerCase().indexOf(filter) > -1 || number.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
    
    // Add event listeners to the input fields
    document.getElementById("caisseSearch").addEventListener("input", function() {
        filterTableCaisse("caisseSearch", "caisseTable");
    });


    //POOL VM
    
    function filterTablePool() {
        var input = document.getElementById("poolSearch").value.toLowerCase();
        var table = document.getElementById("poolVMTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    document.getElementById("poolSearch").addEventListener("input", filterTablePool)



    // VM

    function filterTableVM() {
        var input = document.getElementById("vmSearch").value.toLowerCase();
        var table = document.getElementById("vmTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    document.getElementById("vmSearch").addEventListener("input", filterTableVM);




    //SCENARIO 

    function filterTableScenario() {
        var input = document.getElementById("scenarioSearch").value.toLowerCase();
        var table = document.getElementById("scenarioTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }

    document.getElementById("scenarioSearch").addEventListener("input", filterTableScenario);


    //PROCESS

    function filterTableProcess() {
        var input = document.getElementById("processSearch").value.toLowerCase();
        var table = document.getElementById("processTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }

    document.getElementById("processSearch").addEventListener("input", filterTableProcess);


    // Traitement 

    function filterTable() {
        var input = document.getElementById("traitementSearch").value.toLowerCase();
        var table = document.getElementById("traitementTable");
        var table1 = document.getElementById("traitementTable1");
        var table2 = document.getElementById("traitementTable2");

        var rows = table.getElementsByTagName("tr");
        var rows1 = table1.getElementsByTagName("tr");
        var rows2 = table2.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }

        for (var i = 1; i < rows1.length; i++) {
            var row = rows1[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }

        for (var i = 1; i < rows2.length; i++) {
            var row = rows2[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }

    document.getElementById("traitementSearch").addEventListener("input", filterTable);


    //Message 

    function filterMessageTable() {
        var input = document.getElementById("messageSearch").value.toLowerCase();
        var table = document.getElementById("messageTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }

    document.getElementById("messageSearch").addEventListener("input", filterMessageTable);

    // Historique

    function filterHistoryTable() {
        var input = document.getElementById("historySearch").value.toLowerCase();
        var table = document.getElementById("historyTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var rowData = row.textContent.toLowerCase();

            if (rowData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }

    document.getElementById("historySearch").addEventListener("input", filterHistoryTable);






    window.addEventListener('beforeunload', function() {
        resetForm();
    });

    // Fonction pour réinitialiser les champs du formulaire
    function resetForm() {
        // Réinitialisez les champs du formulaire en affectant des valeurs vides ou par défaut
        const messageSearch = document.getElementById('messageSearch');
        const traitementSearch = document.getElementById('traitementSearch');
        const processSearch = document.getElementById("processSearch");
        const scenarioSearch = document.getElementById("scenarioSearch");
        const vmSearch = document.getElementById("vmSearch");
        const poolSearch = document.getElementById("poolSearch");
        const caisseSearch = document.getElementById("caisseSearch");
        const traitementByCaisseTable = document.getElementById("traitementByCaisseTable");
        const historySearch = document.getElementById("historySearch");
        const filterType = document.getElementById("filterType");

        if(traitementByCaisseTable) {
            traitementByCaisseTable.value = '';
        }

        messageSearch.value = '';
        traitementSearch.value = '';
        processSearch.value = '';
        scenarioSearch.value = '';
        vmSearch.value = '';
        poolSearch.value = '';
        caisseSearch.value = '';
        historySearch.value = '';
        filterType.value = '';
    }






    var filterButton = document.getElementById("filterButton");
    var modal = document.getElementById("filterModal");
    var closeButton = document.getElementById("filterClose");
    var filterType = document.getElementById("filterType");
    var filterOptions = document.getElementById("filterOptions");
    var filterLabel = document.getElementById("filterLabel");
    var filterValue = document.getElementById("filterValue");

    filterButton.onclick = function() {
      modal.style.display = "block";
    }

    closeButton.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    filterType.onchange = function() {
      var selectedFilter = filterType.value;
      filterOptions.style.display = "none";
      filterValue.innerHTML = ''; // Clear previous options

      if (selectedFilter === "caisse") {
        filterLabel.textContent = "Sélectionner une caisse :";
        var caisses = new Set();
        var rows = document.querySelectorAll('#traitementTable tbody tr, #traitementTable1 tbody tr, #traitementTable2 tbody tr');
        rows.forEach(function(row) {
          var caisse = row.cells[4].textContent.trim();
          if (caisse) {
            caisses.add(caisse);
          }
        });

        caisses.forEach(function(caisse) {
          var option = document.createElement("option");
          option.value = caisse;
          option.textContent = caisse;
          filterValue.appendChild(option);
        });

        filterOptions.style.display = "block";
      } else if (selectedFilter === "pool") {
        filterLabel.textContent = "Sélectionner un pool :";
        var pools = new Set();
        var rows = document.querySelectorAll('#traitementTable tbody tr, #traitementTable1 tbody tr, #traitementTable2 tbody tr');
        rows.forEach(function(row) {
          var pool = row.cells[5].textContent.trim();
          if (pool) {
            pools.add(pool);
          }
        });

        pools.forEach(function(pool) {
          var option = document.createElement("option");
          option.value = pool;
          option.textContent = pool;
          filterValue.appendChild(option);
        });

        filterOptions.style.display = "block";
      } else if (selectedFilter === "scenario") {
        filterLabel.textContent = "Sélectionner un scénario :";
        var scenarios = new Set();
        var rows = document.querySelectorAll('#traitementTable tbody tr, #traitementTable1 tbody tr, #traitementTable2 tbody tr');
        rows.forEach(function(row) {
          var scenario = row.cells[7].textContent.trim();
          if (scenario) {
            scenarios.add(scenario);
          }
        });

        scenarios.forEach(function(scenario) {
          var option = document.createElement("option");
          option.value = scenario;
          option.textContent = scenario;
          filterValue.appendChild(option);
        });

        filterOptions.style.display = "block";
      } else if (selectedFilter === "etape") {
        filterLabel.textContent = "Sélectionner une étape :";
        var etapes = new Set();
        var rows = document.querySelectorAll('#traitementTable tbody tr, #traitementTable1 tbody tr, #traitementTable2 tbody tr');
        rows.forEach(function(row) {
          var etape = row.cells[9].textContent.trim();
          if (etape) {
            etapes.add(etape);
          }
        });

        etapes.forEach(function(etape) {
          var option = document.createElement("option");
          option.value = etape;
          option.textContent = etape;
          filterValue.appendChild(option);
        });

        filterOptions.style.display = "block";
      }
    }

    document.getElementById("applyFilterButton").onclick = function() {
      var selectedFilter = filterType.value;
      var selectedValue = filterValue.value;

      var rows = document.querySelectorAll('#traitementTable tbody tr, #traitementTable1 tbody tr, #traitementTable2 tbody tr');
      rows.forEach(function(row) {
        row.style.display = "";
        if (selectedFilter === "caisse") {
          var caisse = row.cells[4].textContent.trim();
          if (caisse !== selectedValue) {
            row.style.display = "none";
          }
        } else if (selectedFilter === "pool") {
          var pool = row.cells[5].textContent.trim();
          if (pool !== selectedValue) {
            row.style.display = "none";
          }
        } else if (selectedFilter === "scenario") {
          var scenario = row.cells[7].textContent.trim();
          if (scenario !== selectedValue) {
            row.style.display = "none";
          }
        } else if (selectedFilter === "etape") {
          var etape = row.cells[9].textContent.trim();
          if (etape !== selectedValue) {
            row.style.display = "none";
          }
        }
      });

      modal.style.display = "none";
    }










    const valDeMarneButton = document.getElementById("VDMbutton");
    const vaucluseButton = document.getElementById("VAUbutton");
    const periodButton = document.getElementById("PERIODbutton");

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = `${name}=${value || ""}${expires}; path=/`;
    }

    function getCookie(name) {
        const nameEQ = `${name}=`;
        const cookies = document.cookie.split(';');
        for (let c of cookies) {
            c = c.trim();
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length);
            }
        }
        return null;
    }

    function showTab(tabName) {
        document.getElementById("VDMTab").style.display = tabName === "VDMTab" ? "" : "none";
        document.getElementById("VAUTab").style.display = tabName === "VAUTab" ? "" : "none";
        document.getElementById("PERIODTab").style.display = tabName === "PERIODTab" ? "" : "none";
        
        valDeMarneButton.classList.toggle("active", tabName === "VDMTab");
        vaucluseButton.classList.toggle("active", tabName === "VAUTab");
        periodButton.classList.toggle("active", tabName === "PERIODTab");
        
        setCookie("lastClickedTab", tabName, 30);
    }

    const lastClickedTab = getCookie("lastClickedTab") || "PERIODTab";
    showTab(lastClickedTab);

    valDeMarneButton.addEventListener("click", () => showTab("VDMTab"));
    vaucluseButton.addEventListener("click", () => showTab("VAUTab"));
    periodButton.addEventListener("click", () => showTab("PERIODTab"));


});