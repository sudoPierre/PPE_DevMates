using DevMatesAdmin.Models;
using DevMatesAdmin.Services;

namespace DevMatesAdmin.Forms;

/// <summary>
/// Tableau de bord principal — liste et gestion de tous les utilisateurs via l'API
/// </summary>
public class MainForm : Form
{
    private readonly ApiService    _apiService;
    private DataGridView           _grille       = null!;
    private Button                 _btnActualiser = null!;
    private Button                 _btnBannir     = null!;
    private Button                 _btnSupprimer  = null!;
    private Button                 _btnToggleActif = null!;
    private Label                  _lblStatut     = null!;
    private List<User>             _utilisateurs  = [];

    public MainForm(ApiService apiService)
    {
        _apiService = apiService;
        InitialiserComposants();
        _ = ChargerUtilisateursAsync();
    }

    private void InitialiserComposants()
    {
        Text        = "DevMates — Tableau de bord administrateur";
        Size        = new Size(960, 620);
        MinimumSize = new Size(800, 500);
        StartPosition = FormStartPosition.CenterScreen;
        Font        = new Font("Segoe UI", 9.5f);

        // ── Grille de données ────────────────────────────────────────────────
        _grille = new DataGridView
        {
            Dock                         = DockStyle.Fill,
            ReadOnly                     = true,
            SelectionMode                = DataGridViewSelectionMode.FullRowSelect,
            MultiSelect                  = false,
            AllowUserToAddRows           = false,
            AllowUserToDeleteRows        = false,
            AllowUserToResizeRows        = false,
            AutoSizeColumnsMode          = DataGridViewAutoSizeColumnsMode.Fill,
            BackgroundColor              = Color.White,
            BorderStyle                  = BorderStyle.None,
            RowHeadersVisible            = false,
            ColumnHeadersHeightSizeMode  = DataGridViewColumnHeadersHeightSizeMode.DisableResizing,
            ColumnHeadersHeight          = 36,
        };
        _grille.ColumnHeadersDefaultCellStyle.Font      = new Font("Segoe UI", 9.5f, FontStyle.Bold);
        _grille.ColumnHeadersDefaultCellStyle.BackColor = Color.FromArgb(248, 249, 250);
        _grille.EnableHeadersVisualStyles = false;

        // ── Barre d'outils inférieure ────────────────────────────────────────
        var barreOutils = new Panel { Dock = DockStyle.Bottom, Height = 58, BackColor = Color.FromArgb(248, 249, 250) };
        barreOutils.Paint += (s, e) =>
            e.Graphics.DrawLine(Pens.LightGray, 0, 0, barreOutils.Width, 0);

        _btnActualiser  = CréerBouton("↻  Actualiser",     Color.FromArgb(108, 117, 125));
        _btnToggleActif = CréerBouton("●  Activer / Désactiver", Color.FromArgb(40, 167, 69));
        _btnBannir      = CréerBouton("⛔  Bannir",          Color.FromArgb(255, 153, 0));
        _btnSupprimer   = CréerBouton("✕  Supprimer",        Color.FromArgb(220, 53, 69));

        _lblStatut = new Label
        {
            AutoSize  = true,
            ForeColor = Color.Gray,
            Font      = new Font("Segoe UI", 9f),
            Anchor    = AnchorStyles.Right | AnchorStyles.Top,
        };

        var flux = new FlowLayoutPanel
        {
            Dock          = DockStyle.Fill,
            FlowDirection = FlowDirection.LeftToRight,
            Padding       = new Padding(8, 10, 0, 0),
            WrapContents  = false,
        };
        flux.Controls.AddRange(new Control[] { _btnActualiser, _btnToggleActif, _btnBannir, _btnSupprimer });

        barreOutils.Controls.Add(flux);
        barreOutils.Controls.Add(_lblStatut);

        Controls.Add(_grille);
        Controls.Add(barreOutils);

        // ── Abonnement aux événements ────────────────────────────────────────
        _btnActualiser.Click   += async (_, __) => await ChargerUtilisateursAsync();
        _btnBannir.Click       += async (_, __) => await BannirAsync();
        _btnSupprimer.Click    += async (_, __) => await SupprimerAsync();
        _btnToggleActif.Click  += async (_, __) => await ToggleActifAsync();

        Resize += (_, __) => PositionnerStatut();
    }

    private static Button CréerBouton(string texte, Color fond) =>
        new()
        {
            Text      = texte,
            Width     = 170,
            Height    = 36,
            BackColor = fond,
            ForeColor = Color.White,
            FlatStyle = FlatStyle.Flat,
            Margin    = new Padding(0, 0, 8, 0),
            Font      = new Font("Segoe UI", 9.5f, FontStyle.Bold),
            Cursor    = Cursors.Hand,
        };

    // ── Chargement et affichage des données ──────────────────────────────────

