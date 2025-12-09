<?php
    $this->assign('title', 'Novo Agendamento');
    $hoje = date('Y-m-d'); // usado como mínimo no date

    // Mapas para uso no JS
    $nomesServicos    = $servicos; // id => nome
    $nomesPrestadores = array();
    foreach ($prestadores as $id => $nomeCompleto) {
        $nomesPrestadores[$id] = $nomeCompleto;
    }
?>

<div class="main-container agendamento-page">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Novo Agendamento</h1>
        <p class="page-subtitle">
            Cadastre um agendamento com um ou vários serviços para o mesmo cliente.
        </p>
    </div>

    <div class="form-container agendamento-form-container">
        <?php echo $this->Form->create('Agendamento', array(
            'url' => array('controller' => 'agendamentos', 'action' => 'salvar')
        )); ?>

        <!-- DADOS DO CLIENTE -->
        <div class="form-section">
            <h2 class="form-section-title">Dados do cliente</h2>
            <p class="form-section-subtitle">
                Informe os dados básicos do cliente para contato.
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label for="cliente-nome">Nome do cliente *</label>
                    <?php echo $this->Form->input('Agendamento.cliente_nome', array(
                        'label'       => false,
                        'id'          => 'cliente-nome',
                        'required'    => true,
                        'placeholder' => 'Ex: Maria Silva'
                    )); ?>
                </div>
                <div class="form-group">
                    <label for="cliente-email">E-mail</label>
                    <?php echo $this->Form->input('Agendamento.cliente_email', array(
                        'label'       => false,
                        'id'          => 'cliente-email',
                        'placeholder' => 'Ex: maria@email.com'
                    )); ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cliente-telefone">Telefone</label>
                    <?php echo $this->Form->input('cliente_telefone', array(
                        'label'      => false,
                        'id'         => 'cliente-telefone',
                        'placeholder'=> '(82) 9 9999-9999'
                    )); ?>
                </div>
            </div>

        </div>

        <!-- SERVIÇOS DO AGENDAMENTO (LISTA DINÂMICA) -->
        <div class="form-section">
            <h2 class="form-section-title">Serviços do agendamento</h2>
            <p class="form-section-subtitle">
                Adicione um ou mais serviços. Cada serviço pode ter prestador, período e valor específicos.
            </p>

            <div id="lista-servicos-agendamento"></div>

            <div class="form-row full" style="margin-top: 16px;">
                <button type="button" class="btn-import" id="btn-adicionar-servico">
                    + Adicionar serviço
                </button>
            </div>
        </div>

        <!-- RESUMO / ORÇAMENTO -->
        <div class="form-section">
            <h2 class="form-section-title">Resumo e orçamento</h2>
            <p class="form-section-subtitle">
                Visão geral dos serviços deste agendamento e valor total previsto.
            </p>

            <div class="table-responsive">
                <table class="prestadores-table" id="tabela-resumo-servicos">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Prestador</th>
                            <th>Período</th>
                            <th style="text-align: right; padding-right: 24px;">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- preenchido via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; padding-right: 24px; font-weight: 600;">
                                Total
                            </td>
                            <td style="text-align: right; padding-right: 24px; font-weight: 600;" id="resumo-total">
                                R$ 0,00
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- MENSAGENS PARA WHATSAPP -->
        <div class="form-section">
            <h2 class="form-section-title">Textos para WhatsApp</h2>
            <p class="form-section-subtitle">
                Copie e cole os textos abaixo para enviar ao cliente e aos prestadores.
            </p>

            <div class="form-row full">
                <div class="form-group">
                    <label>Mensagem para o cliente</label>
                    <textarea id="msg-whats-cliente" rows="5" readonly
                        style="width:100%; resize: vertical;"></textarea>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Mensagens para os prestadores</label>
                    <div id="mensagens-prestadores">
                        <p style="font-size: 13px; color:#667085;">
                            Adicione pelo menos um serviço com prestador selecionado
                            para gerar as mensagens de WhatsApp.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="form-actions">
            <?php echo $this->Html->link(
                'Cancelar',
                array('controller' => 'agendamentos', 'action' => 'index'),
                array('class' => 'btn-cancel')
            ); ?>

            <button type="submit" class="btn-submit" id="btn-salvar-agendamento">
                Confirmar agendamento
            </button>
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
</div>

