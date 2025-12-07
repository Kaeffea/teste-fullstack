<!-- importar.ctp -->
<?php $this->assign('title', 'Importar Prestadores'); ?>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Importar Prestadores</h1>
        <p class="page-subtitle">Faça upload de um arquivo CSV com os dados dos prestadores</p>
    </div>
    
    <!-- Formulário -->
    <div class="form-container">
        <div class="form-section">
            <h2 class="form-section-title">Faça o upload da sua lista de servidores</h2>
            <p class="form-section-subtitle">O arquivo deve estar no formato CSV com as seguintes colunas: nome, sobrenome, email, telefone, servicos, valores</p>
            
            <!-- Área de Upload -->
            <?php echo $this->Form->create('Prestador', array(
                'url' => array('controller' => 'prestadores', 'action' => 'processar_importacao'),
                'type' => 'file',
                'id' => 'form-importacao'
            )); ?>
            
            <div class="upload-area" id="upload-area" onclick="document.getElementById('csv-input').click()">
                <div class="upload-icon">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <p class="upload-text">
                    <strong>Clique para enviar</strong> ou arraste e solte
                </p>
                <p class="upload-hint">CSV (máx. 25 MB)</p>
                
                <?php echo $this->Form->input('arquivo', array(
                    'type' => 'file',
                    'id' => 'csv-input',
                    'accept' => '.csv',
                    'label' => false,
                    'div' => false
                )); ?>
            </div>
            
            <!-- Preview do arquivo -->
            <div id="file-info" style="display: none; margin-top: 20px;">
                <div style="display: flex; align-items: center; padding: 16px; background: #F9FAFB; border-radius: 8px; border: 1px solid #EAECF0;">
                    <svg width="40" height="40" fill="none" stroke="#EF4444" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div style="flex: 1; margin-left: 12px;">
                        <div id="file-name" style="font-weight: 500; color: #101828;"></div>
                        <div id="file-size" style="font-size: 14px; color: #667085;"></div>
                    </div>
                    <button type="button" onclick="removerArquivo()" style="background: transparent; border: none; cursor: pointer; color: #EF4444;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Barra de progresso -->
                <div id="progress-bar" style="display: none; margin-top: 12px;">
                    <div style="background: #EAECF0; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div id="progress-fill" style="background: #EF4444; height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <div style="margin-top: 8px; text-align: center; font-size: 14px; color: #667085;">
                        <span id="progress-text">0%</span>
                    </div>
                </div>
            </div>
            
            <!-- Exemplo de CSV -->
            <div style="margin-top: 24px; padding: 20px; background: #F9FAFB; border-radius: 8px; border: 1px solid #EAECF0;">
                <h3 style="font-size: 14px; font-weight: 600; color: #101828; margin-bottom: 12px;">📄 Exemplo de formato CSV:</h3>
                <pre style="background: #ffffff; padding: 12px; border-radius: 6px; font-size: 12px; overflow-x: auto; color: #344054;">nome;sobrenome;email;telefone;servicos;valores
João;Silva;joao@email.com;(82) 99604-9202;Pintura|Elétrica;200.00|150.00
Maria;Santos;maria@email.com;(82) 99604-9203;Hidráulica;180.00</pre>
                
                <p style="margin-top: 12px; font-size: 13px; color: #667085;">
                    <strong>Observações:</strong><br>
                    • Separador: ponto e vírgula (;)<br>
                    • Para múltiplos serviços, use pipe (|) como separador<br>
                    • Valores monetários podem ter pontos ou vírgulas<br>
                    • Primeira linha deve conter os cabeçalhos
                </p>
            </div>
            
            <!-- Botões -->
            <div class="form-actions">
                <?php echo $this->Html->link('Cancelar', 
                    array('action' => 'index'), 
                    array('class' => 'btn-cancel')
                ); ?>
                
                <button type="button" id="btn-importar" class="btn-submit" onclick="iniciarImportacao()" disabled>
                    Adicionar
                </button>
            </div>
            
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<!-- Modal de Sucesso -->
<div id="modal-sucesso" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div style="text-align: center;">
            <div style="width: 56px; height: 56px; margin: 0 auto 20px; background: #ECFDF5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" fill="none" stroke="#047857" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h3 class="modal-title">Lista enviada com sucesso!</h3>
            <p class="modal-subtitle">Confira seus servidores na tabela abaixo</p>
            
            <div style="margin-top: 24px;">
                <button type="button" class="btn-submit" onclick="window.location.href='<?php echo $this->Html->url(array('action' => 'index')); ?>'">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Upload de arquivo
document.getElementById('csv-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Mostrar info do arquivo
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = formatarTamanho(file.size);
        document.getElementById('file-info').style.display = 'block';
        document.getElementById('btn-importar').disabled = false;
    }
});

// Drag and Drop
const uploadArea = document.getElementById('upload-area');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#7F56D9';
    uploadArea.style.background = '#F4F3FF';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#D0D5DD';
    uploadArea.style.background = '#F9FAFB';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#D0D5DD';
    uploadArea.style.background = '#F9FAFB';
    
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.csv')) {
        document.getElementById('csv-input').files = e.dataTransfer.files;
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = formatarTamanho(file.size);
        document.getElementById('file-info').style.display = 'block';
        document.getElementById('btn-importar').disabled = false;
    } else {
        alert('Por favor, envie apenas arquivos CSV.');
    }
});

// Remover arquivo
function removerArquivo() {
    document.getElementById('csv-input').value = '';
    document.getElementById('file-info').style.display = 'none';
    document.getElementById('btn-importar').disabled = true;
}

// Iniciar importação com animação
function iniciarImportacao() {
    document.getElementById('progress-bar').style.display = 'block';
    document.getElementById('btn-importar').disabled = true;
    
    // Simular progresso
    let progress = 0;
    const interval = setInterval(function() {
        progress += 10;
        document.getElementById('progress-fill').style.width = progress + '%';
        document.getElementById('progress-text').textContent = progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            // Submeter formulário
            document.getElementById('form-importacao').submit();
        }
    }, 100);
}

// Formatar tamanho do arquivo
function formatarTamanho(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Mostrar modal de sucesso se houver mensagem
<?php if ($this->Session->check('Message.flash')): ?>
    setTimeout(function() {
        const flash = document.querySelector('.alert-success');
        if (flash) {
            document.getElementById('modal-sucesso').style.display = 'flex';
        }
    }, 500);
<?php endif; ?>
</script>