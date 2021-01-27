/**
 *  Verification du N de SIRET lors de l'inscription d'un utilisateur
 *  souhaitant creer son espace boutique en ligne.
 *  La verification se fait aupres de l'API 'Sirene' de l'INSEE
 */

const inseeUrl = 'https://api.insee.fr/entreprises/sirene/V3/siret';
const key = 'e14af49a-ee42-3e53-a592-09aa8d3f51e1';

async function fetchShopData(value) {

    const response = await fetch(`${inseeUrl}/${value}`, {
            method: `get`,
            headers: new Headers( {
                'Authorization': `Bearer ${key}`,
                'Content-Type': 'application/json'
            })
        });

    if (response.status !== 200) {
        return false;
    }

    return await response.json();
}

// On attend le chargement du DOM
window.onload = async function () {


    const SIRETform = document.querySelector('#SIRET-Form');
    const sendBtn = document.querySelector('#send');
    const continueBtn = document.querySelector('#continue');
    const SHOPform = document.querySelector('#API-data');

    sendBtn.addEventListener('click', async function (e) {
        const errorMsg = document.querySelector('#errorMsg');

        // On previent le rechargement de la page
        e.preventDefault();

        // on recupere la valeur saisie
        const number = getSiret();
        console.log(number);

        // On resout la promesse
        const json = await fetchShopData(number);
        showSpinner();
        setTimeout(function () {

            if (!json) {
                errorMsg.classList.remove('d-none');
                hideSpinner();
                return;
            }
            errorMsg.classList.add('d-none');
            // Affectation aux champs du formulaire
            const addressLine1Input = document.querySelector('#addressLine1');
            addressLine1Input.value = getShopData(json)['addressLine1'];

            const shopNameInput = document.querySelector('#shopName');
            shopNameInput.value = getShopData(json)['name'];

            const cityInput = document.querySelector('#city');
            cityInput.value = getShopData(json)['city'];

            const zipcodeInput = document.querySelector('#zipcode');
            zipcodeInput.value = getShopData(json)['zipcode'];

            const siret2 = document.querySelector('#SIRET2');
            siret2.value = number;

            // On affiche le formulaire suivant et on cache le champ SIRET
            SIRETform.classList.add('d-none');
            SHOPform.classList.remove('d-none');

            hideSpinner();

        }, 2500)

    });

    continueBtn.addEventListener('click', function (e) {
        // On previent le rechargement de la page
        e.preventDefault();
        const addressLine1Input = document.querySelector('#addressLine1');
        const shopNameInput = document.querySelector('#shopName');
        const cityInput = document.querySelector('#city');
        const zipcodeInput = document.querySelector('#zipcode');

        const nameVerify = document.querySelector('#nameVerify');
        nameVerify.innerHTML = `${shopNameInput.value}`;

        const addressVerify = document.querySelector('#addressVerify');
        addressVerify.innerHTML = `${addressLine1Input.value}, ${zipcodeInput.value} ${cityInput.value}`

        const extraData = document.querySelector('#extra-data').classList.remove('d-none');
        this.classList.add('d-none')
    });

    document.querySelector('#goBack').addEventListener('click', function (e) {
        e.preventDefault();

        SIRETform.classList.remove('d-none');
        SHOPform.classList.add('d-none');
    })
}

function getSiret() {
    // On recupere la valeur
    let raw = document.getElementById('SIRET').value;
    return raw.replace(/\s/g,'');
}

function getShopData(data) {

    // Recuperation de donnees
    const shopName = data['etablissement']['uniteLegale']['denominationUniteLegale'];
    const streetNumber = data['etablissement']['adresseEtablissement']['numeroVoieEtablissement'];
    const streetType = data['etablissement']['adresseEtablissement']['typeVoieEtablissement'];
    const streetName = data['etablissement']['adresseEtablissement']['libelleVoieEtablissement'];
    const city = data['etablissement']['adresseEtablissement']['libelleCommuneEtablissement'];
    const zipCode = data['etablissement']['adresseEtablissement']['codePostalEtablissement'];
    const addressLine1 = `${streetNumber} ${streetType} ${streetName}`;

    return {'addressLine1': addressLine1, 'name': shopName, 'city': city, 'zipcode': zipCode}
}

function showSpinner()
{
    document.querySelector('#spinner').classList.remove('d-none');
}

function hideSpinner()
{
    document.querySelector('#spinner').classList.add('d-none');
}

