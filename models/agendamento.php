<?php
require_once '../config/database.php';
verificarLogin();

$database = new Database();
$conn = $database->getConnection();

// Buscar serviços ativos
$query = "SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome";
$stmt = $conn->prepare($query);
$stmt->execute();
$servicos = $stmt->fetchAll();

// Buscar barbeiros ativos
$query = "SELECT * FROM barbeiros WHERE ativo = 1 ORDER BY nome";
$stmt = $conn->prepare($query);
$stmt->execute();
$barbeiros = $stmt->fetchAll();

$sucesso = '';
$erro = '';

// Processar agendamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agendar'])) {
    $servico_id = $_POST['servico_id'];
    $barbeiro_id = $_POST['barbeiro_id'];
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];
    $observacoes = trim($_POST['observacoes']);
    
    // Validações
    if (empty($servico_id) || empty($barbeiro_id) || empty($data_agendamento) || empty($hora_agendamento)) {
        $erro = 'Todos os campos obrigatórios devem ser preenchidos.';
    } else {
        // Verificar se a data não é no passado
        $data_hora_agendamento = $data_agendamento . ' ' . $hora_agendamento;
        if (strtotime($data_hora_agendamento) <= time()) {
            $erro = 'Não é possível agendar para data/hora no passado.';
        } else {
            // Verificar se o horário está disponível
            $query = "SELECT COUNT(*) as total FROM agendamentos 
                      WHERE barbeiro_id = ? AND data_agendamento = ? AND hora_agendamento = ? 
                      AND status IN ('agendado', 'confirmado')";
            $stmt = $conn->prepare($query);
            $stmt->execute([$barbeiro_id, $data_agendamento, $hora_agendamento]);
            $conflito = $stmt->fetch();
            
            if ($conflito['total'] > 0) {
                $erro = 'Este horário já está ocupado. Escolha outro horário.';
            } else {
                // Buscar valor do serviço
                $query = "SELECT preco FROM servicos WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$servico_id]);
                $servico = $stmt->fetch();
                
                // Inserir agendamento
                $query = "INSERT INTO agendamentos (cliente_id, barbeiro_id, servico_id, data_agendamento, hora_agendamento, observacoes, valor) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                
                if ($stmt->execute([$_SESSION['usuario_id'], $barbeiro_id, $servico_id, $data_agendamento, $hora_agendamento, $observacoes, $servico['preco']])) {
                    $sucesso = 'Agendamento realizado com sucesso!';
                } else {
                    $erro = 'Erro ao realizar agendamento. Tente novamente.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - Barbearia Premium</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .agendamento-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .agendamento-card h1 {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn {
            background: #e74c3c;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background: #c0392b;
        }
        
        .btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        
        .servico-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            display: none;
        }
        
        .servico-info.active {
            display: block;
        }
        
        .loading {
            text-align: center;
            color: #666;
            padding: 1rem;
        }
        
        .horarios-disponiveis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .horario-btn {
            padding: 0.5rem;
            border: 2px solid #e1e5e9;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .horario-btn:hover {
            border-color: #667eea;
        }
        
        .horario-btn.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .horario-btn:disabled {
            background: #f8f9fa;
            color: #bdc3c7;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">✂️ Barbearia Premium</div>
            <div class="nav-links">
                <a href="index.php">Início</a>
                <a href="perfil.php">Meu Perfil</a>
                <a href="agendamento.php">Agendar</a>
                <span>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="agendamento-card">
            <h1>📅 Fazer Agendamento</h1>
            
            <?php if ($erro): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($sucesso); ?>
                    <br><a href="perfil.php" style="color: #363; text-decoration: underline;">Ver meus agendamentos</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="agendamentoForm">
                <div class="form-group">
                    <label for="servico_id">Serviço *</label>
                    <select id="servico_id" name="servico_id" required onchange="mostrarInfoServico()">
                        <option value="">Selecione um serviço</option>
                        <?php foreach ($servicos as $servico): ?>
                            <option value="<?php echo $servico['id']; ?>" 
                                    data-preco="<?php echo $servico['preco']; ?>"
                                    data-duracao="<?php echo $servico['duracao']; ?>"
                                    data-descricao="<?php echo htmlspecialchars($servico['descricao']); ?>">
                                <?php echo htmlspecialchars($servico['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="servicoInfo" class="servico-info"></div>
                </div>
                
                <div class="form-group">
                    <label for="barbeiro_id">Barbeiro *</label>
                    <select id="barbeiro_id" name="barbeiro_id" required onchange="carregarHorarios()">
                        <option value="">Selecione um barbeiro</option>
                        <?php foreach ($barbeiros as $barbeiro): ?>
                            <option value="<?php echo $barbeiro['id']; ?>">
                                <?php echo htmlspecialchars($barbeiro['nome']); ?>
                                <?php if ($barbeiro['especialidade']): ?>
                                    - <?php echo htmlspecialchars($barbeiro['especialidade']); ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="data_agendamento">Data *</label>
                        <input type="date" id="data_agendamento" name="data_agendamento" 
                               min="<?php echo date('Y-m-d'); ?>" required onchange="carregarHorarios()">
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_agendamento">Horário *</label>
                        <input type="hidden" id="hora_agendamento" name="hora_agendamento" required>
                        <div id="horariosDisponiveis" class="horarios-disponiveis">
                            <p style="color: #666; grid-column: 1/-1; text-align: center;">
                                Selecione um barbeiro e uma data para ver os horários disponíveis
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" 
                              placeholder="Alguma observação especial? (opcional)"></textarea>
                </div>
                
                <button type="submit" name="agendar" class="btn" id="btnAgendar" disabled>
                    Confirmar Agendamento
                </button>
            </form>
        </div>
    </div>

    <script>
        function mostrarInfoServico() {
            const select = document.getElementById('servico_id');
            const info = document.getElementById('servicoInfo');
            const option = select.selectedOptions[0];
            
            if (option && option.value) {
                const preco = option.getAttribute('data-preco');
                const duracao = option.getAttribute('data-duracao');
                const descricao = option.getAttribute('data-descricao');
                
                info.innerHTML = `
                    <strong>Descrição:</strong> ${descricao}<br>
                    <strong>Preço:</strong> R$ ${parseFloat(preco).toLocaleString('pt-BR', {minimumFractionDigits: 2})}<br>
                    <strong>Duração:</strong> ${duracao} minutos
                `;
                info.classList.add('active');
            } else {
                info.classList.remove('active');
            }
            
            verificarFormCompleto();
        }
        
        function carregarHorarios() {
            const barbeiroId = document.getElementById('barbeiro_id').value;
            const data = document.getElementById('data_agendamento').value;
            const container = document.getElementById('horariosDisponiveis');
            
            if (!barbeiroId || !data) {
                container.innerHTML = '<p style="color: #666; grid-column: 1/-1; text-align: center;">Selecione um barbeiro e uma data para ver os horários disponíveis</p>';
                return;
            }
            
            container.innerHTML = '<p class="loading" style="grid-column: 1/-1;">Carregando horários...</p>';
            
            fetch('horarios_disponiveis.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `barbeiro_id=${barbeiroId}&data=${data}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.horarios.length > 0) {
                        container.innerHTML = '';
                        data.horarios.forEach(horario => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'horario-btn';
                            btn.textContent = horario.hora;
                            btn.onclick = () => selecionarHorario(horario.hora, btn);
                            container.appendChild(btn);
                        });
                    } else {
                        container.innerHTML = '<p style="color: #e74c3c; grid-column: 1/-1; text-align: center;">Nenhum horário disponível para esta data</p>';
                    }
                } else {
                    container.innerHTML = '<p style="color: #e74c3c; grid-column: 1/-1; text-align: center;">Erro ao carregar horários</p>';
                }
            })
            .catch(error => {
                container.innerHTML = '<p style="color: #e74c3c; grid-column: 1/-1; text-align: center;">Erro ao carregar horários</p>';
            });
        }
        
        function selecionarHorario(hora, btn) {
            // Remover seleção anterior
            document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selected'));
            
            // Selecionar novo horário
            btn.classList.add('selected');
            document.getElementById('hora_agendamento').value = hora;
            
            verificarFormCompleto();
        }
        
        function verificarFormCompleto() {
            const servico = document.getElementById('servico_id').value;
            const barbeiro = document.getElementById('barbeiro_id').value;
            const data = document.getElementById('data_agendamento').value;
            const hora = document.getElementById('hora_agendamento').value;
            const btn = document.getElementById('btnAgendar');
            
            if (servico && barbeiro && data && hora) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }
        
        // Definir data mínima como hoje
        document.getElementById('data_agendamento').min = new Date().toISOString().split('T')[0];
        
        // Definir data máxima como 30 dias no futuro
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        document.getElementById('data_agendamento').max = maxDate.toISOString().split('T')[0];
    </script>
</body>
</html>