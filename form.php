<?php 
// Make sure PHPSpreadsheet is loaded
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to fetch options from a table
function fetchOptions($pdo, $table, $columns) {
    $stmt = $pdo->query("SELECT $columns FROM $table");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get bank code
function getBankCode($pdo, $bankName) {
    $stmt = $pdo->prepare("SELECT CODE_Banque FROM banques WHERE LIB_Banque = :bankName");
    $stmt->bindParam(':bankName', $bankName);
    $stmt->execute();
    return $stmt->fetchColumn();
}

$agents = fetchOptions($pdo, 'agents', 'Matricule, Nom, Prenom');
$agences = fetchOptions($pdo, 'agences', 'CODE_Agence, LIB_Agence');
$banks = fetchOptions($pdo, 'banques', 'LIB_Banque');
$moyenPaiement = fetchOptions($pdo, 'typemp', 'Type_MP, Lib_MP');

// Handle file upload and form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file']) && isset($_POST['amount'])) {
    $fileTmpPath = $_FILES['excel_file']['tmp_name'];
    $fileType = mime_content_type($fileTmpPath);
    $amountToPay = (float) $_POST['amount'];

    // Initialize total invoice amount and invoice details array
    $totalInvoiceAmount = 0;
    $invoiceDetails = [];

    // Check if the file is an Excel or CSV file
    $allowedTypes = [
        'application/vnd.ms-excel', // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'text/csv' // CSV
    ];

    if (in_array($fileType, $allowedTypes)) {
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($fileTmpPath);
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $data = $worksheet->toArray();

                foreach ($data as $index => $row) {
                    if ($index === 0) continue; // Skip header row

                    $fac_num = htmlspecialchars($row[0] ?? '');
                    $ligne = htmlspecialchars($row[1] ?? '');
                    $invoiceAmount = (float) ($row[2] ?? 0.0);

                    // Store invoice details for later use
                    $invoiceDetails[] = [
                        'fac_num' => $fac_num,
                        'ligne' => $ligne,
                        'amount' => $invoiceAmount
                    ];

                    $totalInvoiceAmount += $invoiceAmount;
                }
            }

            // Compare total invoice amount with the amount to pay
            if ($totalInvoiceAmount > $amountToPay) {
                echo "The total invoice amount exceeds the amount entered.";
            } else {
                // Verify that facture numbers and line values match those in the database
                $validInvoices = [];
                foreach ($invoiceDetails as $detail) {
                    $stmt = $pdo->prepare("
                        SELECT * FROM factures 
                        WHERE NUM_Facture = :fac_num AND Ligne = :ligne
                    ");
                    $stmt->execute([
                        ':fac_num' => $detail['fac_num'],
                        ':ligne' => $detail['ligne']
                    ]);

                    if ($stmt->rowCount() > 0) {
                        $validInvoices[] = $detail;
                    } else {
                        echo "Invalid facture number or line: Fac Num: {$detail['fac_num']}, Ligne: {$detail['ligne']}<br>";
                    }
                }

                if (count($validInvoices) === count($invoiceDetails)) {
                    // Begin a transaction to ensure atomicity
                    $pdo->beginTransaction();

                    try {
                        // Insert into the transactions table
                        $agentId = $_POST['agent'];
                        $agenceCode = $_POST['agence'];
                        $paymentOption = $_POST['moyen_paiement'];

                        $stmt = $pdo->prepare("
                            INSERT INTO transactions (NUM_Transaction, Montant_TRS, CodeAgence, MatriculeAgent, DateTransaction) 
                            VALUES (0, :amount, :agence, :agent, NOW())
                        ");
                        $stmt->execute([
                            ':amount' => $amountToPay,
                            ':agence' => $agenceCode,
                            ':agent' => $agentId
                        ]);

                        // Get the latest transaction number
                        $transactionNum = $pdo->lastInsertId();

                        // Get bank code
                        $bankName = $_POST['bank'];
                        $bankCode = getBankCode($pdo, $bankName);

                        if (!$bankCode) {
                            throw new Exception("Invalid bank code.");
                        }

                        // Insert into moyen_paiements table
                        $stmt = $pdo->prepare("
                            INSERT INTO moyen_paiements (Code_Banque, TypeMPaiement, Montant_MP, DateMP, num_transaction) 
                            VALUES (:bankCode, :paymentType, :amount, CURDATE(), :transactionNum)
                        ");
                        $stmt->execute([
                            ':bankCode' => $bankCode,
                            ':paymentType' => $paymentOption,
                            ':amount' => $amountToPay,
                            ':transactionNum' => $transactionNum
                        ]);

                        // Update factures table
                        $facNumList = implode(',', array_map(fn($inv) => $pdo->quote($inv['fac_num']), $invoiceDetails));
                        $stmt = $pdo->prepare("
                            UPDATE factures 
                            SET num_Transaction = :transactionNum, Date_Reglement = CURDATE(), Etat = 'S' 
                            WHERE NUM_Facture IN ($facNumList)
                        ");
                        $stmt->execute([
                            ':transactionNum' => $transactionNum
                        ]);

                        // Commit the transaction
                        $pdo->commit();

                        echo "Transaction successfully recorded and factures updated.";
                    } catch (Exception $e) {
                        // Rollback the transaction on error
                        $pdo->rollBack();
                        echo "Error: " . htmlspecialchars($e->getMessage() ?? '');
                    }
                } else {
                    echo "Some invoices are invalid.";
                }
            }
        } catch (Exception $e) {
            echo "Error loading file: " . htmlspecialchars($e->getMessage() ?? '');
        }
    } else {
        echo "Invalid file type. Please upload an Excel or CSV file.";
    }
}
?>

<!-- HTML form to upload file and input payment details -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Form</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="excel_file">Charger fichier Excel :</label>
        <input type="file" name="excel_file" id="excel_file" accept=".xls,.xlsx,.csv">
        <br><br>

        <label for="agent">Agent:</label>
        <select name="agent" id="agent">
            <?php foreach ($agents as $agent): ?>
                <option value="<?php echo htmlspecialchars($agent['Matricule']); ?>">
                    <?php echo htmlspecialchars($agent['Nom'] . ' ' . $agent['Prenom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="agence">Agence:</label>
        <select name="agence" id="agence">
            <?php foreach ($agences as $agence): ?>
                <option value="<?php echo htmlspecialchars($agence['CODE_Agence']); ?>">
                    <?php echo htmlspecialchars($agence['LIB_Agence']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="bank">Banque:</label>
        <select name="bank" id="bank">
            <?php foreach ($banks as $bank): ?>
                <option value="<?php echo htmlspecialchars($bank['LIB_Banque']); ?>">
                    <?php echo htmlspecialchars($bank['LIB_Banque']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="moyen_paiement">Moyen de paiement:</label>
        <select name="moyen_paiement" id="moyen_paiement">
            <?php foreach ($moyenPaiement as $payment): ?>
                <option value="<?php echo htmlspecialchars($payment['Type_MP']); ?>">
                    <?php echo htmlspecialchars($payment['Lib_MP']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="amount">Montant a payer:</label>
        <input type="number" name="amount" id="amount" step="0.01" required>
        <br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>