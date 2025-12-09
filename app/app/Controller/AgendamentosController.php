<?php
App::uses('AppController', 'Controller');

class AgendamentosController extends AppController {

    public $name = 'Agendamentos';

    public $uses = array(
        'Agendamento',
        'AgendamentoItem',
        'Prestador',
        'Servico',
        'PrestadorServico'
    );

    public $components = array('Paginator', 'RequestHandler');
    public $helpers    = array('Html', 'Form', 'Paginator');

    /**
     * Calcula a menor data_inicio e a maior data_fim dos itens
     * para resumir o período do agendamento.
     *
     * @param array $agendamento
     * @return array [$dataInicio, $dataFim] em Y-m-d ou [null, null]
     */
    protected function _calcularPeriodo(array $agendamento) {
        $inicio = null;
        $fim    = null;

        if (!empty($agendamento['AgendamentoItem'])) {
            foreach ($agendamento['AgendamentoItem'] as $item) {
                if (!empty($item['data_inicio'])) {
                    if ($inicio === null || $item['data_inicio'] < $inicio) {
                        $inicio = $item['data_inicio'];
                    }
                }
                if (!empty($item['data_fim'])) {
                    if ($fim === null || $item['data_fim'] > $fim) {
                        $fim = $item['data_fim'];
                    }
                }
            }
        }

        return array($inicio, $fim);
    }

/**
 * Calcula o status dinâmico de um agendamento
 * considerando TODOS os itens (serviços) e datas.
 *
 * Regras:
 *  - Se pelo menos 1 item estiver em andamento hoje -> "Em produção"
 *  - Senão, se houver item futuro -> "Marcado"
 *  - Senão (todos passados) -> "Finalizado"
 */
protected function _statusDinamico($agendamento) {
    if (empty($agendamento['AgendamentoItem'])) {
        // Sem itens ainda → considerado marcado
        return 'Marcado';
    }

    $tz   = new DateTimeZone(date_default_timezone_get());
    $hoje = new DateTime('today', $tz); // só a data de hoje

    $temPassado      = false;
    $temFuturo       = false;
    $temEmAndamento  = false;

    foreach ($agendamento['AgendamentoItem'] as $item) {
        if (empty($item['data_inicio']) || empty($item['data_fim'])) {
            continue;
        }

        try {
            $inicio = new DateTime($item['data_inicio'], $tz);
            $fim    = new DateTime($item['data_fim'], $tz);

            // zera horário pra comparar só a data
            $inicio->setTime(0, 0, 0);
            $fim->setTime(0, 0, 0);
        } catch (Exception $e) {
            continue;
        }

        if ($hoje < $inicio) {
            $temFuturo = true;
        } elseif ($hoje > $fim) {
            $temPassado = true;
        } else {
            // hoje está entre início e fim (inclusive)
            $temEmAndamento = true;
        }
    }

    if ($temEmAndamento) {
        return 'Em produção';
    }

    if ($temFuturo) {
        return 'Marcado';
    }

    // só sobrou: tudo pra trás
    return 'Finalizado';
}




    public function beforeFilter() {
        parent::beforeFilter();

        $this->Paginator->settings = array(
            'Agendamento' => array(
                'limit' => 10,
                'order' => array('Agendamento.id' => 'DESC'),
                'contain' => array(
                    'AgendamentoItem' => array(
                        'Prestador',
                        'Servico'
                    )
                )
            )
        );
    }

    /**
     * index - lista agendamentos com filtros simples (id, nome do cliente, status)
     */
    public function index() {
        $conditions = array();
        $busca      = '';
        $status     = '';

        if (!empty($this->request->query['busca'])) {
            $busca = trim($this->request->query['busca']);

            // permite buscar por ID ou por nome
            if (ctype_digit($busca)) {
                $conditions['Agendamento.id'] = (int)$busca;
            } else {
                $conditions['Agendamento.cliente_nome LIKE'] = '%' . $busca . '%';
            }
        }

        if (!empty($this->request->query['status'])) {
            $status = $this->request->query['status'];
            $conditions['Agendamento.status'] = $status;
        }

        $this->Paginator->settings['Agendamento']['conditions'] = $conditions;

        $agendamentos = $this->Paginator->paginate('Agendamento');
        // Anexa período e status dinâmico em cada agendamento
        foreach ($agendamentos as &$ag) {
            $statusCalculado = $this->_statusDinamico($ag);
            list($dataInicioPeriodo, $dataFimPeriodo) = $this->_calcularPeriodo($ag);

            $ag['Agendamento']['status_calculado']   = $statusCalculado;
            $ag['Agendamento']['periodo_inicio']     = $dataInicioPeriodo;
            $ag['Agendamento']['periodo_fim']        = $dataFimPeriodo;
        }
        unset($ag);

        $this->set(compact('agendamentos', 'busca', 'status'));
    }

