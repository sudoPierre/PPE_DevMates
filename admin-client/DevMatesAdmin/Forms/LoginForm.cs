using DevMatesAdmin.Services;

namespace DevMatesAdmin.Forms;

/// <summary>
/// Formulaire de connexion administrateur
/// Vérifie les identifiants auprès de l'API avant d'ouvrir le tableau de bord
/// </summary>
public class LoginForm : Form
{
    private readonly ApiService _apiService;

    private TextBox _txtEmail    = null!;
    private TextBox _txtMotDePasse = null!;
    private Button  _btnConnexion  = null!;
    private Label   _lblErreur     = null!;

    /// <summary>Token JWT retourné par l'API après connexion réussie</summary>
    public string Token { get; private set; } = string.Empty;

    public LoginForm(ApiService apiService)
    {
        _apiService = apiService;
        InitialiserComposants();
    }

    private void InitialiserComposants()
    {
        Text            = "DevMates Admin — Connexion";
        Size            = new Size(420, 340);
        StartPosition   = FormStartPosition.CenterScreen;
        FormBorderStyle = FormBorderStyle.FixedDialog;
        MaximizeBox     = false;
        MinimizeBox     = false;
        BackColor       = Color.White;
        Font            = new Font("Segoe UI", 9.5f);

        // Titre de l'application
        var lblTitre = new Label
        {
            Text      = "DevMates Admin",
            Font      = new Font("Segoe UI", 18, FontStyle.Bold),
            ForeColor = Color.FromArgb(255, 79, 90),
            AutoSize  = true,
            Location  = new Point(95, 22),
        };

        var lblSousTitre = new Label
        {
            Text      = "Espace administrateur",
            AutoSize  = true,
            ForeColor = Color.Gray,
            Location  = new Point(130, 62),
        };

        var lblEmail = new Label { Text = "Adresse email :", AutoSize = true, Location = new Point(40, 100) };
        _txtEmail = new TextBox
        {
            Location      = new Point(40, 120),
            Width         = 325,
            PlaceholderText = "admin@devmates.com",
        };

        var lblMdp = new Label { Text = "Mot de passe :", AutoSize = true, Location = new Point(40, 158) };
        _txtMotDePasse = new TextBox
        {
            Location     = new Point(40, 178),
            Width        = 325,
            PasswordChar = '●',
        };

        _btnConnexion = new Button
        {
            Text      = "Se connecter",
            Location  = new Point(40, 220),
            Width     = 325,
            Height    = 40,
            BackColor = Color.FromArgb(255, 79, 90),
            ForeColor = Color.White,
            FlatStyle = FlatStyle.Flat,
            Font      = new Font("Segoe UI", 10, FontStyle.Bold),
        };
        _btnConnexion.FlatAppearance.BorderSize = 0;

        _lblErreur = new Label
        {
            ForeColor = Color.FromArgb(220, 53, 69),
            AutoSize  = true,
            Location  = new Point(40, 272),
            Text      = string.Empty,
        };

        _btnConnexion.Click += BtnConnexion_Click;
        _txtMotDePasse.KeyDown += (_, e) =>
        {
            if (e.KeyCode == Keys.Enter) _ = ConnecterAsync();
        };

        Controls.AddRange(new Control[]
        {
            lblTitre, lblSousTitre,
            lblEmail, _txtEmail,
            lblMdp,   _txtMotDePasse,
            _btnConnexion, _lblErreur,
        });
    }

    private void BtnConnexion_Click(object? sender, EventArgs e) =>
        _ = ConnecterAsync();

    private async Task ConnecterAsync()
    {
        _lblErreur.Text     = string.Empty;
        _btnConnexion.Enabled = false;
        _btnConnexion.Text  = "Connexion...";

        try
        {
            var resultat = await _apiService.ConnecterAsync(
                _txtEmail.Text.Trim(),
                _txtMotDePasse.Text
            );

            if (resultat.User.Role != "admin")
            {
                _lblErreur.Text = "Accès refusé. Ce panneau est réservé aux administrateurs.";
                return;
            }

            Token = resultat.Token;
            _apiService.SetToken(Token);
            DialogResult = DialogResult.OK;
            Close();
        }
        catch (Exception ex)
        {
            _lblErreur.Text = ex.Message;
        }
        finally
        {
            _btnConnexion.Enabled = true;
            _btnConnexion.Text   = "Se connecter";
        }
    }
}
