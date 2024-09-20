
<?php
// Informations de connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les données de la table transactions
    $query = "SELECT NUM_Transaction, Montant_TRS, CodeAgence, MatriculeAgent, DateTransaction FROM transactions";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Récupération des résultats
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo 'Erreur de connexion : ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
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

<h1>Liste des transactions</h1>

<table>
    <thead>
        <tr>
            <th>Numéro de Transaction</th>
            <th>Montant</th>
            <th>Code Agence</th>
            <th>Matricule Agent</th>
            <th>Date de Transaction</th> <!-- New column for DateTransaction -->
        </tr>
    </thead>
    <tbody>
        <?php if ($transactions): ?>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['NUM_Transaction']) ?></td>
                    <td><?= htmlspecialchars($transaction['Montant_TRS']) ?></td>
                    <td><?= htmlspecialchars($transaction['CodeAgence']) ?></td>
                    <td><?= htmlspecialchars($transaction['MatriculeAgent']) ?></td>
                    <td><?= htmlspecialchars($transaction['DateTransaction']) ?></td> <!-- Display DateTransaction -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Aucune transaction trouvée.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
