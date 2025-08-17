<footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shopping-cart text-2xl text-primary mr-3"></i>
                    <span class="text-2xl font-bold">Minha Loja</span>
                </div>
                <p class="text-gray-300 mb-4">
                    Sua loja online de confiança com os melhores produtos e preços.
                    Oferecemos uma experiência de compra segura e conveniente.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-primary">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-primary">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-primary">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-primary">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-300 hover:text-primary">
                            Início
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('produtos.index') }}" class="text-gray-300 hover:text-primary">
                            Produtos
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Sobre Nós
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Contato
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Atendimento</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Central de Ajuda
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Política de Privacidade
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Termos de Uso
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-primary">
                            Trocas e Devoluções
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-300 text-sm">
                    © {{ date('Y') }} Minha Loja. Todos os direitos reservados.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <img src="https://via.placeholder.com/40x25?text=Visa" alt="Visa" class="h-6">
                    <img src="https://via.placeholder.com/40x25?text=Master" alt="Mastercard" class="h-6">
                    <img src="https://via.placeholder.com/40x25?text=Pix" alt="Pix" class="h-6">
                </div>
            </div>
        </div>
    </div>
</footer>
