using Newtonsoft.Json;

namespace DevMatesAdmin.Models;

/// <summary>
/// Représentation d'un utilisateur reçu depuis l'API REST DevMates
/// </summary>
public class User
{
    [JsonProperty("id")]
    public int Id { get; set; }

    [JsonProperty("email")]
    public string Email { get; set; } = string.Empty;

    [JsonProperty("role")]
    public string Role { get; set; } = string.Empty;

    [JsonProperty("is_active")]
    public bool IsActive { get; set; }

    [JsonProperty("is_banned")]
    public bool IsBanned { get; set; }

    [JsonProperty("display_name")]
    public string? DisplayName { get; set; }

    [JsonProperty("created_at")]
    public string CreatedAt { get; set; } = string.Empty;

    /// <summary>Libellé du rôle traduit en français pour l'affichage dans le DataGrid</summary>
    public string RoleLibelle => Role switch
    {
        "admin"          => "Administrateur",
        "developpeur"    => "Développeur",
        "porteur_projet" => "Porteur de projet",
        _                => Role,
    };

    /// <summary>Nom affiché avec fallback pour le DataGrid</summary>
    public string NomAffiche => DisplayName ?? "(non renseigné)";

    /// <summary>Libellé du statut actif pour le DataGrid</summary>
    public string EstActifLibelle => IsActive ? "✓ Oui" : "✗ Non";

    /// <summary>Libellé du statut banni pour le DataGrid</summary>
    public string EstBanniLibelle => IsBanned ? "✓ Oui" : "✗ Non";
}

/// <summary>
/// Réponse de l'API lors de la connexion (/api/auth/login)
/// </summary>
public class LoginResponse
{
    [JsonProperty("token")]
    public string Token { get; set; } = string.Empty;

    [JsonProperty("user")]
    public User User { get; set; } = new();
}

/// <summary>
/// Enveloppe générique pour les messages d'erreur de l'API
/// </summary>
public class ApiErreur
{
    [JsonProperty("erreur")]
    public string Erreur { get; set; } = string.Empty;
}
