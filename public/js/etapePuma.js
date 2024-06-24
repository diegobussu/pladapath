document.addEventListener("DOMContentLoaded", function() {
    const etapeSelect = document.getElementById("etape");

    // DATE DEBUT ETAPE 2

    var dateDebutEtape2 = document.getElementById('date_debut_etape_2');

    if (dateDebutEtape2) {
        dateDebutEtape2.addEventListener("input", function () {

            var selectedEtape = etapeSelect.value;
            var scenarioId = document.getElementById('scenario').value;

            Etape2(selectedEtape, scenarioId, nbreDossier);
        });
    }

    function Etape2(selectedEtape, scenarioId, nbreDossier) {
    
        $.ajax({
            url: '/etape_select',
            method: 'GET',
            data: { selectedEtape: selectedEtape, scenarioId: scenarioId, nbreDossier: nbreDossier },
            success: function(response) {
                calculEtape3and4(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }

    function calculEtape3and4(response) {

        var delai1 = parseInt(response.delai1);
        var delai2 = parseInt(response.delai2);
        var delai3 = parseInt(response.delai3);  

        var dateDebut = moment(document.getElementById('datedebut').value);
        var dateFin = moment(document.getElementById('datefin').value);

        if (dateDebut.isValid() && dateFin.isValid()) {

            var differenceEnMinutes = dateFin.diff(dateDebut, 'minutes');

            if (delai1 || delai2 || delai3) {
                var dateDebutEtape2 = moment(document.getElementById('date_debut_etape_2').value);
                var dateFinEtape2 = moment(dateDebutEtape2).add(differenceEnMinutes, 'minutes');

                var dateDebutEtape3 = moment(dateFinEtape2).add(delai2, 'days');
                var dateFinEtape3 = moment(dateDebutEtape3).add(differenceEnMinutes, 'minutes');

                var dateDebutEtape4 = moment(dateFinEtape3).add(delai3, 'days');
                var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');
                
                document.querySelector('#etape3 input[name="date_debut_etape_3"]').value = dateDebutEtape3.format('YYYY-MM-DDTHH:mm');
                document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = dateDebutEtape4.format('YYYY-MM-DDTHH:mm');

                document.querySelector('#etape2 input[name="date_fin_etape_2"]').value = dateFinEtape2.format('YYYY-MM-DDTHH:mm');
                document.querySelector('#etape3 input[name="date_fin_etape_3"]').value = dateFinEtape3.format('YYYY-MM-DDTHH:mm');
                document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');
            }
        }
    }



    // DATE DEBUT ETAPE 3

    var dateDebutEtape3 = document.getElementById('date_debut_etape_3');

    if (dateDebutEtape3) {
        dateDebutEtape3.addEventListener("input", function () {

            var selectedEtape = etapeSelect.value;
            var scenarioId = document.getElementById('scenario').value;
    
            Etape3(selectedEtape, scenarioId, nbreDossier);
        });
    }

    function Etape3(selectedEtape, scenarioId, nbreDossier) {
    
        $.ajax({
            url: '/etape_select',
            method: 'GET',
            data: { selectedEtape: selectedEtape, scenarioId: scenarioId, nbreDossier: nbreDossier },
            success: function(response) {
                calculEtape4(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }


    function calculEtape4(response) {

        var delai1 = parseInt(response.delai1);
        var delai2 = parseInt(response.delai2);
        var delai3 = parseInt(response.delai3);  

        var dateDebut = moment(document.getElementById('datedebut').value);
        var dateFin = moment(document.getElementById('datefin').value);

        if (dateDebut.isValid() && dateFin.isValid()) {

            var differenceEnMinutes = dateFin.diff(dateDebut, 'minutes');

            if (delai1 || delai2 || delai3) {
                var dateDebutEtape3 = moment(document.getElementById('date_debut_etape_3').value);
                var dateFinEtape3 = moment(dateDebutEtape3).add(differenceEnMinutes, 'minutes');

                var dateDebutEtape4 = moment(dateFinEtape3).add(delai3, 'days');
                var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');
                
                document.querySelector('#etape4 input[name="date_debut_etape_4"]').value = dateDebutEtape4.format('YYYY-MM-DDTHH:mm');

                document.querySelector('#etape3 input[name="date_fin_etape_3"]').value = dateFinEtape3.format('YYYY-MM-DDTHH:mm');
                document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');
            }
        }
    }






    // DATE DEBUT ETAPE 4

    var dateDebutEtape4 = document.getElementById('date_debut_etape_4');

    if (dateDebutEtape4) {
        dateDebutEtape4.addEventListener("input", function () {

            var selectedEtape = etapeSelect.value;
            var scenarioId = document.getElementById('scenario').value;
    
            Etape4(selectedEtape, scenarioId, nbreDossier);
        });
    }

    function Etape4(selectedEtape, scenarioId, nbreDossier) {
    
        $.ajax({
            url: '/etape_select',
            method: 'GET',
            data: { selectedEtape: selectedEtape, scenarioId: scenarioId, nbreDossier: nbreDossier },
            success: function(response) {
                calculEndDateEtape4(response);
            },
            error: function (error) {
                console.error('Erreur lors de l\'évènement', error);
            }
        });
    }


    function calculEndDateEtape4(response) {

        var delai1 = parseInt(response.delai1);
        var delai2 = parseInt(response.delai2);
        var delai3 = parseInt(response.delai3);  

        var dateDebut = moment(document.getElementById('datedebut').value);
        var dateFin = moment(document.getElementById('datefin').value);

        if (dateDebut.isValid() && dateFin.isValid()) {

            var differenceEnMinutes = dateFin.diff(dateDebut, 'minutes');

            if (delai1 || delai2 || delai3) {
                var dateDebutEtape4 = moment(document.getElementById('date_debut_etape_4').value);
                var dateFinEtape4 = moment(dateDebutEtape4).add(differenceEnMinutes, 'minutes');
            
                document.querySelector('#etape4 input[name="date_fin_etape_4"]').value = dateFinEtape4.format('YYYY-MM-DDTHH:mm');
            }
        }
    }
});