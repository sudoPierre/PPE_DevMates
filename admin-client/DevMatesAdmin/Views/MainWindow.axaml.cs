using System.Collections.ObjectModel;
using Avalonia.Controls;
using Avalonia.Interactivity;
using DevMatesAdmin.Helpers;
using DevMatesAdmin.Models;
using DevMatesAdmin.Services;

namespace DevMatesAdmin.Views;

public partial class MainWindow : Window
{
    private readonly ApiService _apiService;

    // ObservableCollection permet au DataGrid de se rafraîchir automatiquement
    private readonly ObservableCollection<User> _utilisateurs = new();

    // Constructeur sans paramètre requis par le designer Avalonia
    public MainWindow() : this(new ApiService()) { }

    public MainWindow(ApiService apiService)
    {
        _apiService = apiService;
        InitializeComponent();
        GrilleUtilisateurs.ItemsSource = _utilisateurs;
        _ = ChargerUtilisateursAsync();
    }

    // ── Chargement des données ───────────────────────────────────────────────

    private async Task ChargerUtilisateursAsync()
    {
        MettreAJourStatut("Chargement...");
        try
        {
            var liste = await _apiService.ObtenirUtilisateursAsync();
            _utilisateurs.Clear();
            foreach (var u in liste)
                _utilisateurs.Add(u);
            MettreAJourStatut($"{_utilisateurs.Count} utilisateur(s)");
        }
        catch (Exception ex)
        {
            MettreAJourStatut("Erreur de chargement");
            await DialogHelper.InformerAsync(this, "Erreur réseau", ex.Message);
        }
    }

    // ── Gestionnaires de boutons ─────────────────────────────────────────────

    private async void BtnActualiser_Click(object? sender, RoutedEventArgs e) =>
        await ChargerUtilisateursAsync();

    private async void BtnToggle_Click(object? sender, RoutedEventArgs e)
    {
        var user = UtilisateurSélectionné();
        if (user is null) { await InfoSélectionAsync(); return; }

        var action = user.IsActive ? "désactiver" : "activer";
        if (!await DialogHelper.ConfirmerAsync(this, "Confirmation",
                $"Voulez-vous {action} le compte de « {user.Email} » ?")) return;

        try
        {
            await _apiService.ModifierStatutActifAsync(user.Id, !user.IsActive);
            await ChargerUtilisateursAsync();
        }
        catch (Exception ex)
        {
            await DialogHelper.InformerAsync(this, "Erreur", ex.Message);
        }
    }

    private async void BtnBannir_Click(object? sender, RoutedEventArgs e)
    {
        var user = UtilisateurSélectionné();
        if (user is null) { await InfoSélectionAsync(); return; }

        if (!await DialogHelper.ConfirmerAsync(this, "Bannissement",
                $"Bannir « {user.Email} » ?\nIl ne pourra plus se connecter à la plateforme.")) return;

        try
        {
            await _apiService.BannirUtilisateurAsync(user.Id);
            await ChargerUtilisateursAsync();
        }
        catch (Exception ex)
        {
            await DialogHelper.InformerAsync(this, "Erreur", ex.Message);
        }
    }

    private async void BtnSupprimer_Click(object? sender, RoutedEventArgs e)
    {
        var user = UtilisateurSélectionné();
        if (user is null) { await InfoSélectionAsync(); return; }

        if (!await DialogHelper.ConfirmerAsync(this, "Suppression définitive",
                $"SUPPRIMER définitivement « {user.Email} » ?\n\n" +
                "Son profil, ses matchs et ses messages seront également supprimés.\n" +
                "Cette action est irréversible.")) return;

        try
        {
            await _apiService.SupprimerUtilisateurAsync(user.Id);
            await ChargerUtilisateursAsync();
        }
        catch (Exception ex)
        {
            await DialogHelper.InformerAsync(this, "Erreur", ex.Message);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private User? UtilisateurSélectionné() =>
        GrilleUtilisateurs.SelectedItem as User;

    private void MettreAJourStatut(string texte) =>
        LblStatut.Text = texte;

    private Task InfoSélectionAsync() =>
        DialogHelper.InformerAsync(this, "Sélection requise",
            "Veuillez sélectionner un utilisateur dans la liste.");
}
