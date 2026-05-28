using Avalonia;

namespace DevMatesAdmin;

/// <summary>
/// Point d'entrée de l'application d'administration DevMates (Avalonia — cross-platform)
/// </summary>
internal class Program
{
    // STA requis sous Windows pour COM ; ignoré silencieusement sur macOS/Linux
    [STAThread]
    public static void Main(string[] args) =>
        BuildAvaloniaApp().StartWithClassicDesktopLifetime(args);

    public static AppBuilder BuildAvaloniaApp() =>
        AppBuilder.Configure<App>()
            .UsePlatformDetect()   // Win32 sous Windows, AvaloniaNative sous macOS
            .WithInterFont()
            .LogToTrace();
}
