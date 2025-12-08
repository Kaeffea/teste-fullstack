<?php $this->assign('title', 'Dashboard'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Olá, Seu João!</h1>
        <p class="page-subtitle">Visão geral do seu negócio</p>
    </div>

    <style>
        .stats-grid{
            margin-top: 32px;
        }
    </style>

    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $totalPrestadores; ?></div>
                <div class="stat-label">Prestadores</div>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $totalServicos; ?></div>
                <div class="stat-label">Serviços Cadastrados</div>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $totalRelacoes; ?></div>
                <div class="stat-label">Serviços Oferecidos</div>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">R$ <?php echo number_format($valorMedio[0]['media'], 2, ',', '.'); ?></div>
                <div class="stat-label">Valor Médio</div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Rankings -->
    <div class="dashboard-content">
        <!-- Serviços Mais Oferecidos -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>🔥 Serviços Mais Populares</h3>
                <p>Os mais oferecidos pelos prestadores</p>
            </div>
            <div class="ranking-list">
                <?php foreach ($servicosMaisOferecidos as $index => $servico): ?>
                    <div class="ranking-item">
                        <div class="ranking-position">#<?php echo $index + 1; ?></div>
                        <div class="ranking-info">
                            <div class="ranking-name"><?php echo h($servico['Servico']['nome']); ?></div>
                            <div class="ranking-meta">
                                <?php echo $servico[0]['total']; ?> prestador(es) • 
                                Média R$ <?php echo number_format($servico[0]['media_preco'], 2, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="ranking-chart">
                            <div class="chart-bar" style="width: <?php echo min(100, ($servico[0]['total'] / $totalPrestadores) * 100); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Prestadores Versáteis -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>⭐ Prestadores Mais Versáteis</h3>
                <p>Quem oferece mais serviços diferentes</p>
            </div>
            <div class="ranking-list">
                <?php foreach ($prestadoresVersateis as $index => $prest): ?>
                    <div class="ranking-item">
                        <div class="ranking-position">#<?php echo $index + 1; ?></div>
                        <div class="ranking-info">
                            <div class="ranking-name">
                                <?php echo h($prest['Prestador']['nome'] . ' ' . $prest['Prestador']['sobrenome']); ?>
                            </div>
                            <div class="ranking-meta">
                                <?php echo $prest[0]['total_servicos']; ?> serviço(s) oferecido(s)
                            </div>
                        </div>
                        <div class="ranking-badge">
                            <?php echo $prest[0]['total_servicos']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Últimos Cadastrados -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>🆕 Últimos Cadastrados</h3>
            <p>Prestadores adicionados recentemente</p>
        </div>
        <div class="recent-list">
            <?php foreach ($ultimosCadastrados as $prest): ?>
                <div class="recent-item">
                    <?php if (!empty($prest['Prestador']['foto'])): ?>
                        <img src="<?php echo $this->webroot; ?>files/uploads/<?php echo h($prest['Prestador']['foto']); ?>" 
                             alt="Foto" class="recent-avatar">
                    <?php else: 
                        $iniciais = strtoupper(substr($prest['Prestador']['nome'], 0, 1) . substr($prest['Prestador']['sobrenome'], 0, 1));
                    ?>
                        <div class="recent-avatar-placeholder"><?php echo $iniciais; ?></div>
                    <?php endif; ?>
                    
                    <div class="recent-info">
                        <div class="recent-name">
                            <?php echo h($prest['Prestador']['nome'] . ' ' . $prest['Prestador']['sobrenome']); ?>
                        </div>
                        <div class="recent-meta">
                            <?php echo h($prest['Prestador']['email']); ?> • 
                            <?php echo date('d/m/Y', strtotime($prest['Prestador']['created'])); ?>
                        </div>
                    </div>
                    
                    <?php echo $this->Html->link(
                        'Ver detalhes →',
                        array('action' => 'edit', $prest['Prestador']['id']),
                        array('class' => 'recent-link')
                    ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
            .btn-add
            {
                text-decoration: none !important;
                margin-bottom: 32px;
            }
    </style>

    <!-- Botão para ir à lista -->
    <div style="margin-top: 32px; text-align: center;">
        <?php echo $this->Html->link(
            'Ver todos os prestadores →',
            array('action' => 'index'),
            array('class' => 'btn-add', 'style' => 'display: inline-flex;')
        ); ?>
    </div>
</div>