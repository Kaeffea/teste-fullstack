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
        $busca = '';
        if (!empty($this->request->query['busca'])) {
            $busca = $this->request->query['busca'];
            $conditions['OR'] = array(
                'Prestador.nome LIKE' => '%' . $busca . '%',
                'Prestador.sobrenome LIKE' => '%' . $busca . '%',
                'Prestador.email LIKE' => '%' . $busca . '%'
            );
        }
        
        // Aplicar condições na configuração do Paginator
        if (!isset($this->Paginator->settings['Prestador'])) {
            $this->Paginator->settings['Prestador'] = array();
        }
        $this->Paginator->settings['Prestador']['conditions'] = $conditions;
        
        // Buscar prestadores com paginação
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
        $this->set('busca', $busca);
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $this->Prestador->create();
            
            // Upload da foto (Mantenha seu código de upload aqui)
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
        
        $servicos = $this->Servico->find('list', array(
            'fields' => array('Servico.id', 'Servico.nome'),
            'order' => array('Servico.nome' => 'ASC')
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
            'fields' => array('Servico.id', 'Servico.nome'),
            'order' => array('Servico.nome' => 'ASC')
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
     * deleteSelected - Excluir múltiplos prestadores
     * POST /prestadores/deleteSelected
     */
    public function deleteSelected() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $ids = array();
        if (!empty($this->request->data['ids']) && is_array($this->request->data['ids'])) {
            $ids = array_map('intval', $this->request->data['ids']);
        }

        if (empty($ids)) {
            $this->Session->setFlash('Nenhum prestador selecionado para exclusão.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'index'));
        }

        // Busca todos os prestadores para poder apagar as fotos depois
        $prestadores = $this->Prestador->find('all', array(
            'conditions' => array('Prestador.id' => $ids),
            'fields' => array('Prestador.id', 'Prestador.foto')
        ));

        // Exclui todos
        $this->Prestador->deleteAll(
            array('Prestador.id' => $ids),
            true,   // cascade
            true    // callbacks
        );

        // Deleta as fotos físicas
        foreach ($prestadores as $p) {
            if (!empty($p['Prestador']['foto'])) {
                $this->_deletarFoto($p['Prestador']['foto']);
            }
        }

        $this->Session->setFlash('Prestadores excluídos com sucesso!', 'default', array('class' => 'alert alert-success'));
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
            // Verifica se o checkbox está marcado OU se tem valor preenchido
            $checked = !empty($dados['checked']) || (isset($dados['checked']) && $dados['checked'] == '1');
            
            if ($checked && !empty($dados['valor'])) {
                // Limpar valor (remover R$, pontos e trocar vírgula por ponto)
                $valor = $dados['valor'];
                
                // Se já vier como número (ex: 200.00), não precisa limpar
                if (!is_numeric($valor)) {
                    $valor = preg_replace('/[^0-9.,]/', '', $valor);
                    if (strpos($valor, ',') !== false) {
                        $valor = str_replace('.', '', $valor); // Remove ponto de milhar
                        $valor = str_replace(',', '.', $valor); // Troca vírgula decimal por ponto
                    }
                }
                
                // Converter para float
                $valor = floatval($valor);
                
                // Só salvar se o valor for maior que zero
                if ($valor > 0) {
                    $this->PrestadorServico->create();
                    $resultado = $this->PrestadorServico->save(array(
                        'PrestadorServico' => array(
                            'prestador_id' => $prestadorId,
                            'servico_id' => $servicoId,
                            'valor' => $valor
                        )
                    ));
                    
                    // Debug (remover depois)
                    if (!$resultado) {
                        $this->log("Erro ao salvar serviço $servicoId para prestador $prestadorId", 'debug');
                    }
                }
            }
        }
    }

    /**
     * importar - Exibe formulário de importação
     * GET /prestadores/importar
     */
    public function importar() {
        // Apenas exibe o formulário
    }
    
    /**
     * processar_importacao - Processa arquivo CSV
     * POST /prestadores/processar_importacao
     */
    public function processar_importacao() {
        if (!$this->request->is('post')) {
            return $this->redirect(array('action' => 'index'));
        }
        
        // Verificar se arquivo foi enviado
        if (empty($this->request->data['Prestador']['arquivo']['name'])) {
            $this->Session->setFlash('Por favor, selecione um arquivo CSV.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'importar'));
        }
        
        $arquivo = $this->request->data['Prestador']['arquivo'];
        
        // Validar extensão
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        if (strtolower($extensao) !== 'csv') {
            $this->Session->setFlash('Por favor, envie um arquivo CSV válido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'importar'));
        }
        
        // Ler arquivo CSV
        $dados = $this->_lerCSV($arquivo['tmp_name']);
        
        if (empty($dados)) {
            $this->Session->setFlash('Arquivo CSV vazio ou inválido.', 'default', array('class' => 'alert alert-error'));
            return $this->redirect(array('action' => 'importar'));
        }
        
        // Processar importação
        $sucessos = 0;
        $erros = 0;
        $mensagensErro = array();
        
        foreach ($dados as $linha => $prestador) {
            // Validar dados obrigatórios
            if (empty($prestador['nome']) || empty($prestador['email'])) {
                $erros++;
                $mensagensErro[] = "Linha " . ($linha + 2) . ": Nome e email são obrigatórios";
                continue;
            }
            
            // Verificar se email já existe
            $existe = $this->Prestador->find('count', array(
                'conditions' => array('Prestador.email' => $prestador['email'])
            ));
            
            if ($existe > 0) {
                $erros++;
                $mensagensErro[] = "Linha " . ($linha + 2) . ": Email {$prestador['email']} já cadastrado";
                continue;
            }
            
            // Preparar dados para salvar
            $dadosPrestador = array(
                'Prestador' => array(
                    'nome' => $prestador['nome'],
                    'sobrenome' => isset($prestador['sobrenome']) ? $prestador['sobrenome'] : '',
                    'email' => $prestador['email'],
                    'telefone' => isset($prestador['telefone']) ? $prestador['telefone'] : ''
                )
            );
            
            // Salvar prestador
            $this->Prestador->create();
            if ($this->Prestador->save($dadosPrestador)) {
                $sucessos++;
                
                // Se tiver serviços, associar
                if (!empty($prestador['servicos']) && !empty($prestador['valores'])) {
                    $this->_associarServicosImportacao(
                        $this->Prestador->id, 
                        $prestador['servicos'], 
                        $prestador['valores']
                    );
                }
            } else {
                $erros++;
                $mensagensErro[] = "Linha " . ($linha + 2) . ": Erro ao salvar prestador";
            }
        }
        
        // Mensagem de resultado
        if ($sucessos > 0) {
            $msg = "$sucessos prestador(es) importado(s) com sucesso!";
            if ($erros > 0) {
                $msg .= " $erros erro(s) encontrado(s).";
            }
            $this->Session->setFlash($msg, 'default', array('class' => 'alert alert-success'));
        } else {
            $this->Session->setFlash('Nenhum prestador foi importado. Verifique o arquivo.', 'default', array('class' => 'alert alert-error'));
        }
        
        // Salvar erros em sessão para exibir
        if (!empty($mensagensErro)) {
            $this->Session->write('erros_importacao', $mensagensErro);
        }
        
        return $this->redirect(array('action' => 'index'));
    }
    
    /**
     * _lerCSV - Lê arquivo CSV e retorna array de dados
     * @param string $caminho - caminho do arquivo
     * @return array
     */
    private function _lerCSV($caminho) {
        $dados = array();
        
        if (($handle = fopen($caminho, 'r')) !== false) {
            // Ler cabeçalho
            $headers = fgetcsv($handle, 1000, ';');
            
            // Se não houver cabeçalho, tentar vírgula
            if (count($headers) === 1) {
                rewind($handle);
                $headers = fgetcsv($handle, 1000, ',');
            }
            
            // Normalizar cabeçalhos (remover BOM, espaços, acentos)
            $headers = array_map(function($h) {
                $h = trim($h);
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // Remove BOM
                $h = strtolower($h);
                $h = $this->_removerAcentos($h);
                return $h;
            }, $headers);
            
            // Ler linhas
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                // Se só tem 1 coluna, tentar vírgula
                if (count($row) === 1) {
                    $row = str_getcsv($row[0], ',');
                }
                
                // Combinar cabeçalhos com dados
                if (count($row) === count($headers)) {
                    $linha = array_combine($headers, $row);
                    
                    // Processar serviços (se houver coluna 'servicos')
                    if (isset($linha['servicos'])) {
                        $linha['servicos'] = array_map('trim', explode('|', $linha['servicos']));
                    }
                    
                    // Processar valores (se houver coluna 'valores')
                    if (isset($linha['valores'])) {
                        $linha['valores'] = array_map('trim', explode('|', $linha['valores']));
                    }
                    
                    $dados[] = $linha;
                }
            }
            
            fclose($handle);
        }
        
        return $dados;
    }
    
    /**
     * _associarServicosImportacao - Associa serviços ao prestador na importação
     * @param int $prestadorId
     * @param array $nomeServicos
     * @param array $valores
     */
    private function _associarServicosImportacao($prestadorId, $nomeServicos, $valores) {
        foreach ($nomeServicos as $index => $nomeServico) {
            // Buscar serviço pelo nome
            $servico = $this->Servico->find('first', array(
                'conditions' => array('Servico.nome LIKE' => '%' . trim($nomeServico) . '%'),
                'fields' => array('Servico.id')
            ));
            
            if (!empty($servico)) {
                $valor = isset($valores[$index]) ? $this->_limparValor($valores[$index]) : 0;
                
                $this->PrestadorServico->create();
                $this->PrestadorServico->save(array(
                    'PrestadorServico' => array(
                        'prestador_id' => $prestadorId,
                        'servico_id' => $servico['Servico']['id'],
                        'valor' => $valor
                    )
                ));
            }
        }
    }
    
    /**
     * _limparValor - Limpa valor monetário detectando formato BR ou US
     * @param string $valor
     * @return float
     */
    private function _limparValor($valor) {
        // Remove R$ e espaços
        $valor = str_replace(array('R$', ' '), '', $valor);
        
        // Verifica se tem vírgula (formato BR: 1.000,00)
        if (strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor); // Remove ponto de milhar
            $valor = str_replace(',', '.', $valor); // Troca vírgula por ponto
        } 
        // Se NÃO tem vírgula, mas tem ponto (formato CSV/US: 1000.00),
        // a gente não faz nada, pois o floatval já entende.
        
        return floatval($valor);
    }
    
    /**
     * _removerAcentos - Remove acentos de string
     * @param string $string
     * @return string
     */
    private function _removerAcentos($string) {
        $acentos = array(
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n'
        );
        return strtr($string, $acentos);
    }
}
