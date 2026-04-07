async function convertCurrency() {
    let amount = document.getElementById("amount").value;
    let fromCurrency = document.getElementById("fromCurrency").value;
    let toCurrency = document.getElementById("toCurrency").value;
    let resultElement = document.getElementById("result");

    if (amount === "" || amount <= 0) {
        resultElement.innerText = "Veuillez entrer un montant valide.";
        return;
    }

    let apiKey = "91b97f3214eab6963d8d0061";  
    let url = `https://v6.exchangerate-api.com/v6/${apiKey}/latest/${fromCurrency}`;

    try {
        let response = await fetch(url);
        let data = await response.json();
        let rate = data.conversion_rates[toCurrency];

        if (!rate) {
            resultElement.innerText = "Conversion impossible.";
            return;
        }

        let convertedAmount = (amount * rate).toFixed(2);
        resultElement.innerText = `${amount} ${fromCurrency} = ${convertedAmount} ${toCurrency}`;
    } catch (error) {
        resultElement.innerText = "Erreur lors de la récupération des taux.";
    }
}
