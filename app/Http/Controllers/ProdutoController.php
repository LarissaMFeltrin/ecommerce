<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produto::with('categoria')->ativo();

        // Filtro por categoria
        if ($request->has('categoria')) {
            $query->porCategoria($request->categoria);
        }

        // Busca por nome
        if ($request->has('busca')) {
            $query->where('nome', 'like', '%' . $request->busca . '%');
        }

        // Ordenação
        $ordenacao = $request->get('ordenacao', 'nome');
        $direcao = $request->get('direcao', 'asc');

        switch ($ordenacao) {
            case 'preco':
                $query->orderBy('preco', $direcao);
                break;
            case 'nome':
            default:
                $query->orderBy('nome', $direcao);
                break;
        }

        $produtos = $query->paginate(12);
        $categorias = Categoria::ativa()->get();

        return view('produtos.index', compact('produtos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        $produto->load('categoria', 'avaliacoes.usuario');

        // Produtos relacionados da mesma categoria
        $produtosRelacionados = Produto::ativo()
            ->porCategoria($produto->id_categoria)
            ->where('id', '!=', $produto->id)
            ->limit(4)
            ->get();

        return view('produtos.show', compact('produto', 'produtosRelacionados'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Buscar produtos via AJAX
     */
    public function buscar(Request $request)
    {
        $termo = $request->get('termo');

        $produtos = Produto::ativo()
            ->where('nome', 'like', '%' . $termo . '%')
            ->orWhere('descricao', 'like', '%' . $termo . '%')
            ->limit(10)
            ->get(['id', 'nome', 'preco', 'imagem']);

        return response()->json($produtos);
    }

    /**
     * Produtos por categoria
     */
    public function porCategoria($slug)
    {
        $categoria = Categoria::where('slug', $slug)->firstOrFail();

        $produtos = Produto::ativo()
            ->porCategoria($categoria->id)
            ->paginate(12);

        // Buscar categorias relacionadas (outras categorias ativas)
        $categoriasRelacionadas = Categoria::ativa()
            ->where('id', '!=', $categoria->id)
            ->limit(6)
            ->get();

        return view('produtos.categoria', compact('produtos', 'categoria', 'categoriasRelacionadas'));
    }
}