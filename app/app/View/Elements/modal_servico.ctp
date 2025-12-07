<!-- Modal Cadastrar Serviço (Componente Reutilizável) -->
<div id="modal-servico" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cadastre um serviço</h3>
        </div>
        
        <div id="modal-servico-alert" style="display: none; margin-bottom: 16px; padding: 12px; border-radius: 8px;"></div>
        
        <div class="form-group" style="margin-bottom: 16px;">
            <label>Nome do Serviço</label>
            <input type="text" id="novo-servico-nome" placeholder="Planejamento e Arquitetura" style="width: 100%; padding: 10px 14px; border: 1px solid #D0D5DD; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div class="form-group">
            <label>Descrição</label>
            <textarea id="novo-servico-descricao" placeholder="Adicione uma descrição" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid #D0D5DD; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="fecharModalServico()">Cancelar</button>
            <button type="button" class="btn-submit" id="btn-salvar-servico">
                <span id="btn-salvar-text">Cadastrar</span>
                <span id="btn-salvar-loading" style="display: none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#modal-servico-alert.success {
    background: #ECFDF5;
    color: #047857;
    border: 1px solid #A7F3D0;
}

#modal-servico-alert.error {
    background: #FEF2F2;
    color: #DC2626;
    border: 1px solid #FECACA;
}
</style>

<script>
jQuery(function($) {
    window.abrirModalServico = function() {
        $('#modal-servico').css('display', 'flex');
        $('#novo-servico-nome').focus();
    };

    window.fecharModalServico = function() {
        $('#modal-servico').hide();
        $('#novo-servico-nome').val('');
        $('#novo-servico-descricao').val('');
        $('#modal-servico-alert').hide();
    };

    window.salvarNovoServicoAjax = function(callback) {
        const nome = $('#novo-servico-nome').val().trim();
        const descricao = $('#novo-servico-descricao').val().trim();
        
        // Validação
        if (!nome) {
            mostrarAlerta('Por favor, preencha o nome do serviço.', 'error');
            return;
        }
        
        // Desabilitar botão e mostrar loading
        const $btn = $('#btn-salvar-servico');
        $btn.prop('disabled', true);
        $('#btn-salvar-text').hide();
        $('#btn-salvar-loading').show();
        
        // Fazer requisição AJAX
        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "prestadores", "action" => "cadastrar_servico")); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                nome: nome,
                descricao: descricao
            },
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    
                    // Aguardar 1s e fechar modal
                    setTimeout(function() {
                        fecharModalServico();
                        
                        // Callback para atualizar a lista
                        if (typeof callback === 'function') {
                            callback(response.servico);
                        }
                    }, 1000);
                } else {
                    mostrarAlerta(response.message, 'error');
                    $btn.prop('disabled', false);
                    $('#btn-salvar-text').show();
                    $('#btn-salvar-loading').hide();
                }
            },
            error: function() {
                mostrarAlerta('Erro ao cadastrar serviço. Tente novamente.', 'error');
                $btn.prop('disabled', false);
                $('#btn-salvar-text').show();
                $('#btn-salvar-loading').hide();
            }
        });
    };
    
    function mostrarAlerta(mensagem, tipo) {
        const $alert = $('#modal-servico-alert');
        $alert.removeClass('success error').addClass(tipo);
        $alert.text(mensagem);
        $alert.show();
    }
    
    // Clicar no botão Cadastrar
    $('#btn-salvar-servico').on('click', function() {
        salvarNovoServicoAjax(window.onNovoServicoCadastrado);
    });
    
    // Enter no campo nome
    $('#novo-servico-nome, #novo-servico-descricao').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            salvarNovoServicoAjax(window.onNovoServicoCadastrado);
        }
    });
    
    // Fechar ao clicar fora
    $('#modal-servico').on('click', function(e) {
        if (e.target === this) {
            fecharModalServico();
        }
    });
});
</script>