// =============================================
// FICHIER : Views/MainForm.cs
// RÔLE    : Fenêtre principale du panel d'administration.
//           Affiche tous les utilisateurs dans un tableau
//           et donne accès aux actions : modifier,
//           supprimer, bannir/débannir, profil admin.
// =============================================

using System;
using System.Collections.Generic;
using System.Data;
using System.Drawing;
using System.Windows.Forms;
using DevMatesAdmin.Controllers;
using DevMatesAdmin.Models;

namespace DevMatesAdmin.Views
{
    public class MainForm : Form
    {
        // --- Contrôles de la fenêtre ---
        private DataGridView dgvUtilisateurs;
        private Button       btnRafraichir;
        private Button       btnModifier;
        private Button       btnSupprimer;
        private Button       btnBannir;
        private Button       btnProfil;
        private Button       btnDeconnexion;
        private Label        lblBienvenue;
        private Panel        panneauBoutons;

        private readonly UserController _ctrl = new UserController();

        // Liste des utilisateurs en mémoire (synchronisée avec le tableau affiché)
        private List<User> _utilisateurs;

        /// <summary>
        /// Constructeur : initialise la fenêtre et charge les données depuis la base.
        /// </summary>
        public MainForm()
        {
            InitialiserControles();
            ChargerUtilisateurs();
        }

        /// <summary>
        /// Crée et positionne tous les contrôles de la fenêtre principale.
        /// </summary>
        private void InitialiserControles()
        {
            this.Text          = "DevMates Admin — Gestion des utilisateurs";
            this.Size          = new Size(1050, 640);
            this.StartPosition = FormStartPosition.CenterScreen;

            // --- Label de bienvenue (affiche l'email de l'admin connecté) ---
            lblBienvenue = new Label
            {
                Text     = "Connecté en tant que : " + (AuthController.AdminConnecte?.Email ?? "?"),
                Location = new Point(10, 10),
                AutoSize = true,
                Font     = new Font("Arial", 9, FontStyle.Italic)
            };

            // --- Tableau des utilisateurs (DataGridView) ---
            dgvUtilisateurs = new DataGridView
            {
                Location              = new Point(10, 40),
                Size                  = new Size(1010, 490),
                ReadOnly              = true,            // L'admin ne tape pas directement dans le tableau
                SelectionMode         = DataGridViewSelectionMode.FullRowSelect,
                MultiSelect           = false,           // Une seule sélection à la fois
                AutoSizeColumnsMode   = DataGridViewAutoSizeColumnsMode.Fill,
                AllowUserToAddRows    = false,
                AllowUserToDeleteRows = false,
                BackgroundColor       = SystemColors.Window
            };

            // --- Panneau contenant les boutons d'action (en bas) ---
            panneauBoutons = new Panel
            {
                Location = new Point(10, 545),
                Size     = new Size(1010, 50)
            };

            // Création des boutons dans l'ordre (chaque bouton a une largeur de 145px)
            btnRafraichir  = CreerBouton("Rafraîchir",       0, Color.FromArgb(70, 130, 180));
            btnModifier    = CreerBouton("Modifier",          1, Color.FromArgb(60, 160, 60));
            btnSupprimer   = CreerBouton("Supprimer",         2, Color.FromArgb(200, 60, 60));
            btnBannir      = CreerBouton("Bannir / Débannir", 3, Color.FromArgb(200, 140, 0));
            btnProfil      = CreerBouton("Mon Profil",        4, Color.FromArgb(100, 100, 180));
            btnDeconnexion = CreerBouton("Déconnexion",       5, Color.FromArgb(120, 120, 120));

            // Abonnement aux événements de clic
            btnRafraichir.Click  += (s, e) => ChargerUtilisateurs();
            btnModifier.Click    += BtnModifier_Click;
            btnSupprimer.Click   += BtnSupprimer_Click;
            btnBannir.Click      += BtnBannir_Click;
            btnProfil.Click      += BtnProfil_Click;
            btnDeconnexion.Click += BtnDeconnexion_Click;

            panneauBoutons.Controls.AddRange(new Control[]
            {
                btnRafraichir, btnModifier, btnSupprimer, btnBannir, btnProfil, btnDeconnexion
            });

            // Ajout de tous les contrôles à la fenêtre
            this.Controls.AddRange(new Control[] { lblBienvenue, dgvUtilisateurs, panneauBoutons });
        }

