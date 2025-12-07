<?php $this->assign('title', 'Editar Prestador'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Editar Prestador de Serviço</h1>
    </div>
    
    <!-- Formulário -->
    <div class="form-container">
        <?php echo $this->Form->create('Prestador', array(
            'url' => array('controller' => 'prestadores', 'action' => 'edit', $this->request->data['Prestador']['id']),
            'type' => 'file',
            'inputDefaults' => array(
                'label' => false,
                'div' => false
            )
        )); ?>
        
        <?php echo $this->Form->hidden('id'); ?>
        
        <!-- Seção: Informações Pessoais -->
        <div class="form-section">
            <h2 class="form-section-title">Informações pessoais</h2>
            <p class="form-section-subtitle">Atualize suas informações e foto.</p>
            
            <!-- Nome (2 colunas) -->
            <div class="form-row">
                <div class="form-group">
                    <label>Nome</label>
                    <?php echo $this->Form->input('nome', array(
                        'type' => 'text',
                        'required' => true
                    )); ?>
                </div>
                
                <div class="form-group">
                    <label>Sobrenome</label>
                    <?php echo $this->Form->input('sobrenome', array(
                        'type' => 'text',
                        'required' => true
                    )); ?>
                </div>
            </div>
            
            <!-- Email -->
            <div class="form-row full">
                <div class="form-group">
                    <label>Email</label>
                    <?php echo $this->Form->input('email', array(
                        'type' => 'email',
                        'required' => true
                    )); ?>
                </div>
            </div>
            
            <!-- Foto -->
            <div class="form-row full">
                <div class="form-group">
                    <label>Sua foto</label>
                    <p style="font-size: 13px; color: #667085; margin-bottom: 8px;">Ela aparecerá no seu perfil.</p>
                    
                    <?php if (!empty($this->request->data['Prestador']['foto'])): ?>
                        <div class="foto-preview" style="margin-bottom: 16px;">
                            <img src="<?php echo $this->webroot; ?>files/uploads/<?php echo h($this->request->data['Prestador']['foto']); ?>" 
                                 alt="Foto atual" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #EAECF0;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="upload-area" onclick="document.getElementById('file-input').click()">
                        <div class="upload-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <p class="upload-text">
                            <strong>Clique para enviar nova foto</strong> ou arraste e solte
                        </p>
                        <p class="upload-hint">SVG, PNG, JPG ou GIF (max. 800x400px)</p>
                        
                        <?php echo $this->Form->input('foto', array(
                            'type' => 'file',
                            'id' => 'file-input',
                            'accept' => 'image/jpeg,image/jpg,image/png,image/gif,image/svg+xml'
                        )); ?>
                    </div>
                    
                    <!-- Preview da nova foto -->
                    <div id="foto-preview" class="foto-preview" style="display: none; margin-top: 16px;">
                        <img id="foto-preview-img" src="" alt="Preview">
                    </div>
                </div>
            </div>
            
            <!-- Telefone -->
            <div class="form-row full">
                <div class="form-group">
                    <label>Telefone</label>
                    <?php echo $this->Form->input('telefone', array(
                        'type' => 'text',
                        'id' => 'telefone',
                        'required' => true
                    )); ?>
                </div>
            </div>
        </div>
        
        <!-- Seção: Serviços -->
        <div class="form-section">
            <h2 class="form-section-title">Quais serviço você vai prestar?</h2>

            <!-- Select + botão vermelho -->
            <div class="servicos-select-row">
                <div class="servicos-select-wrapper">
                    <div class="servicos-select" id="servicos-select">
                        <span id="servicos-select-placeholder" class="servicos-select-placeholder">Selecione o serviço</span>
                        <span id="servicos-select-tags" class="servicos-select-tags"></span>
                        <span class="servicos-select-caret">&#9662;</span>
                    </div>

                    <div class="servicos-dropdown" id="servicos-dropdown">
                        <input type="text" id="servicos-search" class="servicos-dropdown-search" placeholder="Buscar serviço...">
                        <div class="servicos-dropdown-list">
                            <?php foreach ($servicos as $id => $nome): 
                                $checked = isset($servicosVinculados[$id]);
                            ?>
                                <label class="servicos-dropdown-item" data-nome="<?php echo h(mb_strtolower($nome, 'UTF-8')); ?>">
                                    <input type="checkbox" class="servico-option" data-id="<?php echo $id; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                    <span><?php echo h($nome); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-servico-inline" onclick="abrirModalServico()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Cadastrar serviço
                </button>
            </div>
            
            <!-- Lista de serviços selecionados -->
            <div class="servicos-grid" id="servicos-selecionados">
                <?php foreach ($servicos as $id => $nome): 
                    $checked = isset($servicosVinculados[$id]);
                    $valor = $checked ? $servicosVinculados[$id]['valor'] : '';
                    $display = $checked ? '' : 'display:none;';
                ?>
                    <div class="servico-item" id="servico-item-<?php echo $id; ?>" data-servico-id="<?php echo $id; ?>" style="<?php echo $display; ?>">
                        <span class="servico-nome"><?php echo h($nome); ?></span>
                        
                        <div class="servico-valor-input">
                            <input type="text" 
                                   name="data[Servicos][<?php echo $id; ?>][valor]" 
                                   id="valor_<?php echo $id; ?>"
                                   placeholder="R$ 0,00"
                                   class="valor-input"
                                   value="<?php echo $valor !== '' ? 'R$ ' . number_format($valor, 2, ',', '.') : ''; ?>">
                        </div>

                        <input type="hidden" 
                               name="data[Servicos][<?php echo $id; ?>][checked]" 
                               id="checked_<?php echo $id; ?>" 
                               value="<?php echo $checked ? '1' : ''; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Botões do Formulário -->
        <div class="form-actions">
            <?php echo $this->Html->link('Cancelar', 
                array('action' => 'index'), 
                array('class' => 'btn-cancel')
            ); ?>
            
            <button type="submit" class="btn-submit">Salvar</button>
        </div>
        
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<!-- Modal Cadastrar Serviço (igual ao add.ctp) -->
<div id="modal-servico" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cadastre um serviço</h3>
        </div>
        
        <div class="form-group" style="margin-bottom: 16px;">
            <label>Nome do Serviço</label>
            <input type="text" id="novo-servico-nome" placeholder="Planejamento e Arquitetura">
        </div>
        
        <div class="form-group">
            <label>Descrição</label>
            <input type="text" id="novo-servico-descricao" placeholder="Adicione uma descrição">
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="fecharModalServico()">Cancelar</button>
            <button type="button" class="btn-submit" onclick="salvarNovoServico()">Cadastrar</button>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    /* Preview da nova foto */
    $('#file-input').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#foto-preview-img').attr('src', ev.target.result);
                $('#foto-preview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    /* Máscara de telefone */
    $('#telefone').on('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (value.length > 0) {
            value = value.replace(/^(\d*)/, '($1');
        }
        
        e.target.value = value;
    });

    /* Multi-select de serviços */
    const $dropdown = $('#servicos-dropdown');
    const $trigger  = $('#servicos-select');
    const $search   = $('#servicos-search');

    function ajustarAlturaDropdown() {
        const dd = $dropdown[0];
        if (!dd) return;

        // Altura "ideal" da lista (cabe ~5–6 serviços)
        const ALTURA_BASE = 220;
        const MARGEM_TELA = 80; // margem de respiro em relação ao viewport

        // Altura baseada APENAS na altura da tela, não na posição do select
        const alturaMaxTela = window.innerHeight - MARGEM_TELA;
        const altura = Math.max(140, Math.min(ALTURA_BASE, alturaMaxTela));

        // Aplica a altura calculada SEM depender da posição
        $dropdown.find('.servicos-dropdown-list').css('max-height', altura + 'px');

        // --- Scroll da página pra garantir que o dropdown apareça inteiro ---
        const rect = dd.getBoundingClientRect();
        const dropdownBottom = window.scrollY + rect.top + altura + 24; // folga de 24px
        const alvoScroll = Math.max(0, dropdownBottom - window.innerHeight);

        jQuery('html, body').stop().animate(
            { scrollTop: alvoScroll },
            200
        );
    }




    function syncRow(id, checked) {
        const $row = $('#servico-item-' + id);
        const $checkedInput = $('#checked_' + id);

        if (checked) {
            $row.show();
            $checkedInput.val('1');
        } else {
            $row.hide();
            $row.find('.valor-input').val('');
            $checkedInput.val('');
        }
    }

    function updateTags() {
        const $options = $('.servico-option:checked');
        const count = $options.length;
        const $tags = $('#servicos-select-tags');
        const $placeholder = $('#servicos-select-placeholder');

        if (count === 0) {
            $trigger.removeClass('has-selection');
            $tags.text('');
            $placeholder.show();
        } else {
            $trigger.addClass('has-selection');
            $placeholder.hide();
            if (count === 1) {
                $tags.text(
                    $options.first().closest('.servicos-dropdown-item').find('span').text()
                );
            } else {
                $tags.text(count + ' serviços selecionados');
            }
        }
    }

    function filterList(term) {
        term = term.toLowerCase();
        $('.servicos-dropdown-item').each(function() {
            const nome = $(this).data('nome');
            $(this).toggle(nome.indexOf(term) !== -1);
        });
    }

    $trigger.on('click', function(e) {
        e.stopPropagation();
        $dropdown.toggleClass('open');
        if ($dropdown.hasClass('open')) {
            $search.val('');
            filterList('');
            $search.focus();

            // ajusta a altura de acordo com o espaço da tela
            setTimeout(ajustarAlturaDropdown, 0);
        }
    });

    $(document).on('click', function() {
        $dropdown.removeClass('open');
    });

    $dropdown.on('click', function(e) {
        e.stopPropagation();
    });

    $('.servico-option').on('change', function() {
        const id = $(this).data('id');
        syncRow(id, $(this).is(':checked'));
        updateTags();
    });

    $search.on('input', function() {
        filterList($(this).val());
    });

    // Inicializar estado com o que veio do backend
    $('.servico-item').each(function() {
        const id = $(this).data('servico-id');
        const visible = $(this).is(':visible');
        $('.servico-option[data-id="' + id + '"]').prop('checked', visible);
    });
    updateTags();

    /* Máscara simples de valor no edit (mantém R$ XX,YY) */
    $('.valor-input').each(function() {
        $(this).on('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                e.target.value = 'R$ ' + value;
            }
        });

        $(this).on('blur', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                e.target.value = value; // número puro para o backend
            }
        });

        $(this).on('focus', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                e.target.value = 'R$ ' + value;
            }
        });
    });

    /* Modal novo serviço */
    window.abrirModalServico = function() {
        $('#modal-servico').css('display', 'flex');
    };

    window.fecharModalServico = function() {
        $('#modal-servico').hide();
        $('#novo-servico-nome').val('');
        $('#novo-servico-descricao').val('');
    };

    window.salvarNovoServico = function() {
        const nome = $('#novo-servico-nome').val();
        const descricao = $('#novo-servico-descricao').val();

        if (!nome) {
            alert('Por favor, preencha o nome do serviço.');
            return;
        }

        alert('Funcionalidade de cadastro de serviço será implementada via AJAX.');
        window.fecharModalServico();
    };

    $('#modal-servico').on('click', function(e) {
        if (e.target === this) {
            window.fecharModalServico();
        }
    });
});
</script>