    private async Task ChargerUtilisateursAsync()
    {
        MettreAJourStatut("Chargement...");
        _grille.DataSource = null;

        try
        {
            _utilisateurs = await _apiService.ObtenirUtilisateursAsync();
            AfficherUtilisateurs();
            MettreAJourStatut($"{_utilisateurs.Count} utilisateur(s) chargé(s)");
        }
        catch (Exception ex)
        {
            MettreAJourStatut("Erreur de chargement");
            MessageBox.Show(
                $"Impossible de charger les utilisateurs :\n\n{ex.Message}",
                "Erreur réseau",
                MessageBoxButtons.OK,
                MessageBoxIcon.Error
            );
        }
    }

    private void AfficherUtilisateurs()
    {
        var source = _utilisateurs.Select(u => new
        {
            ID          = u.Id,
            Nom         = u.DisplayName ?? "(non renseigné)",
            Email       = u.Email,
            Rôle        = u.RoleLibelle,
            Actif       = u.IsActive ? "✓ Oui" : "✗ Non",
            Banni       = u.IsBanned  ? "✓ Oui" : "✗ Non",
            Inscription = u.CreatedAt,
        }).ToList();

        _grille.DataSource = source;

        // Coloriage des lignes bannies pour une détection visuelle rapide
        foreach (DataGridViewRow ligne in _grille.Rows)
        {
            var userId = (int)ligne.Cells["ID"].Value;
            var user   = _utilisateurs.First(u => u.Id == userId);
            if (user.IsBanned)
                ligne.DefaultCellStyle.BackColor = Color.FromArgb(255, 235, 235);
            else if (!user.IsActive)
                ligne.DefaultCellStyle.BackColor = Color.FromArgb(245, 245, 245);
        }
    }

    // ── Actions administrateur ───────────────────────────────────────────────

    private async Task BannirAsync()
    {
        var user = UtilisateurSélectionné();
        if (user is null) { InfoSélection(); return; }

        if (MessageBox.Show(
                $"Bannir l'utilisateur \"{user.Email}\" ?\nIl ne pourra plus se connecter.",
                "Confirmation de bannissement",
                MessageBoxButtons.YesNo, MessageBoxIcon.Warning) != DialogResult.Yes) return;

        try
        {
            await _apiService.BannirUtilisateurAsync(user.Id);
            await ChargerUtilisateursAsync();
            MessageBox.Show("Utilisateur banni.", "Succès", MessageBoxButtons.OK, MessageBoxIcon.Information);
        }
        catch (Exception ex) { AfficherErreur(ex.Message); }
    }

    private async Task SupprimerAsync()
    {
        var user = UtilisateurSélectionné();
        if (user is null) { InfoSélection(); return; }

        if (MessageBox.Show(
                $"SUPPRESSION DÉFINITIVE de \"{user.Email}\" ?\n\nSon profil, ses matchs et ses messages seront également supprimés.\nCette action est irréversible.",
                "Confirmation de suppression",
                MessageBoxButtons.YesNo, MessageBoxIcon.Warning) != DialogResult.Yes) return;

        try
        {
            await _apiService.SupprimerUtilisateurAsync(user.Id);
            await ChargerUtilisateursAsync();
            MessageBox.Show("Utilisateur supprimé définitivement.", "Succès", MessageBoxButtons.OK, MessageBoxIcon.Information);
        }
        catch (Exception ex) { AfficherErreur(ex.Message); }
    }

    private async Task ToggleActifAsync()
    {
        var user = UtilisateurSélectionné();
        if (user is null) { InfoSélection(); return; }

        var nouvelEtat = !user.IsActive;
        var action     = nouvelEtat ? "activer" : "désactiver";

        if (MessageBox.Show(
                $"Voulez-vous {action} le compte de \"{user.Email}\" ?",
                "Confirmation",
                MessageBoxButtons.YesNo, MessageBoxIcon.Question) != DialogResult.Yes) return;

        try
        {
            await _apiService.ModifierStatutActifAsync(user.Id, nouvelEtat);
            await ChargerUtilisateursAsync();
        }
        catch (Exception ex) { AfficherErreur(ex.Message); }
    }

    // ── Helpers UI ───────────────────────────────────────────────────────────

    private User? UtilisateurSélectionné()
    {
        if (_grille.SelectedRows.Count == 0) return null;
        var id = (int)_grille.SelectedRows[0].Cells["ID"].Value;
        return _utilisateurs.FirstOrDefault(u => u.Id == id);
    }

    private void MettreAJourStatut(string texte)
    {
        _lblStatut.Text = texte;
        PositionnerStatut();
    }

    private void PositionnerStatut()
    {
        var barreOutils = _lblStatut.Parent;
        if (barreOutils is null) return;
        _lblStatut.Location = new Point(
            barreOutils.Width - _lblStatut.Width - 16,
            (barreOutils.Height - _lblStatut.Height) / 2
        );
    }

    private static void InfoSélection() =>
        MessageBox.Show("Veuillez sélectionner un utilisateur dans la liste.", "Sélection requise",
            MessageBoxButtons.OK, MessageBoxIcon.Information);

    private static void AfficherErreur(string message) =>
        MessageBox.Show(message, "Erreur", MessageBoxButtons.OK, MessageBoxIcon.Error);
}
