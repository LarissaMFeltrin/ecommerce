<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrador;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        // Verificar se o usuário é administrador
        $admin = Administrador::where('id_usuario', $usuario->id)
            ->where('ativo', true)
            ->first();

        if (!$admin) {
            abort(403, 'Acesso negado. Área restrita para administradores.');
        }

        // Verificar se é super administrador (acesso total)
        if ($admin->isSuperAdmin()) {
            // Super admin tem acesso total, mas não define empresa_id
            $request->attributes->set('admin_tipo', 'super_admin');
            $request->attributes->set('empresa_id', null);
            return $next($request);
        }

        // Verificar se é administrador de empresa
        if ($admin->isEmpresaAdmin()) {
            $request->attributes->set('admin_tipo', 'empresa_admin');
            $request->attributes->set('empresa_id', $admin->empresa_id);

            // Adicionar empresa_id ao request para uso nos controladores
            $request->merge(['empresa_id' => $admin->empresa_id]);

            return $next($request);
        }

        // Se chegou aqui, é um administrador sem empresa definida
        abort(403, 'Acesso negado. Administrador sem empresa associada.');
    }
}
