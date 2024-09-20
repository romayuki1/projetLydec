<?php
// Database connection information
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to retrieve data from the Moyen_Paiements table
    $query = "
        SELECT 
            mp.Code_Banque,
            mp.TypeMPaiement,
            mp.Montant_MP,
            mp.DateMP,
            t.Montant_TRS AS Montant_Transaction,
            mp.num_transaction
        FROM 
            Moyen_Paiements mp
        LEFT JOIN 
            transactions t ON mp.num_transaction = t.NUM_Transaction
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch the results
    $moyenPaiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle connection errors
    echo 'Connection error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moyens de Paiement</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Liste des Moyens de Paiement</h1>

<table>
    <thead>
        <tr>
            <th>Code Banque</th>
            <th>Type de Paiement</th>
            <th>Montant</th>
            <th>Date de Paiement</th>
            <th>Montant Transaction</th>
            <th>Numéro Transaction</th> <!-- New column for num_transaction -->
        </tr>
    </thead>
    <tbody>
        <?php if ($moyenPaiements): ?>
            <?php foreach ($moyenPaiements as $paiement): ?>
                <tr>
                    <td><?= htmlspecialchars($paiement['Code_Banque'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($paiement['TypeMPaiement'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($paiement['Montant_MP'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($paiement['DateMP'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($paiement['Montant_Transaction'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($paiement['num_transaction'] ?? 'N/A') ?></td> <!-- Display num_transaction -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Aucun moyen de paiement trouvé.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
