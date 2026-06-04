import React, { useState, useEffect, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import api from '../api/api';

// Intervalle du Short Polling : 4 secondes (compromis entre réactivité et charge serveur)
const POLLING_MS = 4000;

export default function Chat() {
  const { matchId }  = useParams();
  const { user }     = useAuth();
  const navigate     = useNavigate();
  const [messages, setMessages]       = useState([]);
  const [texte, setTexte]             = useState('');
  const [envoi, setEnvoi]             = useState(false);
  const [erreur, setErreur]           = useState('');
  const finListeRef  = useRef(null);
  const dernierRef   = useRef(null); // horodatage DATETIME du dernier message connu
  const intervalRef  = useRef(null);

  // Chargement initial de l'historique complet
  useEffect(() => {
    chargerHistorique();
    intervalRef.current = setInterval(rafraichir, POLLING_MS);
    return () => clearInterval(intervalRef.current);
  }, [matchId]);

  // Défilement automatique vers le bas à chaque nouveau message
  useEffect(() => {
    finListeRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const chargerHistorique = async () => {
    try {
      const { data } = await api.get(`/messages/${matchId}`);
      setMessages(data);
      // Initialise le curseur de polling : dernier message ou maintenant si aucun message
      // Sans cette initialisation, le polling reste désactivé et les nouveaux messages n'arrivent jamais
      dernierRef.current = data.length > 0
        ? data[data.length - 1].sent_at
        : new Date().toISOString().slice(0, 19).replace('T', ' ');
    } catch {
      setErreur('Impossible de charger les messages. Vérifiez votre connexion.');
    }
  };

  // Short Polling : demande uniquement les messages postérieurs au dernier connu
  const rafraichir = async () => {
    if (!dernierRef.current) return;
    try {
      const { data } = await api.get(`/messages/${matchId}`, {
        params: { since: dernierRef.current },
      });
      if (data.length > 0) {
        // Déduplique par ID pour éviter les doublons lors d'une recharge simultanée
        setMessages((prev) => {
          const ids = new Set(prev.map((m) => m.id));
          const nouveaux = data.filter((m) => !ids.has(m.id));
          return nouveaux.length > 0 ? [...prev, ...nouveaux] : prev;
        });
        dernierRef.current = data[data.length - 1].sent_at;
      }
    } catch {
      // Échec silencieux : la prochaine itération réessaiera
    }
  };

  const envoyerMessage = async (e) => {
    e.preventDefault();
    const contenu = texte.trim();
    if (!contenu || envoi) return;

    setEnvoi(true);
    setErreur('');
    try {
      await api.post(`/messages/${matchId}`, { content: contenu });
      setTexte('');
      await chargerHistorique(); // Recharge pour inclure le message envoyé
    } catch {
      setErreur("Erreur lors de l'envoi du message. Réessayez.");
    } finally {
      setEnvoi(false);
    }
  };

  const estMoi = (msg) => msg.sender_id === user?.id;

  return (
    <div style={{ display: 'flex', flexDirection: 'column', height: '100dvh', maxWidth: 480, margin: '0 auto', background: '#fff' }}>

      {/* En-tête de conversation */}
      <div style={{ padding: '1rem 1.25rem', background: 'var(--couleur-primaire)', color: '#fff', display: 'flex', alignItems: 'center', gap: '0.75rem', boxShadow: '0 2px 8px rgba(0,0,0,0.15)' }}>
        <button
          onClick={() => navigate('/match')}
          style={{ background: 'none', border: 'none', color: '#fff', fontSize: '1.5rem', cursor: 'pointer', lineHeight: 1 }}
          aria-label="Retour"
        >
          ←
        </button>
        <h3 style={{ margin: 0, fontWeight: 700 }}>Conversation</h3>
      </div>

      {/* Zone des messages — défilable */}
      <div style={{ flex: 1, overflowY: 'auto', padding: '1rem', background: '#f4f6f9', display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
        {messages.length === 0 ? (
          <p style={{ textAlign: 'center', color: '#a0aec0', marginTop: '2rem' }}>
            Aucun message. Dites bonjour !
          </p>
        ) : (
          messages.map((msg, i) => (
            <div
              key={msg.id ?? i}
              style={{ display: 'flex', justifyContent: estMoi(msg) ? 'flex-end' : 'flex-start' }}
            >
              <div style={{
                maxWidth: '72%',
                padding: '0.6rem 1rem',
                borderRadius: estMoi(msg) ? '18px 18px 4px 18px' : '18px 18px 18px 4px',
                background: estMoi(msg) ? 'var(--couleur-primaire)' : '#fff',
                color: estMoi(msg) ? '#fff' : '#2d3748',
                boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
              }}>
                {!estMoi(msg) && (
                  <p style={{ fontSize: '0.7rem', fontWeight: 700, marginBottom: '0.2rem', opacity: 0.75 }}>
                    {msg.sender_name}
                  </p>
                )}
                <p style={{ wordBreak: 'break-word', lineHeight: 1.5 }}>{msg.content}</p>
                <p style={{ fontSize: '0.65rem', opacity: 0.65, textAlign: 'right', marginTop: '0.25rem' }}>
                  {new Date(msg.sent_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                </p>
              </div>
            </div>
          ))
        )}
        <div ref={finListeRef} />
      </div>

      {/* Bandeau d'erreur */}
      {erreur && (
        <div style={{ padding: '0.5rem 1rem', background: '#fee2e2', color: 'var(--couleur-danger)', fontSize: '0.85rem', textAlign: 'center' }}>
          {erreur}
        </div>
      )}

      {/* Zone de saisie */}
      <form
        onSubmit={envoyerMessage}
        style={{ padding: '0.75rem 1rem', background: '#fff', display: 'flex', gap: '0.75rem', borderTop: '1px solid #e2e8f0' }}
      >
        <input
          type="text"
          value={texte}
          onChange={(e) => setTexte(e.target.value)}
          placeholder="Écrire un message..."
          style={{ flex: 1, margin: 0, padding: '0.65rem 1rem' }}
          autoComplete="off"
        />
        <button
          type="submit"
          className="btn btn-primaire"
          disabled={envoi || !texte.trim()}
          style={{ padding: '0.65rem 1.25rem', flexShrink: 0 }}
        >
          Envoyer
        </button>
      </form>
    </div>
  );
}
