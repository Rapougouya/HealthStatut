// Simuler une liste d'utilisateurs
let users = [];

// Ajouter un utilisateur
document.getElementById('addUserForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const nom = document.getElementById('nom').value;
  const prenom = document.getElementById('prenom').value;
  const role = document.getElementById('role').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  const user = { nom, prenom, role, email, password };
  users.push(user);

  // Mettre à jour la table
  updateUserTable();
  document.getElementById('addUserForm').reset();
});

// Mettre à jour la table des utilisateurs
function updateUserTable() {
  const tbody = document.getElementById('userTableBody');
  tbody.innerHTML = '';

  users.forEach((user, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${user.nom}</td>
      <td>${user.prenom}</td>
      <td>${user.role}</td>
      <td>${user.email}</td>
      <td>
        <button onclick="editUser(${index})">Modifier</button>
        <button onclick="deleteUser(${index})">Supprimer</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Modifier un utilisateur
function editUser(index) {
  const user = users[index];
  document.getElementById('nom').value = user.nom;
  document.getElementById('prenom').value = user.prenom;
  document.getElementById('role').value = user.role;
  document.getElementById('email').value = user.email;
  document.getElementById('password').value = user.password;

  // Supprimer l'utilisateur de la liste
  users.splice(index, 1);
  updateUserTable();
}

// Supprimer un utilisateur
function deleteUser(index) {
  users.splice(index, 1);
  updateUserTable();
}