<style>
/* Header e container */
.agendamento-page .page-header {
    padding: 32px 24px 20px;
}
.agendamento-form-container {
    padding: 24px 24px 32px;
}

.btn-import,
.btn-dashboard,
.btn-add {
    text-decoration: none !important;
}

/* Card de serviço */
.servico-card {
    border: 1px solid #EAECF0;
    border-radius: 12px;
    padding: 16px 16px 12px;
    margin-bottom: 12px;
    background: #FFFFFF;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
}

.servico-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.servico-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #101828;
}

.servico-card-subtitle {
    font-size: 12px;
    color: #667085;
}

.servico-card-remove {
    border: none;
    background: transparent;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    width: 28px;
    height: 28px;
    color: #DC2626;
    transition: background 0.2s;
}

.servico-card-remove:hover {
    background: #FEF2F2;
}

/* Evita que os campos de serviço estourem pra fora do card */
.servico-card .form-row .form-group {
    min-width: 0;              /* permite o grid encolher o conteúdo */
}

/* Garante que os campos respeitam a largura do card */
.servico-card .form-group input,
.servico-card .form-group select {
    width: 100%;
    max-width: 100%;
}

/* Mobile: tudo em uma coluna dentro do card de serviço */
@media (max-width: 768px) {
    .servico-card .form-row {
        grid-template-columns: 1fr;
    }
}


/* Inputs de data e modo */
.input-date {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #D0D5DD;
    border-radius: 8px;
    font-size: 14px;
    color: #101828;
    outline: none;
    transition: all 0.2s;
}
.input-date:focus {
    border-color: #7F56D9;
    box-shadow: 0 0 0 4px rgba(127, 86, 217, 0.1);
}

/* Toggle do modo de prestador */
.modo-toggle-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.modo-toggle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid #D0D5DD;
    background: #FFFFFF;
    cursor: pointer;
    font-size: 13px;
    color: #344054;
    transition: all 0.2s;
}
.modo-toggle input {
    display: none;
}
.modo-toggle-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    border: 2px solid #D0D5DD;
    background: #FFFFFF;
    box-sizing: border-box;
}
.modo-toggle-active {
    border-color: #EF4444;
    background: #FEF2F2;
    color: #B91C1C;
}
.modo-toggle-active .modo-toggle-dot {
    border-color: #EF4444;
    background: #EF4444;
}

/* Cards de prestadores disponíveis */
.card-prestador {
    border: 1px solid #EAECF0;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 8px;
    background: #F9FAFB;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    justify-content: space-between;
    gap: 12px;
}
.card-prestador:hover {
    background: #F3F4F6;
    border-color: #D0D5DD;
}
.card-prestador.selected {
    border-color: #EF4444;
    background: #FEF2F2;
    box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.1);
}
.card-prestador-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.card-prestador-nome {
    font-size: 14px;
    font-weight: 600;
    color: #101828;
}
.card-prestador-contato {
    font-size: 12px;
    color: #667085;
}
.card-prestador-valor {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
}

/* linha de prestador + botão */
.prestador-row {
    align-items: flex-end;
    gap: 16px;
}

.form-group-botao-prestador {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.form-group-botao-prestador .btn-verificar-disponibilidade {
    width: 100%;
}


/* Mensagens de disponibilidade */
.msg-disponivel,
.msg-indisponivel {
    padding: 8px 10px;
    border-radius: 8px;
    font-size: 13px;
}
.msg-disponivel {
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    color: #047857;
}
.msg-indisponivel {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #DC2626;
}

/* Resumo */
#tabela-resumo-servicos tbody td {
    font-size: 13px;
}
#tabela-resumo-servicos tfoot td {
    font-size: 14px;
}

/* Textos WhatsApp */
#msg-whats-cliente,
#mensagens-prestadores textarea {
    font-family: inherit;
    font-size: 13px;
    border-radius: 8px;
    border: 1px solid #D0D5DD;
    padding: 10px 12px;
}

