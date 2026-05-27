using System.Net.Http.Headers;
using System.Text;
using DevMatesAdmin.Models;
using Newtonsoft.Json;

namespace DevMatesAdmin.Services;

/// <summary>
/// Service de communication avec l'API REST DevMates
/// Utilise HttpClient — aucune connexion directe à MySQL n'est effectuée ici
/// </summary>
public class ApiService
{
    // Une seule instance HttpClient partagée (bonne pratique .NET)
    private static readonly HttpClient _client = new()
    {
        Timeout = TimeSpan.FromSeconds(15),
    };

    private readonly string _baseUrl;

    public ApiService(string baseUrl = "http://localhost:8080/api")
    {
        _baseUrl = baseUrl.TrimEnd('/');
    }

    /// <summary>
    /// Définit le token JWT dans l'en-tête Authorization de toutes les requêtes suivantes
    /// </summary>
    public void SetToken(string token)
    {
        _client.DefaultRequestHeaders.Authorization =
            new AuthenticationHeaderValue("Bearer", token);
    }

    // ── Authentification ────────────────────────────────────────────────────────

    /// <summary>
    /// Authentifie l'administrateur auprès de l'API et retourne la réponse avec le token
    /// </summary>
    public async Task<LoginResponse> ConnecterAsync(string email, string motDePasse)
    {
        var corps  = Serialiser(new { email, password = motDePasse });
        var reponse = await _client.PostAsync($"{_baseUrl}/auth/login", corps);
        var json   = await reponse.Content.ReadAsStringAsync();

        if (!reponse.IsSuccessStatusCode)
        {
            var err = JsonConvert.DeserializeObject<ApiErreur>(json);
            throw new Exception(err?.Erreur ?? $"Erreur HTTP {(int)reponse.StatusCode}");
        }

        return JsonConvert.DeserializeObject<LoginResponse>(json)
            ?? throw new Exception("Réponse invalide du serveur.");
    }

    // ── Gestion des utilisateurs (admin) ────────────────────────────────────────

    /// <summary>Récupère la liste complète des utilisateurs</summary>
    public async Task<List<User>> ObtenirUtilisateursAsync()
    {
        var reponse = await _client.GetAsync($"{_baseUrl}/admin/users");
        var json    = await reponse.Content.ReadAsStringAsync();

        if (!reponse.IsSuccessStatusCode)
            throw new Exception("Impossible de récupérer la liste des utilisateurs.");

        return JsonConvert.DeserializeObject<List<User>>(json) ?? [];
    }

    /// <summary>Bannit un utilisateur par son identifiant</summary>
    public async Task BannirUtilisateurAsync(int userId)
    {
        var reponse = await _client.PostAsync($"{_baseUrl}/admin/users/{userId}/ban", null);
        await AssertSuccesAsync(reponse, "Impossible de bannir cet utilisateur.");
    }

    /// <summary>Supprime définitivement un utilisateur</summary>
    public async Task SupprimerUtilisateurAsync(int userId)
    {
        var reponse = await _client.DeleteAsync($"{_baseUrl}/admin/users/{userId}");
        await AssertSuccesAsync(reponse, "Impossible de supprimer cet utilisateur.");
    }

    /// <summary>Active ou désactive le compte d'un utilisateur</summary>
    public async Task ModifierStatutActifAsync(int userId, bool actif)
    {
        var requete = new HttpRequestMessage(HttpMethod.Put, $"{_baseUrl}/admin/users/{userId}")
        {
            Content = Serialiser(new { is_active = actif }),
        };
        var reponse = await _client.SendAsync(requete);
        await AssertSuccesAsync(reponse, "Impossible de modifier le statut de l'utilisateur.");
    }

    /// <summary>Modifie le rôle d'un utilisateur</summary>
    public async Task ModifierRoleAsync(int userId, string role)
    {
        var requete = new HttpRequestMessage(HttpMethod.Put, $"{_baseUrl}/admin/users/{userId}")
        {
            Content = Serialiser(new { role }),
        };
        var reponse = await _client.SendAsync(requete);
        await AssertSuccesAsync(reponse, "Impossible de modifier le rôle de l'utilisateur.");
    }

    // ── Helpers privés ──────────────────────────────────────────────────────────

    private static StringContent Serialiser(object obj) =>
        new(JsonConvert.SerializeObject(obj), Encoding.UTF8, "application/json");

    private static async Task AssertSuccesAsync(HttpResponseMessage reponse, string msgEchec)
    {
        if (reponse.IsSuccessStatusCode) return;

        var json = await reponse.Content.ReadAsStringAsync();
        var err  = JsonConvert.DeserializeObject<ApiErreur>(json);
        throw new Exception(err?.Erreur ?? msgEchec);
    }
}
