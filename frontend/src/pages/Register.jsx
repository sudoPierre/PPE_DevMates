import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import api from '../api/api';

export default function Register() {
  const { login }  = useAuth();
  const navigate   = useNavigate();
  const [form, setForm]     = useState({ email: '', password: '', role: 'developpeur' });
  const [erreur, setErreur] = useState('');
  const [charge, setCharge] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErreur('');
    setCharge(true);
    try {
      const { data } = await api.post('/auth/register', form);
      login(data.user, data.token);
      navigate('/profile'); // Redirection vers le profil pour le compléter
    } catch (err) {
      setErreur(err.response?.data?.erreur ?? "Erreur lors de l'inscription.");
    } finally {
      setCharge(false);
    }
  };

  return (
    <div className="container" style={{ paddingTop: '4rem' }}>
      <div className="carte">
        <h1 style={{ textAlign: 'center', marginBottom: '0.5rem', color: 'var(--couleur-primaire)', fontSize: '2rem' }}>
          DevMates
        </h1>
        <p style={{ textAlign: 'center', color: '#718096', marginBottom: '2rem', fontSize: '0.9rem' }}>
          Rejoignez la communauté des développeurs et porteurs de projets
        </p>
        <h2 style={{ marginBottom: '1.5rem' }}>Créer un compte</h2>

        {erreur && <p className="msg-erreur">{erreur}</p>}

        <form onSubmit={handleSubmit}>
          <label>Adresse email</label>
          <input
            type="email"
            placeholder="vous@exemple.com"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            required
            autoComplete="email"
          />
          <label>Mot de passe <span style={{ fontWeight: 400, color: '#718096' }}>(min. 8 caractères)</span></label>
          <input
            type="password"
            placeholder="••••••••"
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
            minLength={8}
            required
            autoComplete="new-password"
          />
          <label>Je suis…</label>
          <select
            value={form.role}
            onChange={(e) => setForm({ ...form, role: e.target.value })}
          >
            <option value="developpeur">Développeur — je cherche un projet</option>
            <option value="porteur_projet">Porteur de projet — je cherche un développeur</option>
          </select>

          <button type="submit" className="btn btn-primaire btn-bloc" disabled={charge}>
            {charge ? 'Inscription en cours...' : "S'inscrire"}
          </button>
        </form>

        <p style={{ textAlign: 'center', marginTop: '1.25rem', fontSize: '0.9rem' }}>
          Déjà un compte ?{' '}
          <Link to="/login" style={{ color: 'var(--couleur-primaire)', fontWeight: 600 }}>
            Se connecter
          </Link>
        </p>
      </div>
    </div>
  );
}
