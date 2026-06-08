// =============================================
// FICHIER : Views/LoginForm.cs
// RÔLE    : Fenêtre de connexion de l'administrateur.
//           Premier écran affiché au lancement de
//           l'application.
// =============================================

using System;
using System.Drawing;
using System.Windows.Forms;
using DevMatesAdmin.Controllers;

namespace DevMatesAdmin.Views
{
    public class LoginForm : Form
    {
        // --- Contrôles de la fenêtre ---
        private Label   lblTitre;
        private Label   lblEmail;
        private Label   lblMotDePasse;
        private TextBox txtEmail;
        private TextBox txtMotDePasse;
        private Button  btnConnexion;

        private readonly AuthController _auth = new AuthController();

        /// <summary>
        /// Constructeur : crée et configure tous les contrôles de la fenêtre.
        /// </summary>
        public LoginForm()
        {
            InitialiserControles();
        }

        /// <summary>
        /// Crée et positionne tous les contrôles de la fenêtre de connexion.
        /// </summary>
        private void InitialiserControles()
        {
            // --- Propriétés de la fenêtre ---
            this.Text            = "DevMates Admin — Connexion";
            this.Size            = new Size(420, 300);
            this.StartPosition   = FormStartPosition.CenterScreen;
            this.FormBorderStyle = FormBorderStyle.FixedDialog; // Pas redimensionnable
            this.MaximizeBox     = false;
            this.MinimizeBox     = false;

            // --- Titre ---
            lblTitre = new Label
            {
                Text      = "DevMates — Panel Administrateur",
                Font      = new Font("Arial", 13, FontStyle.Bold),
                Location  = new Point(10, 20),
                Size      = new Size(380, 30),
                TextAlign = ContentAlignment.MiddleCenter
            };

            // --- Label et champ Email ---
            lblEmail = new Label  { Text = "Email :",        Location = new Point(40, 80),  AutoSize = true };
            txtEmail = new TextBox{ Location = new Point(160, 77), Size = new Size(210, 23) };

            // --- Label et champ Mot de passe ---
            lblMotDePasse = new Label  { Text = "Mot de passe :", Location = new Point(40, 125), AutoSize = true };
            txtMotDePasse = new TextBox
            {
                Location     = new Point(160, 122),
                Size         = new Size(210, 23),
                PasswordChar = '*' // Les caractères saisis sont masqués
            };

            // --- Bouton Connexion ---
            btnConnexion = new Button
            {
                Text     = "Se connecter",
                Location = new Point(140, 180),
                Size     = new Size(130, 40)
            };
            btnConnexion.Click += BtnConnexion_Click;

            // Permet aussi de valider avec la touche Entrée
            this.AcceptButton = btnConnexion;

            // --- Ajout de tous les contrôles à la fenêtre ---
            this.Controls.AddRange(new Control[]
            {
                lblTitre, lblEmail, txtEmail, lblMotDePasse, txtMotDePasse, btnConnexion
            });
        }

        /// <summary>
        /// Événement : clic sur "Se connecter".
        /// Vérifie les identifiants via AuthController.
        /// Si OK → ouvre MainForm. Sinon → message d'erreur.
        /// </summary>
        private void BtnConnexion_Click(object sender, EventArgs e)
        {
            string email = txtEmail.Text.Trim();
            string mdp   = txtMotDePasse.Text;

            // Vérification basique : champs non vides
            if (string.IsNullOrEmpty(email) || string.IsNullOrEmpty(mdp))
            {
                MessageBox.Show("Veuillez remplir les deux champs.",
                    "Champs manquants", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            // Tentative de connexion via le controller
            if (_auth.Connexion(email, mdp))
            {
                // Connexion réussie → afficher la fenêtre principale
                MainForm main = new MainForm();
                main.Show();
                this.Hide(); // Cacher la fenêtre de connexion (pas la fermer)

                // Quand l'admin ferme MainForm → fermer aussi l'application
                main.FormClosed += (s, args) => this.Close();
            }
            else
            {
                MessageBox.Show(
                    "Email ou mot de passe incorrect.\n" +
                    "Vérifiez que votre compte a bien le rôle 'admin' dans la base.",
                    "Connexion refusée", MessageBoxButtons.OK, MessageBoxIcon.Error);

                txtMotDePasse.Clear(); // Effacer le mot de passe pour une nouvelle tentative
                txtMotDePasse.Focus();
            }
        }
    }
}
