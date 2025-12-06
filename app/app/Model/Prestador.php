<?php
App::uses('AppModel', 'Model');

/**
 * Model Prestador
 * Representa os prestadores de serviço cadastrados
 */
class Prestador extends AppModel {
    
    public $name = 'Prestador';
    
    // Campos que serão preenchidos automaticamente
    public $actsAs = array('Containable');
    
    // Validações
    public $validate = array(
        'nome' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'O nome é obrigatório'
            )
        ),
        'sobrenome' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'O sobrenome é obrigatório'
            )
        ),
        'email' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Por favor, insira um email válido'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Este email já está cadastrado'
            )
        ),
        'telefone' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'O telefone é obrigatório'
            )
        )
    );
    
    // Relacionamento
    public $hasAndBelongsToMany = array(
        'Servico' => array(
            'className' => 'Servico',
            'joinTable' => 'prestadores_servicos',
            'foreignKey' => 'prestador_id',
            'associationForeignKey' => 'servico_id',
            'with' => 'PrestadorServico',
            'unique' => true
        )
    );
}