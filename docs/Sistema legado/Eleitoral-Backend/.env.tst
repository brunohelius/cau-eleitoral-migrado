APP_NAME=Eleitoral
APP_ENV=tst
APP_KEY=gO19v4TgVgRG6gfdsgGGAmDN0XfJCuNPoBeHb6p7
APP_DEBUG=true
APP_URL=http://eleitoral.local
APP_TIMEZONE=America/Fortaleza

LOG_CHANNEL=single
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=pgsql
DB_HOST=172.16.0.80
DB_PORT=5401
DB_DATABASE=cau_corp_db
DB_USERNAME=causer
DB_PASSWORD=3kRxWKgdJSLtkkX

QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=siccau-hom@caubr.gov.br
MAIL_PASSWORD=Yur72041
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=siccau-hom@caubr.gov.br
MAIL_SENDER=siccau-hom@caubr.gov.br
MAIL_FROM_NAME="SICCAU - Sistema de Informação e Comunicação do CAU"
CAU_EMAIL_DESTINATARIOS_TESTE=robson.reis@squadra.com.br;lucas.camara@squadra.com.br;daiane.silva@squadra.com.br

JWT_SECRET=Bw5FlCgeW7eNWQStgQUMRG9lWKQqYqnYmVbWwQFgZWphjyx5BomO7Cbf5fhdr6Qm
JWT_ALGO=HS256

SICCAU_STORAGE_PATH=/opt/volumes/pvc-devops-siccau-hmg-dev

URL_SERVICO_COMPLEMENTO_PROFISSIONAIS="https://servicos-teste2.caubr.gov.br/app/view/sight/noLayout.php?form=validaProfissionaisEleitoral"
URL_ACESSO=https://acesso-backend-tst.caubr.gov.br
URL_PLATAFORMA=https://plataforma-backend-tst.caubr.gov.br
CALENDARIO_TOKEN_API=dGhpYWdvLnZlbG9zb0BzcXVhZHJhLmNvbS5iciZoYXNoPTIyNDUwMTgxOA
CALENDARIO_URL_API=https://api.calendario.com.br/
