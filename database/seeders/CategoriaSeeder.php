<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nome' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'descricao' => 'Smartphones, tablets, notebooks e outros dispositivos eletrônicos',
                'ativa' => true,
            ],
            [
                'nome' => 'Informática',
                'slug' => 'informatica',
                'descricao' => 'Computadores, periféricos e acessórios para informática',
                'ativa' => true,
            ],
            [
                'nome' => 'Roupas',
                'slug' => 'roupas',
                'descricao' => 'Roupas masculinas, femininas e infantis',
                'ativa' => true,
            ],
            [
                'nome' => 'Calçados',
                'slug' => 'calcados',
                'descricao' => 'Tênis, sapatos, sandálias e outros calçados',
                'ativa' => true,
            ],
            [
                'nome' => 'Casa e Jardim',
                'slug' => 'casa-e-jardim',
                'descricao' => 'Decoração, móveis e produtos para casa e jardim',
                'ativa' => true,
            ],
            [
                'nome' => 'Esportes',
                'slug' => 'esportes',
                'descricao' => 'Equipamentos e acessórios esportivos',
                'ativa' => true,
            ],
            [
                'nome' => 'Livros',
                'slug' => 'livros',
                'descricao' => 'Livros de diversos gêneros e autores',
                'ativa' => true,
            ],
            [
                'nome' => 'Brinquedos',
                'slug' => 'brinquedos',
                'descricao' => 'Brinquedos para todas as idades',
                'ativa' => true,
            ],
            [
                'nome' => 'Beleza',
                'slug' => 'beleza',
                'descricao' => 'Cosméticos, perfumes e produtos de beleza',
                'ativa' => true,
            ],
            [
                'nome' => 'Automotivo',
                'slug' => 'automotivo',
                'descricao' => 'Acessórios e produtos para automóveis',
                'ativa' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        $this->command->info('Categorias criadas com sucesso!');
    }
}