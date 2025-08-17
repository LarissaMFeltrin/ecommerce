<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar categorias
        $eletronicos = Categoria::where('slug', 'eletronicos')->first();
        $informatica = Categoria::where('slug', 'informatica')->first();
        $roupas = Categoria::where('slug', 'roupas')->first();
        $calcados = Categoria::where('slug', 'calcados')->first();
        $casa = Categoria::where('slug', 'casa-e-jardim')->first();
        $esportes = Categoria::where('slug', 'esportes')->first();

        $produtos = [
            // Eletrônicos
            [
                'nome' => 'Smartphone Samsung Galaxy A54',
                'descricao' => 'Smartphone Samsung Galaxy A54 128GB 5G 6.4" 50MP + 5MP + 12MP + 32MP Android 13',
                'preco' => 1899.99,
                'estoque' => 15,
                'id_categoria' => $eletronicos->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Galaxy+A54',
                'ativo' => true,
            ],
            [
                'nome' => 'iPhone 15 128GB',
                'descricao' => 'iPhone 15 128GB 6.1" 48MP + 12MP + 12MP iOS 17',
                'preco' => 5999.99,
                'estoque' => 8,
                'id_categoria' => $eletronicos->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=iPhone+15',
                'ativo' => true,
            ],
            [
                'nome' => 'Tablet iPad 10ª Geração',
                'descricao' => 'iPad 10ª Geração 64GB 10.9" Wi-Fi + Cellular iOS 16',
                'preco' => 3999.99,
                'estoque' => 12,
                'id_categoria' => $eletronicos->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=iPad+10',
                'ativo' => true,
            ],

            // Informática
            [
                'nome' => 'Notebook Dell Inspiron 15',
                'descricao' => 'Notebook Dell Inspiron 15 3000 Intel Core i5 8GB 256GB SSD Windows 11',
                'preco' => 3499.99,
                'estoque' => 10,
                'id_categoria' => $informatica->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Dell+Inspiron',
                'ativo' => true,
            ],
            [
                'nome' => 'Mouse Gamer Logitech G502',
                'descricao' => 'Mouse Gamer Logitech G502 HERO 25K DPI RGB Programável',
                'preco' => 299.99,
                'estoque' => 25,
                'id_categoria' => $informatica->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=G502+HERO',
                'ativo' => true,
            ],
            [
                'nome' => 'Teclado Mecânico Corsair K70',
                'descricao' => 'Teclado Mecânico Corsair K70 RGB MK.2 Cherry MX Red',
                'preco' => 899.99,
                'estoque' => 18,
                'id_categoria' => $informatica->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=K70+RGB',
                'ativo' => true,
            ],

            // Roupas
            [
                'nome' => 'Camiseta Básica Masculina',
                'descricao' => 'Camiseta básica 100% algodão, disponível em várias cores',
                'preco' => 29.99,
                'estoque' => 50,
                'id_categoria' => $roupas->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Camiseta+Basica',
                'ativo' => true,
            ],
            [
                'nome' => 'Calça Jeans Masculina',
                'descricao' => 'Calça jeans masculina slim fit, alta durabilidade',
                'preco' => 89.99,
                'estoque' => 30,
                'id_categoria' => $roupas->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Calca+Jeans',
                'ativo' => true,
            ],
            [
                'nome' => 'Vestido Feminino Elegante',
                'descricao' => 'Vestido feminino elegante para ocasiões especiais',
                'preco' => 159.99,
                'estoque' => 20,
                'id_categoria' => $roupas->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Vestido+Elegante',
                'ativo' => true,
            ],

            // Calçados
            [
                'nome' => 'Tênis Nike Air Max',
                'descricao' => 'Tênis Nike Air Max 270 masculino, conforto e estilo',
                'preco' => 399.99,
                'estoque' => 22,
                'id_categoria' => $calcados->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Nike+Air+Max',
                'ativo' => true,
            ],
            [
                'nome' => 'Sapato Social Masculino',
                'descricao' => 'Sapato social masculino couro legítimo, elegante e confortável',
                'preco' => 199.99,
                'estoque' => 15,
                'id_categoria' => $calcados->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Sapato+Social',
                'ativo' => true,
            ],

            // Casa e Jardim
            [
                'nome' => 'Luminária de Mesa LED',
                'descricao' => 'Luminária de mesa LED com controle de intensidade',
                'preco' => 89.99,
                'estoque' => 35,
                'id_categoria' => $casa->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Luminaria+LED',
                'ativo' => true,
            ],
            [
                'nome' => 'Vaso Decorativo Cerâmica',
                'descricao' => 'Vaso decorativo em cerâmica, ideal para plantas',
                'preco' => 45.99,
                'estoque' => 40,
                'id_categoria' => $casa->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Vaso+Ceramica',
                'ativo' => true,
            ],

            // Esportes
            [
                'nome' => 'Bola de Futebol Nike',
                'descricao' => 'Bola de futebol Nike oficial, tamanho 5',
                'preco' => 129.99,
                'estoque' => 28,
                'id_categoria' => $esportes->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Bola+Nike',
                'ativo' => true,
            ],
            [
                'nome' => 'Raquete de Tênis Wilson',
                'descricao' => 'Raquete de tênis Wilson Pro Staff, profissional',
                'preco' => 599.99,
                'estoque' => 12,
                'id_categoria' => $esportes->id,
                'imagem' => 'https://via.placeholder.com/400x300?text=Raquete+Wilson',
                'ativo' => true,
            ],
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }

        $this->command->info('Produtos criados com sucesso!');
    }
}