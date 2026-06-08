// =============================================
// FICHIER : Views/EditUserForm.cs
// RÔLE    : Fenêtre de modification d'un utilisateur.
//           Sert aussi à modifier le profil de l'admin
//           connecté (paramètre estProfil = true).
//           Le formulaire est pré-rempli avec les
//           données actuelles de l'utilisateur.
// =============================================

using System;
using System.Drawing;
using System.Windows.Forms;
using DevMatesAdmin.Controllers;
using DevMatesAdmin.Models;

namespace DevMatesAdmin.Views
{
    public class EditUserForm : Form
    {
        // --- Champs du formulaire ---
        private TextBox  txtNom;
        private TextBox  txtPrenom;
        private TextBox  txtEmail;
        private TextBox  txtCompetences;
        private TextBox  txtBio;
        private ComboBox cmbRole;
        private TextBox  txtNouveauMdp;
        private TextBox  txtConfirmMdp;

        // --- Boutons ---
        private Button   btnEnregistrer;
        private Button   btnAnnuler;

        private readonly UserController _ctrl     = new UserController();
        private readonly int            _userId;   // Id de l'utilisateur à modifier
        private readonly bool           _estProfil; // true = mode profil admin
        private User                    _user;     // Données actuelles (chargées depuis la base)

        /// <summary>
        /// Constructeur.
        /// userId    : identifiant de l'utilisateur à modifier.
        /// estProfil : si true, seuls email et mot de passe sont éditables (mode "Mon Profil").
        /// </summary>
        public EditUserForm(int userId, bool estProfil = false)
        {
            _userId    = userId;
            _estProfil = estProfil;
            InitialiserControles();
            ChargerDonnees();
        }

