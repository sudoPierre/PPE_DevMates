import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import api from '../api/api';

export default function Profile() {
  const { user, logout } = useAuth();
  const navigate         = useNavigate();
  const [profil, setProfil]       = useState(null);
  const [form, setForm]           = useState({});
  const [skillInput, setSkillInput] = useState('');
  const [message, setMessage]     = useState('');
  const [erreur, setErreur]       = useState('');
  const [charge, setCharge]       = useState(false);

  useEffect(() => {
    api.get('/profile').then(({ data }) => {
      setProfil(data);
      setForm({
        display_name:  data.display_name  || '',
        bio:           data.bio           || '',
        skills:        data.skills        || [],
        project_ideas: data.project_ideas || [],
        github_url:    data.github_url    || '',
        linkedin_url:  data.linkedin_url  || '',
      });
    });
  }, []);

  const ajouterSkill = () => {
    const s = skillInput.trim();
    if (s && !form.skills.includes(s)) {
      setForm({ ...form, skills: [...form.skills, s] });
    }
    setSkillInput('');
  };

  const retirerSkill = (skill) =>
    setForm({ ...form, skills: form.skills.filter((s) => s !== skill) });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage('');
    setErreur('');
    setCharge(true);
    try {
      await api.put('/profile', form);
      setMessage('Profil mis à jour avec succès !');
    } catch (err) {
      setErreur(err.response?.data?.erreur ?? 'Erreur lors de la mise à jour.');
    } finally {
      setCharge(false);
    }
  };

  if (!profil) {
    return (
      <div className="container" style={{ paddingTop: '3rem', textAlign: 'center' }}>
        <p>Chargement du profil...</p>
      </div>
    );
  }

  return (
    <div className="container" style={{ paddingTop: '1.5rem' }}>
      {/* Barre de navigation */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.25rem' }}>
        <h2>Mon Profil</h2>
        <div style={{ display: 'flex', gap: '0.5rem' }}>
          <button onClick={() => navigate('/match')} className="btn btn-secondaire" style={{ padding: '0.5rem 1rem', fontSize: '0.875rem' }}>
            Découvrir
          </button>
          <button onClick={() => navigate('/conversations')} className="btn btn-secondaire" style={{ padding: '0.5rem 1rem', fontSize: '0.875rem' }}>
            Conversations
          </button>
          <button onClick={logout} className="btn btn-danger" style={{ padding: '0.5rem 1rem', fontSize: '0.875rem' }}>
            Déconnexion
          </button>
        </div>
      </div>

      <div className="carte">
        {message && <p className="msg-succes">{message}</p>}
        {erreur   && <p className="msg-erreur">{erreur}</p>}

        <form onSubmit={handleSubmit}>
          <label>Nom affiché *</label>
          <input
            type="text"
            value={form.display_name}
            onChange={(e) => setForm({ ...form, display_name: e.target.value })}
            placeholder="Votre nom ou pseudo"
            required
          />

          <label>Biographie</label>
          <textarea
            rows={4}
            value={form.bio}
            onChange={(e) => setForm({ ...form, bio: e.target.value })}
            placeholder="Décrivez votre parcours, vos projets, ce que vous recherchez..."
          />

          <label>Compétences techniques</label>
          <div style={{ display: 'flex', gap: '0.5rem', marginBottom: '0.5rem' }}>
            <input
              type="text"
              value={skillInput}
              onChange={(e) => setSkillInput(e.target.value)}
              onKeyDown={(e) => { if (e.key === 'Enter') { e.preventDefault(); ajouterSkill(); } }}
              placeholder="Ex: React, PHP, Docker… puis Entrée"
              style={{ marginBottom: 0, flex: 1 }}
            />
            <button type="button" onClick={ajouterSkill} className="btn btn-secondaire" style={{ padding: '0.5rem 1rem' }}>
              +
            </button>
          </div>
          {form.skills.length > 0 && (
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '0.5rem', marginBottom: '1rem' }}>
              {form.skills.map((s) => (
                <span key={s} className="badge" onClick={() => retirerSkill(s)} title="Cliquer pour supprimer">
                  {s} ✕
                </span>
              ))}
            </div>
          )}

          <label>Profil GitHub</label>
          <input
            type="url"
            value={form.github_url}
            onChange={(e) => setForm({ ...form, github_url: e.target.value })}
            placeholder="https://github.com/votre-profil"
          />

          <label>Profil LinkedIn</label>
          <input
            type="url"
            value={form.linkedin_url}
            onChange={(e) => setForm({ ...form, linkedin_url: e.target.value })}
            placeholder="https://linkedin.com/in/votre-profil"
          />

          <button type="submit" className="btn btn-primaire btn-bloc" disabled={charge}>
            {charge ? 'Enregistrement...' : 'Enregistrer les modifications'}
          </button>
        </form>
      </div>
    </div>
  );
}
