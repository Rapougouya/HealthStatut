<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Utilisateurs</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="dashboard">
    <!-- Header -->
    <header>
      <h1>Gestion des Utilisateurs</h1>
      <a href="dashboard.html" class="back-button">Retour au tableau de bord</a>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Formulaire pour ajouter un utilisateur -->
      <section class="add-user">
        <h2>Ajouter un Utilisateur</h2>
        <form id="addUserForm">
          <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
          </div>
          <div class="form-group">
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>
          </div>
          <div class="form-group">
            <label for="role">Rôle:</label>
            <select id="role" name="role" required>
              <option value="medecin">Médecin</option>
              <option value="infirmier">Infirmier</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit">Ajouter</button>
        </form>
      </section>

      <!-- Liste des utilisateurs -->
      <section class="user-list">
        <h2>Liste des Utilisateurs</h2>
        <table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Rôle</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <!-- Les utilisateurs seront ajoutés ici dynamiquement -->
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="utilisateurs.js"></script>
</body>
</html>