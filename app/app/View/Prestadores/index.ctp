<!-- index.ctp -->
<?php $this->assign('title', 'Prestadores de Serviço'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Prestadores de Serviço</h1>
        <p class="page-subtitle">Veja sua lista de prestadores de serviço</p>
    </div>

<!-- Mensagens de Importação -->
<?php if ($this->Session->check('importacao_sucesso')): ?>
    <?php
        $temErros  = $this->Session->check('erros_importacao');
        $temAvisos = $this->Session->check('avisos_importacao');

        // Estados possíveis:
        // - sucesso_total      = sem erros e sem avisos
        // - sucesso_com_avisos = sem erros, mas com avisos  ✅ continua verde
        // - parcial            = teve sucesso, mas também teve erros 🟡
        if ($temErros) {
            $estado = 'parcial';
        } elseif ($temAvisos) {
            $estado = 'sucesso_com_avisos';
        } else {
            $estado = 'sucesso_total';
        }
    ?>
    
    <!-- Modal de Sucesso / Parcial -->
    <div id="modal-import-success" class="modal-overlay" style="display: flex;">
        <div class="modal">
            <div style="text-align: center;">

                <?php if ($estado === 'parcial'): ?>
                    <!-- Ícone amarelo (triângulo com !) – sucesso parcial (teve erros) -->
                    <div style="width: 56px; height: 56px; margin: 0 auto 20px; background: #FFFBEB; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="28" height="28" fill="none" stroke="#B45309" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a1 1 0 00.86 1.5h18.64a1 1 0 00.86-1.5L13.71 3.86a1 1 0 00-1.72 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 style="font-size: 20px; font-weight: 600; color: #92400E; margin-bottom: 8px;">
                        Importação concluída com erros e avisos
                    </h3>
                    <p style="font-size: 14px; color: #B45309; margin-bottom: 24px;">
                        Alguns prestadores não foram importados ou possuem dados incompletos. Veja os detalhes abaixo.
                    </p>

                <?php else: ?>
                    <!-- Ícone verde (check) – sucesso total OU sucesso com avisos -->
                    <div style="width: 56px; height: 56px; margin: 0 auto 20px; background: #ECFDF5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="28" height="28" fill="none" stroke="#047857" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h3 style="font-size: 20px; font-weight: 600; color: #166534; margin-bottom: 8px;">
                        <?php if ($estado === 'sucesso_com_avisos'): ?>
                            Importação concluída com sucesso
                        <?php else: ?>
                            Lista enviada com sucesso!
                        <?php endif; ?>
                    </h3>
                    <p style="font-size: 14px; color: #4B5563; margin-bottom: 24px;">
                        <?php if ($estado === 'sucesso_com_avisos'): ?>
                            Seu arquivo foi importado. Alguns itens geraram avisos informativos. Veja os detalhes abaixo.
                        <?php else: ?>
                            Confira seus prestadores na tabela abaixo.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                
                <?php if ($temErros || $temAvisos): ?>
                    <div style="text-align: left; max-height: 200px; overflow-y: auto; background: #F9FAFB; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                        <?php if ($temErros): ?>
                            <p style="font-size: 13px; font-weight: 600; color: #DC2626; margin-bottom: 8px;">❌ Erros:</p>
                            <ul style="font-size: 12px; color: #DC2626; margin: 0 0 12px 20px;">
                                <?php foreach ($this->Session->read('erros_importacao') as $erro): ?>
                                    <li><?php echo h($erro); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <?php if ($temAvisos): ?>
                            <p style="font-size: 13px; font-weight: 600; color: #D97706; margin-bottom: 8px;">⚠️ Avisos:</p>
                            <ul style="font-size: 12px; color: #D97706; margin: 0 0 0 20px;">
                                <?php foreach ($this->Session->read('avisos_importacao') as $aviso): ?>
                                    <li><?php echo h($aviso); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <button type="button" class="btn-submit" onclick="fecharModalImportacao()">
                    OK
                </button>
            </div>
        </div>
    </div>

<?php elseif ($this->Session->check('erros_importacao') || $this->Session->check('avisos_importacao')): ?>

    <!-- Modal de Erro na Importação (nenhum registro salvo) -->
    <div id="modal-import-error" class="modal-overlay" style="display: flex;">
        <div class="modal">
            <div style="text-align: center;">
                <div style="width: 56px; height: 56px; margin: 0 auto 20px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" fill="none" stroke="#DC2626" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a1 1 0 00.86 1.5h18.64a1 1 0 00.86-1.5L13.71 3.86a1 1 0 00-1.72 0z"></path>
                    </svg>
                </div>
                
                <h3 style="font-size: 20px; font-weight: 600; color: #B91C1C; margin-bottom: 8px;">
                    Não foi possível importar a lista
                </h3>
                <p style="font-size: 14px; color: #991B1B; margin-bottom: 24px;">
                    Verifique os erros e avisos abaixo e ajuste o arquivo CSV para tentar novamente.
                </p>

                <div style="text-align: left; max-height: 260px; overflow-y: auto; background: #FEF2F2; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #FECACA;">
                    <?php if ($this->Session->check('erros_importacao')): ?>
                        <p style="font-size: 13px; font-weight: 600; color: #DC2626; margin-bottom: 8px;">❌ Erros:</p>
                        <ul style="font-size: 12px; color: #B91C1C; margin: 0 0 12px 20px;">
                            <?php foreach ($this->Session->read('erros_importacao') as $erro): ?>
                                <li><?php echo h($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($this->Session->check('avisos_importacao')): ?>
                        <p style="font-size: 13px; font-weight: 600; color: #D97706; margin-bottom: 8px;">⚠️ Avisos:</p>
                        <ul style="font-size: 12px; color: #92400E; margin: 0 0 0 20px;">
                            <?php foreach ($this->Session->read('avisos_importacao') as $aviso): ?>
                                <li><?php echo h($aviso); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <button type="button" class="btn-submit" onclick="fecharModalImportacaoErro()">
                    Entendi
                </button>
            </div>
        </div>
    </div>

<?php endif; ?>


    
    <!-- Actions Bar -->
    <div class="actions-bar">
        <!-- Busca -->
        <div class="search-input">
            <svg class="search-icon icon-search" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <?php echo $this->Form->create(false, array(
                'type' => 'get',
                'url' => array('controller' => 'prestadores', 'action' => 'index'),
                'inputDefaults' => array('label' => false, 'div' => false),
                'id' => 'prestadores-search-form'
            )); ?>
            <?php echo $this->Form->input('busca', array(
                'type' => 'text',
                'placeholder' => 'Buscar',
                'value' => $busca
            )); ?>
            <?php echo $this->Form->end(); ?>
        </div>
        
        <style>
            .btn-add,
            .btn-import {
                text-decoration: none !important;
            }
        </style>
        <!-- Botões -->
        <div class="button-group">
            <?php echo $this->Html->link(
                '<svg class="icon-upload" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Importar',
                array('action' => 'importar'),
                array('class' => 'btn-import', 'escape' => false)
            ); ?>
            
            <?php echo $this->Html->link(
                '<svg class="icon-plus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Adicionar novo prestador',
                array('action' => 'add'),
                array('class' => 'btn-add', 'escape' => false)
            ); ?>
        </div>
    </div>
    
    <!-- Tabela -->
    <?php if (empty($prestadores)): ?>
        <div style="padding: 48px; text-align: center; color: #667085;">
            <p>Nenhum prestador encontrado.</p>
        </div>
    <?php else: ?>
        <table class="prestadores-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <div class="checkbox-cell">
                            <input type="checkbox" id="select-all-prestadores">
                        </div>
                    </th>
                    <th>Prestador</th>
                    <th>Telefone</th>
                    <th>Serviços</th>
                    <th>Valor</th>
                    <th class="actions-column">
                        <button type="button" class="btn-delete-selected" id="btn-delete-selected">
                            <svg class="icon-delete" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestadores as $prestador): ?>
                    <?php $idPrestador = (int)$prestador['Prestador']['id']; ?>
                    <tr data-prestador-id="<?php echo $idPrestador; ?>">
                        <!-- Checkbox seleção -->
                        <td class="checkbox-column">
                            <div class="checkbox-cell">
                                <input 
                                    type="checkbox" 
                                    class="checkbox-prestador" 
                                    value="<?php echo $idPrestador; ?>">
                            </div>
                        </td>

                        <!-- Prestador (Nome + Email + Foto) -->
                        <td>
                            <div class="prestador-info">
                                <?php if (!empty($prestador['Prestador']['foto'])): ?>
                                    <img src="<?php echo $this->webroot; ?>files/uploads/<?php echo h($prestador['Prestador']['foto']); ?>" 
                                         alt="Foto" 
                                         class="prestador-avatar">
                                <?php else: 
                                    $iniciais = strtoupper(substr($prestador['Prestador']['nome'], 0, 1) . substr($prestador['Prestador']['sobrenome'], 0, 1));
                                ?>
                                    <div class="prestador-avatar-placeholder"><?php echo $iniciais; ?></div>
                                <?php endif; ?>
                                
                                <div class="prestador-details">
                                    <div class="prestador-nome">
                                        <?php echo h($prestador['Prestador']['nome'] . ' ' . $prestador['Prestador']['sobrenome']); ?>
                                    </div>
                                    <div class="prestador-email">
                                        <?php echo h($prestador['Prestador']['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Telefone -->
                        <td class="telefone">
                            <?php echo h($prestador['Prestador']['telefone']); ?>
                        </td>
                        
                        <!-- Serviços -->
                        <td class="servicos-list">
                            <?php 
                            if (!empty($prestador['ServicosComPreco'])) {
                                $totalServicos = count($prestador['ServicosComPreco']);
                                $primeiroServico = h($prestador['ServicosComPreco'][0]['Servico']['nome']);
                                
                                if ($totalServicos == 1) {
                                    // Apenas 1 serviço
                                    echo $primeiroServico;
                                } else {
                                    // Múltiplos serviços - mostrar primeiro + badge
                                    echo $primeiroServico . ' ';
                                    echo '<span class="badge-servicos" title="Ver todos os serviços" data-id="' . $prestador['Prestador']['id'] . '">+' . ($totalServicos - 1) . ' mais</span>';
                                    
                                    // Tooltip escondido com todos os serviços
                                    echo '<div class="tooltip-servicos" id="tooltip-' . $prestador['Prestador']['id'] . '">';
                                    echo '<div class="tooltip-title">Todos os serviços:</div>';
                                    foreach ($prestador['ServicosComPreco'] as $ps) {
                                        echo '<div class="tooltip-item" style="display: flex; justify-content: space-between; gap: 15px;">';
                                        echo '<span>' . h($ps['Servico']['nome']) . '</span>';
                                        echo '<span class="tooltip-valor">R$ ' . number_format($ps['PrestadorServico']['valor'], 2, ',', '.') . '</span>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                            } else {
                                echo '<span style="color: #98A2B3;">Nenhum serviço</span>';
                            }
                            ?>
                        </td>
                        
                        <!-- Valor -->
                        <td class="valor">
                            <?php 
                            if (!empty($prestador['ServicosComPreco'])) {
                                $totalServicos = count($prestador['ServicosComPreco']);
                                $primeiroValor = $prestador['ServicosComPreco'][0]['PrestadorServico']['valor'];
                                
                                echo 'R$ ' . number_format($primeiroValor, 2, ',', '.');
                                
                                if ($totalServicos > 1) {
                                    echo ' <span style="color: #667085; font-size: 12px;">(' . $totalServicos . ' serviços)</span>';
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        
                        <!-- Ações -->
                        <td class="actions-column">
                            <div class="actions-cell">
                                <!-- Editar -->
                                <?php echo $this->Html->link(
                                    '<svg class="icon-edit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                                    array('controller' => 'prestadores', 'action' => 'edit', $prestador['Prestador']['id']),
                                    array('class' => 'btn-icon', 'escape' => false, 'title' => 'Editar')
                                ); ?>
                                
                                <!-- Excluir -->
                                <?php echo $this->Form->postLink(
                                    '<svg class="icon-delete" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
                                    array('controller' => 'prestadores', 'action' => 'delete', $prestador['Prestador']['id']),
                                    array('class' => 'btn-icon delete', 'escape' => false, 'title' => 'Excluir'),
                                    'Tem certeza que deseja excluir este prestador?'
                                ); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <div class="pagination-container">
            <div class="pagination-info">
                Página <?php echo $this->Paginator->counter('{:page}'); ?> de <?php echo $this->Paginator->counter('{:pages}'); ?>
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
.badge-servicos {
    display: inline-block;
    padding: 2px 8px;
    background: #F2F4F7;
    color: #344054;
    font-size: 12px;
    font-weight: 500;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.badge-servicos:hover {
    background: #7F56D9;
    color: #ffffff;
}

.tooltip-servicos {
    display: none;
    position: absolute;
    background: #ffffff;
    border: 1px solid #EAECF0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 12px;
    min-width: 300px;
    z-index: 1000;
    margin-top: 8px;
}

.tooltip-servicos.show {
    display: block;
}

.tooltip-title {
    font-size: 12px;
    font-weight: 600;
    color: #344054;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #EAECF0;
}

.tooltip-item {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 13px;
    color: #667085;
}

.tooltip-valor {
    font-weight: 500;
    color: #101828;
}

/* Coluna de ações (header + linhas) */
.prestadores-table th.actions-column,
.prestadores-table td.actions-column {
    width: 72px;
    text-align: right;
    padding-right: 24px;
}

/* Botão de excluir selecionados (só ícone) */
.btn-delete-selected {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 8px;
    border: 1px solid #F97066;
    background: #F97066;
    color: #FFF;
    cursor: pointer;
    transition: opacity 0.2s, box-shadow 0.2s, background 0.2s, transform 0.1s;
}

.btn-delete-selected.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    box-shadow: none;
}

.btn-delete-selected:not(.disabled):hover {
    background: #F04438;
    transform: translateY(-1px);
}

/* Checkbox alinhado */
.checkbox-column {
    width: 40px;
}

.checkbox-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.checkbox-prestador,
#select-all-prestadores {
    width: 16px;
    height: 16px;
    margin: 0;
    vertical-align: middle;
}
</style>

<script>
jQuery(document).ready(function($) {
    /* ------------------- BUSCA ------------------- */
    var timer;
    $('input[name="busca"]').on('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            $('#prestadores-search-form').submit(); // só o form da busca
        }, 500);
    });
    
    /* ------------------- TOOLTIP SERVIÇOS ------------------- */
    $('.badge-servicos').on('click', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        const tooltip = $('#tooltip-' + id);
        
        // Fechar outros tooltips
        $('.tooltip-servicos').removeClass('show');
        
        // Toggle deste tooltip
        tooltip.toggleClass('show');
        
        // Posicionar próximo ao badge
        const badgePos = $(this).offset();
        tooltip.css({
            top: badgePos.top + 25,
            left: badgePos.left
        });
    });
    
    // Fechar tooltip ao clicar fora
    $(document).on('click', function() {
        $('.tooltip-servicos').removeClass('show');
    });
    
    // Prevenir fechar ao clicar no tooltip
    $('.tooltip-servicos').on('click', function(e) {
        e.stopPropagation();
    });

    /* ------------------- SELEÇÃO MÚLTIPLA ------------------- */
    const STORAGE_KEY = 'prestadores_selecionados';
    
    function loadSelectedIds() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : [];
        } catch (e) {
            return [];
        }
    }

    function saveSelectedIds(ids) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }

    function updateUIFromSelection() {
        const selectedIds = loadSelectedIds();
        let countOnPage = 0;
        let totalCheckboxes = 0;

        $('.checkbox-prestador').each(function() {
            const id = parseInt($(this).val(), 10);
            totalCheckboxes++;
            if (selectedIds.indexOf(id) !== -1) {
                $(this).prop('checked', true);
                countOnPage++;
            } else {
                $(this).prop('checked', false);
            }
        });

        // Atualiza "selecionar todos" da página
        if (totalCheckboxes > 0 && countOnPage === totalCheckboxes) {
            $('#select-all-prestadores').prop('checked', true);
        } else {
            $('#select-all-prestadores').prop('checked', false);
        }

        // Apenas muda o estilo do botão (continua clicável para mostrar alerta)
        if (selectedIds.length === 0) {
            $('#btn-delete-selected').addClass('disabled');
        } else {
            $('#btn-delete-selected').removeClass('disabled');
        }
    }

    function toggleIdInSelection(id, checked) {
        const ids = loadSelectedIds();
        const index = ids.indexOf(id);
        if (checked && index === -1) {
            ids.push(id);
        }
        if (!checked && index !== -1) {
            ids.splice(index, 1);
        }
        saveSelectedIds(ids);
        updateUIFromSelection();
    }

    // Inicializa estado dos checkboxes ao carregar a página
    updateUIFromSelection();

    // Clique em checkboxes individuais
    $(document).on('change', '.checkbox-prestador', function() {
        const id = parseInt($(this).val(), 10);
        toggleIdInSelection(id, $(this).is(':checked'));
    });

    // Selecionar todos da página atual
    $('#select-all-prestadores').on('change', function() {
        const checked = $(this).is(':checked');
        $('.checkbox-prestador').each(function() {
            const id = parseInt($(this).val(), 10);
            $(this).prop('checked', checked);
            toggleIdInSelection(id, checked);
        });
    });

    // Botão de excluir selecionados
    $('#btn-delete-selected').on('click', function() {
        const ids = loadSelectedIds();
        
        if (!ids.length) {
            alert('Nenhum prestador selecionado.');
            return;
        }

        if (!confirm('Tem certeza que deseja excluir os prestadores selecionados?')) {
            return;
        }

        // Limpa seleção salva antes de enviar
        localStorage.removeItem(STORAGE_KEY);

        // Cria form dinâmico para envio por POST
        const form = $('<form>', {
            method: 'post',
            action: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "deleteSelected")); ?>'
        });

        for (let i = 0; i < ids.length; i++) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'ids[]',
                value: ids[i]
            }));
        }

        $('body').append(form);
        form.submit();
    });
});
</script>

<script>
function fecharModalImportacao() {
    var modal = document.getElementById('modal-import-success');
    if (modal) {
        modal.style.display = 'none';
    }

    // Limpar sessão de importação
    jQuery.ajax({
        url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "limpar_sessao_importacao")); ?>',
        type: 'POST'
    });
}

function fecharModalImportacaoErro() {
    var modal = document.getElementById('modal-import-error');
    if (modal) {
        modal.style.display = 'none';
    }

    // Limpar sessão de importação
    jQuery.ajax({
        url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "limpar_sessao_importacao")); ?>',
        type: 'POST'
    });
}
</script>
