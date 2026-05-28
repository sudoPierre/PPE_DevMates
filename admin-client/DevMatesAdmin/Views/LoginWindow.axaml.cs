using Avalonia.Controls;
using Avalonia.Interactivity;
using DevMatesAdmin.Services;

namespace DevMatesAdmin.Views;

public partial class LoginWindow : Window
{
    private readonly ApiService _apiService;

    // Constructeur sans paramètre requis par le designer Avalonia
    public LoginWindow() : this(new ApiService()) { }

    public LoginWindow(ApiService apiService)
    {
        _apiService = apiService;
        InitializeComponent();

        // Permettre la connexion via la touche Entrée depuis le champ mot de passe
        TxtPassword.KeyDown += async (_, e) =>
        {
            if (e.Key == Avalonia.Input.Key.Enter)
                await ConnecterAsync();
        };
    }

    private async void BtnConnexion_Click(object? sender, RoutedEventArgs e) =>
        await ConnecterAsync();

    private async Task ConnecterAsync()
    {
        AfficherErreur(string.Empty, visible: false);
        BtnConnexion.IsEnabled = false;
        BtnConnexion.Content   = "Connexion...";

        try
        {
            var resultat = await _apiService.ConnecterAsync(
                TxtEmail.Text?.Trim() ?? string.Empty,
                TxtPassword.Text       ?? string.Empty
            );

            if (resultat.User.Role != "admin")
            {
                AfficherErreur("Accès refusé. Ce panneau est réservé aux administrateurs.");
                return;
            }

            _apiService.SetToken(resultat.Token);

            // Ouvrir le tableau de bord puis fermer la fenêtre de connexion
            // L'application reste active grâce à la MainWindow ouverte
            var mainWindow = new MainWindow(_apiService);
            mainWindow.Show();
            Close();
        }
        catch (Exception ex)
        {
            AfficherErreur(ex.Message);
        }
        finally
        {
            BtnConnexion.IsEnabled = true;
            BtnConnexion.Content   = "Se connecter";
        }
    }

    private void AfficherErreur(string message, bool visible = true)
    {
        LblErreur.Text      = message;
        LblErreur.IsVisible = visible;
    }
}
