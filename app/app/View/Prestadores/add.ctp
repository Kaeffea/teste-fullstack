<?php $this->assign('title', 'Cadastro de Prestador'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Cadastro de Prestador de Serviço</h1>
    </div>
    
    <!-- Formulário -->
    <div class="form-container">
        <?php echo $this->Form->create('Prestador', array(
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
            
            <div class="servicos-grid">
                <?php foreach ($servicos as $id => $nome): ?>
                    <div class="servico-item">
                        <input type="checkbox" 
                               name="data[Servicos][<?php echo $id; ?>][checked]" 
                               id="servico_<?php echo $id; ?>"
                               value="1"
                               onchange="toggleValorInput(<?php echo $id; ?>)">
                        
                        <label for="servico_<?php echo $id; ?>" class="servico-nome">
                            <?php echo h($nome); ?>
                        </label>
                        
                        <div class="servico-valor-input">
                            <input type="text" 
                                   name="data[Servicos][<?php echo $id; ?>][valor]" 
                                   id="valor_<?php echo $id; ?>"
                                   placeholder="R$ 0,00"
                                   class="valor-input"
                                   disabled>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Botão Cadastrar Serviço -->
            <button type="button" class="btn-add-servico" onclick="abrirModalServico()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Cadastrar serviço
            </button>
        </div>
        
        <!-- Seção: Valor do Serviço (removida, agora é por serviço) -->
        
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
// Preview da foto
document.getElementById('file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('foto-preview-img').src = e.target.result;
            document.getElementById('foto-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Máscara de telefone
document.getElementById('telefone').addEventListener('input', function(e) {
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

// Habilitar/desabilitar input de valor
function toggleValorInput(id) {
    const checkbox = document.getElementById('servico_' + id);
    const valorInput = document.getElementById('valor_' + id);
    valorInput.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        valorInput.value = '';
    }
}

// Máscara de valor (R$)
document.querySelectorAll('.valor-input').forEach(function(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        e.target.value = 'R$ ' + value;
    });
});

// Modal de novo serviço
function abrirModalServico() {
    document.getElementById('modal-servico').style.display = 'flex';
}

function fecharModalServico() {
    document.getElementById('modal-servico').style.display = 'none';
    document.getElementById('novo-servico-nome').value = '';
    document.getElementById('novo-servico-descricao').value = '';
}

function salvarNovoServico() {
    const nome = document.getElementById('novo-servico-nome').value;
    const descricao = document.getElementById('novo-servico-descricao').value;
    
    if (!nome) {
        alert('Por favor, preencha o nome do serviço.');
        return;
    }
    
    // Aqui você pode fazer uma requisição AJAX para salvar o serviço
    // Por enquanto, vamos apenas mostrar um alert
    alert('Funcionalidade de cadastro de serviço será implementada via AJAX.');
    fecharModalServico();
}

// Fechar modal ao clicar fora
document.getElementById('modal-servico').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalServico();
    }
});
</script>