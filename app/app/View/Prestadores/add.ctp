<!-- add.ctp -->
<?php $this->assign('title', 'Cadastro de Prestador'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Cadastro de Prestador de Serviço</h1>
    </div>
    
    <!-- Formulário -->
    <div class="form-container">
        <?php echo $this->Form->create('Prestador', array(
            'url' => array('controller' => 'prestadores', 'action' => 'add'),
            'type' => 'file',
            'inputDefaults' => array(
                'label' => false,
                'div' => false
            )
        )); ?>
                
        <!-- Seção: Informações Pessoais -->
        <div class="form-section">
            <h2 class="form-section-title">Informações pessoais</h2>
            <p class="form-section-subtitle">Cadastre suas informações e adicione uma foto.</p>
            
            <!-- Nome (2 colunas) -->
            <div class="form-row">
                <div class="form-group">
                    <label>Nome</label>
                    <?php echo $this->Form->input('nome', array(
                        'type' => 'text',
                        'placeholder' => 'Eduardo',
                        'required' => true
                    )); ?>
                </div>
                
                <div class="form-group">
                    <label>Sobrenome</label>
                    <?php echo $this->Form->input('sobrenome', array(
                        'type' => 'text',
                        'placeholder' => 'Oliveira',
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
                        'placeholder' => 'eduardo@edcity.com.br',
                        'required' => true
                    )); ?>
                </div>
            </div>
            
            <!-- Foto -->
            <div class="form-row full">
                <div class="form-group">
                    <label>Sua foto</label>
                    <p style="font-size: 13px; color: #667085; margin-bottom: 8px;">Ela aparecerá no seu perfil.</p>
                    
                    <div class="upload-area" onclick="document.getElementById('file-input').click()">
                        <div class="upload-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <p class="upload-text">
                            <strong>Clique para enviar</strong> ou arraste e solte
                        </p>
                        <p class="upload-hint">SVG, PNG, JPG ou GIF (max. 800x400px)</p>
                        
                        <?php echo $this->Form->input('foto', array(
                            'type' => 'file',
                            'id' => 'file-input',
                            'accept' => 'image/jpeg,image/jpg,image/png,image/gif,image/svg+xml'
                        )); ?>
                    </div>
                    
                    <!-- Preview da foto -->
                    <div id="foto-preview" class="foto-preview" style="display: none;">
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
                        'placeholder' => '(__)____-____',
                        'id' => 'telefone',
                        'required' => true
                    )); ?>
                </div>
            </div>
        </div>
        
        <!-- Seção: Serviços -->
        <div class="form-section">
            <h2 class="form-section-title">Quais serviço você vai prestar?</h2>

            <!-- Select + botão vermelho, como no Figma -->
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
                            <?php foreach ($servicos as $id => $nome): ?>
                                <div class="servicos-dropdown-item"
                                    data-servico-id="<?php echo $id; ?>"
                                    data-nome="<?php echo h(mb_strtolower($nome, 'UTF-8')); ?>">

                                    <label class="servicos-dropdown-label">
                                        <input type="checkbox"
                                            class="servico-option"
                                            data-id="<?php echo $id; ?>">
                                        <span class="servicos-dropdown-name">
                                            <?php echo h($nome); ?>
                                        </span>
                                    </label>
                                    <button type="button"
                                        class="servico-more-btn"
                                        onclick="abrirModalServicoAcoesDoBotao(<?php echo (int)$id; ?>, '<?php echo h(addslashes($nome)); ?>')">
                                    <svg class="icon-more" viewBox="0 0 20 20" fill="none">
                                        <circle cx="4" cy="10" r="1.5"></circle>
                                        <circle cx="10" cy="10" r="1.5"></circle>
                                        <circle cx="16" cy="10" r="1.5"></circle>
                                    </svg>
                                </button>
                                </div>
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
            
            <!-- Lista de serviços selecionados (cada um com seu preço) -->
            <div class="servicos-grid" id="servicos-selecionados">
                <?php foreach ($servicos as $id => $nome): ?>
                    <div class="servico-item" id="servico-item-<?php echo $id; ?>" data-servico-id="<?php echo $id; ?>" style="display:none;">
                        <span class="servico-nome"><?php echo h($nome); ?></span>
                        
                        <div class="servico-valor-input">
                            <input type="text" 
                                   name="data[Servicos][<?php echo $id; ?>][valor]" 
                                   id="valor_<?php echo $id; ?>"
                                   placeholder="R$ 0,00"
                                   class="valor-input">
                        </div>

                        <input type="hidden" 
                               name="data[Servicos][<?php echo $id; ?>][checked]" 
                               id="checked_<?php echo $id; ?>" 
                               value="">
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

