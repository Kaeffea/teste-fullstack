<?php
App::uses('AppModel', 'Model');

class Agendamento extends AppModel {

    public $name = 'Agendamento';

    public $actsAs = array('Containable');

    public $hasMany = array(
        'AgendamentoItem' => array(
            'className'  => 'AgendamentoItem',
            'foreignKey' => 'agendamento_id',
            'dependent'  => true
        )
    );

    // Apenas para referência, se quiser validar depois
    public $validate = array(
        'cliente_nome' => array(
            'notBlank' => array(
                'rule'    => 'notBlank',
                'message' => 'Informe o nome do cliente.'
            )
        )
    );

    /**
     * Recalcula o total do agendamento com base nos itens.
     * Chamar após salvar/alterar itens.
     */
    public function atualizarTotal($agendamentoId) {
        $this->id = (int)$agendamentoId;
        if (!$this->exists()) {
            return false;
        }

        $total = $this->AgendamentoItem->find('first', array(
            'conditions' => array('AgendamentoItem.agendamento_id' => $agendamentoId),
            'fields'     => array('SUM(AgendamentoItem.valor) AS soma')
        ));

        $valorTotal = !empty($total[0]['soma']) ? (float)$total[0]['soma'] : 0.0;

        return $this->saveField('total', $valorTotal);
    }
}
