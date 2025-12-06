<?php
App::uses('AppController', 'Controller');

/**
 * Controller de Prestadores
 * Gerencia CRUD completo de prestadores de serviço
 */
class PrestadoresController extends AppController {
    
    public $name = 'Prestadores';
    
    // Usar os models
    public $uses = array('Prestador', 'Servico', 'PrestadorServico');
    
    // Componentes necessários
    public $components = array('Paginator', 'RequestHandler');
    
    // Helpers para as views
    public $helpers = array('Html', 'Form', 'Paginator');
    
    /**
     * beforeFilter - executado antes de cada action
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // Configurar paginação
        $this->Paginator->settings = array(
            'Prestador' => array(
                'limit' => 6, // 6 por página (igual ao Figma)
                'order' => array('Prestador.id' => 'DESC'),
                'contain' => array('Servico') // Trazer serviços junto
            )
        );
    }
    
    /**
     * index - Lista todos os prestadores
     * GET /prestadores
     */
    public function index() {
        // Configurar o model para usar Containable
        $this->Prestador->Behaviors->load('Containable');
        
        // Busca (se houver termo de busca)
        $conditions = array();
        if (!empty($this->request->query['busca'])) {
            $busca = $this->request->query['busca'];
            $conditions['OR'] = array(
                'Prestador.nome LIKE' => '%' . $busca . '%',
                'Prestador.sobrenome LIKE' => '%' . $busca . '%',
                'Prestador.email LIKE' => '%' . $busca . '%'
            );
        }
        
        // Buscar prestadores com paginação
        $this->Paginator->settings['conditions'] = $conditions;
        $prestadores = $this->Paginator->paginate('Prestador');
        
        // Para cada prestador, buscar os serviços com preço
        foreach ($prestadores as &$prestador) {
            if (!empty($prestador['Prestador']['id'])) {
                $prestador['ServicosComPreco'] = $this->PrestadorServico->find('all', array(
                    'conditions' => array('PrestadorServico.prestador_id' => $prestador['Prestador']['id']),
                    'contain' => array('Servico')
                ));
            }
        }
        
        // Enviar para a view
        $this->set('prestadores', $prestadores);
        $this->set('busca', isset($busca) ? $busca : '');
    }
    
