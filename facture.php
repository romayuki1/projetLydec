<?php
// Database connection information
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to retrieve data from the Factures table
    $query = "
        SELECT 
            f.NUM_Facture,
            f.Ligne,
            f.Montant_FAC,
            f.Etat,
            f.Date_Reglement,
            t.Montant_TRS AS Montant_Transaction,
            f.num_Transaction
        FROM 
            Factures f
        LEFT JOIN 
            transactions t ON f.num_Transaction = t.NUM_Transaction
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch the results
    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Factures</title>
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

<h1>Liste des factures</h1>

<table>
    <thead>
        <tr>
            <th>Numéro Facture</th>
            <th>Ligne</th>
            <th>Montant Facture</th>
            <th>État</th>
            <th>Date de Règlement</th>
            <th>Montant Transaction</th>
            <th>Numéro Transaction</th> <!-- New column header -->
        </tr>
    </thead>
    <tbody>
        <?php if ($factures): ?>
            <?php foreach ($factures as $facture): ?>
                <tr>
                    <td><?= htmlspecialchars($facture['NUM_Facture'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['Ligne'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['Montant_FAC'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['Etat'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['Date_Reglement'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['Montant_Transaction'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($facture['num_Transaction'] ?? 'N/A') ?></td> <!-- New column for num_Transaction -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucune facture trouvée.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