        /// <summary>
        /// Crée et positionne tous les contrôles du formulaire selon le mode (profil ou modification).
        /// </summary>
        private void InitialiserControles()
        {
            this.Text            = _estProfil ? "Mon Profil — Modifier" : "Modifier un utilisateur";
            this.Size            = new Size(480, 560);
            this.StartPosition   = FormStartPosition.CenterParent;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox     = false;

            int x      = 10;  // Position X pour les labels
            int xField = 160; // Position X pour les champs texte
            int y      = 20;  // Position Y courante (incrémentée à chaque ligne)
            int ecart  = 50;  // Espace vertical entre chaque champ

            // --- Champs de base ---
            AjouterLabel("Nom :",           x, y); txtNom         = AjouterChamp(xField, y); y += ecart;
            AjouterLabel("Prénom :",        x, y); txtPrenom      = AjouterChamp(xField, y); y += ecart;
            AjouterLabel("Email :",         x, y); txtEmail       = AjouterChamp(xField, y); y += ecart;
            AjouterLabel("Compétences :",   x, y); txtCompetences = AjouterChamp(xField, y); y += ecart;
            AjouterLabel("Bio :",           x, y); txtBio         = AjouterChamp(xField, y, hauteur: 60, multiline: true); y += ecart + 30;

            // --- Champ Rôle (liste déroulante) ---
            AjouterLabel("Rôle :", x, y);
            cmbRole = new ComboBox
            {
                Location      = new Point(xField, y),
                Size          = new Size(290, 23),
                DropDownStyle = ComboBoxStyle.DropDownList // Sélection uniquement, pas de saisie libre
            };
            cmbRole.Items.AddRange(new string[] { "user", "admin" });
            this.Controls.Add(cmbRole);
            y += ecart;

            // --- Champs mot de passe (affichés uniquement en mode profil) ---
            Label lblNouveauMdp  = AjouterLabel("Nouveau MDP :",  x, y); txtNouveauMdp = AjouterChamp(xField, y, mdp: true); y += ecart;
            Label lblConfirmMdp  = AjouterLabel("Confirmer MDP :", x, y); txtConfirmMdp = AjouterChamp(xField, y, mdp: true); y += ecart;

            // En mode "modification utilisateur" : les champs MDP sont cachés
            lblNouveauMdp.Visible = _estProfil;
            txtNouveauMdp.Visible = _estProfil;
            lblConfirmMdp.Visible = _estProfil;
            txtConfirmMdp.Visible = _estProfil;

            // En mode "profil admin" : les champs non-profil sont grisés (lecture seule)
            if (_estProfil)
            {
                txtNom.Enabled         = false;
                txtPrenom.Enabled      = false;
                txtCompetences.Enabled = false;
                txtBio.Enabled         = false;
                cmbRole.Enabled        = false;
            }

            // --- Boutons Enregistrer / Annuler ---
            btnEnregistrer = new Button
            {
                Text      = "Enregistrer",
                Location  = new Point(90, y + 10),
                Size      = new Size(120, 38),
                BackColor = Color.FromArgb(60, 160, 60),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnAnnuler = new Button
            {
                Text      = "Annuler",
                Location  = new Point(230, y + 10),
                Size      = new Size(120, 38),
                BackColor = Color.FromArgb(150, 150, 150),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };

            btnEnregistrer.Click += BtnEnregistrer_Click;
            btnAnnuler.Click     += (s, e) => this.Close();

            this.Controls.AddRange(new Control[] { btnEnregistrer, btnAnnuler });
        }

        // --- Méthodes utilitaires pour créer les contrôles ---

        /// <summary>
        /// Crée et ajoute un Label à la fenêtre. Retourne le label créé.
        /// </summary>
        private Label AjouterLabel(string texte, int x, int y)
        {
            var lbl = new Label { Text = texte, Location = new Point(x, y + 3), AutoSize = true };
            this.Controls.Add(lbl);
            return lbl;
        }

        /// <summary>
        /// Crée et ajoute un TextBox à la fenêtre. Retourne le TextBox créé.
        /// </summary>
        private TextBox AjouterChamp(int x, int y, int hauteur = 23, bool multiline = false, bool mdp = false)
        {
            var txt = new TextBox
            {
                Location     = new Point(x, y),
                Size         = new Size(290, hauteur),
                Multiline    = multiline,
                PasswordChar = mdp ? '*' : '\0'
            };
            this.Controls.Add(txt);
            return txt;
        }

        /// <summary>
        /// Charge les données de l'utilisateur depuis la base et pré-remplit le formulaire.
        /// </summary>
        private void ChargerDonnees()
        {
            _user = _ctrl.ObtenirUtilisateur(_userId);

            if (_user == null)
            {
                MessageBox.Show("Utilisateur introuvable en base.", "Erreur",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
                this.Close();
                return;
            }

            // Pré-remplissage des champs avec les valeurs actuelles
            txtNom.Text         = _user.Nom;
            txtPrenom.Text      = _user.Prenom;
            txtEmail.Text       = _user.Email;
            txtCompetences.Text = _user.Competences;
            txtBio.Text         = _user.Bio;

            // Sélectionner le rôle actuel dans la liste déroulante
            if (cmbRole.Items.Contains(_user.Role))
                cmbRole.SelectedItem = _user.Role;
            else
                cmbRole.SelectedIndex = 0; // "user" par défaut si rôle inconnu
        }

        /// <summary>
        /// Événement : clic sur "Enregistrer".
        /// Valide les champs puis appelle le controller pour mettre à jour la base.
        /// </summary>
        private void BtnEnregistrer_Click(object sender, EventArgs e)
        {
            if (_estProfil)
            {
                // --- Mode Profil Admin : email + mot de passe ---

                if (string.IsNullOrWhiteSpace(txtEmail.Text))
                {
                    MessageBox.Show("L'adresse email ne peut pas être vide.",
                        "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }

                // Si un nouveau mot de passe est saisi, vérifier la confirmation
                if (!string.IsNullOrEmpty(txtNouveauMdp.Text)
                    && txtNouveauMdp.Text != txtConfirmMdp.Text)
                {
                    MessageBox.Show("Les deux mots de passe ne correspondent pas.",
                        "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }

                // Le controller gère le hashage bcrypt si un nouveau MDP est fourni
                bool ok = _ctrl.ModifierProfil(_userId, txtEmail.Text, txtNouveauMdp.Text);

                if (ok)
                {
                    MessageBox.Show("Profil mis à jour avec succès.",
                        "Succès", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    this.Close();
                }
                else
                {
                    MessageBox.Show("Erreur lors de la mise à jour du profil.",
                        "Erreur", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            else
            {
                // --- Mode Modification Utilisateur ---

                if (string.IsNullOrWhiteSpace(txtEmail.Text))
                {
                    MessageBox.Show("L'adresse email ne peut pas être vide.",
                        "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }

                // Mise à jour de l'objet User avec les nouvelles valeurs saisies
                _user.Nom         = txtNom.Text.Trim();
                _user.Prenom      = txtPrenom.Text.Trim();
                _user.Email       = txtEmail.Text.Trim();
                _user.Competences = txtCompetences.Text.Trim();
                _user.Bio         = txtBio.Text.Trim();
                _user.Role        = cmbRole.SelectedItem?.ToString() ?? "user";

                bool ok = _ctrl.ModifierUtilisateur(_user);

                if (ok)
                {
                    MessageBox.Show("Utilisateur modifié avec succès.",
                        "Succès", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    this.Close();
                }
                else
                {
                    MessageBox.Show("Erreur lors de la modification.",
                        "Erreur", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
        }
    }
}
