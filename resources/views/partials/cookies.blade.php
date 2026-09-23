{{-- Banner global de cookies. La elección se conserva durante un año. --}}
<div id="ps-cookie-banner"
     class="ps-cookie-banner"
     role="dialog"
     aria-modal="false"
     aria-live="polite"
     aria-labelledby="ps-cookie-title"
     hidden>
    <div class="ps-cookie-inner">
        <div class="ps-cookie-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 1 0 9 9 3 3 0 0 1-3-3h-3a2 2 0 0 1-2-2V4a9 9 0 0 0-1-.1Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 10.5h.01M12 7.5h.01M15.5 10.5h.01M8 15h.01M12 14h.01"/>
            </svg>
        </div>

        <div class="ps-cookie-copy">
            <h2 id="ps-cookie-title" class="ps-cookie-title">Tu privacidad es importante</h2>
            <p class="ps-cookie-text">
                Usamos cookies necesarias para que PrismaSalud funcione correctamente. Si lo autorizas,
                también podemos usar cookies analíticas y de personalización para mejorar tu experiencia.
                <a href="{{ route('privacidad.index') }}">Consulta nuestra política de privacidad.</a>
            </p>
        </div>

        <div class="ps-cookie-actions">
            <button type="button" class="ps-cookie-button ps-cookie-button-primary" data-ps-cookie-choice="all">
                Aceptar
            </button>
            <button type="button" class="ps-cookie-button ps-cookie-button-secondary" data-ps-cookie-choice="cancel">
                Cancelar
            </button>
            <button type="button" class="ps-cookie-button ps-cookie-button-secondary" data-ps-cookie-choice="essential">
                Solo las necesarias
            </button>
        </div>
    </div>
</div>

<style>
    .ps-cookie-banner {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .ps-cookie-banner[hidden] {
        display: none !important;
    }

    .ps-cookie-banner {
        position: fixed;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 10000;
        color: #1f2937;
        background: #ffffff;
        border-top: 1px solid #d1fae5;
        box-shadow: 0 -12px 32px rgba(15, 23, 42, 0.16);
    }

    .ps-cookie-inner {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
        max-width: 76rem;
        margin: 0 auto;
        padding: 1rem 1.25rem;
    }

    .ps-cookie-icon {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        color: #0f766e;
        background: #ecfdf5;
        border-radius: 0.75rem;
    }

    .ps-cookie-icon svg {
        width: 1.5rem;
        height: 1.5rem;
    }

    .ps-cookie-copy {
        flex: 1 1 auto;
        min-width: 0;
    }

    .ps-cookie-title {
        margin: 0 0 0.25rem;
        color: #14532d;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .ps-cookie-text {
        margin: 0;
        color: #4b5563;
        font-size: 0.8rem;
        line-height: 1.5;
    }

    .ps-cookie-text a {
        color: #0f766e;
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .ps-cookie-text a:hover {
        color: #115e59;
    }

    .ps-cookie-actions {
        display: flex;
        flex: 0 0 auto;
        flex-direction: column;
        gap: 0.5rem;
        width: 11.5rem;
    }

    .ps-cookie-button {
        width: 100%;
        min-height: 2.4rem;
        padding: 0.55rem 1rem;
        border: 1px solid transparent;
        border-radius: 0.6rem;
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1.2;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .ps-cookie-button:focus-visible,
    .ps-cookie-text a:focus-visible {
        outline: 3px solid rgba(13, 148, 136, 0.35);
        outline-offset: 2px;
    }

    .ps-cookie-button-primary {
        color: #ffffff;
        background: #0f766e;
    }

    .ps-cookie-button-primary:hover {
        background: #115e59;
    }

    .ps-cookie-button-secondary {
        color: #374151;
        background: #ffffff;
        border-color: #d1d5db;
    }

    .ps-cookie-button-secondary:hover {
        color: #0f766e;
        background: #f0fdfa;
        border-color: #5eead4;
    }

    @media (max-width: 640px) {
        .ps-cookie-inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem;
        }

        .ps-cookie-icon {
            width: 2.35rem;
            height: 2.35rem;
        }

        .ps-cookie-text {
            font-size: 0.76rem;
        }

        .ps-cookie-actions {
            flex-direction: column;
            width: 100%;
        }
    }
</style>

<script>
    (() => {
        'use strict';

        const cookieName = 'prismasalud_cookie_consent';
        const banner = document.getElementById('ps-cookie-banner');

        if (!banner) {
            return;
        }

        const readConsent = () => {
            const prefix = cookieName + '=';
            const entry = document.cookie
                .split('; ')
                .find((cookie) => cookie.startsWith(prefix));

            if (!entry) {
                return null;
            }

            try {
                const consent = JSON.parse(decodeURIComponent(entry.slice(prefix.length)));

                if (consent.version !== 1 || consent.necessary !== true) {
                    return null;
                }

                return consent;
            } catch (error) {
                return null;
            }
        };

        const applyConsent = (consent) => {
            const value = consent.analytics && consent.marketing ? 'all' : 'essential';
            document.documentElement.dataset.cookieConsent = value;
        };

        const closeBanner = () => {
            banner.hidden = true;
        };

        const saveConsent = (choice) => {
            const selectedChoice = ['all', 'cancel', 'essential'].includes(choice) ? choice : 'essential';
            const acceptAll = selectedChoice === 'all';
            const consent = {
                version: 1,
                necessary: true,
                analytics: acceptAll,
                marketing: acceptAll,
                choice: selectedChoice === 'cancel' ? 'essential' : selectedChoice,
                updatedAt: new Date().toISOString(),
            };

            const secure = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = cookieName + '=' + encodeURIComponent(JSON.stringify(consent))
                + '; Max-Age=31536000; Path=/; SameSite=Lax' + secure;

            applyConsent(consent);
            closeBanner();
            window.dispatchEvent(new CustomEvent('cookie-consent:update', { detail: consent }));
        };

        banner.querySelectorAll('[data-ps-cookie-choice]').forEach((button) => {
            button.addEventListener('click', () => {
                const choice = button.dataset.psCookieChoice;

                // Cancelar solo oculta el aviso en esta página. No guarda una
                // preferencia, por lo que volverá a mostrarse al cambiar de vista.
                if (choice === 'cancel') {
                    banner.hidden = true;
                    return;
                }

                saveConsent(choice);
            });
        });

        const consent = readConsent();

        if (consent) {
            applyConsent(consent);
            banner.hidden = true;
        } else {
            window.requestAnimationFrame(() => {
                banner.hidden = false;
            });
        }
    })();
</script>
