function clearTable(){
    let Table = document.getElementById("resultTable");
    Table.innerHTML = "";
}
function clearErrors(){
    let errors = document.getElementById("errors");
    errors.innerHTML = "";
}
function checkInput(amount,numOfPayments,interestRate) {
    let errors = [];
    if(!amount || !numOfPayments || !interestRate) {
        errors.push("Please enter all numbers");
        return errors;
    }
    if(amount < 5000 || amount > 50000) {
        errors.push("The loan must be between 5000 and 50000");
    }
    if(Math.round(numOfPayments) !== numOfPayments || numOfPayments < 0) {
        errors.push("The number of payments has to be a positive integer");
    }
    if(interestRate < 0 || interestRate > 100) {
        errors.push("Interest rate should be between 0 and 100 %");
    }
    return errors;
}
function buildTableHeaders()
{
    let row = document.createElement('tr');
    let cell = document.createElement("th");
    cell.textContent = 'No.';
    row.appendChild(cell);
    let cell1 = document.createElement("th");
    cell1.textContent = 'Remaining credit amount';
    row.appendChild(cell1);
    let cell2 = document.createElement("th");
    cell2.textContent = 'Principal part';
    row.appendChild(cell2);
    let cell3 = document.createElement("th");
    cell3.textContent = 'Interest';
    row.appendChild(cell3);
    let cell4 = document.createElement("th");
    cell4.textContent = 'Total payment';
    row.appendChild(cell4);
    // Append the row to the table with id "resultTable"
    document.getElementById("resultTable").appendChild(row);
}
function buildTableRow(rowNumber,remainingCreditAmount,principalPart,interest,totalAmount)
{
    // Create a new table row
    var row = document.createElement("tr");
    // Create and append table data cells for each parameter
    var cell = document.createElement("td");
    cell.textContent=rowNumber;
    row.appendChild(cell);

    var cell1 = document.createElement("td");
    cell1.textContent = remainingCreditAmount;
    row.appendChild(cell1);

    var cell2 = document.createElement("td");
    cell2.textContent = principalPart;
    row.appendChild(cell2);

    var cell3 = document.createElement("td");
    cell3.textContent = interest;
    row.appendChild(cell3);

    var cell4 = document.createElement("td");
    cell4.textContent = totalAmount;
    row.appendChild(cell4);

    // Append the row to the table with id "resultTable"
    document.getElementById("resultTable").appendChild(row);
}
function buildLastTableRow(totalPrincipalPart,totalInterest,totalPayment) {
    var row = document.createElement('tr');
    var cell = document.createElement('td');
    cell.textContent= "";
    row.appendChild(cell);

    var cell1 = document.createElement('td');
    cell1.textContent= "Total:";
    row.appendChild(cell1);

    var cell2 =document.createElement('td');
    cell2.textContent=totalPrincipalPart+" EUR";
    row.appendChild(cell2);

    var cell3 = document.createElement('td');
    cell3.textContent=totalInterest+" EUR";
    row.appendChild(cell3);

    var cell4 = document.createElement('td');
    cell4.textContent=totalPayment+" EUR";
    row.appendChild(cell4);

    document.getElementById("resultTable").appendChild(row);
}
function PMT(ir,np, pv, fv = 0){
    // ir: interest rate
    // np: number of payment
    // pv: present value or loan amount
    // fv: future value. default is 0

    let presentValueInterstFector = Math.pow((1 + ir), np);
    let pmt = ir * pv  * (presentValueInterstFector + fv)/(presentValueInterstFector-1);
    return pmt.toFixed(2);
}
function monthlyPayment(amount, PMT, interest) {
    let principalPart;
    const interestAmount = (amount / 12) * interest;
    if(amount < PMT) {
        principalPart = amount;
    }
    else {
        principalPart = PMT - interestAmount;
    }
    const remainingAmount = amount - principalPart;
    const totalAmount = interestAmount + principalPart;
    return remainingAmount.toFixed(2) + " " + principalPart.toFixed(2) + " " + interestAmount.toFixed(2) + " " + totalAmount.toFixed(2);
}
document.getElementById("calculate").addEventListener("click", function() {
    // Call the calculatePayment function and store the result
    clearErrors();
    let amount = parseFloat(document.getElementById("amount").value);
    let numOfPayments = parseFloat(document.getElementById("numOfPayments").value);
    let interestRate = parseFloat(document.getElementById("interest").value);
    let errors = checkInput(amount,numOfPayments,interestRate);
    if(errors.length > 0) {
        for(let i = 0;i < errors.length;i++) {
            const error = document.createElement("p");
            error.textContent = errors[i];
            document.getElementById("errors").appendChild(error);
        }
    }
    else {
        let totalPrincipalPart = 0;
        let totalInterest = 0;
        let totalPayment = 0;
        const result = PMT((interestRate/100)/12,numOfPayments, amount);
        let currentAmount = amount;
        clearTable();
        buildTableHeaders();
        for (let i = 0; i < numOfPayments; i++) {
            let values = monthlyPayment(currentAmount, result,interestRate/100).split(' ');
            currentAmount = parseFloat(values[0]);
            totalPrincipalPart += parseFloat(values[1]);
            totalInterest += parseFloat(values[2]);
            totalPayment += parseFloat(values[3]);
            buildTableRow(i+1,currentAmount.toFixed(2), values[1], values[2], values[3])
        }
        buildLastTableRow(totalPrincipalPart.toFixed(2), totalInterest.toFixed(2),totalPayment.toFixed(2));
    }
});