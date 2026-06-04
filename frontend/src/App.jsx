import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import LandingPage     from './pages/LandingPage';
import MentionsLegales from './pages/MentionsLegales';
import Login          from './pages/Login';
import Register       from './pages/Register';
import Profile        from './pages/Profile';
import Matching       from './pages/Matching';
import Conversations  from './pages/Conversations';
import Chat           from './pages/Chat';

// Garde de route — redirige vers /login si non authentifié
function RoutePrivee({ children }) {
  const { estConnecte } = useAuth();
  return estConnecte ? children : <Navigate to="/login" replace />;
}

function Routes_() {
  const { estConnecte } = useAuth();
  return (
    <Routes>
      <Route path="/"               element={estConnecte ? <Navigate to="/match" replace /> : <LandingPage />} />
      <Route path="/mentions-legales" element={<MentionsLegales />} />
      <Route path="/login"          element={estConnecte ? <Navigate to="/match" replace /> : <Login />} />
      <Route path="/register"       element={estConnecte ? <Navigate to="/match" replace /> : <Register />} />
      <Route path="/profile"           element={<RoutePrivee><Profile /></RoutePrivee>} />
      <Route path="/match"             element={<RoutePrivee><Matching /></RoutePrivee>} />
      <Route path="/conversations"     element={<RoutePrivee><Conversations /></RoutePrivee>} />
      <Route path="/chat/:matchId"     element={<RoutePrivee><Chat /></RoutePrivee>} />
      <Route path="*"               element={<Navigate to={estConnecte ? '/match' : '/'} replace />} />
    </Routes>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes_ />
      </BrowserRouter>
    </AuthProvider>
  );
}
