export const environment = {
    production: true,
    urlLogout: 'https://acesso.caubr.gov.br',
    url: 'https://eleitoral-backend.caubr.gov.br',
    urlLocal: 'https://eleitoral-profissional.caubr.gov.br',
    urlPortal: 'https://plataforma.caubr.gov.br',
    urlApiSecurity: 'https://acesso-backend.caubr.gov.br',
    urlsModulo: [
        { id: 3, url: "https://eleitoral.caubr.gov.br" },
        { id: 5, url: "https://financeiro.caubr.gov.br" },
        { id: 6, url: "https://fiscalizacao.caubr.gov.br" },
        { id: 10, url: "https://rrt-social.caubr.gov.br" },
        { id: 13, url: "https://plataforma.caubr.gov.br" },
        { id: 14, url: "https://denuncia.caubr.gov.br" },
        { id: 16, url: "https://rrt-social-profissional.caubr.gov.br" }
    ],
    cookieName: 'cookie-servicos',
    cookieAuth: 'access-token',
};
