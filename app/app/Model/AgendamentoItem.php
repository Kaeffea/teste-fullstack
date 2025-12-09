<?php
App::uses('AppModel', 'Model');

class AgendamentoItem extends AppModel {

    public $name = 'AgendamentoItem';

    public $actsAs = array('Containable');

    public $belongsTo = array(
        'Agendamento' => array(
            'className'  => 'Agendamento',
            'foreignKey' => 'agendamento_id'
        ),
        'Prestador' => array(
            'className'  => 'Prestador',
            'foreignKey' => 'prestador_id'
        ),
        'Servico' => array(
            'className'  => 'Servico',
            'foreignKey' => 'servico_id'
        )
    );

    public $validate = array(
        'servico_id' => array(
            'notBlank' => array(
                'rule'    => 'notBlank',
                'message' => 'Selecione o serviço.'
            )
        ),
        'data_inicio' => array(
            'notBlank' => array(
                'rule'    => 'notBlank',
                'message' => 'Informe a data de início.'
            )
        ),
        'duracao_dias' => array(
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'A duração deve ser um número de dias.'
            ),
            'minValue' => array(
                'rule'    => array('comparison', '>=', 1),
                'message' => 'A duração mínima é de 1 dia.'
            )
        )
    );

    /**
     * Calcula data_fim com base em data_inicio + duracao_dias.
     * Considera que a duração é em dias inteiros e o serviço ocupa o dia inteiro.
     *
     * @param string $dataInicio formato Y-m-d
     * @param int    $duracaoDias
     * @return string data_fim em Y-m-d
     */
    public function calcularDataFim($dataInicio, $duracaoDias) {
        $inicio = new DateTime($dataInicio);
        // duração 1 dia -> fim = próprio dia
        $inicio->modify('+' . max((int)$duracaoDias - 1, 0) . ' day');
        return $inicio->format('Y-m-d');
    }

    /**
     * Verifica se o prestador está disponível para o intervalo solicitado.
     *
     * Regras:
     * - Novo serviço EXCLUSIVO (exclusivo=1):
     *     não pode sobrepor com NENHUM outro serviço (exclusivo ou não)
     * - Novo serviço NÃO exclusivo (exclusivo=0):
     *     não pode sobrepor com serviços exclusivos já marcados
     *     (pode sobrepor com outros não exclusivos)
     *
     * Apenas itens com status 'marcado' ou 'em_producao' contam como bloqueio.
     *
     * @param int    $prestadorId
     * @param string $dataInicio formato Y-m-d
     * @param string $dataFim    formato Y-m-d
     * @param int    $exclusivo  0 ou 1
     * @param int|null $ignoreId opcional, para ignorar o próprio item (edição)
     * @return bool true se disponível, false se existe conflito
     */
    public function isDisponivel($prestadorId, $dataInicio, $dataFim, $exclusivo = 0, $ignoreId = null) {
        $prestadorId = (int)$prestadorId;
        $exclusivo   = (int)$exclusivo;

        if (!$prestadorId || !$dataInicio || !$dataFim) {
            return false;
        }

        $conditions = array(
            'AgendamentoItem.prestador_id' => $prestadorId,
            'AgendamentoItem.status'       => array('marcado', 'em_producao'),
            // intervalo se sobrepõe se inicio <= fim existente E fim >= inicio existente
            'AgendamentoItem.data_inicio <=' => $dataFim,
            'AgendamentoItem.data_fim >='    => $dataInicio,
        );

        // Se o novo serviço NÃO é exclusivo,
        // só nos preocupamos com os existentes exclusivos
        if (!$exclusivo) {
            $conditions['AgendamentoItem.exclusivo'] = 1;
        }

        if ($ignoreId) {
            $conditions['AgendamentoItem.id !='] = (int)$ignoreId;
        }

        $conflitos = $this->find('count', array(
            'conditions' => $conditions
        ));

        return $conflitos == 0;
    }

    /**
     * Verifica se um prestador está disponível em um intervalo de datas,
     * respeitando regra de serviço exclusivo.
     *
     * Regras:
     * - Considera apenas agendamentos com status diferente de "rascunho" e "cancelado"
     * - Se o NOVO serviço for exclusivo:
     *      → qualquer serviço existente que tenha interseção de datas bloqueia
     * - Se o NOVO serviço NÃO for exclusivo:
     *      → só bloqueia se houver um serviço EXISTENTE exclusivo que intersete
     *
     * @param int    $prestadorId
     * @param string $dataInicio  (YYYY-MM-DD)
     * @param string $dataFim     (YYYY-MM-DD)
     * @param bool   $exclusivo   (true = novo serviço exclusivo)
     * @return array ['disponivel' => bool, 'conflitos' => array]
     */
    public function verificarDisponibilidade($prestadorId, $dataInicio, $dataFim, $exclusivo = false)
    {
        $prestadorId = (int)$prestadorId;
        if (!$prestadorId || !$dataInicio || !$dataFim) {
            return array('disponivel' => false, 'conflitos' => array());
        }

        // Garante as datas no formato certo
        $dataInicio = date('Y-m-d', strtotime($dataInicio));
        $dataFim    = date('Y-m-d', strtotime($dataFim));

        // Associa com Agendamento para filtrar por status
        $this->bindModel(array(
            'belongsTo' => array(
                'Agendamento' => array(
                    'className'  => 'Agendamento',
                    'foreignKey' => 'agendamento_id'
                )
            )
        ), false);

        // Busca todos os itens que tenham interseção de datas
        $conflitos = $this->find('all', array(
            'conditions' => array(
                'AgendamentoItem.prestador_id' => $prestadorId,

                // intervalo existente intersectando com [dataInicio, dataFim]
                'AgendamentoItem.data_inicio <=' => $dataFim,
                'AgendamentoItem.data_fim >='    => $dataInicio,

                // só considera agendamentos realmente "válidos" na agenda
                'Agendamento.status NOT' => array('rascunho', 'cancelado')
            ),
            'contain' => array('Agendamento')
        ));

        if (empty($conflitos)) {
            return array('disponivel' => true, 'conflitos' => array());
        }

        $bloqueios = array();

        foreach ($conflitos as $item) {
            $existenteExclusivo = !empty($item['AgendamentoItem']['exclusivo']);

            // Se o novo é exclusivo → qualquer overlap bloqueia
            if ($exclusivo) {
                $bloqueios[] = $item;
                continue;
            }

            // Se o novo NÃO é exclusivo, mas o existente é exclusivo → bloqueia
            if ($existenteExclusivo) {
                $bloqueios[] = $item;
            }
        }

        if (empty($bloqueios)) {
            return array('disponivel' => true, 'conflitos' => array());
        }

        return array('disponivel' => false, 'conflitos' => $bloqueios);
    }
}