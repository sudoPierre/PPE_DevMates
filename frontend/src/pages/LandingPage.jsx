import React from 'react';
import { Link } from 'react-router-dom';

export default function LandingPage() {
  return (
    <div className="landing">

      {/* ── En-tête ── */}
      <header className="landing-header">
        <span className="landing-logo">DevMates</span>
        <nav className="landing-nav">
          <Link to="/login"    className="btn btn-secondaire" style={{ padding: '0.5rem 1.25rem', fontSize: '0.9rem' }}>
            Se connecter
          </Link>
          <Link to="/register" className="btn btn-primaire"   style={{ padding: '0.5rem 1.25rem', fontSize: '0.9rem' }}>
            S'inscrire
          </Link>
        </nav>
      </header>

      {/* ── Héro ── */}
      <section className="landing-hero">
        <div className="landing-hero-contenu">
          <h1 className="landing-titre">
            Trouvez le partenaire&nbsp;technique idéal<br />pour vos projets
          </h1>
          <p className="landing-sous-titre">
            DevMates met en relation développeurs et porteurs de projet grâce à un
            algorithme de matching intelligent basé sur vos compétences et vos ambitions.
          </p>
          <div className="landing-cta">
            <Link to="/register" className="btn btn-primaire landing-btn-hero">
              Créer mon compte gratuitement
            </Link>
            <Link to="/login" className="btn landing-btn-fantome">
              J'ai déjà un compte
            </Link>
          </div>
        </div>
        <div className="landing-hero-visuel" aria-hidden="true">
          <div className="landing-carte-flottante">
            <span className="landing-avatar">👩‍💻</span>
            <div>
              <strong>Alice D.</strong>
              <p>React · Node.js · PostgreSQL</p>
            </div>
          </div>
          <div className="landing-carte-flottante landing-carte-decalee">
            <span className="landing-avatar">🧑‍🚀</span>
            <div>
              <strong>Marc T.</strong>
              <p>Porteur de projet · IA · SaaS</p>
            </div>
          </div>
          <div className="landing-icone-match" aria-label="Match">❤️</div>
        </div>
      </section>

      {/* ── Fonctionnalités ── */}
      <section className="landing-fonctionnalites">
        <h2 className="landing-section-titre">Tout ce dont vous avez besoin</h2>
        <div className="landing-grille">

          <div className="landing-feature-carte">
            <span className="landing-feature-icone">🎯</span>
            <h3>Algorithme de Matching</h3>
            <p>
              Notre moteur analyse vos compétences, votre disponibilité et vos préférences
              pour vous proposer les profils les plus compatibles.
            </p>
          </div>

          <div className="landing-feature-carte">
            <span className="landing-feature-icone">💬</span>
            <h3>Messagerie en temps réel</h3>
            <p>
              Discutez directement avec vos matchs via notre système de messagerie intégré,
              sans quitter la plateforme.
            </p>
          </div>

          <div className="landing-feature-carte">
            <span className="landing-feature-icone">👤</span>
            <h3>Gestion de profil</h3>
            <p>
              Mettez en avant vos compétences, vos projets passés et vos disponibilités
              pour attirer les meilleures opportunités.
            </p>
          </div>

        </div>
      </section>

      {/* ── Appel à l'action final ── */}
      <section className="landing-cta-final">
        <h2>Prêt à trouver votre associé&nbsp;technique ?</h2>
        <p>Rejoignez la communauté DevMates dès aujourd'hui — c'est gratuit.</p>
        <Link to="/register" className="btn btn-primaire landing-btn-hero">
          Commencer maintenant
        </Link>
      </section>

      {/* ── Pied de page ── */}
      <footer className="landing-footer">
        <span>© {new Date().getFullYear()} DevMates — DevCode, Paris. Tous droits réservés.</span>
        <Link to="/mentions-legales" className="landing-footer-lien">
          Mentions légales
        </Link>
      </footer>

    </div>
  );
}
