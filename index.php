<?php
// Connection to MS Access
$conn = new COM("ADODB.Connection");
$conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=loan_request_db.accdb");

// Fetch next auto-increment ID
$rs = $conn->Execute("SELECT MAX(LoanID) AS MaxID FROM LoanRequests");
$nextID = $rs->Fields("MaxID")->Value + 1;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RequestedDate = date('Y-m-d');
    $PartnerName = $_POST['partner'];
    $CustomerName = $_POST['customer'];
    $Amount = $_POST['amount'];
    $LoanPlan = $_POST['loanplan'];
    $RequestedMonth = $_POST['reqmonth'];
    $PriorityIndex = $_POST['priority']; // received from JS
    $LoanPriorityNumber = "PR" . str_pad($PriorityIndex, 2, "0", STR_PAD_LEFT);
    $Status = $_POST['status'];
    $Remark = $_POST['remark'];

    $sql = "INSERT INTO LoanRequests (RequestedDate, PartnerName, CustomerName, Amount, LoanPlan, RequestedMonth, LoanPriorityNumber, Status, Remark) 
            VALUES ('$RequestedDate', '$PartnerName', '$CustomerName', $Amount, '$LoanPlan', '$RequestedMonth', '$LoanPriorityNumber', '$Status', '$Remark')";
    $conn->Execute($sql);
    echo "<div class='alert alert-success m-3'>Loan Request Submitted Successfully</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Loan Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 p-4 bg-white rounded shadow">
    <h3>Loan Request Form</h3>
    <form method="POST" onsubmit="return validateForm()">
        <div class="mb-3">
            <label class="form-label">Loan Request Number</label>
            <input type="text" class="form-control" value="<?= $nextID ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Requested Date</label>
            <input type="text" class="form-control" value="<?= date('Y-m-d') ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Partner Name</label>
            <select name="partner" class="form-select" required>
                <option value="">Select Partner</option>
                <?php
                $partners = ["Adhiban Thibakar", "AM Avantikamayi", "Arun", "Ayyappan", "Baskar", "Bharath", "Chandra Muthu", "Jeyanth", "Jovin", "Karthi Nandhu", "Kathir", "Madasamy", "Mahesh Santhosh", "Mathavan", "Murugavel", "Muthulingam", "Ruban"];
                foreach ($partners as $p) echo "<option value='$p'>$p</option>";
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" name="customer" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount (₹)</label>
            <input type="number" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Loan Plan</label>
            <select name="loanplan" class="form-select" id="loanplan" required onchange="setPriority()">
                <option value="">Select Plan</option>
                <?php
                $plans = ["10 or 20 Month plan", "40 to 60 Month", "1.50%", "1.5 EMI Plan", "1.25 % EMI Plan", "13 % EMI Plan", "1.10 % Interest Plan"];
                foreach ($plans as $index => $plan) echo "<option value='$plan' data-index='$index'>" . $plan . "</option>";
                ?>
            </select>
            <input type="hidden" name="priority" id="priorityIndex">
        </div>
        <div class="mb-3">
            <label class="form-label">Requested Month</label>
            <input type="month" name="reqmonth" class="form-control" value="<?= date('Y-m', strtotime('+1 month')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" onchange="setStatusColor(this)">
                <option value="Processed" class="text-success">Processed</option>
                <option value="Completed" class="text-warning">Completed</option>
                <option value="Cancel by Customer" class="text-danger">Cancel by Customer</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>

<script>
function setPriority() {
    let dropdown = document.getElementById('loanplan');
    let selectedIndex = dropdown.selectedIndex - 1;
    document.getElementById('priorityIndex').value = selectedIndex;
}

function validateForm() {
    // Basic JS validation
    const amount = document.querySelector('input[name="amount"]').value;
    if (amount <= 0) {
        alert("Amount must be greater than 0");
        return false;
    }
    return true;
}

function setStatusColor(el) {
    el.classList.remove("text-success", "text-warning", "text-danger");
    if (el.value === "Processed") el.classList.add("text-success");
    else if (el.value === "Completed") el.classList.add("text-warning");
    else if (el.value === "Cancel by Customer") el.classList.add("text-danger");
}
</script>
</body>
</html>
