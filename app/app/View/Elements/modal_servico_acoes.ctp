<div id="modal-servico-acoes" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-header-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="modal-header-text">
                <h3 id="modal-servico-acoes-titulo" class="modal-title">Editar Serviço</h3>
                <p class="modal-subtitle">Ver detalhes, editar ou excluir este serviço.</p>
            </div>
        </div>

        <div class="form-section">
            <div class="form-row full">
                <div class="form-group">
                    <label>Nome do serviço</label>
                    <input type="text" 
                           id="servico-acoes-nome" 
                           placeholder="Ex: Desenvolvimento Frontend">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="servico-acoes-descricao"
                              rows="4"
                              placeholder="Adicione uma descrição detalhada do serviço..."></textarea>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <!-- Botão Excluir à esquerda -->
            <button type="button"
                    id="btn-excluir-servico"
                    class="btn-icon btn-icon-danger"
                    title="Excluir serviço">
                <svg class="icon-delete" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>

            <!-- Botões Cancelar e Salvar à direita -->
            <div class="modal-actions-right">
                <button type="button"
                        class="btn-cancel"
                        onclick="window.fecharModalServicoAcoes()">
                    Cancelar
                </button>

                <button type="button"
                        id="btn-salvar-servico-edicao"
                        class="btn-submit">
                    Salvar alterações
                </button>
            </div>
        </div>
    </div>
</div>