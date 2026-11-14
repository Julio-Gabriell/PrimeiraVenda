<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Primeira Venda | Loja & Gestão</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fafaf9',
                            100: '#f5f5f4',
                            200: '#e7e5e4',
                            300: '#d6d3d1',
                            400: '#a8a29e',
                            500: '#78716c',
                            600: '#57534e',
                            700: '#44403c',
                            800: '#292524',
                            900: '#1c1917',
                            950: '#0c0a09',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Vue 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

    <style>
        [v-cloak] { display: none; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-brand-50 text-brand-900 antialiased min-h-screen flex flex-col">
    <div id="app" v-cloak class="flex flex-col min-h-screen">

        <!-- Notificação Toast -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-y-4 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-4 opacity-0"
        >
            <div v-if="toast.visivel" class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium border"
                 :class="toast.tipo === 'erro' ? 'bg-red-50 text-red-900 border-red-200' : 'bg-brand-900 text-white border-brand-800'">
                <svg v-if="toast.tipo === 'sucesso'" class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg v-else class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>@{{ toast.mensagem }}</span>
            </div>
        </transition>

        <!-- Navbar Minimalista -->
        <header class="bg-white border-b border-brand-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                
                <!-- Logo -->
                <div class="flex items-center gap-6">
                    <div class="cursor-pointer" @click="abaAtiva = 'loja'">
                        <span class="text-sm font-bold tracking-widest uppercase text-brand-950">PRIMEIRA VENDA</span>
                        <span class="block text-[11px] text-brand-400 font-normal">Catálogo & Checkout</span>
                    </div>

                    <!-- Navegação entre Loja e Estoque -->
                    <nav class="hidden md:flex items-center gap-1 bg-brand-100 p-1 rounded-lg">
                        <button 
                            @click="abaAtiva = 'loja'"
                            :class="abaAtiva === 'loja' ? 'bg-white text-brand-900 shadow-sm' : 'text-brand-600 hover:text-brand-900'"
                            class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all"
                        >
                            Catálogo
                        </button>
                        <button 
                            @click="abaAtiva = 'estoque'"
                            :class="abaAtiva === 'estoque' ? 'bg-white text-brand-900 shadow-sm' : 'text-brand-600 hover:text-brand-900'"
                            class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5"
                        >
                            <span>Ajuste de Estoque</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </button>
                    </nav>
                </div>

                <!-- Ações Direita -->
                <div class="flex items-center gap-3">
                    <!-- Botão Mobile alternar aba -->
                    <div class="flex md:hidden bg-brand-100 p-1 rounded-lg text-xs font-medium">
                        <button @click="abaAtiva = 'loja'" :class="abaAtiva === 'loja' ? 'bg-white shadow-sm' : ''" class="px-2 py-1 rounded">Loja</button>
                        <button @click="abaAtiva = 'estoque'" :class="abaAtiva === 'estoque' ? 'bg-white shadow-sm' : ''" class="px-2 py-1 rounded">Estoque</button>
                    </div>

                    <!-- Botão Sacola / Carrinho -->
                    <button 
                        @click="drawerCarrinho = true"
                        class="flex items-center gap-2 bg-brand-900 hover:bg-brand-800 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition-all"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Sacola</span>
                        <span v-if="totalItensCarrinho > 0" class="bg-white text-brand-900 text-[11px] font-bold px-1.5 py-0.2 rounded-full ml-0.5">
                            @{{ totalItensCarrinho }}
                        </span>
                    </button>
                </div>
            </div>
        </header>

        <!-- CONTEÚDO PRINCIPAL: CATÁLOGO -->
        <main v-if="abaAtiva === 'loja'" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Barra Superior de Filtros e Busca -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <!-- Categorias -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <button 
                        v-for="cat in categorias" 
                        :key="cat"
                        @click="categoriaSelecionada = cat"
                        :class="categoriaSelecionada === cat ? 'bg-brand-900 text-white' : 'bg-white text-brand-600 hover:text-brand-900 border border-brand-200'"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all"
                    >
                        @{{ cat }}
                    </button>
                </div>

                <!-- Campo de Busca -->
                <div class="relative w-full sm:w-72">
                    <input 
                        type="text" 
                        v-model="termoBusca"
                        placeholder="Buscar produto..."
                        class="w-full bg-white border border-brand-200 rounded-lg pl-9 pr-3 py-1.5 text-xs text-brand-900 placeholder-brand-400 focus:outline-none focus:ring-1 focus:ring-brand-900 focus:border-brand-900"
                    />
                    <svg class="w-4 h-4 text-brand-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="carregando" class="py-20 text-center">
                <div class="inline-block w-6 h-6 border-2 border-brand-400 border-t-brand-900 rounded-full animate-spin"></div>
                <p class="text-xs text-brand-500 mt-3">Carregando catálogo...</p>
            </div>

            <!-- Grid de Produtos -->
            <div v-else-if="produtosFiltrados.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div 
                    v-for="produto in produtosFiltrados" 
                    :key="produto.id"
                    class="bg-white border border-brand-200 rounded-xl overflow-hidden hover:border-brand-400 transition-all flex flex-col justify-between group"
                >
                    <!-- Imagem ou Placeholder Neutro -->
                    <div class="aspect-square bg-brand-100 relative overflow-hidden flex items-center justify-center">
                        <img 
                            v-if="produto.url_imagem" 
                            :src="produto.url_imagem" 
                            :alt="produto.nome"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="text-center p-6 text-brand-400">
                            <svg class="w-12 h-12 mx-auto stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="text-[11px] block mt-1 tracking-wider uppercase font-semibold text-brand-400">@{{ produto.categoria || 'Produto' }}</span>
                        </div>

                        <!-- Badge de Estoque -->
                        <div class="absolute top-3 right-3">
                            <span 
                                v-if="(produto.estoque?.quantidade || 0) > 0" 
                                class="bg-white/90 backdrop-blur-sm text-brand-700 text-[11px] font-medium px-2 py-0.5 rounded-md border border-brand-200 shadow-sm"
                            >
                                @{{ produto.estoque.quantidade }} disp.
                            </span>
                            <span 
                                v-else 
                                class="bg-red-50 text-red-700 text-[11px] font-medium px-2 py-0.5 rounded-md border border-red-200"
                            >
                                Esgotado
                            </span>
                        </div>
                    </div>

                    <!-- Dados do Produto -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-semibold tracking-wider text-brand-400 uppercase">@{{ produto.categoria || 'Geral' }}</span>
                            <h3 class="text-sm font-semibold text-brand-900 mt-0.5 line-clamp-1">@{{ produto.nome }}</h3>
                            <p class="text-xs text-brand-500 mt-1 line-clamp-2">@{{ produto.descricao || 'Sem descrição cadastrada.' }}</p>
                        </div>

                        <div class="pt-4 mt-4 border-t border-brand-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-brand-400 block font-normal">Preço à vista</span>
                                <span class="text-sm font-bold text-brand-900">R$ @{{ formatarPreco(produto.preco) }}</span>
                            </div>

                            <button 
                                @click="adicionarAoCarrinho(produto)"
                                :disabled="(produto.estoque?.quantidade || 0) <= 0"
                                :class="(produto.estoque?.quantidade || 0) > 0 
                                    ? 'bg-brand-900 hover:bg-brand-800 text-white' 
                                    : 'bg-brand-200 text-brand-400 cursor-not-allowed'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1"
                            >
                                <span>Adicionar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vazio -->
            <div v-else class="py-16 text-center bg-white rounded-xl border border-brand-200">
                <p class="text-sm text-brand-600 font-medium">Nenhum produto encontrado com os filtros aplicados.</p>
                <button @click="categoriaSelecionada = 'Todas'; termoBusca = ''" class="mt-3 text-xs text-brand-900 underline font-semibold">
                    Limpar filtros
                </button>
            </div>
        </main>

        <!-- CONTEÚDO PRINCIPAL: GESTÃO DE ESTOQUE -->
        <main v-if="abaAtiva === 'estoque'" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-lg font-bold text-brand-950">Ajuste Manual de Estoque</h1>
                    <p class="text-xs text-brand-500">Monitore as quantidades e efetue entradas ou saídas de mercadorias pontuais.</p>
                </div>
                <button 
                    @click="carregarProdutos"
                    class="self-start sm:self-auto px-3 py-1.5 bg-white border border-brand-200 hover:border-brand-300 rounded-lg text-xs font-medium text-brand-700 transition-all flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Atualizar lista</span>
                </button>
            </div>

            <!-- Tabela de Estoque -->
            <div class="bg-white border border-brand-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-brand-50/75 border-b border-brand-200 text-brand-500 font-semibold uppercase text-[11px] tracking-wider">
                                <th class="py-3 px-4">Produto</th>
                                <th class="py-3 px-4">Categoria</th>
                                <th class="py-3 px-4">Preço Unit.</th>
                                <th class="py-3 px-4 text-center">Quantidade Atual</th>
                                <th class="py-3 px-4">Última Origem</th>
                                <th class="py-3 px-4 text-right">Ajuste Rápido</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="produto in produtos" :key="produto.id" class="hover:bg-brand-50/50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-brand-900">@{{ produto.nome }}</div>
                                    <div class="text-[11px] text-brand-400">ID: #@{{ produto.id }}</div>
                                </td>
                                <td class="py-3 px-4 text-brand-600">@{{ produto.categoria || '—' }}</td>
                                <td class="py-3 px-4 text-brand-900 font-medium">R$ @{{ formatarPreco(produto.preco) }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span 
                                        :class="(produto.estoque?.quantidade || 0) > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200'"
                                        class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold border"
                                    >
                                        @{{ produto.estoque?.quantidade || 0 }} unid.
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-[11px] font-mono text-brand-500 bg-brand-100 px-2 py-0.5 rounded">
                                        @{{ produto.estoque?.ultima_origem || 'CADASTRO' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            @click="ajustarEstoque(produto.id, 1, 'remover')"
                                            :disabled="(produto.estoque?.quantidade || 0) <= 0"
                                            class="w-7 h-7 rounded bg-brand-100 hover:bg-brand-200 disabled:opacity-40 text-brand-700 font-bold transition-all flex items-center justify-center"
                                            title="Diminuir 1 unidade"
                                        >
                                            -1
                                        </button>
                                        <button 
                                            @click="ajustarEstoque(produto.id, 1, 'adicionar')"
                                            class="w-7 h-7 rounded bg-brand-100 hover:bg-brand-200 text-brand-700 font-bold transition-all flex items-center justify-center"
                                            title="Adicionar 1 unidade"
                                        >
                                            +1
                                        </button>
                                        <button 
                                            @click="abrirModalAjuste(produto)"
                                            class="px-2.5 py-1 rounded bg-brand-900 hover:bg-brand-800 text-white font-medium transition-all"
                                        >
                                            Definir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- DRAWER DA SACOLA / CARRINHO -->
        <div v-if="drawerCarrinho" class="fixed inset-0 z-40 overflow-hidden">
            <!-- Overlay -->
            <div 
                @click="drawerCarrinho = false" 
                class="absolute inset-0 bg-brand-950/40 backdrop-blur-sm transition-opacity"
            ></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-md bg-white border-l border-brand-200 flex flex-col justify-between shadow-2xl">
                    
                    <!-- Topo -->
                    <div class="p-6 border-b border-brand-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-brand-900 uppercase tracking-wider">Sua Sacola</h2>
                            <p class="text-xs text-brand-500">Revise os itens antes de prosseguir</p>
                        </div>
                        <button @click="drawerCarrinho = false" class="p-1 rounded-md text-brand-400 hover:text-brand-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Lista de Itens -->
                    <div class="p-6 flex-1 overflow-y-auto divide-y divide-brand-100">
                        <div v-if="carrinho.length === 0" class="py-16 text-center">
                            <svg class="w-10 h-10 mx-auto text-brand-300 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-xs text-brand-500 mt-2">Sua sacola está vazia no momento.</p>
                            <button @click="drawerCarrinho = false" class="mt-3 text-xs text-brand-900 font-semibold underline">
                                Explorar produtos
                            </button>
                        </div>

                        <div v-for="item in carrinho" :key="item.produto_id" class="py-4 flex items-center justify-between gap-3">
                            <div class="flex-1">
                                <h4 class="text-xs font-semibold text-brand-900 line-clamp-1">@{{ item.nome }}</h4>
                                <span class="text-[11px] text-brand-500">R$ @{{ formatarPreco(item.preco) }} un.</span>
                            </div>

                            <!-- Controles de Quantidade -->
                            <div class="flex items-center gap-1.5 bg-brand-100 p-1 rounded-lg">
                                <button 
                                    @click="alterarQtdCarrinho(item.produto_id, -1)"
                                    class="w-5 h-5 rounded bg-white text-brand-800 text-xs font-bold flex items-center justify-center hover:bg-brand-200"
                                >-</button>
                                <span class="w-6 text-center text-xs font-semibold text-brand-900">@{{ item.quantidade }}</span>
                                <button 
                                    @click="alterarQtdCarrinho(item.produto_id, 1)"
                                    class="w-5 h-5 rounded bg-white text-brand-800 text-xs font-bold flex items-center justify-center hover:bg-brand-200"
                                >+</button>
                            </div>

                            <!-- Subtotal e Remover -->
                            <div class="text-right">
                                <div class="text-xs font-bold text-brand-900">R$ @{{ formatarPreco(item.preco * item.quantidade) }}</div>
                                <button @click="removerDoCarrinho(item.produto_id)" class="text-[10px] text-red-600 hover:text-red-800 mt-0.5">
                                    Remover
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Checkout & Finalização -->
                    <div v-if="carrinho.length > 0" class="p-6 border-t border-brand-200 bg-brand-50/50 space-y-4">
                        
                        <!-- Dados do Cliente -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-semibold text-brand-700 uppercase tracking-wider">Identificação do Comprador</label>
                            <input 
                                type="text" 
                                v-model="checkoutForm.cliente_nome"
                                placeholder="Nome completo"
                                class="w-full bg-white border border-brand-200 rounded-lg px-3 py-1.5 text-xs text-brand-900 focus:outline-none focus:ring-1 focus:ring-brand-900"
                            />
                            <input 
                                type="text" 
                                v-model="checkoutForm.cliente_telefone"
                                placeholder="WhatsApp (ex: 11999999999)"
                                class="w-full bg-white border border-brand-200 rounded-lg px-3 py-1.5 text-xs text-brand-900 focus:outline-none focus:ring-1 focus:ring-brand-900"
                            />
                        </div>

                        <!-- Resumo de Valores -->
                        <div class="pt-2 border-t border-brand-200 flex justify-between items-baseline">
                            <span class="text-xs text-brand-600">Total a pagar:</span>
                            <span class="text-base font-bold text-brand-950">R$ @{{ formatarPreco(valorTotalCarrinho) }}</span>
                        </div>

                        <!-- Botão Finalizar -->
                        <button 
                            @click="finalizarPedido"
                            :disabled="finalizandoPedido"
                            class="w-full bg-brand-900 hover:bg-brand-800 disabled:opacity-50 text-white py-2.5 rounded-lg text-xs font-semibold tracking-wider uppercase transition-all flex items-center justify-center gap-2"
                        >
                            <svg v-if="finalizandoPedido" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>@{{ finalizandoPedido ? 'Processando Venda...' : 'Finalizar Pedido com Pix' }}</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL DE CONFIRMAÇÃO DE VENDA (PIX & WEBHOOK) -->
        <div v-if="modalVendaSucesso" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-950/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-brand-200 shadow-2xl relative">
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-brand-900">Pedido Realizado com Sucesso!</h3>
                    <p class="text-xs text-brand-500 mt-1">Pedido #@{{ pedidoConcluido.id }} • @{{ pedidoConcluido.cliente_nome }}</p>
                </div>

                <!-- Detalhes do Pix -->
                <div class="mt-6 bg-brand-50 p-4 rounded-xl border border-brand-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs text-brand-600 font-medium">Valor Total:</span>
                        <span class="text-sm font-bold text-brand-900">R$ @{{ formatarPreco(pedidoConcluido.valor_total) }}</span>
                    </div>

                    <div class="mt-3">
                        <label class="block text-[11px] font-semibold text-brand-600 uppercase mb-1">Código Pix Copia e Cola:</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                readonly 
                                :value="pedidoConcluido.pix_copia_cola || '00020126580014br.gov.bcb.pix0136exemplo-chave-pix-primeiravenda'"
                                class="w-full bg-white border border-brand-200 rounded-lg px-2.5 py-1.5 text-[11px] font-mono text-brand-700 select-all"
                            />
                            <button 
                                @click="copiarPix"
                                class="px-3 py-1.5 bg-brand-900 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold whitespace-nowrap transition-all"
                            >
                                @{{ pixCopiado ? 'Copiado!' : 'Copiar' }}
                            </button>
                        </div>
                    </div>

                    <!-- Status do Webhook n8n -->
                    <div class="mt-4 pt-3 border-t border-brand-200 flex items-center justify-between text-[11px]">
                        <span class="text-brand-500">Notificação n8n:</span>
                        <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Webhook disparado
                        </span>
                    </div>
                </div>

                <!-- Botão Fechar -->
                <button 
                    @click="fecharModalSucesso"
                    class="mt-6 w-full bg-brand-900 hover:bg-brand-800 text-white py-2 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all"
                >
                    Continuar Comprando
                </button>
            </div>
        </div>

        <!-- MODAL DE AJUSTE DE ESTOQUE ESPECÍFICO -->
        <div v-if="modalAjuste.visivel" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-950/40 backdrop-blur-sm">
            <div class="bg-white rounded-xl max-w-sm w-full p-5 border border-brand-200 shadow-xl">
                <h3 class="text-sm font-bold text-brand-900">Definir Estoque</h3>
                <p class="text-xs text-brand-500 mt-0.5">@{{ modalAjuste.produto?.nome }}</p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-[11px] text-brand-600 font-medium mb-1">Nova Quantidade em Unidades:</label>
                        <input 
                            type="number" 
                            min="0"
                            v-model.number="modalAjuste.quantidade"
                            class="w-full bg-white border border-brand-200 rounded-lg px-3 py-1.5 text-xs text-brand-900 font-semibold focus:outline-none focus:ring-1 focus:ring-brand-900"
                        />
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-2">
                    <button 
                        @click="modalAjuste.visivel = false"
                        class="px-3 py-1.5 text-xs text-brand-600 hover:text-brand-900 font-medium"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="confirmarAjusteModal"
                        class="px-4 py-1.5 bg-brand-900 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold"
                    >
                        Salvar Alteração
                    </button>
                </div>
            </div>
        </div>

        <!-- Rodapé Simples -->
        <footer class="bg-white border-t border-brand-200 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center text-xs text-brand-400">
                <span>Primeira Venda &copy; {{ date('Y') }} — Ambiente Integrado com Webhooks & Gestão</span>
            </div>
        </footer>

    </div>

    <!-- Script Vue 3 -->
    <script>
        const { createApp, ref, computed, onMounted } = Vue;

        createApp({
            setup() {
                const abaAtiva = ref('loja');
                const produtos = ref([]);
                const carregando = ref(true);
                const categoriaSelecionada = ref('Todas');
                const termoBusca = ref('');

                // Carrinho
                const drawerCarrinho = ref(false);
                const carrinho = ref([]);
                const finalizandoPedido = ref(false);
                const checkoutForm = ref({
                    cliente_nome: '',
                    cliente_telefone: '',
                });

                // Confirmação de Venda
                const modalVendaSucesso = ref(false);
                const pedidoConcluido = ref({});
                const pixCopiado = ref(false);

                // Modal Estoque
                const modalAjuste = ref({
                    visivel: false,
                    produto: null,
                    quantidade: 0,
                });

                // Toast
                const toast = ref({
                    visivel: false,
                    mensagem: '',
                    tipo: 'sucesso',
                });

                function mostrarToast(mensagem, tipo = 'sucesso') {
                    toast.value = { visivel: true, mensagem, tipo };
                    setTimeout(() => {
                        toast.value.visivel = false;
                    }, 3500);
                }

                function formatarPreco(valor) {
                    const numero = parseFloat(valor) || 0;
                    return numero.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                // Carrega catálogo da API
                async function carregarProdutos() {
                    carregando.value = true;
                    try {
                        const res = await fetch('/api/produtos');
                        if (!res.ok) throw new Error('Falha ao obter produtos');
                        produtos.value = await res.json();
                    } catch (e) {
                        mostrarToast('Erro ao carregar produtos da API', 'erro');
                    } finally {
                        carregando.value = false;
                    }
                }

                // Categorias únicas
                const categorias = computed(() => {
                    const lista = new Set(['Todas']);
                    produtos.value.forEach(p => {
                        if (p.categoria) lista.add(p.categoria);
                    });
                    return Array.from(lista);
                });

                // Filtro de produtos
                const produtosFiltrados = computed(() => {
                    return produtos.value.filter(p => {
                        const bateCategoria = categoriaSelecionada.value === 'Todas' || p.categoria === categoriaSelecionada.value;
                        const bateBusca = !termoBusca.value || 
                            p.nome.toLowerCase().includes(termoBusca.value.toLowerCase()) ||
                            (p.descricao && p.descricao.toLowerCase().includes(termoBusca.value.toLowerCase()));
                        return bateCategoria && bateBusca;
                    });
                });

                // Adicionar ao Carrinho
                function adicionarAoCarrinho(produto) {
                    const estoqueDisp = produto.estoque?.quantidade || 0;
                    if (estoqueDisp <= 0) {
                        mostrarToast('Produto sem estoque disponível.', 'erro');
                        return;
                    }

                    const itemExistente = carrinho.value.find(i => i.produto_id === produto.id);
                    if (itemExistente) {
                        if (itemExistente.quantidade < estoqueDisp) {
                            itemExistente.quantidade++;
                            mostrarToast(`${produto.nome} adicionado.`);
                        } else {
                            mostrarToast('Limite de estoque atingido para este item.', 'erro');
                        }
                    } else {
                        carrinho.value.push({
                            produto_id: produto.id,
                            nome: produto.nome,
                            preco: parseFloat(produto.preco),
                            quantidade: 1,
                            estoque_max: estoqueDisp,
                        });
                        mostrarToast(`${produto.nome} adicionado à sacola.`);
                    }
                }

                function alterarQtdCarrinho(produtoId, delta) {
                    const item = carrinho.value.find(i => i.produto_id === produtoId);
                    if (!item) return;

                    const novaQtd = item.quantidade + delta;
                    if (novaQtd <= 0) {
                        removerDoCarrinho(produtoId);
                    } else if (novaQtd <= item.estoque_max) {
                        item.quantidade = novaQtd;
                    } else {
                        mostrarToast('Quantidade máxima em estoque atingida.', 'erro');
                    }
                }

                function removerDoCarrinho(produtoId) {
                    carrinho.value = carrinho.value.filter(i => i.produto_id !== produtoId);
                }

                const totalItensCarrinho = computed(() => {
                    return carrinho.value.reduce((acc, i) => acc + i.quantidade, 0);
                });

                const valorTotalCarrinho = computed(() => {
                    return carrinho.value.reduce((acc, i) => acc + (i.preco * i.quantidade), 0);
                });

                // Finalizar Pedido -> Dispara POST /api/pedidos e aciona o Webhook n8n
                async function finalizarPedido() {
                    if (carrinho.value.length === 0) return;
                    if (!checkoutForm.value.cliente_nome.trim()) {
                        mostrarToast('Por favor, informe seu nome completo.', 'erro');
                        return;
                    }

                    finalizandoPedido.value = true;
                    try {
                        const payload = {
                            cliente_nome: checkoutForm.value.cliente_nome,
                            cliente_telefone: checkoutForm.value.cliente_telefone || null,
                            valor_total: valorTotalCarrinho.value,
                            status: 'PENDENTE',
                            pix_copia_cola: '00020126580014br.gov.bcb.pix0136' + Math.random().toString(36).substring(2) + '5204000053039865406' + valorTotalCarrinho.value.toFixed(2) + '5802BR5909PrimeiraVenda6009SAO PAULO',
                            checkout_url: window.location.origin + '/checkout/' + Date.now(),
                            itens: carrinho.value.map(i => ({
                                produto_id: i.produto_id,
                                quantidade: i.quantidade,
                                preco_unitario: i.preco,
                            }))
                        };

                        const res = await fetch('/api/pedidos', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(payload),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Erro ao processar pedido');
                        }

                        pedidoConcluido.value = data.pedido;
                        carrinho.value = [];
                        drawerCarrinho.value = false;
                        modalVendaSucesso.value = true;
                        mostrarToast('Pedido confirmado e webhook enviado com sucesso!');

                        // Recarregar os produtos para atualizar o estoque na tela
                        await carregarProdutos();
                    } catch (e) {
                        mostrarToast(e.message || 'Erro ao finalizar pedido', 'erro');
                    } finally {
                        finalizandoPedido.value = false;
                    }
                }

                function copiarPix() {
                    const codigo = pedidoConcluido.value.pix_copia_cola;
                    navigator.clipboard.writeText(codigo).then(() => {
                        pixCopiado.value = true;
                        setTimeout(() => pixCopiado.value = false, 2500);
                        mostrarToast('Código Pix copiado para a área de transferência.');
                    });
                }

                function fecharModalSucesso() {
                    modalVendaSucesso.value = false;
                    pedidoConcluido.value = {};
                    checkoutForm.value = { cliente_nome: '', cliente_telefone: '' };
                }

                // Ajuste de Estoque
                async function ajustarEstoque(produtoId, quantidade, operacao) {
                    try {
                        const res = await fetch(`/api/estoque/${produtoId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({
                                quantidade: quantidade,
                                operacao: operacao,
                                origem: 'AJUSTE_MANUAL',
                            }),
                        });

                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Erro ao ajustar estoque');

                        mostrarToast('Estoque atualizado com sucesso.');
                        await carregarProdutos();
                    } catch (e) {
                        mostrarToast(e.message || 'Erro ao ajustar estoque', 'erro');
                    }
                }

                function abrirModalAjuste(produto) {
                    modalAjuste.value = {
                        visivel: true,
                        produto: produto,
                        quantidade: produto.estoque?.quantidade || 0,
                    };
                }

                async function confirmarAjusteModal() {
                    const { produto, quantidade } = modalAjuste.value;
                    if (!produto) return;

                    await ajustarEstoque(produto.id, quantidade, 'definir');
                    modalAjuste.value.visivel = false;
                }

                onMounted(() => {
                    carregarProdutos();
                });

                return {
                    abaAtiva,
                    produtos,
                    carregando,
                    categoriaSelecionada,
                    termoBusca,
                    categorias,
                    produtosFiltrados,
                    drawerCarrinho,
                    carrinho,
                    totalItensCarrinho,
                    valorTotalCarrinho,
                    checkoutForm,
                    finalizandoPedido,
                    modalVendaSucesso,
                    pedidoConcluido,
                    pixCopiado,
                    modalAjuste,
                    toast,
                    formatarPreco,
                    carregarProdutos,
                    adicionarAoCarrinho,
                    alterarQtdCarrinho,
                    removerDoCarrinho,
                    finalizarPedido,
                    copiarPix,
                    fecharModalSucesso,
                    ajustarEstoque,
                    abrirModalAjuste,
                    confirmarAjusteModal,
                };
            }
        }).mount('#app');
    </script>
</body>
</html>
