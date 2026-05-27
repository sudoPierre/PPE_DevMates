import React, { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext(null);

/**
 * Fournisseur du contexte d'authentification global
 * Persiste le token JWT et les infos utilisateur dans localStorage
 */
export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(() => localStorage.getItem('dm_token'));

  useEffect(() => {
    const stored = localStorage.getItem('dm_user');
    if (stored && token) {
      try {
        setUser(JSON.parse(stored));
      } catch {
        logout();
      }
    }
  }, []);

  const login = (userData, jwt) => {
    setUser(userData);
    setToken(jwt);
    localStorage.setItem('dm_token', jwt);
    localStorage.setItem('dm_user', JSON.stringify(userData));
  };

  const logout = () => {
    setUser(null);
    setToken(null);
    localStorage.removeItem('dm_token');
    localStorage.removeItem('dm_user');
  };

  return (
    <AuthContext.Provider value={{ user, token, login, logout, estConnecte: !!token }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth doit être utilisé dans un AuthProvider');
  return ctx;
};
