<?php 

include_once '../model/usuario.php';

class UsuarioController {
    
    private $requestMethod;
    private $id;
    private $Usuario;

    const STATUS_CODE_HEADER = 'status_code_header';
    const HTTP_1_1_200_OK = 'HTTP/1.1 200 OK';

    public function __construct($requestMethod, $id)
    {       
        $this->requestMethod = $requestMethod;
        $this->id = $id;

        $this->usuario = new Usuario();

    }

    public function APIRESTFULL()
    {
        switch ($this->requestMethod) {
            case 'GET':                
                $response = ($this->id) ? $this->getUsuario($this->id) : $this->getAllUsuario() ;
                break;
            case 'POST':
                $response = $this->createUsuario();
                break;
            case 'PUT':
                $response = $this->updateUsuario($this->id);
                break;
            case 'DELETE':
                $response = $this->deleteUsuario($this->id);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response[self::STATUS_CODE_HEADER]);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsuario()
    {
        $result = $this->usuario->findAll();
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUsuario($id)
    {
        $result = $this->usuario->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUsuario()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usuario->insert($input);
        $response[self::STATUS_CODE_HEADER] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUsuario($id)
    {        
        $result = $this->usuario->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usuario->update($id, $input);
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = null;
        return $response;
    }

    private function deleteUsuario($id)
    {
        $result = $this->usuario->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->usuario->delete($id);
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = null;
        return $response;
    }

    private function validatePerson($input)
    {
        if (! isset($input['nome']) || ! isset($input['descricao']) || ! isset($input['ativo']) || ! isset($input['cricacao'])) {
            return false;
        }        
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response[self::STATUS_CODE_HEADER] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response[self::STATUS_CODE_HEADER] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
?>