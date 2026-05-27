using DevMatesAdmin.Forms;
using DevMatesAdmin.Services;

namespace DevMatesAdmin;

/// <summary>
/// Point d'entrée de l'application d'administration DevMates (Windows Forms)
/// Communication avec l'API REST uniquement via HttpClient — pas de connexion DB directe
/// </summary>
static class Program
{
    [STAThread]
    static void Main()
    {
        ApplicationConfiguration.Initialize();

        // URL de l'API configurable — pointe par défaut vers le backend Docker exposé sur :8080
        var apiService = new ApiService("http://localhost:8080/api");

        // Étape 1 : connexion obligatoire avant accès au tableau de bord
        using var loginForm = new LoginForm(apiService);
        if (loginForm.ShowDialog() != DialogResult.OK)
        {
            return; // L'utilisateur a fermé la fenêtre ou la connexion a échoué
        }

        // Étape 2 : tableau de bord principal
        Application.Run(new MainForm(apiService));
    }
}
