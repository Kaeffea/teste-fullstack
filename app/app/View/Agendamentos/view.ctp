<?php
$this->assign('title', 'Agendamento #' . (int)$agendamento['Agendamento']['id']);

function formatarDataBr($data) {
    if (empty($data)) return '-';
    return date('d/m/Y', strtotime($data));
}
?>

<div class="main-container">

    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Agendamento #<?php echo (int)$agendamento['Agendamento']['id']; ?></h1>
        <p class="page-subtitle">
            Detalhes do agendamento do cliente
            <strong><?php echo h($agendamento['Agendamento']['cliente_nome']); ?></strong>.
        </p>
    </div>

    <!-- Status + período + ações -->
    <div class="view-header-actions">

        <div class="view-header-top">
            <!-- Status + período (sempre juntos) -->
            <div class="view-status-row">
                <span class="badge-status 
                    <?php
                        switch ($statusCalculado) {
                            case 'Em produção':
                                echo 'badge-status-warning';
                                break;
                            case 'Finalizado':
                                echo 'badge-status-success';
                                break;
                            default:
                                echo 'badge-status-info';
                                break;
                        }
                    ?>">
                    <?php echo h($statusCalculado); ?>
                </span>

                <?php if (!empty($dataInicioPeriodo) && !empty($dataFimPeriodo)): ?>
                    <span class="periodo-info">
                        Período:
                        <?php echo formatarDataBr($dataInicioPeriodo); ?>
                        até
                        <?php echo formatarDataBr($dataFimPeriodo); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Ações (lixeira + voltar) -->
            <div class="button-group">
                <?php echo $this->Form->postLink(
                    '<svg class="icon-delete" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                             a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4
                             a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>',
                    array('action' => 'delete', $agendamento['Agendamento']['id']),
                    array('class' => 'btn-icon delete', 'escape' => false),
                    'Tem certeza que deseja excluir este agendamento?'
                ); ?>

                <?php echo $this->Html->link(
                    'Voltar para agendamentos',
                    array('action' => 'index'),
                    array('class' => 'btn-dashboard')
                ); ?>
            </div>
        </div>

    </div>



    <!-- Conteúdo -->
    <div class="form-container" style="padding-top: 0;">

        <!-- Dados do cliente -->
        <div class="form-section">
            <h2 class="form-section-title">Dados do cliente</h2>
            <p class="form-section-subtitle">Informações básicas para contato.</p>

            <div class="form-row">
                <div class="form-group">
                    <label>Nome do cliente</label>
                    <input type="text" value="<?php echo h($agendamento['Agendamento']['cliente_nome']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="text" value="<?php echo h($agendamento['Agendamento']['cliente_email']); ?>" disabled>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" value="<?php echo h($agendamento['Agendamento']['cliente_telefone']); ?>" disabled>
                </div>
            </div>
        </div>



        <!-- Serviços agendados -->
        <div class="form-section">
            <h2 class="form-section-title">Serviços agendados</h2>
            <p class="form-section-subtitle">
                Lista de todos os serviços, prestadores, períodos e valores deste agendamento.
            </p>

            <div class="table-responsive">
                <table class="prestadores-table">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Prestador</th>
                            <th>Período</th>
                            <th>Exclusivo?</th>
                            <th class="col-valor">Valor</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($agendamento['AgendamentoItem'] as $item): ?>
                            <tr>
                                <td><?php echo h($item['Servico']['nome']); ?></td>

                                <td>
                                    <?php
                                        if (!empty($item['Prestador']['id'])) {
                                            echo h($item['Prestador']['nome'] . ' ' . $item['Prestador']['sobrenome']);
                                        } else {
                                            echo '<span class="text-muted">(não definido)</span>';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?php echo formatarDataBr($item['data_inicio']); ?>
                                    até
                                    <?php echo formatarDataBr($item['data_fim']); ?>
                                </td>

                                <td><?php echo !empty($item['exclusivo']) ? 'Sim' : 'Não'; ?></td>

                                <td class="col-valor">
                                    R$ <?php echo number_format((float)$item['valor'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4" class="total-label">Total</td>
                            <td class="total-value">
                                R$ <?php echo number_format((float)$agendamento['Agendamento']['total'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


<style>
/* Layout superior */
.view-header-actions {
    padding: 16px 24px 12px;
    border-bottom: 1px solid #EAECF0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* linha de cima: status + período à esquerda, ações à direita */
.view-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

/* status + período lado a lado */
.view-status-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.view-status-row .periodo-info {
    font-size: 13px;
    color: #667085;
}

/* grupo de botões à direita */
.view-header-actions .button-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* garante que o ícone de lixeira fique “botãozinho” redondo */
.view-header-actions .btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
}

/* link de voltar com aparência de botão */
.view-header-actions .btn-dashboard {
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

/* --------- Mobile / telas menores --------- */
@media (max-width: 768px) {
    .view-header-actions {
        padding: 16px 16px 12px;
        gap: 12px;
    }

    .view-header-top {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .view-header-actions .button-group {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }

    /* Lixeira: tamanho fixo */
    .view-header-actions .btn-icon {
        flex: 0 0 auto;
    }

    /* Botão voltar: ocupa todo o resto */
    .view-header-actions .btn-dashboard {
        flex: 1;               /* agora expande */
        width: auto;
        text-align: center;
        justify-content: center;
        white-space: nowrap;
    }

    .form-container {
        padding-top: 24px !important; 
    }
}


.view-header-left .periodo-info {
    font-size: 13px;
    color: #667085;
    margin-left: 0;           
}

/* Badges */
.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}

.badge-status-info {
    background: #EEF4FF;
    color: #1D4ED8;
}

.badge-status-warning {
    background: #FFFBEB;
    color: #92400E;
}

.badge-status-success {
    background: #ECFDF3;
    color: #027A48;
}

.text-muted {
    color: #98A2B3;
}

/* grupo de botões à direita */
.view-header-actions .button-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* garante que o ícone de lixeira fique “botãozinho” redondo */
.view-header-actions .btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
}

/* link de voltar com aparência de botão */
.btn-dashboard {
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

/* Tabela */
.col-valor {
    text-align: right;
    padding-right: 24px;
}

.total-label {
    text-align: right;
    padding-right: 24px;
    font-weight: 600;
}

.total-value {
    text-align: right;
    padding-right: 24px;
    font-weight: 600;
}

.form-container {
    padding-top: 24px !important; /* afasta "Dados do cliente" do topo */
}

</style>
