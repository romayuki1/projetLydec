<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard | Lydec</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
<nav>
      <ul>
        <li>
          <a href="index.php" class="logo">
            <img src="lydec.jpg" alt="Logo" />
            <span class="nav-item">DashBoard</span>
          </a>
        </li>
        <li>
          <a href="form.php">
          
            <span class="nav-item">Ajouter Transaction</span>
          </a>
        </li>
        <li>
          <a href="view_transactions.php">
        
            <span class="nav-item">Consulter Transactions</span>
          </a>
        </li>
        <li>
          <a href="agence.php">
            
            <span class="nav-item">Agences</span>
          </a>
        </li>
        <li>
          <a href="agent.php">
            
            <span class="nav-item">Agents</span>
          </a>
        </li>
        <li>
          <a href="banque.php">
            
            <span class="nav-item">Banques</span>
          </a>
        </li>
        <li>
          <a href="facture.php">
            
            <span class="nav-item">Factures</span>
          </a>
        </li>
        <li>
          <a href="view_MP.php">
            
            <span class="nav-item">Historique MP</span>
          </a>
        </li>
        <li>
          <a href="#" class="logout">
          <i class="fas fa-sign-out-alt"></i>
            <span class="nav-item">Log out</span>
          </a>
        </li>
      </ul>
      </nav>
    
    <section class="main">
      <div class="main-top">
        <h1>Lydec - Gestion des transactions</h1>
      </div>
       




      <div class="main-skills">
        <div class="card">
        <i class="fa fa-building"></i>
          <h3>Agences</h3>
            <button class="btn">
             <h3>Continuer</h3> 
            </button>
        </div>
        <div class="card">
         
          <i class='fa fa-bank'></i>
            <h3>Banques</h3>
            <button class="btn">
              <h3>Continuer</h3>
            </button>
        </div>
        
        <div class="card">
        <i class='fa fa-users'></i>
            <h3>Agents</h3>
            <button class="btn" >
              <h3>Continuer</h3>
            </button>

        </div>
        <div class="card">
        <i class='fas fa-money-bill-alt'></i>
            <h3>Factures</h3>
            <button class="btn" >
              <h3>Continuer</h3>
            </button>

        </div>
      </div>
      <section class="main-course">
        <h1>Mes Transactions</h1>
        <div class="course-box">
    
          <div class="course">
            <div class="box">
              <h3>Ajouter Transaction</h3>
              <p>Nouvelle transaction</p>
              <button><a href="form.php">Ajouter</a></button>
              
            </div>
            <div class="box">
              <h3>Historique des Transactions</h3>
              <p>Consulter Historique</p>
              <button><a href="view_transactions.php">Consulter</a></button>
              
            </div>
          </div>
        </div>
      </section>
    </section>
</div>
</body>
</html>
