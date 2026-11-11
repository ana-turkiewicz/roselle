/*
 * Inicialização do Swagger UI da Roselle API.
 *
 * A API autentica por sessão/cookies (Sanctum stateful), então o "Try it out":
 *   - chama o mesmo host desta página (o `servers` da especificação é `/`);
 *   - envia os cookies do navegador;
 *   - obtém o cookie XSRF-TOKEN em /sanctum/csrf-cookie quando ele ainda não existe;
 *   - envia X-XSRF-TOKEN em POST, PUT, PATCH e DELETE, como faz o frontend.
 */
(function () {
    'use strict';

    var SPEC_URL = '/api/docs/openapi.yaml';
    var CSRF_COOKIE_URL = '/sanctum/csrf-cookie';
    var WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    function readXsrfToken() {
        var match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

        return match ? decodeURIComponent(match[1]) : null;
    }

    function fetchCsrfCookie() {
        return fetch(CSRF_COOKIE_URL, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });
    }

    function isSameOrigin(url) {
        try {
            return new URL(url, window.location.href).origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    function requestInterceptor(request) {
        // O download da própria especificação não precisa de cookies nem de CSRF.
        if (request.loadSpec || !isSameOrigin(request.url)) {
            return request;
        }

        request.credentials = 'same-origin';

        if (WRITE_METHODS.indexOf(String(request.method || 'GET').toUpperCase()) === -1) {
            return request;
        }

        function withToken() {
            var token = readXsrfToken();

            if (token) {
                request.headers['X-XSRF-TOKEN'] = token;
            }

            return request;
        }

        // Se a busca do cookie falhar, a requisição segue mesmo assim e a API responde por si.
        return readXsrfToken() ? withToken() : fetchCsrfCookie().then(withToken, withToken);
    }

    function responseInterceptor(response) {
        // Um 419 não renova o cookie XSRF-TOKEN; busca um novo para a próxima tentativa.
        if (response.status === 419 && isSameOrigin(response.url)) {
            return fetchCsrfCookie().then(
                function () { return response; },
                function () { return response; }
            );
        }

        return response;
    }

    /*
     * O cookie de sessão é HttpOnly e definido pelo servidor no login; não existe valor
     * para digitar em um diálogo "Authorize". O cadeado continua marcando as operações
     * protegidas, mas não abre o diálogo.
     */
    function SessionCookieAuthPlugin(system) {
        var React = system.React;
        var hint = 'Requer sessão autenticada. Execute POST /api/auth/login nesta página.';

        return {
            wrapComponents: {
                authorizeBtn: function () {
                    return function () {
                        return null;
                    };
                },
                authorizeOperationBtn: function (Original) {
                    return function (props) {
                        return React.createElement(
                            'span',
                            { title: hint },
                            React.createElement(Original, Object.assign({}, props, { onClick: function () {} }))
                        );
                    };
                },
            },
        };
    }

    window.addEventListener('load', function () {
        window.ui = SwaggerUIBundle({
            url: SPEC_URL,
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [SwaggerUIBundle.presets.apis],
            plugins: [SessionCookieAuthPlugin],
            layout: 'BaseLayout',
            // Sem chamadas ao validador externo (validator.swagger.io).
            validatorUrl: null,
            displayRequestDuration: true,
            requestInterceptor: requestInterceptor,
            responseInterceptor: responseInterceptor,
        });
    });
})();
