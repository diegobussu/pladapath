document.addEventListener('DOMContentLoaded', function () {
    var poolFilterInput = document.getElementById('poolFilter');
    var poolSuggestionsList = document.getElementById('poolSuggestions');
    const noPoolFoundMessage = document.getElementById('noPoolFoundMessage');
    var yearInput = document.getElementById('yearInput');

    // Fetch the list of PoolVms from the server
    fetch('/dossier_statistiques_per_pool/listPool')  // Update the endpoint accordingly
        .then(response => response.json())
        .then(data => {
            // Assuming the list of PoolVms is available in the 'data' variable
            const listAllPoolVM = data;

            poolFilterInput.addEventListener('input', function () {
                const selectedPoolValue = poolFilterInput.value.toLowerCase();

                // Split the value to extract the ID
                const parts = selectedPoolValue.split('-');
                if (parts.length === 2) {
                    selectedPoolId = parts[0];
                }

                let poolFound = false;

                poolSuggestionsList.querySelectorAll('option').forEach(function (option) {
                    const optionText = option.textContent.toLowerCase();
                    if (optionText.includes(selectedPoolValue)) {
                        option.style.display = 'block';
                        poolFound = true;
                    } else {
                        option.style.display = 'none';
                    }
                });

                if (!poolFound) {
                    noPoolFoundMessage.style.display = 'block';
                } else {
                    noPoolFoundMessage.style.display = 'none';
                }
            });
        });

    yearInput.addEventListener('input', function () {
        updateStatistics2();
    });

    let selectedPoolId = null;
    // Check if the selectedPoolObject is defined

    window.updateStatistics2 = function () {
        // Get the entered year from the input field
        const yearInput = document.getElementById('yearInput');
        const selectedYear = yearInput.value;
        console.log('Selected Year:', selectedYear);



        const selectedPoolObject = listAllPoolVM.find(poolVm => poolVm.id === selectedPoolId);
        console.log('Selected Pool:', selectedPoolObject);

        const selectedPoolId = selectedPoolObject ? selectedPoolObject.id : null;
        console.log('Selected Pool ID:', selectedPoolId);
            

        // Récupérer les statistiques depuis le serveur pour l'année et la pool sélectionnées
        fetch(`/dossier_statistiques_per_pool/${selectedYear}/${selectedPoolId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(dossiers2 => {

            console.log('Selected Pool:', selectedPoolName);
            console.log('Selected Pool:', selectedPoolId);
            console.log('Fetched Statistics:', dossiers2);

            // Filtrer les statistiques par année et mois
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const filteredStatistics = filterStatisticsByYearAndPool(dossiers2, currentYear,selectedPoolId);

            console.log('Filtered Statistics2:', filteredStatistics);

            // Calculer la somme du nombre de dossiers par mois
            const yearlySum = calculateMonthlySum2(filteredStatistics);

            // Créer des graphiques ou effectuer d'autres opérations avec les statistiques filtrées
            updateCharts2(filteredStatistics);

            // Utiliser la somme mensuelle comme vous le souhaitez
            console.log('Monthly Sum:', yearlySum);

        })
        .catch(error => console.error('Erreur lors de la récupération des statistiques:', error));

    };

    function filterStatisticsByYearAndPool(dossiers2, selectedYear, selectedPool) {
        return dossiers2.filter(stat => {
            const statYear = new Date(stat.year).getFullYear();
            return statYear === parseInt(selectedYear) && stat.poolVM === selectedPool;
        });
    }


    // Fonction pour calculer la somme du nombre de dossiers par mois
    function calculateMonthlySum2(dossiers2) {
        return dossiers2.reduce((sum, stat) => sum + stat.totalDossiers, 0);
    }

    let dossierChartInstance;

    function createCharts2(filteredStatistics) {
        // Exemple d'utilisation de Chart.js (assurez-vous d'inclure la bibliothèque dans votre projet)
        const ctx = document.getElementById('dossierChart2').getContext('2d');
    
        // Destroy the existing Chart instance if it exists
        if (dossierChartInstance) {
            dossierChartInstance.destroy();
        }
    
        // Create an array of month names
        const monthNames = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
    
        // Map month numbers to corresponding month names
        const allMonthsLabels = monthNames;
        
        // Map month numbers to corresponding month names
        const monthData = allMonthsLabels.map(monthName => {
            const matchingStat = filteredStatistics.find(stat => parseInt(stat.month) === allMonthsLabels.indexOf(monthName) + 1);
            return matchingStat ? matchingStat.totalDossiers : 0;
        });
    
        const data = {
            labels: allMonthsLabels, // Use allMonthsLabels instead of monthLabels
            datasets: [{
                label: 'Total Dossiers',
                data: monthData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };
        
        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    x: {
                        type: 'category',
                        labels: allMonthsLabels, // Use allMonthsLabels instead of monthLabels
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45,
                            callback: function (value, index, values) {
                                return monthNames[parseInt(value)]; // Convert month numbers to names
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        ticks: {
                            beginAtZero: false,
                            precision: 0,

                        }
                    }
                }
            }
        };
        
    
        // Create a new Chart instance and store the reference
        dossierChartInstance = new Chart(ctx, config);
    
        // Log some information for debugging
        console.log(monthData);
        console.log(allMonthsLabels);
    }


    // Fonction pour mettre à jour les graphiques avec les statistiques filtrées
    function updateCharts2(filteredStatistics) {
        // Destroy the existing Chart instance if it exists
        if (dossierChartInstance) {
            dossierChartInstance.destroy();
        }

        // Call the createCharts function with the new data
        createCharts2(filteredStatistics);
    }

});