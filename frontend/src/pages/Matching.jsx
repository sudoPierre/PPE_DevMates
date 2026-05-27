import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/api';

export default function Matching() {
  const navigate = useNavigate();
  const [candidats, setCandidats]       = useState([]);
  const [index, setIndex]               = useState(0);
  const [matchs, setMatchs]             = useState([]);
  const [matchNouveau, setMatchNouveau] = useState(null);
  const [charge, setCharge]             = useState(true);

  const chargerCandidats = useCallback(async () => {
    setCharge(true);
    try {
      const { data } = await api.get('/match/candidates');
      setCandidats(data);
      setIndex(0);
    } finally {
      setCharge(false);
    }
  }, []);

  const chargerMatchs = useCallback(async () => {
    const { data } = await api.get('/match');
    setMatchs(data);
  }, []);

  useEffect(() => {
    chargerCandidats();
    chargerMatchs();
  }, [chargerCandidats, chargerMatchs]);

  const swipe = async (action) => {
    const candidat = candidats[index];
    if (!candidat) return;

    const { data } = await api.post('/match/action', {
      target_id: candidat.user_id,
      action,
    });

    if (data.is_matched) {
      setMatchNouveau({ ...candidat, match_id: data.match_id });
      chargerMatchs();
    }

    const suivant = index + 1;
    if (suivant >= candidats.length) {
      chargerCandidats();
    } else {
      setIndex(suivant);
    }
  };

  const actuel = candidats[index];
  const roleLabel = (r) => r === 'developpeur' ? 'Développeur' : 'Porteur de projet';
  const initiale  = (name) => (name?.[0] ?? '?').toUpperCase();

  return (
    <div className="container" style={{ paddingTop: '1.5rem' }}>
      {/* Navigation */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem' }}>
        <h2>Découvrir</h2>
        <button onClick={() => navigate('/profile')} className="btn btn-secondaire" style={{ padding: '0.5rem 1rem', fontSize: '0.875rem' }}>
          Mon profil
        </button>
      </div>

      {/* Alerte match mutuel */}
      {matchNouveau && (
        <div className="carte" style={{ background: '#f0fdf4', border: '1.5px solid #86efac', textAlign: 'center' }}>
          <p style={{ fontSize: '2rem', marginBottom: '0.5rem' }}>🎉</p>
          <h3 style={{ color: 'var(--couleur-succes)', marginBottom: '0.5rem' }}>C'est un match !</h3>
          <p style={{ marginBottom: '1rem' }}>
            Vous et <strong>{matchNouveau.display_name}</strong> vous êtes mutuellement appréciés.
          </p>
          <div style={{ display: 'flex', gap: '0.75rem', justifyContent: 'center' }}>
            <button
              onClick={() => { setMatchNouveau(null); navigate(`/chat/${matchNouveau.match_id}`); }}
              className="btn btn-succes"
            >
              Envoyer un message
            </button>
            <button onClick={() => setMatchNouveau(null)} className="btn btn-secondaire">
              Continuer
            </button>
          </div>
        </div>
      )}

      {/* Carte candidat */}
      {charge ? (
        <div className="carte" style={{ textAlign: 'center', padding: '3rem 1.5rem' }}>
          <p style={{ color: '#718096' }}>Chargement des profils...</p>
        </div>
      ) : actuel ? (
        <div className="carte" style={{ textAlign: 'center' }}>
          {/* Avatar */}
          <div style={{
            width: 80, height: 80, borderRadius: '50%',
            background: 'var(--couleur-primaire)',
            margin: '0 auto 1rem',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            color: '#fff', fontSize: '2rem', fontWeight: 700,
          }}>
            {initiale(actuel.display_name)}
          </div>
          <h3 style={{ marginBottom: '0.25rem' }}>{actuel.display_name}</h3>
          <p style={{ color: 'var(--couleur-primaire)', fontWeight: 600, fontSize: '0.875rem', marginBottom: '0.75rem' }}>
            {roleLabel(actuel.role)}
          </p>
          <p style={{ color: '#4a5568', marginBottom: '1.25rem', minHeight: '2.5rem' }}>
            {actuel.bio || <em style={{ color: '#a0aec0' }}>Aucune biographie renseignée.</em>}
          </p>

          {actuel.skills?.length > 0 && (
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '0.4rem', justifyContent: 'center', marginBottom: '1.5rem' }}>
              {actuel.skills.map((s) => (
                <span key={s} style={{ background: '#f1f5f9', color: '#475569', padding: '0.2rem 0.7rem', borderRadius: '20px', fontSize: '0.8rem' }}>
                  {s}
                </span>
              ))}
            </div>
          )}

          {/* Boutons de swipe */}
          <div style={{ display: 'flex', gap: '2rem', justifyContent: 'center' }}>
            <button
              onClick={() => swipe('dislike')}
              style={{ width: 64, height: 64, borderRadius: '50%', border: '2px solid #e2e8f0', background: '#fff', fontSize: '1.75rem', cursor: 'pointer', transition: 'all 0.15s' }}
              aria-label="Passer"
            >
              ✕
            </button>
            <button
              onClick={() => swipe('like')}
              style={{ width: 64, height: 64, borderRadius: '50%', border: 'none', background: 'var(--couleur-primaire)', fontSize: '1.75rem', cursor: 'pointer', color: '#fff', transition: 'all 0.15s' }}
              aria-label="Aimer"
            >
              ♥
            </button>
          </div>
          <p style={{ color: '#a0aec0', fontSize: '0.8rem', marginTop: '1rem' }}>
            {candidats.length - index - 1} profil(s) restant(s)
          </p>
        </div>
      ) : (
        <div className="carte" style={{ textAlign: 'center', padding: '3rem 1.5rem' }}>
          <p style={{ color: '#718096', marginBottom: '1rem' }}>
            Plus aucun nouveau profil à découvrir pour le moment.
          </p>
          <button onClick={chargerCandidats} className="btn btn-primaire">
            Actualiser
          </button>
        </div>
      )}

      {/* Liste des matchs validés */}
      {matchs.length > 0 && (
        <div style={{ marginTop: '1.5rem' }}>
          <h3 style={{ marginBottom: '1rem' }}>Mes matchs ({matchs.length})</h3>
          {matchs.map((m) => (
            <div
              key={m.match_id}
              className="carte"
              style={{ display: 'flex', alignItems: 'center', cursor: 'pointer', padding: '1rem 1.25rem' }}
              onClick={() => navigate(`/chat/${m.match_id}`)}
            >
              <div style={{
                width: 46, height: 46, borderRadius: '50%',
                background: 'var(--couleur-primaire)',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                color: '#fff', fontWeight: 700, flexShrink: 0, marginRight: '1rem',
              }}>
                {initiale(m.display_name)}
              </div>
              <div>
                <strong>{m.display_name}</strong>
                <p style={{ fontSize: '0.8rem', color: '#718096' }}>Cliquer pour discuter →</p>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