    /**
     * add - Adicionar novo prestador
     * GET /prestadores/add (exibe formulário)
     * POST /prestadores/add (processa dados)
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Prestador->create();
            
            // Upload da foto
            if (!empty($this->request->data['Prestador']['foto']['name'])) {
                $foto = $this->_uploadFoto($this->request->data['Prestador']['foto']);
                if ($foto) {
                    $this->request->data['Prestador']['foto'] = $foto;
                } else {
                    unset($this->request->data['Prestador']['foto']);
                }
            } else {
                unset($this->request->data['Prestador']['foto']);
            }
            
            // Salvar prestador
            if ($this->Prestador->save($this->request->data)) {
                $prestadorId = $this->Prestador->id;
                
                // Salvar serviços com preços
                if (!empty($this->request->data['Servicos'])) {
                    $this->_salvarServicos($prestadorId, $this->request->data['Servicos']);
                }
                
                $this->Session->setFlash('Prestador cadastrado com sucesso!', 'default', array('class' => 'alert alert-success'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao cadastrar prestador. Verifique os dados.', 'default', array('class' => 'alert alert-error'));
            }
        }
        
        // Buscar todos os serviços para o formulário
        $servicos = $this->Servico->find('list', array(
            'fields' => array('Servico.id', 'Servico.nome')
        ));
        
        $this->set('servicos', $servicos);
    }
    
    /**
     * edit - Editar prestador existente
     * GET /prestadores/edit/1 (exibe formulário)
     * POST /prestadores/edit/1 (processa dados)
     */
    public function edit($id = null) {
        if (!$id) {
            $this->Session->setFlash('Prestador inválido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->Prestador->id = $id;
        if (!$this->Prestador->exists()) {
            $this->Session->setFlash('Prestador não encontrado.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            // Upload de nova foto
            if (!empty($this->request->data['Prestador']['foto']['name'])) {
                $foto = $this->_uploadFoto($this->request->data['Prestador']['foto']);
                if ($foto) {
                    // Deletar foto antiga
                    $prestadorAntigo = $this->Prestador->findById($id);
                    if (!empty($prestadorAntigo['Prestador']['foto'])) {
                        $this->_deletarFoto($prestadorAntigo['Prestador']['foto']);
                    }
                    $this->request->data['Prestador']['foto'] = $foto;
                } else {
                    unset($this->request->data['Prestador']['foto']);
                }
            } else {
                unset($this->request->data['Prestador']['foto']);
            }
            
            if ($this->Prestador->save($this->request->data)) {
                // Atualizar serviços
                if (isset($this->request->data['Servicos'])) {
                    // Deletar relações antigas
                    $this->PrestadorServico->deleteAll(array('PrestadorServico.prestador_id' => $id));
                    // Salvar novas
                    $this->_salvarServicos($id, $this->request->data['Servicos']);
                }
                
                $this->Session->setFlash('Prestador atualizado com sucesso!', 'default', array('class' => 'alert alert-success'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao atualizar prestador.', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            // GET - carregar dados para o formulário
            $this->request->data = $this->Prestador->findById($id);
            
            // Carregar serviços já vinculados
            $servicosVinculados = $this->PrestadorServico->find('all', array(
                'conditions' => array('PrestadorServico.prestador_id' => $id)
            ));
            
            // Formatar para o formulário
            $servicosData = array();
            foreach ($servicosVinculados as $sv) {
                $servicosData[$sv['PrestadorServico']['servico_id']] = array(
                    'checked' => true,
                    'valor' => $sv['PrestadorServico']['valor']
                );
            }
            $this->set('servicosVinculados', $servicosData);
        }
        
        // Buscar todos os serviços
        $servicos = $this->Servico->find('list', array(
            'fields' => array('Servico.id', 'Servico.nome')
        ));
        
        $this->set('servicos', $servicos);
    }
    
    /**
     * delete - Excluir prestador
     * POST /prestadores/delete/1
     */
    public function delete($id = null) {
        // Só aceita POST
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        
        $this->Prestador->id = $id;
        if (!$this->Prestador->exists()) {
            $this->Session->setFlash('Prestador não encontrado.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }
        
        // Buscar prestador para deletar foto
        $prestador = $this->Prestador->findById($id);
        
        if ($this->Prestador->delete()) {
            // Deletar foto
            if (!empty($prestador['Prestador']['foto'])) {
                $this->_deletarFoto($prestador['Prestador']['foto']);
            }
            
            $this->Session->setFlash('Prestador excluído com sucesso!', 'default', array('class' => 'alert alert-success'));
        } else {
            $this->Session->setFlash('Erro ao excluir prestador.', 'default', array('class' => 'alert alert-error'));
        }
        
        return $this->redirect(array('action' => 'index'));
    }
    
    /**
     * _uploadFoto - Processa upload de foto
     * @param array $foto - dados do arquivo $_FILES
     * @return string|false - nome do arquivo salvo ou false
     */
    private function _uploadFoto($foto) {
        // Validar tipo de arquivo
        $tiposPermitidos = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml');
        if (!in_array($foto['type'], $tiposPermitidos)) {
            $this->Session->setFlash('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou SVG.', 'default', array('class' => 'alert alert-error'));
            return false;
        }
        
        // Validar tamanho (2MB)
        if ($foto['size'] > 2097152) {
            $this->Session->setFlash('Arquivo muito grande. Máximo 2MB.', 'default', array('class' => 'alert alert-error'));
            return false;
        }
        
        // Gerar nome único
        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . '_' . time() . '.' . $extensao;
        
        // Diretório de upload
        $diretorio = WWW_ROOT . 'files' . DS . 'uploads' . DS;
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        
        // Mover arquivo
        if (move_uploaded_file($foto['tmp_name'], $diretorio . $nomeArquivo)) {
            return $nomeArquivo;
        }
        
        return false;
    }
    
    /**
     * _deletarFoto - Remove arquivo de foto
     * @param string $nomeArquivo
     */
    private function _deletarFoto($nomeArquivo) {
        $caminho = WWW_ROOT . 'files' . DS . 'uploads' . DS . $nomeArquivo;
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }
    
    /**
     * _salvarServicos - Salva relação prestador x serviços com preços
     * @param int $prestadorId
     * @param array $servicos - array com [servico_id => ['valor' => X]]
     */
    private function _salvarServicos($prestadorId, $servicos) {
        foreach ($servicos as $servicoId => $dados) {
            if (!empty($dados['checked']) || !empty($dados['valor'])) {
                $valor = !empty($dados['valor']) ? $dados['valor'] : 0;
                
                $this->PrestadorServico->create();
                $this->PrestadorServico->save(array(
                    'PrestadorServico' => array(
                        'prestador_id' => $prestadorId,
                        'servico_id' => $servicoId,
                        'valor' => $valor
                    )
                ));
            }
        }
    }
}