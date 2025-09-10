<?php
require_once '../config/database.php';

// Buscar serviços ativos
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM servicos WHERE ativo = 1 ORDER BY preco";
$stmt = $conn->prepare($query);
$stmt->execute();
$servicos = $stmt->fetchAll();

iniciarSessao();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia Premium</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .nav-links a, .btn {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover, .btn:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .btn-primary {
            background: #3498db;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600"><rect fill="%23444" width="1000" height="600"/><text x="500" y="300" font-size="48" fill="%23888" text-anchor="middle" dominant-baseline="middle">Barbearia</text></svg>');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .services {
            padding: 5rem 2rem;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #2c3e50;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .service-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .price {
            font-size: 2rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .duration {
            color: #95a5a6;
            font-size: 0.9rem;
        }
        
        .cta-section {
            background: #2c3e50;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .btn-large {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            margin: 1rem;
            transition: background 0.3s;
        }
        
        .btn-large:hover {
            background: #c0392b;
        }
        
        footer {
            background: #34495e;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">✂️ Barbearia Premium</div>
            <div class="nav-links">
                <a href="#servicos">Serviços</a>
                <?php if (usuarioLogado()): ?>
                    <a href="perfil.php">Meu Perfil</a>
                    <a href="agendamento.php">Agendar</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/">Painel Admin</a>
                    <?php endif; ?>
                    <a href="logout.php">Sair</a>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                    <a href="registro.php" class="btn btn-primary">Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Barbearia Premium</h1>
            <p>O melhor atendimento em cortes masculinos e cuidados com a barba. Profissionais experientes e ambiente acolhedor.</p>
            <?php if (!usuarioLogado()): ?>
                <a href="registro.php" class="btn-large">Cadastre-se e Agende</a>
            <?php else: ?>
                <a href="agendamento.php" class="btn-large">Fazer Agendamento</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="services" id="servicos">
        <div class="container">
            <h2 class="section-title">Nossos Serviços</h2>
            <div class="services-grid">
                <?php foreach ($servicos as $servico): ?>
                    <div class="service-card">
                        <h3><?php echo htmlspecialchars($servico['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
                        <div class="price">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></div>
                        <div class="duration"><?php echo $servico['duracao']; ?> minutos</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Pronto para um novo visual?</h2>
            <p>Agende seu horário agora mesmo e desfrute do melhor atendimento da cidade!</p>
            <?php if (!usuarioLogado()): ?>
                <a href="registro.php" class="btn-large">Criar Conta</a>
                <a href="login.php" class="btn-large" style="background: #3498db;">Já tenho conta</a>
            <?php else: ?>
                <a href="agendamento.php" class="btn-large">Agendar Horário</a>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Barbearia Premium. Todos os direitos reservados.</p>
            <p>Endereço: Rua das Flores, 123 - Centro | Telefone: (11) 99999-0000</p>
        </div>
    </footer>
</body>
</html>