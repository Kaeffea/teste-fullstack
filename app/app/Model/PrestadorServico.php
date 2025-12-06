<?php
App::uses('AppModel', 'Model');

/**
 * Model PrestadorServico
 * Tabela pivô que armazena a relação entre Prestador e Serviço
 */
class PrestadorServico extends AppModel {
    
    public $name = 'PrestadorServico';
    
    public $actsAs = array('Containable');
    
    // Validações
    public $validate = array(
        'prestador_id' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Prestador inválido'
            )
        ),
        'servico_id' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Serviço inválido'
            )
        ),
        'valor' => array(
            'decimal' => array(
                'rule' => array('decimal', 2),
                'message' => 'Valor inválido'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'O valor é obrigatório'
            )
        )
    );
    
    // Relacionamentos
    public $belongsTo = array(
        'Prestador' => array(
            'className' => 'Prestador',
            'foreignKey' => 'prestador_id'
        ),
        'Servico' => array(
            'className' => 'Servico',
            'foreignKey' => 'servico_id'
        )
    );
}