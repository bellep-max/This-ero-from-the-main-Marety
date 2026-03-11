import React from 'react';
import { Link } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { $t } from '@/i18n';

export default function Footer() {
    const year = new Date().getFullYear();

    return (
        <footer className="py-4 p-md-5 p-lg-6 mt-auto" style={{ backgroundColor: 'var(--text-color, #0a0410)' }}>
            <div className="container d-flex flex-column justify-content-center align-items-center gap-5">
                <div className="d-flex flex-row justify-content-center align-items-center gap-3 gap-md-5 flex-wrap">
                    <Link className="footer-link" to="/page/cookies-and-personal-data">{$t('footer.cookie')}</Link>
                    <Link className="footer-link" to="/page/privacy-policy">{$t('footer.privacy')}</Link>
                    <Link className="footer-link" to="/page/legal-information">{$t('footer.copyrights')}</Link>
                    <Link className="footer-link" to="/page/term-and-condition">{$t('footer.terms')}</Link>
                </div>
                <div className="d-flex flex-row justify-content-center align-items-center gap-3 gap-md-5 flex-wrap">
                    <a href="https://www.patreon.com/Erocasthost" className="footer-link d-flex flex-row gap-2" target="_blank" rel="noreferrer">
                        <FontAwesomeIcon icon={['fab', 'patreon']} />
                        <span className="font-default">Patreon</span>
                    </a>
                    <a href="https://www.facebook.com/" className="footer-link d-flex flex-row gap-2" target="_blank" rel="noreferrer">
                        <FontAwesomeIcon icon={['fab', 'facebook']} />
                        <span className="font-default">Facebook</span>
                    </a>
                    <a href="https://twitter.com/" className="footer-link d-flex flex-row gap-2" target="_blank" rel="noreferrer">
                        <FontAwesomeIcon icon={['fab', 'x-twitter']} />
                        <span className="font-default">X (ex-Twitter)</span>
                    </a>
                    <a href="/feeds/main" className="footer-link d-flex flex-row gap-2" target="_blank" rel="noreferrer">
                        <FontAwesomeIcon icon={['fas', 'rss']} />
                        <span className="font-default">RSS</span>
                    </a>
                </div>
                <div className="border-bottom w-100" style={{ borderColor: 'var(--gray-dark) !important' }}></div>
                <div className="fs-12 text-center font-default" style={{ color: 'var(--light-color, #fff9fb)' }}>
                    &copy; {year} EroCast. All Rights Reserved
                </div>
            </div>
        </footer>
    );
}
