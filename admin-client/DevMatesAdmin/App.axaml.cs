using Avalonia;
using Avalonia.Controls.ApplicationLifetimes;
using Avalonia.Markup.Xaml;
using DevMatesAdmin.Services;
using DevMatesAdmin.Views;

namespace DevMatesAdmin;

public partial class App : Application
{
    public override void Initialize()
    {
        AvaloniaXamlLoader.Load(this);
    }

    public override void OnFrameworkInitializationCompleted()
    {
        if (ApplicationLifetime is IClassicDesktopStyleApplicationLifetime desktop)
        {
            var apiService = new ApiService("http://localhost:8080/api");
            // La fenêtre de connexion est la première fenêtre affichée
            // Elle ouvre MainWindow puis se ferme elle-même en cas de succès
            desktop.MainWindow = new LoginWindow(apiService);
        }

        base.OnFrameworkInitializationCompleted();
    }
}