/* Hint */
.field-hint{
    margin-top: 4px;
    font-size: 12px;
    color: #667085;
    padding-left: 2px;
}

@media (max-width: 768px) {
    .agendamento-form-container {
        padding: 20px 16px 24px;
    }
}
</style>

<script>
jQuery(function($) {
    const HOJE = '<?php echo $hoje; ?>';
    const NOMES_SERVICOS    = <?php echo json_encode($nomesServicos); ?>;
    const NOMES_PRESTADORES = <?php echo json_encode($nomesPrestadores); ?>;
    const MAP_P_SERVICOS    = <?php echo json_encode($mapPrestadorServicos); ?>;

    let contadorServicos = 0;

    /** Utilidades **/
    function formatarDataBr(dataISO) {
        if (!dataISO) return '-';
        const partes = dataISO.split('-'); // yyyy-mm-dd
        if (partes.length !== 3) return dataISO;
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    function parseValor(str) {
        if (!str) return 0;
        str = ('' + str).trim();
        // converte "1.234,56" -> "1234.56"
        str = str.replace(/\./g, '').replace(',', '.');
        const v = parseFloat(str);
        return isNaN(v) ? 0 : v;
    }

    function formatarValorBR(num) {
        const v = parseFloat(num) || 0;
        return v.toFixed(2).replace('.', ',');
    }

    function renumerarServicos() {
    $('.servico-card').each(function(index) {
        $(this)
            .attr('data-index', index)
            .find('[data-role="servico-numero"]')
            .text('Serviço #' + (index + 1));
    });
    }

    /** Cria o HTML de um card de serviço **/
    function criarCardServico(index) {
        const idx = index;

        // Select de prestadores (todos) – filtramos via JS
        let optionsPrestadores = '<option value="">Selecione um prestador</option>';
        $.each(NOMES_PRESTADORES, function(id, nome) {
            optionsPrestadores += '<option value="' + id + '">' + nome + '</option>';
        });

        let optionsServicos = '<option value="">Selecione um serviço</option>';
        $.each(NOMES_SERVICOS, function(id, nome) {
            optionsServicos += '<option value="' + id + '">' + nome + '</option>';
        });

        const html =
        '<div class="servico-card" data-index="' + idx + '">' +
            '<div class="servico-card-header">' +
                '<div>' +
                    '<div class="servico-card-title" data-role="servico-numero">Serviço #' + (idx + 1) + '</div>' +
                    '<div class="servico-card-subtitle">Defina serviço, prestador, período e valor.</div>' +
                '</div>' +
                '<button type="button" class="servico-card-remove" title="Remover serviço">' +
                    '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" ' +
                              'd="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862A2 2 0 0 1 5.867 19.142L5 7m5 ' +
                              '4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />' +
                    '</svg>' +
                '</button>' +
            '</div>' +

            '<div class="form-row">' +
                '<div class="form-group">' +
                    '<label>Serviço *</label>' +
                    '<select class="input-servico" data-campo="servico_id">' +
                        optionsServicos +
                    '</select>' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Data de início *</label>' +
                    '<input type="date" class="input-date input-data-inicio" data-campo="data_inicio" min="' + HOJE + '">' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Duração (dias) *</label>' +
                    '<input type="number" class="input-duracao" data-campo="duracao_dias" min="1" value="1">' +
                '</div>' +
            '</div>' +

            '<div class="form-row">' +
                '<div class="form-group">' +
                    '<label>Modo de agendamento</label>' +
                    '<div class="modo-toggle-group" data-campo="modo_prestador">' +
                        '<label class="modo-toggle modo-toggle-active">' +
                            '<input type="radio" name="modo_' + idx + '" value="nao_escolhi" checked>' +
                            '<span class="modo-toggle-dot"></span>' +
                            '<span>Ainda não escolhi um prestador</span>' +
                        '</label>' +
                        '<label class="modo-toggle">' +
                            '<input type="radio" name="modo_' + idx + '" value="ja_escolhi">' +
                            '<span class="modo-toggle-dot"></span>' +
                            '<span>Já escolhi um prestador</span>' +
                        '</label>' +
                    '</div>' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Serviço exclusivo?</label>' +
                    '<div style="display:flex;align-items:center;gap:8px;margin-top:4px;">' +
                        '<input type="checkbox" class="input-exclusivo" data-campo="exclusivo">' +
                        '<span class="field-hint">Marcado: durante esse período o prestador só poderá realizar este serviço.</span>' +
                    '</div>' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Valor (R$)</label>' +
                    '<input type="text" class="input-valor" data-campo="valor" placeholder="Ex: 250,00">' +
                    '<small class="field-hint">Se vazio, usaremos o valor padrão do prestador/serviço.</small>' +
                '</div>' +
            '</div>' +

            // BLOCO: JÁ ESCOLHI
            '<div class="form-row prestador-row bloco-ja-escolhi" style="display:none; margin-top:8px;">' +
                '<div class="form-group">' +
                    '<label>Prestador *</label>' +
                    '<select class="input-prestador-select" data-campo="prestador_id">' +
                        optionsPrestadores +
                    '</select>' +
                    '<small class="field-hint">' +
                        'A lista mostra apenas prestadores que oferecem o serviço selecionado.' +
                    '</small>' +
                '</div>' +
                '<div class="form-group form-group-botao-prestador">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn-import btn-verificar-disponibilidade">' +
                        'Verificar disponibilidade' +
                    '</button>' +
                    '<div class="mensagem-disponibilidade" style="margin-top:8px;"></div>' +
                '</div>' +
            '</div>' +


            // BLOCO: AINDA NÃO ESCOLHI
            '<div class="form-row bloco-nao-escolhi" style="margin-top:8px;">' +
                '<div class="form-group" style="flex:1;">' +
                    '<button type="button" class="btn-import btn-buscar-prestadores">' +
                        'Buscar prestadores disponíveis' +
                    '</button>' +
                    '<input type="hidden" class="input-prestador-hidden" data-campo="prestador_id">' +
                    '<div class="lista-prestadores-disponiveis" style="margin-top:12px;"></div>' +
                '</div>' +
            '</div>' +

        '</div>';

        return $(html);
    }

    /** Atualiza estilo/toggle dos modos dentro de um card **/
    function atualizarModoCard($card) {
        const modo = $card.find('input[name="modo_' + $card.data('index') + '"]:checked').val();

        $card.find('.modo-toggle').removeClass('modo-toggle-active');
        $card.find('input[name="modo_' + $card.data('index') + '"]:checked')
             .closest('.modo-toggle').addClass('modo-toggle-active');

        if (modo === 'nao_escolhi') {
            $card.find('.bloco-nao-escolhi').show();
            $card.find('.bloco-ja-escolhi').hide();
            $card.find('.input-prestador-select').val('');
            $card.find('.mensagem-disponibilidade').empty();
        } else {
            $card.find('.bloco-nao-escolhi').hide();
            $card.find('.bloco-ja-escolhi').show();
            $card.find('.input-prestador-hidden').val('');
            $card.find('.lista-prestadores-disponiveis').empty();
        }
    }

    /** Filtra prestadores do select de um card com base no serviço **/
    function filtrarPrestadoresCardPorServico($card) {
        const servicoId = parseInt($card.find('.input-servico').val(), 10) || 0;
        const $select   = $card.find('.input-prestador-select');

        $select.find('option').each(function() {
            const val = $(this).val();
            if (!val) {
                $(this).prop('disabled', false).show();
                return;
            }
            const id        = parseInt(val, 10);
            const listaServ = MAP_P_SERVICOS[id] || [];

            if (!servicoId || listaServ.indexOf(servicoId) !== -1) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        $select.val('');
    }

    /** AJAX: buscar prestadores disponíveis (modo "não escolhi") **/
    function buscarPrestadoresDisponiveis($card) {
        const servicoId  = $card.find('.input-servico').val();
        const dataInicio = $card.find('.input-data-inicio').val();
        let   duracao    = parseInt($card.find('.input-duracao').val(), 10);
        const exclusivo  = $card.find('.input-exclusivo').is(':checked') ? 1 : 0;

        const $lista  = $card.find('.lista-prestadores-disponiveis');
        const $hidden = $card.find('.input-prestador-hidden');

        if (!servicoId) {
            alert('Selecione um serviço primeiro.');
            return;
        }
        if (!dataInicio) {
            alert('Informe a data de início.');
            return;
        }
        if (isNaN(duracao) || duracao <= 0) {
            duracao = 1;
            $card.find('.input-duracao').val(1);
        }

        $lista.html('<p style="font-size:13px;color:#667085;">Buscando prestadores disponíveis...</p>');
        $hidden.val('');

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "agendamentos", "action" => "prestadores_disponiveis")); ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                servico_id: servicoId,
                data:       dataInicio,
                duracao:    duracao,
                exclusivo:  exclusivo
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    $lista.html(
                        '<p style="font-size:13px;color:#DC2626;">Não foi possível buscar os prestadores disponíveis.</p>'
                    );
                    return;
                }

                if (!resp.total) {
                    $lista.html(
                        '<p style="font-size:13px;color:#667085;">Nenhum prestador disponível para esse serviço e período.</p>'
                    );
                    return;
                }

                let html = '';
                $.each(resp.prestadores, function(_, p) {
                    const valorNum = parseFloat(p.valor) || 0;
                    html +=
                        '<div class="card-prestador" data-id="' + p.id + '" data-valor="' + valorNum + '">' +
                            '<div class="card-prestador-info">' +
                                '<div class="card-prestador-nome">' + p.nome_completo + '</div>' +
                                '<div class="card-prestador-contato">' +
                                    (p.email ? p.email + ' · ' : '') +
                                    (p.telefone ? p.telefone : '') +
                                '</div>' +
                            '</div>' +
                            '<div class="card-prestador-valor">R$ ' + valorNum.toFixed(2).replace(".", ",") + '</div>' +
                        '</div>';
                });

                $lista.html(html);
            },
            error: function() {
                $lista.html(
                    '<p style="font-size:13px;color:#DC2626;">Erro ao buscar prestadores disponíveis.</p>'
                );
            }
        });
    }

    /** AJAX: verificar disponibilidade (modo "já escolhi") **/
    function verificarDisponibilidadeCard($card) {
        const prestadorId = $card.find('.input-prestador-select').val();
        const dataInicio  = $card.find('.input-data-inicio').val();
        let   duracao     = parseInt($card.find('.input-duracao').val(), 10);
        const exclusivo   = $card.find('.input-exclusivo').is(':checked') ? 1 : 0;

        const $msg = $card.find('.mensagem-disponibilidade');
        $msg.empty();

        if (!prestadorId) {
            alert('Selecione um prestador.');
            return;
        }
        if (!dataInicio) {
            alert('Informe a data de início.');
            return;
        }
        if (isNaN(duracao) || duracao <= 0) {
            duracao = 1;
            $card.find('.input-duracao').val(1);
        }

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "agendamentos", "action" => "disponibilidade_prestador")); ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                prestador_id: prestadorId,
                data:         dataInicio,
                duracao:      duracao,
                exclusivo:    exclusivo
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    $msg.html(
                        '<div class="msg-indisponivel">Não foi possível verificar a disponibilidade.</div>'
                    );
                    return;
                }

                if (resp.disponivel) {
                    $msg.html(
                        '<div class="msg-disponivel">' +
                            (resp.message || 'Prestador disponível para o período informado.') +
                        '</div>'
                    );
                } else {
                    $msg.html(
                        '<div class="msg-indisponivel">' +
                            (resp.message || 'Prestador indisponível para o período informado.') +
                        '</div>'
                    );
                }
            },
            error: function() {
                $msg.html(
                    '<div class="msg-indisponivel">Erro ao verificar disponibilidade do prestador.</div>'
                );
            }
        });
    }

    /** AJAX: valor padrão do prestador/serviço (modo "já escolhi") **/
    function atualizarValorPadraoCard($card) {
        const prestadorId = $card.find('.input-prestador-select').val();
        const servicoId   = $card.find('.input-servico').val();

        if (!prestadorId || !servicoId) return;

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "agendamentos", "action" => "valor_padrao")); ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                prestador_id: prestadorId,
                servico_id:   servicoId
            },
            success: function(resp) {
                if (resp && resp.success && resp.valor !== null) {
                    const v = parseFloat(resp.valor);
                    if (!isNaN(v)) {
                        $card.find('.input-valor').val(v.toFixed(2).replace('.', ','));
                        atualizarResumoETextos();
                    }
                }
            }
        });
    }

    /** Recalcula resumo + textos WhatsApp **/
    function atualizarResumoETextos() {
        const $tbodyResumo = $('#tabela-resumo-servicos tbody');
        $tbodyResumo.empty();

        let total = 0;
        const itens = [];
        const mapaPrestadores = {}; // prestadorId => { nome, itens[] }

        $('.servico-card').each(function() {
            const $card      = $(this);
            const servicoId  = $card.find('.input-servico').val();
            const dataInicio = $card.find('.input-data-inicio').val();
            const duracao    = parseInt($card.find('.input-duracao').val(), 10) || 1;
            const exclusivo  = $card.find('.input-exclusivo').is(':checked') ? 1 : 0;
            const modo       = $card.find('input[name="modo_' + $card.data('index') + '"]:checked').val();
            const valorStr   = $card.find('.input-valor').val();
            const valorNum   = parseValor(valorStr);
            let prestadorId  = null;

            if (!servicoId || !dataInicio) {
                return; // ignora cards incompletos no resumo
            }

            if (modo === 'nao_escolhi') {
                prestadorId = $card.find('.input-prestador-hidden').val() || null;
            } else {
                prestadorId = $card.find('.input-prestador-select').val() || null;
            }

            const servicoNome   = NOMES_SERVICOS[servicoId] || 'Serviço #' + servicoId;
            const prestadorNome = prestadorId ? (NOMES_PRESTADORES[prestadorId] || ('Prestador #' + prestadorId)) : '(a definir)';

            // Calcula data fim no front (apenas para exibir, o back calcula de novo)
            const d = new Date(dataInicio + 'T00:00:00');
            d.setDate(d.getDate() + (duracao - 1));
            const ano = d.getFullYear();
            const mes = String(d.getMonth() + 1).padStart(2, '0');
            const dia = String(d.getDate()).padStart(2, '0');
            const dataFimISO = ano + '-' + mes + '-' + dia;

            const periodoLabel = formatarDataBr(dataInicio) + ' até ' + formatarDataBr(dataFimISO);

            // Adiciona linha no resumo
            $('<tr>')
                .append($('<td>').text(servicoNome))
                .append($('<td>').text(prestadorNome))
                .append($('<td>').text(periodoLabel))
                .append($('<td style="text-align:right;padding-right:24px;">').text('R$ ' + formatarValorBR(valorNum)))
                .appendTo($tbodyResumo);

            total += valorNum;

            const item = {
                servicoNome:   servicoNome,
                prestadorId:   prestadorId,
                prestadorNome: prestadorNome,
                dataInicio:    dataInicio,
                dataFim:       dataFimISO,
                exclusivo:     exclusivo,
                valor:         valorNum
            };
            itens.push(item);

            if (prestadorId) {
                if (!mapaPrestadores[prestadorId]) {
                    mapaPrestadores[prestadorId] = {
                        nome: prestadorNome,
                        itens: []
                    };
                }
                mapaPrestadores[prestadorId].itens.push(item);
            }
        });

        $('#resumo-total').text('R$ ' + formatarValorBR(total));

        // Mensagem para o cliente
        const nomeCliente  = $('#cliente-nome').val() || 'cliente';
        const telefoneCli  = $('#cliente-telefone').val() || '';
        const obsCliente   = $('#cliente-observacoes').val() || '';
        let textoCliente   = '';

        if (!itens.length) {
            textoCliente = 'Preencha pelo menos um serviço para gerar o texto do WhatsApp.';
        } else {
            textoCliente += 'Olá ' + nomeCliente + ', tudo bem?\n';
            textoCliente += 'Segue o resumo do seu agendamento no Seu João:\n\n';

            itens.forEach(function(it, idx) {
                textoCliente += (idx + 1) + ') ' + it.servicoNome +
                    ' com ' + it.prestadorNome +
                    ', de ' + formatarDataBr(it.dataInicio) +
                    ' até ' + formatarDataBr(it.dataFim) +
                    (it.exclusivo ? ' (exclusivo)' : '') +
                    ' – R$ ' + formatarValorBR(it.valor) + '\n';
            });

            textoCliente += '\nTotal previsto: R$ ' + formatarValorBR(total) + '.\n';
            if (telefoneCli) {
                textoCliente += 'Telefone cadastrado: ' + telefoneCli + '.\n';
            }
            if (obsCliente) {
                textoCliente += 'Observações: ' + obsCliente + '\n';
            }
            textoCliente += '\nQualquer dúvida é só me chamar aqui 🙂';
        }
        $('#msg-whats-cliente').val(textoCliente);

        // Mensagens para prestadores
        const $boxPrestadores = $('#mensagens-prestadores');
        $boxPrestadores.empty();

        const nomeClienteForPrest = nomeCliente;

        if (!Object.keys(mapaPrestadores).length) {
            $boxPrestadores.html(
                '<p style="font-size:13px;color:#667085;">' +
                'Selecione pelo menos um prestador em algum serviço para gerar as mensagens.' +
                '</p>'
            );
        } else {
            $.each(mapaPrestadores, function(prestadorId, info) {
                const nomePrest = info.nome || ('Prestador #' + prestadorId);
                let texto = 'Olá ' + nomePrest + ', tudo bem?\n';
                texto += 'Segue(m) o(s) serviço(s) agendado(s) para você:\n\n';

                info.itens.forEach(function(it, idx) {
                    texto += (idx + 1) + ') ' + it.servicoNome +
                        ' para o cliente ' + nomeClienteForPrest +
                        ', de ' + formatarDataBr(it.dataInicio) +
                        ' até ' + formatarDataBr(it.dataFim) +
                        (it.exclusivo ? ' (exclusivo)' : '') +
                        ' – R$ ' + formatarValorBR(it.valor) + '\n';
                });

                texto += '\nQualquer ajuste, por favor me avise.';

                const bloco =
                    '<div style="margin-bottom:12px;">' +
                        '<label style="font-size:13px;font-weight:500;color:#344054;">' +
                            'Mensagem para ' + nomePrest +
                        '</label>' +
                        '<textarea rows="4" readonly ' +
                            'style="width:100%;margin-top:4px;resize:vertical;">' +
                            texto +
                        '</textarea>' +
                    '</div>';

                $boxPrestadores.append(bloco);
            });
        }
    }

    /** Adiciona um novo card de serviço **/
    function adicionarServico() {
        const idx = contadorServicos++;
        const $card = criarCardServico(idx);
        $('#lista-servicos-agendamento').append($card);
        renumerarServicos();
        atualizarResumoETextos();
    }

    // Botão principal para adicionar serviço
    $('#btn-adicionar-servico').on('click', function() {
        adicionarServico();
    });

    // Começamos com 1 serviço por padrão
    adicionarServico();

    /** Delegação de eventos para os cards **/

    // Troca de modo (já escolhi / ainda não escolhi)
    $('#lista-servicos-agendamento').on('change', 'input[type="radio"][name^="modo_"]', function() {
        const $card = $(this).closest('.servico-card');
        atualizarModoCard($card);
        atualizarResumoETextos();
    });

    // Troca de serviço
    $('#lista-servicos-agendamento').on('change', '.input-servico', function() {
        const $card = $(this).closest('.servico-card');
        filtrarPrestadoresCardPorServico($card);
        atualizarResumoETextos();

        // Se estiver no modo "já escolhi", atualizar valor padrão se já tiver prestador
        if ($card.find('input[type="radio"][name^="modo_"]:checked').val() === 'ja_escolhi') {
            atualizarValorPadraoCard($card);
        }
    });

    // Troca de data/duração/exclusivo/valor
    $('#lista-servicos-agendamento').on('change keyup', '.input-data-inicio, .input-duracao, .input-exclusivo, .input-valor', function() {
        atualizarResumoETextos();
    });

    // Buscar prestadores disponíveis (modo "não escolhi")
    $('#lista-servicos-agendamento').on('click', '.btn-buscar-prestadores', function() {
        const $card = $(this).closest('.servico-card');
        buscarPrestadoresDisponiveis($card);
    });

    // Selecionar prestador de um card (lista de disponíveis)
    $('#lista-servicos-agendamento').on('click', '.card-prestador', function() {
        const $card = $(this).closest('.servico-card');

        $card.find('.card-prestador').removeClass('selected');
        $(this).addClass('selected');

        const id       = $(this).data('id');
        const valorNum = parseFloat($(this).data('valor')) || 0;

        $card.find('.input-prestador-hidden').val(id);
        if (valorNum > 0) {
            $card.find('.input-valor').val(formatarValorBR(valorNum));
        }

        atualizarResumoETextos();
    });

    // Verificar disponibilidade (modo "já escolhi")
    $('#lista-servicos-agendamento').on('click', '.btn-verificar-disponibilidade', function() {
        const $card = $(this).closest('.servico-card');
        verificarDisponibilidadeCard($card);
    });

    // Troca de prestador no select (modo "já escolhi")
    $('#lista-servicos-agendamento').on('change', '.input-prestador-select', function() {
        const $card = $(this).closest('.servico-card');
        atualizarValorPadraoCard($card);
        atualizarResumoETextos();
    });

    // Remover card de serviço
    $('#lista-servicos-agendamento').on('click', '.servico-card-remove', function() {
        if ($('.servico-card').length === 1) {
            alert('O agendamento precisa ter pelo menos um serviço.');
            return;
        }
        $(this).closest('.servico-card').remove();
        renumerarServicos(); 
        atualizarResumoETextos();
    });

    /** SUBMIT: monta data[Itens][...] dinamicamente **/
    $('form').on('submit', function(e) {
        const $form = $(this);

        // Validação básica do cliente
        const nomeCliente = $('#cliente-nome').val().trim();
        if (!nomeCliente) {
            e.preventDefault();
            alert('Informe o nome do cliente.');
            return false;
        }

        // Remove inputs Itens[...] antigos (se houver)
        $form.find('input[name^="Itens["]').remove();

        let indexItem = 0;
        let temPeloMenosUm = false;

        $('.servico-card').each(function() {
            const $card      = $(this);
            const servicoId  = $card.find('.input-servico').val();
            const dataInicio = $card.find('.input-data-inicio').val();
            let   duracao    = parseInt($card.find('.input-duracao').val(), 10);
            const exclusivo  = $card.find('.input-exclusivo').is(':checked') ? 1 : 0;
            const modo       = $card.find('input[type="radio"][name^="modo_"]:checked').val();
            let   prestadorId;

            if (!servicoId || !dataInicio) {
                return; // ignora cards incompletos
            }

            if (isNaN(duracao) || duracao <= 0) {
                duracao = 1;
            }

            if (modo === 'nao_escolhi') {
                prestadorId = $card.find('.input-prestador-hidden').val();
            } else {
                prestadorId = $card.find('.input-prestador-select').val();
            }

            if (!prestadorId) {
                // serviço preenchido mas sem prestador -> erro
                e.preventDefault();
                alert('Selecione um prestador para todos os serviços preenchidos.');
                temPeloMenosUm = false;
                return false; // quebra o each
            }

            const valorStr = $card.find('.input-valor').val();
            const valorNum = parseValor(valorStr);

            function addHidden(name, value) {
                $form.append(
                    $('<input>', { type: 'hidden', name: name, value: value })
                );
            }

            addHidden('Itens[' + indexItem + '][prestador_id]', prestadorId);
            addHidden('Itens[' + indexItem + '][servico_id]',   servicoId);
            addHidden('Itens[' + indexItem + '][data_inicio]',  dataInicio);
            addHidden('Itens[' + indexItem + '][duracao_dias]', duracao);
            addHidden('Itens[' + indexItem + '][exclusivo]',    exclusivo ? 1 : 0);
            if (valorStr) {
                addHidden('Itens[' + indexItem + '][valor]', valorStr);
            }

            indexItem++;
            temPeloMenosUm = true;
        });

        if (!temPeloMenosUm) {
            e.preventDefault();
            alert('Adicione pelo menos um serviço completo (serviço, data e prestador).');
            return false;
        }

        // deixa enviar normalmente
    });
});
</script>
