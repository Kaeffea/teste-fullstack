<?php
App::uses('AppModel', 'Model');

/**
 * Model Servico
 * Representa os tipos de serviços disponíveis
 */
class Servico extends AppModel {
    
    public $name = 'Servico';
    
    public $actsAs = array('Containable');
    
    // Validações
    public $validate = array(
        'nome' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'O nome do serviço é obrigatório'
            )
        )
    );
    
    // Relacionamento
    public $hasAndBelongsToMany = array(
        'Prestador' => array(
            'className' => 'Prestador',
            'joinTable' => 'prestadores_servicos',
            'foreignKey' => 'servico_id',
            'associationForeignKey' => 'prestador_id',
            'with' => 'PrestadorServico',
            'unique' => true
        )
    );
}