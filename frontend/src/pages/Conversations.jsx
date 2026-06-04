import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/api';

export default function Conversations() {
  const navigate = useNavigate();
  const [matchs, setMatchs] = useState([]);
  const [charge, setCharge] = useState(true);

  useEffect(() => {
    api.get('/match')
      .then(({ data }) => setMatchs(data))
      .finally(() => setCharge(false));
  }, []);

  const initiale = (name) => (name?.[0] ?? '?').toUpperCase();

  return (
    <div className="container" style={{ paddingTop: '1.5rem' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.25rem' }}>
        <h2>Mes conversations</h2>
        <button onClick={() => navigate('/match')} className="btn btn-secondaire" style={{ padding: '0.5rem 1rem', fontSize: '0.875rem' }}>
          Découvrir
        </button>
      </div>

      {charge ? (
        <div className="carte" style={{ textAlign: 'center', padding: '2rem' }}>
          <p style={{ color: '#718096' }}>Chargement...</p>
        </div>
      ) : matchs.length === 0 ? (
        <div className="carte" style={{ textAlign: 'center', padding: '3rem 1.5rem' }}>
          <p style={{ color: '#718096', marginBottom: '0.75rem' }}>Aucune conversation pour le moment.</p>
          <p style={{ color: '#a0aec0', fontSize: '0.9rem', marginBottom: '1.5rem' }}>
            Likez des profils pour obtenir vos premiers matchs !
          </p>
          <button onClick={() => navigate('/match')} className="btn btn-primaire">
            Découvrir des profils
          </button>
        </div>
      ) : (
        matchs.map((m) => (
          <div
            key={m.match_id}
            className="carte"
            style={{ display: 'flex', alignItems: 'center', cursor: 'pointer', padding: '1rem 1.25rem', marginBottom: '0.5rem' }}
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
              <p style={{ fontSize: '0.8rem', color: '#718096', margin: 0 }}>Ouvrir la conversation →</p>
            </div>
          </div>
        ))
      )}
    </div>
  );
}
