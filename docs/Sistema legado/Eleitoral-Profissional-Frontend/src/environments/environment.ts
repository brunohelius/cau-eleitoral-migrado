 export const environment = {
  production: false,
  urlLocal: 'http://localhost:4200',
  url: 'http://localhost:8000',
  urlLogout: 'https://acesso-frontend-dev.caubr.gov.br',
  urlPortal: 'https://plataforma-backend-dev.caubr.gov.br',
  urlApiSecurity: 'https://acesso-backend-dev.caubr.gov.br',
  urlsModulo: [
    { id: 3, url: "http://localhost:4200" },
    { id: 5, url: "https://test-financeiro-frontend.caubr.gov.br" },
    { id: 6, url: "https://fiscalizacao-frontend-dev.caubr.gov.br" },
    { id: 10, url: "https://test-rrt-social.caubr.gov.br" },
    { id: 13, url: "https://plataforma-frontend-dev.caubr.gov.br" },
    { id: 14, url: "https://denuncia-frontend-dev.caubr.gov.br" },
    { id: 16, url: "https://test-rrt-social-profissional.caubr.gov.br" }
  ],
  cookieName: 'cookie-servicos',
  cookieAuth: 'access-token',
};
