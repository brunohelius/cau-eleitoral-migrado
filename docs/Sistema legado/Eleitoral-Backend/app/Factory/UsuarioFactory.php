<?php


namespace App\Factory;


use App\Config\Constants;
use App\Exceptions\NegocioException;
use App\Security\Token\TokenUtils;
use App\Service\AuthService;
use Illuminate\Http\Request;
use stdClass;

/**
 *
 * @author  Squadra Tecnologia.
 */
class UsuarioFactory
{
    /**
     * @var stdClass
     */
    private $usuarioLogado;

    /**
     * Retorna a instância do 'Usuário Logado'.
     *
     * @return stdClass|null
     */
    public function getUsuarioLogado()
    {
        if (empty($this->usuarioLogado)) {
            $this->setUsuarioLogado();
        }
        return $this->usuarioLogado;
    }

    /**
     * Retorna se o 'Usuário Logado' possuí uma determinada permissão.
     *
     * @param $permissao
     * @return bool
     */
    public function hasPermissao($permissao)
    {
        $hasPermissao = false;

        if (
            !empty($this->getUsuarioLogado())
            and !empty($this->getUsuarioLogado()->permissoes)
            and is_array($this->getUsuarioLogado()->permissoes)
        ) {
            $hasPermissao = in_array($permissao, $this->getUsuarioLogado()->permissoes);
        }

        return $hasPermissao;
    }

    /**
     * Retorna se o 'Usuário Logado' e um Profissional
     *
     * @param $permissao
     * @return bool
     */
    public function isProfissional()
    {
        return $this->hasPermissao(Constants::ROLE_PROFISSIONAL);
    }

    /**
     * Retorna se o 'Usuário Logado' e um Corporativo
     *
     * @param $permissao
     * @return bool
     */
    public function isCorporativo()
    {
        return $this->hasPermissao(Constants::PERMISSAO_ACESSOR_CE_UF) || $this->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);
    }

    /**
     * Retorna se o 'Usuário Logado' e um Assessor CEN
     *
     * @param $permissao
     * @return bool
     */
    public function isCorporativoAssessorCEN()
    {
        return $this->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);
    }

    /**
     * Retorna se o 'Usuário Logado' e um Assessor CE-UF
     *
     * @param $permissao
     * @return bool
     */
    public function isCorporativoAssessorCEUF()
    {
        return $this->hasPermissao(Constants::PERMISSAO_ACESSOR_CE_UF);
    }

    /**
     * Retorna se o 'Usuário Logado' e um Assessor CE-UF
     *
     * @param $idCauUf
     * @return bool
     */
    public function isCorporativoAssessorCeUfPorCauUf($idCauUf)
    {
        $isAssessorCE = $this->isCorporativoAssessorCEUF();
        $idCauUfUsuario = $this->getUsuarioLogado()->idCauUf ?? null;

        return $isAssessorCE && $idCauUfUsuario == $idCauUf;
    }

    /**
     * Retorna a request corrente.
     *
     * @return Request|mixed
     */
    private function getRequest()
    {
        return app('Illuminate\Http\Request');
    }

    /**
     * Seta a instância do 'Usuário Logado'.
     *
     */
    /**
     * Seta a instância do 'Usuário Logado'.
     *
     */
    private function setUsuarioLogado(): void
    {
        $request = $this->getRequest();

        $appToken = TokenUtils::getAppToken($request);

        if (!empty($appToken)) {
            $params = $this->getAuthenticatedUser($request);

            $this->usuarioLogado = new stdClass();

            if ($params != null) {
                $this->usuarioLogado->appToken = $appToken;
                $this->usuarioLogado->id = $params->id ?? null;
                $this->usuarioLogado->nome = $params->nome ?? null;
                $this->usuarioLogado->idCauUf = $params->idFilial ?? null;
                $this->usuarioLogado->permissoes = $params->permissoes ?? [];
                $this->usuarioLogado->idProfissional = $params->idProfissional ?? null;
                $this->usuarioLogado->administrador = $params->administrador ?? false;
                $this->usuarioLogado->administradorFilial = $params->administradorFilial ?? false;
            }
        }
    }

    /**
     * Retorna a request corrente.
     *
     * @return AuthService|mixed
     */
    private function getAuthService()
    {
        return app(AuthService::class);
    }

    /**
     * Retorna a instância do 'Usuário Logado'.
     *
     * @param Request $request
     * @return mixed
     * @throws NegocioException
     */
    private function getAuthenticatedUser(Request $request)
    {
        $token = $this->getAppToken($request);

        /** @var AuthService $authService */
        $authService = app()->make(AuthService::class);
        return $authService->getAuthenticatedUserByToken($token);
    }

    /**
     * Retorna o 'Token' de autorização recuperado da 'Request'.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getAppToken(Request $request)
    {
        $appToken = $request->header(Constants::PARAM_AUTHORIZATION);

        if (!empty($appToken)) {
            $appToken = str_replace(Constants::PARAM_BEARER, '', $appToken);
            $appToken = trim($appToken);
        }
        return $appToken;
    }
}
