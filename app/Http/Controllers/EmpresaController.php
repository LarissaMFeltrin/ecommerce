<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    /**
     * Listar empresas (filtrado por multi-tenancy)
     */
    public function index()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryEmpresas = Empresa::query();

        // Se for admin de empresa, só pode ver sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryEmpresas->where('id', $empresaId);
        }

        $empresas = $queryEmpresas->orderBy('nome')->paginate(15);

        return view('admin.empresas.index', compact('empresas'));
    }

    /**
     * Mostrar formulário para criar empresa (apenas super_admin)
     */
    public function create()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Apenas super_admin pode criar empresas
        if ($empresaId && $adminTipo === 'empresa_admin') {
            abort(403, 'Apenas super administradores podem criar empresas.');
        }

        $ramos = Empresa::getRamosAtividade();
        $planos = Empresa::getPlanos();

        return view('admin.empresas.create', compact('ramos', 'planos'));
    }

    /**
     * Salvar nova empresa (apenas super_admin)
     */
    public function store(Request $request)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Apenas super_admin pode criar empresas
        if ($empresaId && $adminTipo === 'empresa_admin') {
            abort(403, 'Apenas super administradores podem criar empresas.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj',
            'email' => 'required|email|unique:empresas,email',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'dominio' => 'nullable|string|max:100|unique:empresas,dominio',
            'tema' => 'required|string|max:100',
            'cor_primaria' => 'required|string|max:7',
            'cor_secundaria' => 'required|string|max:7',
            'descricao' => 'nullable|string',
            'ramo_atividade' => 'required|string|max:100',
            'plano' => 'required|string|max:100',
            'data_contrato' => 'required|date',
            'data_vencimento' => 'required|date|after:data_contrato',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Upload do logo se fornecido
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('empresas/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Configurações padrão baseadas no ramo de atividade
        $data['configuracoes'] = $this->getConfiguracoesPadrao($request->ramo_atividade);

        $empresa = Empresa::create($data);

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa criada com sucesso!');
    }

    /**
     * Mostrar detalhes da empresa (filtrado por multi-tenancy)
     */
    public function show(Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Se for admin de empresa, só pode ver sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin' && $empresa->id != $empresaId) {
            abort(403, 'Acesso negado. Você só pode visualizar sua própria empresa.');
        }

        $estatisticas = $empresa->getEstatisticas();

        return view('admin.empresas.show', compact('empresa', 'estatisticas'));
    }

    /**
     * Mostrar formulário para editar empresa (filtrado por multi-tenancy)
     */
    public function edit(Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Se for admin de empresa, só pode editar sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin' && $empresa->id != $empresaId) {
            abort(403, 'Acesso negado. Você só pode editar sua própria empresa.');
        }

        $ramos = Empresa::getRamosAtividade();
        $planos = Empresa::getPlanos();

        return view('admin.empresas.edit', compact('empresa', 'ramos', 'planos'));
    }

    /**
     * Atualizar empresa (filtrado por multi-tenancy)
     */
    public function update(Request $request, Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Se for admin de empresa, só pode editar sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin' && $empresa->id != $empresaId) {
            abort(403, 'Acesso negado. Você só pode editar sua própria empresa.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj,' . $empresa->id,
            'email' => 'required|email|unique:empresas,email,' . $empresa->id,
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'dominio' => 'nullable|string|max:100|unique:empresas,dominio,' . $empresa->id,
            'tema' => 'required|string|max:100',
            'cor_primaria' => 'required|string|max:7',
            'cor_secundaria' => 'required|string|max:7',
            'descricao' => 'nullable|string',
            'ramo_atividade' => 'required|string|max:100',
            'plano' => 'required|string|max:100',
            'data_contrato' => 'required|date',
            'data_vencimento' => 'required|date|after:data_contrato',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Upload do logo se fornecido
        if ($request->hasFile('logo')) {
            // Remover logo antigo se existir
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }

            $logoPath = $request->file('logo')->store('empresas/logos', 'public');
            $data['logo'] = $logoPath;
        }

        $empresa->update($data);

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Excluir empresa (apenas super_admin)
     */
    public function destroy(Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Apenas super_admin pode excluir empresas
        if ($empresaId && $adminTipo === 'empresa_admin') {
            abort(403, 'Apenas super administradores podem excluir empresas.');
        }

        // Verificar se a empresa tem dados associados
        if ($empresa->usuarios()->count() > 0 || $empresa->produtos()->count() > 0) {
            return back()->with('error', 'Não é possível excluir uma empresa que possui usuários ou produtos cadastrados.');
        }

        // Remover logo se existir
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }

        $empresa->delete();

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    /**
     * Ativar/desativar empresa (filtrado por multi-tenancy)
     */
    public function toggleStatus(Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Se for admin de empresa, só pode alterar status de sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin' && $empresa->id != $empresaId) {
            abort(403, 'Acesso negado. Você só pode alterar o status de sua própria empresa.');
        }

        $empresa->ativo = !$empresa->ativo;
        $empresa->save();

        $status = $empresa->ativo ? 'ativada' : 'desativada';

        return back()->with('success', "Empresa {$status} com sucesso!");
    }

    /**
     * Mostrar estatísticas da empresa (filtrado por multi-tenancy)
     */
    public function estatisticas(Empresa $empresa)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Se for admin de empresa, só pode ver estatísticas de sua própria empresa
        if ($empresaId && $adminTipo === 'empresa_admin' && $empresa->id != $empresaId) {
            abort(403, 'Acesso negado. Você só pode visualizar estatísticas de sua própria empresa.');
        }

        $estatisticas = $empresa->getEstatisticas();

        return response()->json($estatisticas);
    }

    /**
     * Obter configurações padrão baseadas no ramo de atividade
     */
    private function getConfiguracoesPadrao($ramo)
    {
        $configuracoes = [
            'perfumes' => [
                'frete_gratis_acima' => 15000,
                'taxa_frete_padrao' => 1590,
                'prazo_entrega_padrao' => 3,
                'pagamento_pix' => true,
                'pagamento_cartao_credito' => true,
                'parcelamento_maximo' => 12,
            ],
            'roupas' => [
                'frete_gratis_acima' => 20000,
                'taxa_frete_padrao' => 1990,
                'prazo_entrega_padrao' => 5,
                'pagamento_pix' => true,
                'pagamento_cartao_credito' => true,
                'pagamento_cartao_debito' => true,
                'parcelamento_maximo' => 10,
            ],
            'eletronicos' => [
                'frete_gratis_acima' => 30000,
                'taxa_frete_padrao' => 2500,
                'prazo_entrega_padrao' => 7,
                'pagamento_pix' => true,
                'pagamento_cartao_credito' => true,
                'pagamento_boleto' => true,
                'parcelamento_maximo' => 18,
            ],
            'casa' => [
                'frete_gratis_acima' => 12000,
                'taxa_frete_padrao' => 1200,
                'prazo_entrega_padrao' => 4,
                'pagamento_pix' => true,
                'pagamento_cartao_credito' => true,
                'parcelamento_maximo' => 6,
            ],
        ];

        return $configuracoes[$ramo] ?? $configuracoes['perfumes'];
    }

    /**
     * Buscar empresas por ramo de atividade
     */
    public function buscarPorRamo(Request $request)
    {
        $ramo = $request->get('ramo');
        $empresas = Empresa::porRamo($ramo)->ativa()->get();

        return response()->json($empresas);
    }

    /**
     * Obter empresas por plano
     */
    public function buscarPorPlano(Request $request)
    {
        $plano = $request->get('plano');
        $empresas = Empresa::porPlano($plano)->ativa()->get();

        return response()->json($empresas);
    }
}