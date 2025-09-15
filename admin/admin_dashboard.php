<?php
session_start();

// Verificar se está logado e é admin
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

require_once '../models/Usuario.php';
require_once '../models/Servico.php';

$usuario = new Usuario();
$servico = new Servico();

// Estatísticas básicas
$totalUsuarios = count($usuario->listarTodos());
$totalServicos = count($servico->listarTodos());
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - BarberShop Elite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1f2937',
                        secondary: '#f59e0b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="flex">
        <div class="w-64 bg-primary text-white min-h-screen">
            <div class="p-6">
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-cogs text-secondary mr-2"></i>
                    Admin Panel
                </h2>
            </div>
            
            <nav class="mt-6">
                <a href="#dashboard" onclick="mostrarSecao('dashboard')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Dashboard
                </a>
                <a href="#usuarios" onclick="mostrarSecao('usuarios')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-users mr-3"></i>
                    Usuários
                </a>
                <a href="#servicos" onclick="mostrarSecao('servicos')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-cut mr-3"></i>
                    Serviços
                </a>
                <a href="#barbeiros" onclick="mostrarSecao('barbeiros')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-user-tie mr-3"></i>
                    Barbeiros
                </a>
                <a href="#agendamentos" onclick="mostrarSecao('agendamentos')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-calendar mr-3"></i>
                    Agendamentos
                </a>
                <a href="#configuracoes" onclick="mostrarSecao('configuracoes')" 
                   class="nav-item flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-cog mr-3"></i>
                    Configurações
                </a>
                <div class="border-t border-gray-600 mt-6 pt-6">
                    <a href="../index.php" 
                       class="flex items-center px-6 py-3 text-white hover:bg-gray-700 transition duration-300">
                        <i class="fas fa-home mr-3"></i>
                        Voltar ao Site
                    </a>
                    <a href="#" onclick="logout()" 
                       class="flex items-center px-6 py-3 text-red-400 hover:bg-gray-700 transition duration-300">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sair
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-md p-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-primary">Painel Administrativo</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</span>
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Section -->
            <section id="dashboard" class="p-6">
                <h2 class="text-3xl font-bold text-primary mb-8">Dashboard</h2>
                
                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Total Usuários</p>
                                <p class="text-3xl font-bold text-primary"><?= $totalUsuarios ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 text-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Total Serviços</p>
                                <p class="text-3xl font-bold text-primary"><?= $totalServicos ?></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 text-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cut text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Agendamentos Hoje</p>
                                <p class="text-3xl font-bold text-primary">0</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 text-yellow-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Faturamento Mensal</p>
                                <p class="text-3xl font-bold text-primary">R$ 0,00</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos e outras informações -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-primary mb-4">Agendamentos Recentes</h3>
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-calendar-times text-4xl mb-4"></i>
                            <p>Nenhum agendamento encontrado</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-primary mb-4">Serviços Mais Populares</h3>
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-chart-bar text-4xl mb-4"></i>
                            <p>Dados insuficientes para análise</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Usuários Section -->
            <section id="usuarios" class="p-6 hidden">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-primary">Gerenciar Usuários</h2>
                    <button onclick="abrirModalUsuario()" 
                            class="bg-secondary text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Novo Usuário
                    </button>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="tabelaUsuarios">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="corpoTabelaUsuarios">
                                <!-- Será preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Serviços Section -->
            <section id="servicos" class="p-6 hidden">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-primary">Gerenciar Serviços</h2>
                    <button onclick="abrirModalServico()" 
                            class="bg-secondary text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Novo Serviço
                    </button>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="tabelaServicos">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duração</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="corpoTabelaServicos">
                                <!-- Será preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Outras seções (placeholder) -->
            <section id="barbeiros" class="p-6 hidden">
                <h2 class="text-3xl font-bold text-primary mb-8">Gerenciar Barbeiros</h2>
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Seção de barbeiros será implementada em breve</p>
                </div>
            </section>

            <section id="agendamentos" class="p-6 hidden">
                <h2 class="text-3xl font-bold text-primary mb-8">Gerenciar Agendamentos</h2>
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-calendar text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Seção de agendamentos será implementada em breve</p>
                </div>
            </section>

            <section id="configuracoes" class="p-6 hidden">
                <h2 class="text-3xl font-bold text-primary mb-8">Configurações do Sistema</h2>
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-cog text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Seção de configurações será implementada em breve</p>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal de Serviço -->
    <div id="modalServico" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-primary" id="tituloModalServico">Novo Serviço</h2>
                <button onclick="fecharModalServico()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <form id="formServico" class="space-y-4">
                <input type="hidden" id="servicoId" name="id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Serviço</label>
                    <input type="text" name="nome" id="servicoNome" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                    <textarea name="descricao" id="servicoDescricao" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preço (R$)</label>
                        <input type="number" name="preco" id="servicoPreco" step="0.01" min="0" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duração (min)</label>
                        <input type="number" name="duracao" id="servicoDuracao" min="1" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                    </div>
                </div>
                <button type="submit" 
                        class="w-full bg-secondary text-white py-3 rounded-lg hover:bg-yellow-600 transition duration-300">
                    <i class="fas fa-save mr-2"></i>
                    <span id="textoBotaoServico">Salvar Serviço</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toast de Notificação -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span id="toastMessage"></span>
            <button onclick="fecharToast()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        let servicoEditando = null;

        // Controle de navegação
        function mostrarSecao(secao) {
            // Ocultar todas as seções
            const secoes = document.querySelectorAll('section');
            secoes.forEach(s => s.classList.add('hidden'));
            
            // Mostrar seção selecionada
            document.getElementById(secao).classList.remove('hidden');
            
            // Atualizar navegação ativa
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => item.classList.remove('bg-gray-700'));
            event.target.classList.add('bg-gray-700');
            
            // Carregar dados da seção se necessário
            if (secao === 'usuarios') {
                carregarUsuarios();
            } else if (secao === 'servicos') {
                carregarServicos();
            }
        }

        // Toast de notificação
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastDiv = toast.querySelector('div');
            
            toastMessage.textContent = mensagem;
            
            // Definir cor baseada no tipo
            toastDiv.className = toastDiv.className.replace(/bg-\w+-500/, `bg-${tipo === 'error' ? 'red' : 'green'}-500`);
            
            toast.classList.remove('hidden');
            
            // Auto fechar após 5 segundos
            setTimeout(() => {
                fecharToast();
            }, 5000);
        }

        function fecharToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        // Logout
        async function logout() {
            try {
                const response = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'acao=logout'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = '../index.php';
                }
            } catch (error) {
                console.error('Erro ao fazer logout:', error);
            }
        }

        // Gerenciamento de Usuários
        async function carregarUsuarios() {
            try {
                const response = await fetch('../api/usuarios.php');
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('corpoTabelaUsuarios');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(usuario => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${usuario.nome}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${usuario.email}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${usuario.telefone || '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${usuario.tipo_usuario === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}">
                                    ${usuario.tipo_usuario}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${usuario.status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${usuario.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${new Date(usuario.data_cadastro).toLocaleDateString('pt-BR')}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editarUsuario(${usuario.id})" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="excluirUsuario(${usuario.id})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                mostrarToast('Erro ao carregar usuários', 'error');
            }
        }

        // Gerenciamento de Serviços
        async function carregarServicos() {
            try {
                const response = await fetch('../controllers/ServicoController.php');
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('corpoTabelaServicos');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(servico => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${servico.nome}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                ${servico.descricao || '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                R$ ${parseFloat(servico.preco).toFixed(2).replace('.', ',')}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${servico.duracao} min
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${servico.status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${servico.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editarServico(${servico.id})" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="excluirServico(${servico.id})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar serviços:', error);
                mostrarToast('Erro ao carregar serviços', 'error');
            }
        }

        // Modal de Serviço
        function abrirModalServico(servico = null) {
            const modal = document.getElementById('modalServico');
            const titulo = document.getElementById('tituloModalServico');
            const botao = document.getElementById('textoBotaoServico');
            const form = document.getElementById('formServico');
            
            if (servico) {
                // Modo edição
                titulo.textContent = 'Editar Serviço';
                botao.textContent = 'Atualizar Serviço';
                servicoEditando = servico.id;
                
                // Preencher campos
                document.getElementById('servicoId').value = servico.id;
                document.getElementById('servicoNome').value = servico.nome;
                document.getElementById('servicoDescricao').value = servico.descricao || '';
                document.getElementById('servicoPreco').value = servico.preco;
                document.getElementById('servicoDuracao').value = servico.duracao;
            } else {
                // Modo criação
                titulo.textContent = 'Novo Serviço';
                botao.textContent = 'Salvar Serviço';
                servicoEditando = null;
                form.reset();
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function fecharModalServico() {
            document.getElementById('modalServico').classList.add('hidden');
            document.getElementById('modalServico').classList.remove('flex');
        }

        async function editarServico(id) {
            try {
                const response = await fetch(`../controllers/ServicoController.php?acao=obter&id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    abrirModalServico(data.data);
                }
            } catch (error) {
                console.error('Erro ao carregar serviço:', error);
                mostrarToast('Erro ao carregar serviço', 'error');
            }
        }

        async function excluirServico(id) {
            if (!confirm('Tem certeza que deseja excluir este serviço?')) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('acao', 'excluir');
                formData.append('id', id);
                
                const response = await fetch('../controllers/ServicoController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Serviço excluído com sucesso!');
                    carregarServicos();
                } else {
                    mostrarToast(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao excluir serviço:', error);
                mostrarToast('Erro ao excluir serviço', 'error');
            }
        }

        // Form de Serviço
        document.getElementById('formServico').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('acao', servicoEditando ? 'atualizar' : 'criar');
            
            try {
                const response = await fetch('../controllers/ServicoController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast(data.message);
                    fecharModalServico();
                    carregarServicos();
                } else {
                    mostrarToast(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao salvar serviço:', error);
                mostrarToast('Erro ao salvar serviço', 'error');
            }
        });

        // Funções placeholder
        function abrirModalUsuario() {
            alert('Modal de usuário será implementado em breve!');
        }

        function editarUsuario(id) {
            alert(`Editar usuário ${id} - Função será implementada em breve!`);
        }

        function excluirUsuario(id) {
            alert(`Excluir usuário ${id} - Função será implementada em breve!`);
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Ativar primeiro item da navegação
            document.querySelector('.nav-item').classList.add('bg-gray-700');
        });
    </script>
</body>
</html>