<!-- Modal Cadastrar Serviço -->
<?php echo $this->element('modal_servico'); ?>
<?php echo $this->element('modal_servico_acoes'); ?>

<script>
jQuery(function($) {
    /* -------- Preview da foto -------- */
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

    /* -------- Máscara de telefone -------- */
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

    /* -------- Multi-select de serviços -------- */
    const $dropdown = $('#servicos-dropdown');
    const $trigger  = $('#servicos-select');
    const $search   = $('#servicos-search');

    function ajustarAlturaDropdown() {
        const dd = $dropdown[0];
        if (!dd) return;

        const ALTURA_BASE = 220;
        const MARGEM_TELA = 80;
        const alturaMaxTela = window.innerHeight - MARGEM_TELA;
        const altura = Math.max(140, Math.min(ALTURA_BASE, alturaMaxTela));

        $dropdown.find('.servicos-dropdown-list').css('max-height', altura + 'px');

        const rect = dd.getBoundingClientRect();
        const dropdownBottom = window.scrollY + rect.top + altura + 24;
        const alvoScroll = Math.max(0, dropdownBottom - window.innerHeight);

        jQuery('html, body').stop().animate({ scrollTop: alvoScroll }, 200);
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
                $tags.text($options.first().closest('.servicos-dropdown-item').find('.servicos-dropdown-name').text());
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

    updateTags();

    /* -------- Máscara de valor -------- */
    function aplicarMascaraValor($input) {
        $input.on('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                e.target.value = 'R$ ' + value;
            }
        });

        $input.on('blur', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                e.target.value = value;
            }
        });

        $input.on('focus', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = (parseFloat(value) / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                e.target.value = 'R$ ' + value;
            }
        });
    }

    $('.valor-input').each(function() {
        aplicarMascaraValor($(this));
    });

    /* -------- Callback quando novo serviço é cadastrado -------- */
    window.onNovoServicoCadastrado = function(servico) {
        console.log('Novo serviço cadastrado:', servico);

        const $lista = $('.servicos-dropdown-list');
        const $item = $('<div>', {
            'class': 'servicos-dropdown-item',
            'data-servico-id': servico.id,
            'data-nome': servico.nome.toLowerCase()
        });

        const $label = $('<label>', { 'class': 'servicos-dropdown-label' });
        const $checkbox = $('<input>', {
            type: 'checkbox',
            'class': 'servico-option',
            'data-id': servico.id
        });
        const $nameSpan = $('<span>', { 'class': 'servicos-dropdown-name' }).text(servico.nome);

        $label.append($checkbox).append($nameSpan);

        const $moreBtn = $('<button>', {
            type: 'button',
            'class': 'servico-more-btn',
            click: function() {
                abrirModalServicoAcoesDoBotao(servico.id, servico.nome);
            }
        }).html('<svg class="icon-more" viewBox="0 0 20 20" fill="none"><circle cx="4" cy="10" r="1.5"></circle><circle cx="10" cy="10" r="1.5"></circle><circle cx="16" cy="10" r="1.5"></circle></svg>');

        $item.append($label).append($moreBtn);
        $lista.append($item);

        const $novoItemGrid = $('<div>', {
            'class': 'servico-item',
            id: 'servico-item-' + servico.id,
            'data-servico-id': servico.id,
            style: 'display:none;'
        }).append(
            $('<span>', { 'class': 'servico-nome' }).text(servico.nome),
            $('<div>', { 'class': 'servico-valor-input' }).append(
                $('<input>', {
                    type: 'text',
                    name: 'data[Servicos][' + servico.id + '][valor]',
                    id: 'valor_' + servico.id,
                    placeholder: 'R$ 0,00',
                    'class': 'valor-input'
                })
            ),
            $('<input>', {
                type: 'hidden',
                name: 'data[Servicos][' + servico.id + '][checked]',
                id: 'checked_' + servico.id,
                value: ''
            })
        );

        $('#servicos-selecionados').append($novoItemGrid);

        aplicarMascaraValor($novoItemGrid.find('.valor-input'));

        $checkbox.on('change', function() {
            const id = $(this).data('id');
            syncRow(id, $(this).is(':checked'));
            updateTags();
        });

        alert('Serviço "' + servico.nome + '" cadastrado com sucesso! Agora você pode selecioná-lo.');
    };

    /* -------- Modal de ações de serviço -------- */
    window.abrirModalServicoAcoes = function() {
        $('#modal-servico-acoes').css('display', 'flex').attr('data-open', '1');
    };

    window.abrirModalServicoAcoesDoBotao = function(id, nome) {
        console.log('[Modal Ações] ID:', id, 'Nome:', nome);

        if (!id) {
            alert('Serviço inválido.');
            return;
        }

        $('#modal-servico-acoes-titulo').text(nome || 'Serviço');
        $('#modal-servico-acoes').data('servico-id', id);
        abrirModalServicoAcoes();

        $('#servico-acoes-nome').val('');
        $('#servico-acoes-descricao').val('Carregando...');

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "detalhes_servico")); ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (!resp || !resp.success) {
                    fecharModalServicoAcoes();
                    alert(resp && resp.message ? resp.message : 'Erro ao carregar serviço.');
                    return;
                }

                $('#servico-acoes-nome').val(resp.servico.nome || '');
                $('#servico-acoes-descricao').val(resp.servico.descricao || '');
            },
            error: function() {
                fecharModalServicoAcoes();
                alert('Erro ao carregar os dados do serviço.');
            }
        });
    };

    window.fecharModalServicoAcoes = function() {
        $('#modal-servico-acoes')
            .css('display', 'none')
            .attr('data-open', '0')
            .removeData('servico-id');
        $('#servico-acoes-nome').val('');
        $('#servico-acoes-descricao').val('');
    };

    /* -------- Salvar alterações do serviço -------- */
    $('#btn-salvar-servico-edicao').on('click', function() {
        const id = $('#modal-servico-acoes').data('servico-id');
        const nome = $('#servico-acoes-nome').val().trim();
        const desc = $('#servico-acoes-descricao').val().trim();

        if (!id) {
            alert('Serviço inválido.');
            return;
        }
        if (!nome) {
            alert('O nome do serviço é obrigatório.');
            return;
        }

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "atualizar_servico")); ?>',
            type: 'POST',
            dataType: 'json',
            data: { id: id, nome: nome, descricao: desc },
            success: function(resp) {
                if (!resp.success) {
                    alert(resp.message || 'Erro ao salvar o serviço.');
                    return;
                }

                // Atualiza nome em todos os lugares
                $('.servicos-dropdown-item[data-servico-id="' + id + '"] .servicos-dropdown-name').text(nome);
                $('#servico-item-' + id + ' .servico-nome').text(nome);

                alert('Serviço atualizado com sucesso!');
                fecharModalServicoAcoes();
            },
            error: function() {
                alert('Erro ao salvar o serviço.');
            }
        });
    });

    /* -------- Excluir serviço -------- */
    $('#btn-excluir-servico').on('click', function() {
        const id = $('#modal-servico-acoes').data('servico-id');

        if (!id) {
            alert('Serviço inválido.');
            return;
        }

        if (!confirm('Tem certeza que deseja excluir este serviço? Essa ação não poderá ser desfeita.')) {
            return;
        }

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "excluir_servico")); ?>/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(resp) {
                if (!resp.success) {
                    alert(resp.message || 'Erro ao excluir o serviço.');
                    return;
                }

                $('.servicos-dropdown-item[data-servico-id="' + id + '"]').remove();
                $('#servico-item-' + id).remove();
                updateTags();

                alert('Serviço excluído com sucesso!');
                fecharModalServicoAcoes();
            },
            error: function() {
                alert('Erro ao excluir o serviço.');
            }
        });
    });
});
</script>
