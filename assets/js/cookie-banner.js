document.addEventListener('DOMContentLoaded', () => {
    const cookieBanner = document.getElementById('lgpd-cookie-banner');
    if (!cookieBanner) return;

    const btnAccept = document.getElementById('cookie-btn-accept');
    const btnReject = document.getElementById('cookie-btn-reject');

    // Verifica a preferência atual
    const consent = localStorage.getItem('odyssee_cookie_consent');

    if (!consent) {
        // Exibe o banner suavemente se não houver consentimento definido
        setTimeout(() => {
            cookieBanner.classList.add('show');
        }, 800);
    } else if (consent === 'accepted') {
        // Se já aceitou, inicializa os trackers
        initializeTracking();
    }

    const hideBanner = () => {
        cookieBanner.classList.remove('show');
        setTimeout(() => {
            cookieBanner.style.display = 'none';
        }, 600); // Aguarda o fim da transição CSS
    };

    if (btnAccept) {
        btnAccept.addEventListener('click', () => {
            localStorage.setItem('odyssee_cookie_consent', 'accepted');
            hideBanner();
            initializeTracking();
        });
    }

    if (btnReject) {
        btnReject.addEventListener('click', () => {
            localStorage.setItem('odyssee_cookie_consent', 'rejected');
            hideBanner();
        });
    }

    // Função centralizada para inicializar os rastreamentos após aceite
    function initializeTracking() {
        window.odysseeCookiesAccepted = true;
        
        // Dispara um evento global que pode ser ouvido por outros scripts
        // Exemplo: document.addEventListener('odyssee_cookies_accepted', carregarAnalytics)
        window.dispatchEvent(new Event('odyssee_cookies_accepted'));
        
        // Se houver lógica específica de analytics, ela poderia ser ativada aqui.
    }
});
