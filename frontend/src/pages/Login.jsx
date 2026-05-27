import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import api from '../api/api';

export default function Login() {
  const { login }  = useAuth();
  const navigate   = useNavigate();
  const [form, setForm]       = useState({ email: '', password: '' });
  const [erreur, setErreur]   = useState('');
  const [charge, setCharge]   = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErreur('');
    setCharge(true);
    try {
      const { data } = await api.post('/auth/login', form);
      login(data.user, data.token);
      navigate('/match');
    } catch (err) {
      setErreur(err.response?.data?.erreur ?? 'Erreur de connexion au serveur.');
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
          Trouvez votre associé technique idéal
        </p>
        <h2 style={{ marginBottom: '1.5rem' }}>Connexion</h2>

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
          <label>Mot de passe</label>
          <input
            type="password"
            placeholder="••••••••"
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
            required
            autoComplete="current-password"
          />
          <button type="submit" className="btn btn-primaire btn-bloc" disabled={charge}>
            {charge ? 'Connexion en cours...' : 'Se connecter'}
          </button>
        </form>

        <p style={{ textAlign: 'center', marginTop: '1.25rem', fontSize: '0.9rem' }}>
          Pas encore de compte ?{' '}
          <Link to="/register" style={{ color: 'var(--couleur-primaire)', fontWeight: 600 }}>
            S'inscrire
          </Link>
        </p>
      </div>
    </div>
  );
}
