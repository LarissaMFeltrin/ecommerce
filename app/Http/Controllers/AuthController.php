<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Processar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        // Buscar usuário pelo email
        $usuario = Usuario::where('email', $request->email)->first();

        // Verificar se o usuário existe e a senha está correta (comparação direta)
        if ($usuario && $usuario->senha === $request->senha) {
            // Fazer login manualmente
            Auth::login($usuario);
            $request->session()->regenerate();

            // Limpar qualquer URL intended que possa estar causando problemas
            $request->session()->forget('url.intended');

            // Verificar se o usuário é administrador
            if ($usuario->isAdmin()) {
                // Redirecionar administradores para a área administrativa
                return redirect()->route('admin.administradores.index')
                    ->with('success', 'Login realizado com sucesso! Bem-vindo ao painel administrativo.');
            } else {
                // Redirecionar usuários comuns para a home
                return redirect('/')->with('success', 'Login realizado com sucesso!');
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Mostrar formulário de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Processar registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuarios',
            'senha' => 'required|string|min:6|confirmed',
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
        ]);

        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => $request->senha, // Removido Hash::make() - o mutator já faz isso
            'telefone' => $request->telefone,
            'cpf' => $request->cpf,
            'data_nascimento' => $request->data_nascimento,
        ]);

        Auth::login($usuario);

        return redirect('/')->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Mostrar perfil do usuário
     */
    public function perfil()
    {
        $usuario = Auth::user();
        $enderecos = $usuario->enderecos;
        $pedidos = $usuario->pedidos()->with('itens.produto')->orderBy('created_at', 'desc')->paginate(10);

        return view('auth.perfil', compact('usuario', 'enderecos', 'pedidos'));
    }

    /**
     * Atualizar perfil
     */
    public function atualizarPerfil(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuarios,email,' . $usuario->id,
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
        ]);

        $usuario->update($request->only(['nome', 'email', 'telefone', 'cpf', 'data_nascimento']));

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Alterar senha
     */
    public function alterarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|string|min:6|confirmed',
        ]);

        $usuario = Auth::user();

        if ($usuario->senha !== $request->senha_atual) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta']);
        }

        $usuario->update([
            'senha' => $request->nova_senha // Sem Hash::make() - o mutator já faz isso
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}
