<?php
/**
 * AsyncHttp - класс для работы с http через неблокируемые сокеты
 *
 * @author Jeck (http://jeck.ru)
 */
class AsyncHttp_Client {
    private $sockets = array();
    private $threads = array();
    
    /**
     * Создает сокет и отправляет http запрос
     * @param string $url адрес на который отправляется запрос
     * @param string $method тип запроса, POST или GET
     * @param array $data данные отправляемые при POST запросом
     * @return int $id идентификатор запроса
     * @return false в случае ошибки
     */
    private function request($url, $method='GET', $data=array()) {
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            return false;
        }
        if (!isset($parts['port'])) {
            $parts['port'] = 80;
        }
        if (!isset($parts['path'])) {
            $parts['path'] = '/';
        }
        if ($data && $method == 'POST') {
            $data = http_build_query($data);
        } else {
            $data = false;
        }
        
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($socket, $parts['host'], $parts['port']);
        // Если установить флаг до socket_connect соединения не происходит
        socket_set_nonblock($socket);
        
        socket_write($socket, $method." ".$parts['path']. '?' .$parts['query'] ." HTTP/1.1\r\n");
        socket_write($socket, "Host: ".$parts['host']."\r\n");
        socket_write($socket, "Connection: close\r\n");
        if ($data) {
            socket_write($socket, "Content-Type: application/x-www-form-urlencoded\r\n");
            socket_write($socket, "Content-length: ".strlen($data)."\r\n");
            socket_write($socket, "\r\n");
            socket_write($socket, $data."\r\n");
        }
        socket_write($socket, "\r\n");
        
        $this->sockets[] = $socket;
        return max(array_keys($this->sockets));
    }
    
    /**
     * Выполняет GET запрос с помощью метода AsyncHttp::request
     * @see function request
     * @param string $url
     * @return int $id
     */
    public function get($url) {
        return $this->request($url, 'GET');
    }
    
    /**
     * Выполняет POST запрос с помощью метода AsyncHttp::request
     * @see function request
     * @param string $url
     * @param array $data
     * @return int $id
     */
    public function post($url, $data=array()) {
        return $this->request($url, 'POST', $data);
    }
    
    /**
     * Получает данные из сокетов и возвращает массив идентификаторов
     * успешно выполненных запросов в случае успеха
     * @return bool|array
     */
    public function iteration() {
        if (count($this->sockets) == 0) {
            return false;
        }
        $threads = array();
        foreach ($this->sockets as $key => $socket) {
            $data = socket_read($socket, 0xffff);
            if ($data) {
                $threads[] = $key;
                $this->setThread($key, $data);
                unset($this->sockets[$key]);
                continue;
            }
        }
        // На всякий случай
        usleep(5);
        return $threads;
    }
    
    /**
     * Устанавливает ответ сокета
     * @return void
     */
    private function setThread($id, $data) {
        $this->threads[$id] = $data;
    }
    
    /**
     * Возвращает полученные данные из сокета
     * @param int $id идентификатор сокета
     * @param bool $headers=false если true возвращает данные вместе с заголовками
     * @return bool|array
     */
    public function getThread($id, $headers=false) {
        if (!isset($this->threads[$id])) {
            return false;
        }
        if ($headers) {
            return $this->threads[$id];
        } else {
            return substr($this->threads[$id], strpos($this->threads[$id], "\r\n\r\n") + 4);
        }
    }
}
