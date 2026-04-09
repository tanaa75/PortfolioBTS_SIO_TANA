async function convertCurrency() {
    let amount = document.getElementById("amount").value;
    let fromCurrency = document.getElementById("fromCurrency").value;
    let toCurrency = document.getElementById("toCurrency").value;
    let resultElement = document.getElementById("result");

    if (amount === "" || amount <= 0) {
        resultElement.innerText = "Veuillez entrer un montant valide.";
        return;
    }

    let url = `https://api.coinbase.com/v2/exchange-rates?currency=${fromCurrency}`;

    try {
        let response = await fetch(url);
        let result = await response.json();
        let rate = result.data.rates[toCurrency];

        if (!rate) {
            resultElement.innerText = "Conversion impossible.";
            return;
        }

        let convertedAmount = (amount * parseFloat(rate)).toFixed(2);
        resultElement.innerText = `${amount} ${fromCurrency} = ${convertedAmount} ${toCurrency}`;
    } catch (error) {
        resultElement.innerText = "Erreur lors de la récupération des taux.";
    }
}
