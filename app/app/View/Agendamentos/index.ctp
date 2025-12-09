<?php $this->assign('title', 'Agendamentos'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Agendamentos</h1>
        <p class="page-subtitle">
            Gerencie os serviços marcados com os prestadores do Seu João
        </p>
    </div>

    <!-- Barra de ações -->
    <div class="actions-bar">
        <!-- (Se depois fizermos busca por cliente, dá pra ligar aqui) -->
        <div class="search-input">
            <svg class="search-icon icon-search" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>

            <?php echo $this->Form->create(false, array(
                'type' => 'get',
                'url'  => array('controller' => 'agendamentos', 'action' => 'index'),
                'inputDefaults' => array('label' => false, 'div' => false),
                'id'   => 'agendamentos-search-form'
            )); ?>

            <?php echo $this->Form->input('busca', array(
                'type'        => 'text',
                'placeholder' => 'Buscar por cliente ou e-mail',
                'value'       => isset($busca) ? $busca : ''
            )); ?>

            <?php echo $this->Form->end(); ?>
        </div>

        <!-- Botões -->
        <div class="button-group">

            <?php echo $this->Html->link(
                '<svg class="icon-user" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A7 7 0 0112 14a7 7 0 016.879 3.804M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg> Ver prestadores',
                array('controller' => 'prestadores', 'action' => 'index'),
                array('class' => 'btn-dashboard', 'escape' => false)
            ); ?>



            <?php echo $this->Html->link(
                '<svg class="icon-plus" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"></path>
                 </svg>
                 Novo agendamento',
                array('controller' => 'agendamentos', 'action' => 'novo'), // se sua action for add(), troca aqui
                array('class' => 'btn-add', 'escape' => false)
            ); ?>
        </div>
    </div>

    <!-- Lista de agendamentos -->
    <?php if (empty($agendamentos)): ?>
        <div style="padding: 48px; text-align: center; color: #667085;">
            <p>Nenhum agendamento encontrado.</p>
            <p style="margin-top: 8px; font-size: 13px;">
                Clique em <strong>“Novo agendamento”</strong> para registrar o primeiro serviço.
            </p>
        </div>
    <?php else: ?>

        <div class="table-responsive">
            <table class="agendamentos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Período</th>
                        <th>Serviços</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <?php
                            $a = $agendamento['Agendamento'];

                            // Calcula período com base nos itens
                            $dataInicio = null;
                            $dataFim    = null;
                            $qtdServicos = 0;

                            if (!empty($agendamento['AgendamentoItem'])) {
                                $qtdServicos = count($agendamento['AgendamentoItem']);
                                foreach ($agendamento['AgendamentoItem'] as $item) {
                                    if (empty($item['data_inicio']) || empty($item['data_fim'])) {
                                        continue;
                                    }

                                    $di = $item['data_inicio'];
                                    $df = $item['data_fim'];

                                    if ($dataInicio === null || $di < $dataInicio) {
                                        $dataInicio = $di;
                                    }
                                    if ($dataFim === null || $df > $dataFim) {
                                        $dataFim = $df;
                                    }
                                }
                            }

                            // Formata datas
                            $periodoLabel = 'Sem serviços vinculados';
                            if ($dataInicio && $dataFim) {
                                $periodoLabel = date('d/m/Y', strtotime($dataInicio))
                                    . ' até '
                                    . date('d/m/Y', strtotime($dataFim));
                            }

                
                            // Usa o status calculado automaticamente (injetado pelo controller)
                            $status = $a['status_calculado'];

                            // Define classe visual
                            switch ($status) {
                                case 'Em produção':
                                    $statusClass = 'badge-status-warning';
                                    break;

                                case 'Finalizado':
                                    $statusClass = 'badge-status-success';
                                    break;

                                default:
                                    $statusClass = 'badge-status-info';
                                    break;
                            }

                        ?>
                        <tr>
                            <td>#<?php echo (int)$a['id']; ?></td>

                            <td>
                                <div class="agendamento-cliente">
                                    <div class="cliente-nome">
                                        <?php echo h($a['cliente_nome']); ?>
                                    </div>
                                    <?php if (!empty($a['cliente_email'])): ?>
                                        <div class="cliente-email">
                                            <?php echo h($a['cliente_email']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="col-periodo">
                                <?php echo $periodoLabel; ?>
                            </td>

                            <td class="col-servicos">
                                <?php if ($qtdServicos > 0): ?>
                                    <?php echo $qtdServicos; ?> serviço(s)
                                <?php else: ?>
                                    <span style="color:#98A2B3;">Nenhum serviço</span>
                                <?php endif; ?>
                            </td>

                            <td class="col-total">
                                R$ <?php echo number_format((float)$a['total'], 2, ',', '.'); ?>
                            </td>

                            <td class="col-status">
                                <span class="badge-status <?php echo $statusClass; ?>">
                                    <?php echo h($status); ?>
                                </span>

                            </td>

                            <td class="actions-column">
                                <div class="actions-cell">

                                    <!-- Ver detalhes / prestadores do agendamento -->
                                    <?php echo $this->Html->link(
                                        '<svg class="icon-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7
                                                    C20.268 16.057 16.477 19 12 19S3.732 16.057 2.458 12z" />
                                            <circle cx="12" cy="12" r="3" stroke-width="2" />
                                        </svg>',
                                        array('action' => 'view', $agendamento['Agendamento']['id']),
                                        array('class' => 'btn-icon', 'escape' => false, 'title' => 'Ver detalhes')
                                    ); ?>


                                    <!-- Excluir agendamento -->
                                    <?php echo $this->Form->postLink(
                                        '<svg class="icon-delete" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>',
                                        array('controller' => 'agendamentos', 'action' => 'delete', $agendamento['Agendamento']['id']),
                                        array('class' => 'btn-icon delete', 'escape' => false, 'title' => 'Excluir agendamento'),
                                        'Tem certeza que deseja excluir este agendamento?'
                                    ); ?>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="pagination-container">
            <div class="pagination-info">
                Página <?php echo $this->Paginator->counter('{:page}'); ?>
                de <?php echo $this->Paginator->counter('{:pages}'); ?>
            </div>

            <div class="pagination-buttons">
                <?php if ($this->Paginator->hasPrev()): ?>
                    <?php echo $this->Paginator->prev('Anterior'); ?>
                <?php endif; ?>

                <?php if ($this->Paginator->hasNext()): ?>
                    <?php echo $this->Paginator->next('Próximo'); ?>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>

<style>
/* Reaproveita o estilo da tabela de prestadores, mas com classe própria */
.agendamentos-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 720px;
}

.btn-add,
.btn-import,
.btn-dashboard{
    text-decoration: none !important;
}

.agendamentos-table thead {
    background: #F9FAFB;
    border-top: 1px solid #EAECF0;
    border-bottom: 1px solid #EAECF0;
}

.agendamentos-table th {
    padding: 12px 24px;
    text-align: left;
    font-size: 12px;
    font-weight: 500;
    color: #667085;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.agendamentos-table tbody tr {
    border-bottom: 1px solid #EAECF0;
    transition: background 0.2s;
}

.agendamentos-table tbody tr:hover {
    background: #F9FAFB;
}

.agendamentos-table td {
    padding: 18px 24px;
    font-size: 14px;
    color: #101828;
}

/* Coluna cliente */
.agendamento-cliente {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.cliente-nome {
    font-weight: 500;
    color: #101828;
}

.cliente-email {
    font-size: 13px;
    color: #667085;
}

/* Colunas específicas */
.col-periodo {
    color: #667085;
    min-width: 170px;
}

.col-servicos {
    color: #667085;
}

.col-total {
    font-weight: 600;
    color: #101828;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
}

.agendamentos-table th.actions-column,
.agendamentos-table td.actions-column {
    width: 110px;
    text-align: right;
    padding-right: 24px;
}

.actions-cell {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn-icon {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #667085;
}

.btn-icon:hover {
    background: #F9FAFB;
    color: #344054;
}

.btn-icon.delete:hover {
    background: #FEE2E2;
    color: #DC2626;
}

.icon-user,
.icon-delete {
    width: 18px;
    height: 18px;
}


/* rascunho = cinza */
.status-rascunho {
    background: #F3F4F6;
    color: #4B5563;
    border-color: #E5E7EB;
}

/* marcado = azul */
.status-marcado {
    background: #EFF6FF;
    color: #1D4ED8;
    border-color: #BFDBFE;
}

/* em andamento = amarelo */
.status-em_andamento {
    background: #FFFBEB;
    color: #B45309;
    border-color: #FDE68A;
}

/* concluído = verde */
.status-concluido {
    background: #ECFDF5;
    color: #047857;
    border-color: #A7F3D0;
}

/* cancelado = vermelho */
.status-cancelado {
    background: #FEF2F2;
    color: #DC2626;
    border-color: #FECACA;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}

/* Marcado */
.badge-status-info {
    background: #EEF4FF;
    color: #1D4ED8;
}

/* Em produção */
.badge-status-warning {
    background: #FFFBEB;
    color: #92400E;
}

/* Finalizado */
.badge-status-success {
    background: #ECFDF3;
    color: #027A48;
}


/* Coluna de ações reaproveitando padrão */
.agendamentos-table th.actions-column,
.agendamentos-table td.actions-column {
    width: 96px;
    text-align: right;
    padding-right: 24px;
}

/* Ícones adicionais */
.icon-home {
    width: 16px;
    height: 16px;
}

.icon-eye {
    width: 18px;
    height: 18px;
}

/* Responsivo */
@media (max-width: 768px) {
    .agendamentos-table {
        min-width: 720px;
    }

    .main-container {
        margin: 20px;
    }
}
</style>

<script>
jQuery(function($) {
    // Busca com debounce, igual aos prestadores
    var timer;
    $('input[name="busca"]').on('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            $('#agendamentos-search-form').submit();
        }, 500);
    });
});
</script>