        /// <summary>
        /// Crée un bouton standard coloré, positionné selon son index dans le panneau.
        /// </summary>
        private Button CreerBouton(string texte, int index, Color couleur)
        {
            return new Button
            {
                Text      = texte,
                Location  = new Point(index * 168, 5),
                Size      = new Size(158, 38),
                BackColor = couleur,
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
        }

        /// <summary>
        /// Charge (ou recharge) tous les utilisateurs depuis la base
        /// et les affiche dans le DataGridView avec des colonnes nommées en français.
        /// </summary>
        private void ChargerUtilisateurs()
        {
            _utilisateurs = _ctrl.ObtenirTousLesUtilisateurs();

            // Création d'un DataTable pour contrôler les noms de colonnes affichés
            DataTable tableau = new DataTable();
            tableau.Columns.Add("id",           typeof(int));      // Colonne technique (cachée)
            tableau.Columns.Add("Nom",          typeof(string));
            tableau.Columns.Add("Prénom",       typeof(string));
            tableau.Columns.Add("Email",        typeof(string));
            tableau.Columns.Add("Rôle",         typeof(string));
            tableau.Columns.Add("Compétences",  typeof(string));
            tableau.Columns.Add("Inscription",  typeof(string));   // Formaté en string lisible
            tableau.Columns.Add("Banni",        typeof(string));   // Affiché comme texte clair

            foreach (User u in _utilisateurs)
            {
                string dateStr  = u.DateInscription == DateTime.MinValue ? "-" : u.DateInscription.ToString("dd/MM/yyyy");
                string banniStr = u.Banni ? "OUI" : "non";

                tableau.Rows.Add(u.Id, u.Nom, u.Prenom, u.Email, u.Role, u.Competences, dateStr, banniStr);
            }

            dgvUtilisateurs.DataSource = tableau;

            // Cacher la colonne id (elle sert à retrouver l'utilisateur mais est inutile visuellement)
            if (dgvUtilisateurs.Columns["id"] != null)
                dgvUtilisateurs.Columns["id"].Visible = false;
        }

        /// <summary>
        /// Retourne l'objet User correspondant à la ligne sélectionnée dans le tableau.
        /// Affiche un message si aucune ligne n'est sélectionnée.
        /// </summary>
        private User ObtenirUtilisateurSelectionne()
        {
            if (dgvUtilisateurs.SelectedRows.Count == 0)
            {
                MessageBox.Show("Veuillez d'abord sélectionner un utilisateur dans le tableau.",
                    "Aucune sélection", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return null;
            }

            // On récupère l'id depuis la cellule cachée "id" de la ligne sélectionnée
            int id = (int)dgvUtilisateurs.SelectedRows[0].Cells["id"].Value;

            // On cherche l'utilisateur dans la liste en mémoire par son id
            return _utilisateurs.Find(u => u.Id == id);
        }

        /// <summary>
        /// Ouvre le formulaire de modification pour l'utilisateur sélectionné.
        /// Recharge le tableau après fermeture du formulaire.
        /// </summary>
        private void BtnModifier_Click(object sender, EventArgs e)
        {
            User user = ObtenirUtilisateurSelectionne();
            if (user == null) return;

            // ShowDialog = fenêtre modale (bloque MainForm pendant la modification)
            EditUserForm form = new EditUserForm(user.Id);
            form.ShowDialog(this);
            ChargerUtilisateurs(); // Actualise le tableau après modification
        }

        /// <summary>
        /// Supprime l'utilisateur sélectionné après confirmation par l'admin.
        /// </summary>
        private void BtnSupprimer_Click(object sender, EventArgs e)
        {
            User user = ObtenirUtilisateurSelectionne();
            if (user == null) return;

            DialogResult reponse = MessageBox.Show(
                $"Supprimer définitivement l'utilisateur :\n\n" +
                $"  {user.Prenom} {user.Nom} ({user.Email})\n\n" +
                "Cette action est irréversible !",
                "Confirmer la suppression",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Warning);

            if (reponse == DialogResult.Yes)
            {
                _ctrl.SupprimerUtilisateur(user.Id);
                ChargerUtilisateurs();
            }
        }

        /// <summary>
        /// Bascule l'état de bannissement de l'utilisateur sélectionné.
        /// Si banni → débannit. Si actif → bannit.
        /// </summary>
        private void BtnBannir_Click(object sender, EventArgs e)
        {
            User user = ObtenirUtilisateurSelectionne();
            if (user == null) return;

            string action = user.Banni ? "débannir" : "bannir";

            DialogResult reponse = MessageBox.Show(
                $"Voulez-vous {action} l'utilisateur {user.Prenom} {user.Nom} ?",
                "Confirmer l'action",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question);

            if (reponse == DialogResult.Yes)
            {
                _ctrl.BasculerBannissement(user.Id, user.Banni);
                ChargerUtilisateurs();
            }
        }

        /// <summary>
        /// Ouvre le formulaire de modification du profil de l'admin connecté.
        /// (Email + mot de passe uniquement)
        /// </summary>
        private void BtnProfil_Click(object sender, EventArgs e)
        {
            // estProfil = true → le formulaire s'adapte (seuls email et mdp sont modifiables)
            EditUserForm form = new EditUserForm(AuthController.AdminConnecte.Id, estProfil: true);
            form.ShowDialog(this);
        }

        /// <summary>
        /// Déconnecte l'admin, ferme cette fenêtre et revient à l'écran de connexion.
        /// </summary>
        private void BtnDeconnexion_Click(object sender, EventArgs e)
        {
            new AuthController().Deconnexion();
            LoginForm login = new LoginForm();
            login.Show();
            this.Close();
        }
    }
}
