<?php
// Informations de connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les données de la table agences
    $query = "SELECT CODE_Agence, LIB_Agence FROM agences";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Récupération des résultats
    $agences = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Agences</title>
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

<h1>Liste des agences</h1>

<table>
    <thead>
        <tr>
            <th>Code Agence</th>
            <th>Libellé Agence</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($agences): ?>
            <?php foreach ($agences as $agence): ?>
                <tr>
                    <td><?= htmlspecialchars($agence['CODE_Agence']) ?></td>
                    <td><?= htmlspecialchars($agence['LIB_Agence']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Aucune agence trouvée.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
