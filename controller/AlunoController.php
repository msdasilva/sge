<?php 

include_once '../model/aluno.php';

class AlunoController {
    
    private $requestMethod;
    private $id;
    private $aluno;

    const STATUS_CODE_HEADER = 'status_code_header';
    const HTTP_1_1_200_OK = 'HTTP/1.1 200 OK';

    public function __construct($requestMethod, $id)
    {       
        $this->requestMethod = $requestMethod;
        $this->id = $id;

        $this->aluno = new Aluno();

    }

    public function APIRESTFULL()
    {
        switch ($this->requestMethod) {
            case 'GET':                
                $response = ($this->id) ? $this->getAluno($this->id) : $this->getAllAluno() ;
                break;
            case 'POST':
                $response = $this->createAluno();
                break;
            case 'PUT':
                $response = $this->updateAluno($this->id);
                break;
            case 'DELETE':
                $response = $this->deleteAluno($this->id);
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

    private function getAllAluno()
    {
        $result = $this->aluno->findAll();
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAluno($id)
    {
        $result = $this->aluno->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAluno()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->aluno->insert($input);
        $response[self::STATUS_CODE_HEADER] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateAluno($id)
    {        
        $result = $this->aluno->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->aluno->update($id, $input);
        $response[self::STATUS_CODE_HEADER] = self::HTTP_1_1_200_OK;
        $response['body'] = null;
        return $response;
    }

    private function deleteAluno($id)
    {
        $result = $this->aluno->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->aluno->delete($id);
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