    public function view($id = null) {
        if (!$id || !ctype_digit((string)$id)) {
            $this->Session->setFlash('Agendamento inválido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        $agendamento = $this->Agendamento->find('first', array(
            'conditions' => array('Agendamento.id' => (int)$id),
            'contain' => array(
                'AgendamentoItem' => array(
                    'Prestador',
                    'Servico'
                )
            )
        ));

        if (empty($agendamento)) {
            $this->Session->setFlash('Agendamento não encontrado.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        $statusCalculado = $this->_statusDinamico($agendamento);
        list($dataInicioPeriodo, $dataFimPeriodo) = $this->_calcularPeriodo($agendamento);

        $this->set(compact(
            'agendamento',
            'statusCalculado',
            'dataInicioPeriodo',
            'dataFimPeriodo'
        ));
    }

    public function delete($id = null) {
        if (!$this->request->is('post') && !$this->request->is('delete')) {
            throw new MethodNotAllowedException();
        }

        if (!$id || !ctype_digit((string)$id)) {
            $this->Session->setFlash('Agendamento inválido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->Agendamento->id = (int)$id;

        if (!$this->Agendamento->exists()) {
            $this->Session->setFlash('Agendamento não encontrado.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        // Se no model Agendamento o hasMany AgendamentoItem estiver com 'dependent' => true,
        // os itens serão apagados automaticamente.
        if ($this->Agendamento->delete()) {
            $this->Session->setFlash('Agendamento excluído com sucesso.', 'default', array('class' => 'alert alert-success'));
        } else {
            $this->Session->setFlash('Não foi possível excluir o agendamento.', 'default', array('class' => 'alert alert-error'));
        }

        return $this->redirect(array('action' => 'index'));
    }

    /**
     * (Próximo passo) add / salvar agendamento completo
     * Aqui depois vamos receber:
     * - dados do cliente
     * - lista de itens (servico, prestador, data_inicio, duracao, exclusivo)
     * - conferir disponibilidade com AgendamentoItem->isDisponivel()
     * - calcular valor dos itens via PrestadorServico
     * - salvar tudo e marcar status = 'marcado'
     */

        /**
     * salvar - Cria um novo agendamento com 1 ou vários itens
     * POST /agendamentos/salvar
     *
     * Espera em $this->request->data:
     *  - Agendamento[cliente_nome, cliente_email, cliente_telefone, observacoes]
     *  - Itens[] com [prestador_id, servico_id, data_inicio (Y-m-d), duracao_dias, exclusivo]
     *
     * Se for AJAX, retorna JSON. Se for form normal, faz redirect com flash.
     */
    public function salvar() {
        $isAjax = $this->RequestHandler->isAjax();
        if ($isAjax) {
            $this->autoRender = false;
            $this->response->type('json');
        }

        if (!$this->request->is('post')) {
            if ($isAjax) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Método inválido. Use POST.'
                ));
                return;
            }

            $this->Session->setFlash('Método inválido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        $dadosAgendamento = isset($this->request->data['Agendamento'])
            ? $this->request->data['Agendamento']
            : array();

        $itens = isset($this->request->data['Itens'])
            ? $this->request->data['Itens']
            : array();

        if (empty($itens)) {
            $msg = 'Adicione pelo menos um serviço ao agendamento.';

            if ($isAjax) {
                echo json_encode(array(
                    'success' => false,
                    'message' => $msg
                ));
                return;
            }

            $this->Session->setFlash($msg, 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        // Validação básica do cliente
        if (empty($dadosAgendamento['cliente_nome'])) {
            $msg = 'Informe o nome do cliente.';

            if ($isAjax) {
                echo json_encode(array(
                    'success' => false,
                    'message' => $msg
                ));
                return;
            }

            $this->Session->setFlash($msg, 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        $ds = $this->Agendamento->getDataSource();
        $ds->begin();

        try {
            // 1) Criar agendamento (status = marcado, pois aqui é a confirmação)
            $this->Agendamento->create();
            $salvoCabecalho = $this->Agendamento->save(array(
                'Agendamento' => array(
                    'cliente_nome'     => trim($dadosAgendamento['cliente_nome']),
                    'cliente_email'    => !empty($dadosAgendamento['cliente_email']) ? trim($dadosAgendamento['cliente_email']) : null,
                    'cliente_telefone' => !empty($dadosAgendamento['cliente_telefone']) ? trim($dadosAgendamento['cliente_telefone']) : null,
                    'status'           => 'marcado',
                    'observacoes'      => !empty($dadosAgendamento['observacoes']) ? trim($dadosAgendamento['observacoes']) : null
                )
            ));

            if (!$salvoCabecalho) {
                throw new Exception('Erro ao salvar os dados do cliente.');
            }

            $agendamentoId = (int)$this->Agendamento->id;

            // 2) Processar itens
            $total = 0.0;

            foreach ($itens as $idx => $item) {
                $linha = $idx + 1;

                $prestadorId = !empty($item['prestador_id']) ? (int)$item['prestador_id'] : null;
                $servicoId   = !empty($item['servico_id']) ? (int)$item['servico_id'] : null;
                $dataInicio  = !empty($item['data_inicio']) ? $item['data_inicio'] : null;
                $duracao     = !empty($item['duracao_dias']) ? (int)$item['duracao_dias'] : 1;
                $exclusivo   = !empty($item['exclusivo']) ? 1 : 0;

                $hoje = date('Y-m-d');
                if ($dataInicio < $hoje) {
                    throw new Exception("Item #$linha: a data de início não pode ser no passado.");
                }

                if (!$servicoId || !$dataInicio) {
                    throw new Exception("Item #$linha: serviço e data de início são obrigatórios.");
                }

                // Calcula data_fim
                $dataFim = $this->AgendamentoItem->calcularDataFim($dataInicio, $duracao);

                // Se tiver prestador, checar disponibilidade
                if (!empty($prestadorId)) {
                    $disponivel = $this->AgendamentoItem->isDisponivel(
                        $prestadorId,
                        $dataInicio,
                        $dataFim,
                        $exclusivo
                    );

                    if (!$disponivel) {
                        // Erro específico sobre disponibilidade
                        throw new Exception(
                            "Item #$linha: o prestador selecionado não está disponível entre "
                            . $dataInicio . " e " . $dataFim . "."
                        );
                    }
                }

                // Valor do serviço: permite override vindo do formulário
                $valor = 0.0;

                if (isset($item['valor']) && $item['valor'] !== '') {
                    // aceita "250", "250,00", "250.00" ou "1.250,00"
                    $rawValor = trim($item['valor']);
                    $rawValor = str_replace(array('R$', ' '), '', $rawValor);

                    if (strpos($rawValor, ',') !== false && strpos($rawValor, '.') !== false) {
                        // 1.234,56 -> 1234.56
                        $rawValor = str_replace('.', '', $rawValor);
                        $rawValor = str_replace(',', '.', $rawValor);
                    } elseif (strpos($rawValor, ',') !== false) {
                        // 250,00 -> 250.00
                        $rawValor = str_replace(',', '.', $rawValor);
                    }

                    $valor = (float)$rawValor;
                } else {
                    // Buscar valor padrão do serviço para esse prestador
                    if (!empty($prestadorId)) {
                        $ps = $this->PrestadorServico->find('first', array(
                            'conditions' => array(
                                'PrestadorServico.prestador_id' => $prestadorId,
                                'PrestadorServico.servico_id'   => $servicoId
                            ),
                            'fields' => array('PrestadorServico.valor')
                        ));

                        if (!empty($ps)) {
                            $valor = (float)$ps['PrestadorServico']['valor'];
                        }
                    }
                }


                // Criar item do agendamento
                $this->AgendamentoItem->create();
                $salvoItem = $this->AgendamentoItem->save(array(
                    'AgendamentoItem' => array(
                        'agendamento_id' => $agendamentoId,
                        'prestador_id'   => $prestadorId,
                        'servico_id'     => $servicoId,
                        'data_inicio'    => $dataInicio,
                        'data_fim'       => $dataFim,
                        'duracao_dias'   => $duracao,
                        'exclusivo'      => $exclusivo,
                        'status'         => 'marcado', // já bloqueia agenda
                        'valor'          => $valor
                    )
                ));

                if (!$salvoItem) {
                    throw new Exception("Item #$linha: erro ao salvar o serviço.");
                }

                $total += $valor;
            }

            // 3) Atualizar total do agendamento
            $this->Agendamento->id = $agendamentoId;
            $this->Agendamento->saveField('total', $total);

            $ds->commit();

            if ($isAjax) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Agendamento criado com sucesso!',
                    'agendamento_id' => $agendamentoId
                ));
                return;
            }

            $this->Session->setFlash('Agendamento criado com sucesso!', 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('action' => 'view', $agendamentoId));
        } catch (Exception $e) {
            $ds->rollback();

            if ($isAjax) {
                echo json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage()
                ));
                return;
            }

            $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }
    }

        /**
     * prestadores_disponiveis
     * Retorna lista de prestadores disponíveis para um serviço em uma data/duração.
     *
     * GET /agendamentos/prestadores_disponiveis?servico_id=1&data=2025-12-10&duracao=2&exclusivo=1
     *
     * Retorna JSON:
     *  - success
     *  - prestadores[] com [id, nome_completo, email, telefone, valor]
     */
    public function prestadores_disponiveis() {
        $this->autoRender = false;
        $this->response->type('json');

        if (!$this->request->is('get') && !$this->RequestHandler->isAjax()) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Requisição inválida.'
            ));
            return;
        }

        $servicoId  = !empty($this->request->query['servico_id']) ? (int)$this->request->query['servico_id'] : 0;
        $dataInicio = !empty($this->request->query['data']) ? $this->request->query['data'] : null;
        $duracao    = !empty($this->request->query['duracao']) ? (int)$this->request->query['duracao'] : 1;
        $exclusivo  = !empty($this->request->query['exclusivo']) ? 1 : 0;

        if (!$servicoId || !$dataInicio) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Informe o serviço e a data.'
            ));
            return;
        }

        // Calcular data fim
        $dataFim = $this->AgendamentoItem->calcularDataFim($dataInicio, $duracao);

        // Buscar prestadores que oferecem esse serviço
        $relacoes = $this->PrestadorServico->find('all', array(
            'conditions' => array(
                'PrestadorServico.servico_id' => $servicoId
            ),
            'contain' => array('Prestador')
        ));

        $resultado = array();

        foreach ($relacoes as $rel) {
            $prestador = $rel['Prestador'];
            $valor     = (float)$rel['PrestadorServico']['valor'];

            $prestadorId = (int)$prestador['id'];

            // Checar disponibilidade
            $disponivel = $this->AgendamentoItem->isDisponivel(
                $prestadorId,
                $dataInicio,
                $dataFim,
                $exclusivo
            );

            if ($disponivel) {
                $resultado[] = array(
                    'id'            => $prestadorId,
                    'nome_completo' => $prestador['nome'] . ' ' . $prestador['sobrenome'],
                    'email'         => $prestador['email'],
                    'telefone'      => $prestador['telefone'],
                    'valor'         => $valor
                );
            }
        }

        echo json_encode(array(
            'success'     => true,
            'total'       => count($resultado),
            'prestadores' => $resultado
        ));
    }

    /**
     * disponibilidade_prestador
     *
     * GET /agendamentos/disponibilidade_prestador?prestador_id=1&servico_id=3&data=2025-12-10&duracao=2&exclusivo=1
     *
     * Retorna JSON com:
     *  - success
     *  - disponivel (bool)
     *  - message
     */
    public function disponibilidade_prestador() {
        $this->autoRender = false;
        $this->response->type('json');

        if (!$this->request->is('get') && !$this->RequestHandler->isAjax()) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Requisição inválida.'
            ));
            return;
        }

        $prestadorId = !empty($this->request->query['prestador_id']) ? (int)$this->request->query['prestador_id'] : 0;
        $dataInicio  = !empty($this->request->query['data']) ? $this->request->query['data'] : null;
        $duracao     = !empty($this->request->query['duracao']) ? (int)$this->request->query['duracao'] : 1;
        $exclusivo   = !empty($this->request->query['exclusivo']) ? 1 : 0;

        if (!$prestadorId || !$dataInicio) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Informe o prestador e a data.'
            ));
            return;
        }

        $dataFim = $this->AgendamentoItem->calcularDataFim($dataInicio, $duracao);

        $disponivel = $this->AgendamentoItem->isDisponivel(
            $prestadorId,
            $dataInicio,
            $dataFim,
            $exclusivo
        );

        echo json_encode(array(
            'success'    => true,
            'disponivel' => $disponivel,
            'message'    => $disponivel
                ? 'Prestador disponível para o período informado.'
                : 'Prestador indisponível para o período informado.'
        ));
    }

    /**
     * novo - fluxo simples de criação de agendamento com 1 serviço
     * GET  /agendamentos/novo  -> exibe formulário
     * POST /agendamentos/novo  -> valida e salva (Agendamento + 1 AgendamentoItem)
     */
    public function novo() {
        // Carregar serviços e prestadores para os selects
        $servicos = $this->Servico->find('list', array(
            'fields' => array('Servico.id', 'Servico.nome'),
            'order'  => array('Servico.nome' => 'ASC')
        ));

        $relacoes = $this->PrestadorServico->find('all', array(
            'contain' => array('Prestador', 'Servico'),
            'order'   => array(
                'Prestador.nome'      => 'ASC',
                'Prestador.sobrenome' => 'ASC'
            )
        ));

        $prestadores           = array(); // id => "Nome Sobrenome"
        $mapPrestadorServicos  = array();

        foreach ($relacoes as $rel) {
            $p    = $rel['Prestador'];
            $s    = $rel['Servico'];
            $idP  = (int)$p['id'];
            $idS  = (int)$s['id'];

            if (!isset($prestadores[$idP])) {
                $prestadores[$idP] = $p['nome'] . ' ' . $p['sobrenome'];
            }

            if (!isset($mapPrestadorServicos[$idP])) {
                $mapPrestadorServicos[$idP] = array();
            }

            if (!in_array($idS, $mapPrestadorServicos[$idP], true)) {
                $mapPrestadorServicos[$idP][] = $idS;
            }
        }

        $this->set(compact('servicos', 'prestadores', 'mapPrestadorServicos'));

        if ($this->request->is('post')) {
            $dados = $this->request->data;

            // ----- Dados do cliente -----
            $clienteNome   = trim($dados['Agendamento']['cliente_nome']);
            $clienteEmail  = trim($dados['Agendamento']['cliente_email']);
            $clienteTel    = trim($dados['Agendamento']['cliente_telefone']);

            // ----- Serviço / prestador / agenda -----
            $servicoId     = !empty($dados['Agendamento']['servico_id']) ? (int)$dados['Agendamento']['servico_id'] : null;
            $prestadorId   = !empty($dados['Agendamento']['prestador_id']) ? (int)$dados['Agendamento']['prestador_id'] : null;
            $dataInicioStr = !empty($dados['Agendamento']['data_inicio']) ? $dados['Agendamento']['data_inicio'] : null;
            $duracaoDias   = !empty($dados['Agendamento']['duracao_dias']) ? (int)$dados['Agendamento']['duracao_dias'] : 1;
            $exclusivo     = !empty($dados['Agendamento']['exclusivo']) ? 1 : 0;

            if ($duracaoDias <= 0) {
                $duracaoDias = 1;
            }

            $erros = array();

            if ($clienteNome === '') {
                $erros[] = 'Informe o nome do cliente.';
            }
            if ($clienteEmail === '') {
                $erros[] = 'Informe o e-mail do cliente.';
            }
            if (!$servicoId) {
                $erros[] = 'Selecione um serviço.';
            }
            if (!$prestadorId) {
                $erros[] = 'Selecione um prestador.';
            }
            if (!$dataInicioStr) {
                $erros[] = 'Informe a data de início.';
            }

            // Converter a data de início e calcular data fim usando o helper do model
            $dataInicio = null;
            $dataFim    = null;

            if ($dataInicioStr) {
                // <input type="date"> já manda em YYYY-MM-DD, então em geral basta isso:
                $dataInicio = date('Y-m-d', strtotime($dataInicioStr));

                // usa o método do AgendamentoItem que você já tem
                $dataFim = $this->AgendamentoItem->calcularDataFim($dataInicio, $duracaoDias);
            }

            if (!empty($erros)) {
                $this->Session->setFlash(
                    implode('<br>', $erros),
                    'default',
                    array('class' => 'alert alert-error')
                );
                $this->set(compact('servicos', 'prestadores'));
                return;
            }

            // ----- Checar disponibilidade do prestador -----
            $disponivel = $this->AgendamentoItem->isDisponivel(
                $prestadorId,
                $dataInicio,
                $dataFim,
                $exclusivo
            );

            if (!$disponivel) {
                $this->Session->setFlash(
                    "Este prestador não está disponível entre $dataInicio e $dataFim.",
                    'default',
                    array('class' => 'alert alert-error')
                );
                $this->set(compact('servicos', 'prestadores'));
                return;
            }

            // ----- Descobrir o valor do serviço para este prestador -----
            $valor = 0.0;
            $relacao = $this->PrestadorServico->find('first', array(
                'conditions' => array(
                    'PrestadorServico.prestador_id' => $prestadorId,
                    'PrestadorServico.servico_id'   => $servicoId
                ),
                'fields' => array('PrestadorServico.valor')
            ));

            if (!empty($relacao)) {
                $valor = (float)$relacao['PrestadorServico']['valor'];
            }

            // ----- Salvar Agendamento + Item dentro de transação -----
            $ds = $this->Agendamento->getDataSource();
            $ds->begin();

            try {
                // Cabeçalho do agendamento
                $this->Agendamento->create();
                $okCabecalho = $this->Agendamento->save(array(
                    'Agendamento' => array(
                        'cliente_nome'     => $clienteNome,
                        'cliente_email'    => $clienteEmail,
                        'cliente_telefone' => $clienteTel,
                        'status'           => 'marcado',
                        'observacoes'      => null,
                        'total'            => $valor
                    )
                ));

                if (!$okCabecalho) {
                    throw new Exception('Erro ao salvar os dados do agendamento.');
                }

                $agendamentoId = (int)$this->Agendamento->id;

                // Item único
                $this->AgendamentoItem->create();
                $okItem = $this->AgendamentoItem->save(array(
                    'AgendamentoItem' => array(
                        'agendamento_id' => $agendamentoId,
                        'prestador_id'   => $prestadorId,
                        'servico_id'     => $servicoId,
                        'data_inicio'    => $dataInicio,
                        'data_fim'       => $dataFim,
                        'duracao_dias'   => $duracaoDias,
                        'exclusivo'      => $exclusivo,
                        'status'         => 'marcado',
                        'valor'          => $valor
                    )
                ));

                if (!$okItem) {
                    throw new Exception('Erro ao salvar o serviço deste agendamento.');
                }

                $ds->commit();

                $this->Session->setFlash(
                    'Agendamento criado com sucesso!',
                    'default',
                    array('class' => 'alert alert-success')
                );

                // Já manda pra tela de detalhes bonitinha
                return $this->redirect(array('action' => 'view', $agendamentoId));

            } catch (Exception $e) {
                $ds->rollback();
                $this->Session->setFlash(
                    $e->getMessage(),
                    'default',
                    array('class' => 'alert alert-error')
                );
            }
        }

        $this->set(compact('servicos', 'prestadores'));
    }

    /**
 * valor_padrao
 * Retorna o valor padrão de um serviço para um prestador
 *
 * GET /agendamentos/valor_padrao?prestador_id=1&servico_id=2
 */
public function valor_padrao() {
    $this->autoRender = false;
    $this->response->type('json');

    if (!$this->request->is('get') || !$this->RequestHandler->isAjax()) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Requisição inválida.'
        ));
        return;
    }

    $prestadorId = !empty($this->request->query['prestador_id']) ? (int)$this->request->query['prestador_id'] : 0;
    $servicoId   = !empty($this->request->query['servico_id'])   ? (int)$this->request->query['servico_id']   : 0;

    if (!$prestadorId || !$servicoId) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Informe prestador e serviço.'
        ));
        return;
    }

    $relacao = $this->PrestadorServico->find('first', array(
        'conditions' => array(
            'PrestadorServico.prestador_id' => $prestadorId,
            'PrestadorServico.servico_id'   => $servicoId
        ),
        'fields' => array('PrestadorServico.valor')
    ));

    if (empty($relacao)) {
        echo json_encode(array(
            'success' => true,
            'valor'   => null
        ));
        return;
    }

    echo json_encode(array(
        'success' => true,
        'valor'   => (float)$relacao['PrestadorServico']['valor']
    ));
}

}