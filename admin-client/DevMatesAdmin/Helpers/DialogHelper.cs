using Avalonia.Controls;
using Avalonia.Layout;
using Avalonia.Media;

namespace DevMatesAdmin.Helpers;

/// <summary>
/// Boîtes de dialogue modales légères sans dépendance externe
/// </summary>
public static class DialogHelper
{
    /// <summary>Affiche un message informatif avec un bouton OK</summary>
    public static async Task InformerAsync(Window parent, string titre, string message)
    {
        await AfficherAsync(parent, titre, message, confirmer: false);
    }

    /// <summary>Affiche une question Oui/Non et retourne la réponse</summary>
    public static Task<bool> ConfirmerAsync(Window parent, string titre, string message)
    {
        return AfficherAsync(parent, titre, message, confirmer: true);
    }

    private static async Task<bool> AfficherAsync(Window parent, string titre, string message, bool confirmer)
    {
        var tcs = new TaskCompletionSource<bool>();

        var fenetre = new Window
        {
            Title                 = titre,
            Width                 = 440,
            SizeToContent         = SizeToContent.Height,
            CanResize             = false,
            WindowStartupLocation = WindowStartupLocation.CenterOwner,
            Background            = Brushes.White,
        };

        var contenu = new StackPanel { Margin = new Avalonia.Thickness(28, 24), Spacing = 24 };

        contenu.Children.Add(new TextBlock
        {
            Text         = message,
            TextWrapping = TextWrapping.Wrap,
            MaxWidth     = 390,
            FontSize     = 14,
            LineHeight   = 22,
        });

        var boutons = new StackPanel
        {
            Orientation         = Orientation.Horizontal,
            HorizontalAlignment = HorizontalAlignment.Right,
            Spacing             = 10,
        };

        if (confirmer)
        {
            var btnNon = new Button
            {
                Content = "Annuler",
                Padding = new Avalonia.Thickness(20, 8),
            };
            btnNon.Click += (_, _) => { tcs.TrySetResult(false); fenetre.Close(); };
            boutons.Children.Add(btnNon);
        }

        var btnOk = new Button
        {
            Content    = confirmer ? "Confirmer" : "OK",
            Padding    = new Avalonia.Thickness(20, 8),
            Background = confirmer
                ? new SolidColorBrush(Color.Parse("#DC3545"))
                : new SolidColorBrush(Color.Parse("#4A90D9")),
            Foreground = Brushes.White,
        };
        btnOk.Click += (_, _) => { tcs.TrySetResult(true); fenetre.Close(); };
        boutons.Children.Add(btnOk);

        contenu.Children.Add(boutons);
        fenetre.Content = contenu;

        // Retourner false si la fenêtre est fermée sans cliquer sur un bouton
        fenetre.Closed += (_, _) => tcs.TrySetResult(false);

        await fenetre.ShowDialog(parent);
        return await tcs.Task;
    }
}
