<?php $this->assign('title', 'Prestadores de Serviço'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Prestadores de Serviço</h1>
        <p class="page-subtitle">Veja sua lista de prestadores de serviço</p>
    </div>
    
    <!-- Actions Bar -->
    <div class="actions-bar">
        <!-- Busca -->
        <div class="search-input">
            <svg class="search-icon icon-search" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <?php echo $this->Form->create('Prestador', array(
                'type' => 'get',
                'url' => array('action' => 'index'),
                'inputDefaults' => array('label' => false, 'div' => false)
            )); ?>
            <?php echo $this->Form->input('busca', array(
                'type' => 'text',
                'placeholder' => 'Buscar',
                'value' => $busca
            )); ?>
            <?php echo $this->Form->end(); ?>
        </div>
        
        <!-- Botões -->
        <div class="button-group">
            <?php echo $this->Html->link(
                '<svg class="icon-upload" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Importar',
                array('action' => 'importar'),
                array('class' => 'btn-import', 'escape' => false)
            ); ?>
            
            <?php echo $this->Html->link(
                '<svg class="icon-plus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add novo prestador',
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
                    <th>Prestador</th>
                    <th>Telefone</th>
                    <th>Serviços</th>
                    <th>Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestadores as $prestador): ?>
                    <tr>
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
                                $servicos = array();
                                foreach ($prestador['ServicosComPreco'] as $ps) {
                                    $servicos[] = h($ps['Servico']['nome']);
                                }
                                echo implode(', ', $servicos);
                            } else {
                                echo '<span style="color: #98A2B3;">Nenhum serviço</span>';
                            }
                            ?>
                        </td>
                        
                        <!-- Valor (primeiro serviço) -->
                        <td class="valor">
                            <?php 
                            if (!empty($prestador['ServicosComPreco'])) {
                                echo 'R$ ' . number_format($prestador['ServicosComPreco'][0]['PrestadorServico']['valor'], 2, ',', '.');
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        
                        <!-- Ações -->
                        <td>
                            <div class="actions-cell">
                                <!-- Editar -->
                                <?php echo $this->Html->link(
                                    '<svg class="icon-edit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                                    array('action' => 'edit', $prestador['Prestador']['id']),
                                    array('class' => 'btn-icon', 'escape' => false, 'title' => 'Editar')
                                ); ?>
                                
                                <!-- Excluir -->
                                <?php echo $this->Form->postLink(
                                    '<svg class="icon-delete" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
                                    array('action' => 'delete', $prestador['Prestador']['id']),
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
                <?php echo $this->Paginator->prev('Anterior', array(), null, array('class' => 'disabled')); ?>
                <?php echo $this->Paginator->next('Próximo', array(), null, array('class' => 'disabled')); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto-submit do formulário de busca ao digitar (debounce)
jQuery(document).ready(function($) {
    var timer;
    $('input[name="busca"]').on('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            $('form').submit();
        }, 500);
    });
});
</script>