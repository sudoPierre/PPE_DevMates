import React from 'react';
import { Link } from 'react-router-dom';

export default function MentionsLegales() {
  return (
    <div className="mentions-page">

      <header className="landing-header">
        <Link to="/" className="landing-logo" style={{ textDecoration: 'none' }}>DevMates</Link>
        <nav className="landing-nav">
          <Link to="/login"    className="btn btn-secondaire" style={{ padding: '0.5rem 1.25rem', fontSize: '0.9rem' }}>
            Se connecter
          </Link>
          <Link to="/register" className="btn btn-primaire"   style={{ padding: '0.5rem 1.25rem', fontSize: '0.9rem' }}>
            S'inscrire
          </Link>
        </nav>
      </header>

      <main className="mentions-contenu">
        <Link to="/" className="mentions-retour">← Retour à l'accueil</Link>

        <h1>Mentions légales</h1>
        <p className="mentions-date">Dernière mise à jour : juin 2026</p>

        {/* ── Éditeur du site ── */}
        <section className="mentions-section">
          <h2>1. Éditeur du site</h2>
          <p>
            Le site <strong>DevMates</strong> est édité par la société <strong>DevCode</strong>,
            société par actions simplifiée (SAS) au capital de 10 000 €.
          </p>
          <ul>
            <li><strong>Siège social :</strong> 12 rue de la République, 75001 Paris, France</li>
            <li><strong>SIRET :</strong> 123 456 789 00010</li>
            <li><strong>Directeur de la publication :</strong> Pierre Galliot</li>
            <li><strong>Contact :</strong> contact@devcode.fr</li>
          </ul>
        </section>

        {/* ── Hébergement ── */}
        <section className="mentions-section">
          <h2>2. Hébergement</h2>
          <p>
            Ce site est hébergé sur un serveur VPS mis à disposition par l'école
            <strong> EFREI Paris</strong>.
          </p>
          <ul>
            <li><strong>Établissement :</strong> EFREI Paris — École d'ingénieurs du numérique</li>
            <li><strong>Adresse :</strong> 30–32 avenue de la République, 94800 Villejuif, France</li>
            <li><strong>Site :</strong> efrei.fr</li>
          </ul>
        </section>

        {/* ── Propriété intellectuelle ── */}
        <section className="mentions-section">
          <h2>3. Propriété intellectuelle</h2>
          <p>
            L'ensemble des contenus présents sur le site DevMates (textes, images, graphismes,
            logo, icônes, sons, logiciels) est la propriété exclusive de DevCode ou de ses
            partenaires. Toute reproduction, distribution, modification, adaptation, retransmission
            ou publication, même partielle, de ces différents éléments est strictement interdite
            sans l'accord exprès écrit de DevCode.
          </p>
        </section>

        {/* ── Données personnelles ── */}
        <section className="mentions-section">
          <h2>4. Données personnelles et conformité RGPD</h2>
          <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD — UE 2016/679)
            et à la loi Informatique et Libertés modifiée, DevCode s'engage à protéger la vie
            privée de ses utilisateurs.
          </p>
          <p>
            Les données collectées (adresse e-mail, compétences, préférences de projet) sont
            utilisées uniquement dans le cadre du service DevMates afin de proposer des mises en
            relation pertinentes. Elles ne sont ni revendues ni transmises à des tiers sans
            consentement explicite.
          </p>
          <p>
            Conformément à l'article 17 du RGPD, tout utilisateur dispose d'un <strong>droit
            d'accès, de rectification et de suppression</strong> de ses données. Pour exercer ces
            droits, contactez-nous à : <a href="mailto:rgpd@devcode.fr" className="mentions-lien">rgpd@devcode.fr</a>.
          </p>
          <p>
            Vous pouvez également introduire une réclamation auprès de la Commission Nationale
            de l'Informatique et des Libertés (CNIL) — <a href="https://www.cnil.fr" target="_blank" rel="noreferrer" className="mentions-lien">cnil.fr</a>.
          </p>
        </section>

        {/* ── Cookies ── */}
        <section className="mentions-section">
          <h2>5. Cookies</h2>
          <p>
            DevMates utilise uniquement des cookies techniques strictement nécessaires au
            fonctionnement du service (maintien de session). Aucun cookie publicitaire ou de
            traçage tiers n'est déposé sur votre terminal.
          </p>
        </section>

      </main>

      <footer className="landing-footer">
        <span>© {new Date().getFullYear()} DevMates — DevCode, Paris. Tous droits réservés.</span>
        <Link to="/mentions-legales" className="landing-footer-lien">
          Mentions légales
        </Link>
      </footer>

    </div>
  );
}
